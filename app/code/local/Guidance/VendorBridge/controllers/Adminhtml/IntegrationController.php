<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Adminhtml_IntegrationController extends Mage_Adminhtml_Controller_Action
{
    public function preDispatch()
    {
        parent::preDispatch();

        /** @var $session Mage_Adminhtml_Model_Session */
        $session = Mage::getSingleton('adminhtml/session');
        /** @var $helper Guidance_VendorBridge_Helper_Data */
        $helper  = Mage::helper('vendorbridge');
        $enabled = $helper->isEnabled();
        if (!$enabled) {
            $this->setFlag('', 'no-dispatch', true);
            $session->addError(
                $helper->__(Guidance_VendorBridge_Helper_Data::NOT_ENABLED_MESSAGE)
            );
            $this->_redirectReferer();
            return;
        }
    }

    public function downloadAction()
    {
        $file     = $this->getRequest()->getParam('file');
        $file     = base64_decode($file);
        $fileName = basename($file);
        $content  = file_get_contents($file);
        $this->_sendUploadResponse($fileName, $content);
    }

    /**
     * @param        $fileName
     * @param        $content
     * @param string $contentType
     */
    protected function _sendUploadResponse($fileName, $content, $contentType = 'application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK', '');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die();
    }
}
