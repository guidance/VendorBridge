<?php
require_once (Mage::getModuleDir('controllers', 'Guidance_VendorBridge') . DS . 'Adminhtml' . DS . 'IntegrationController.php');
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Adminhtml_VendorController extends Guidance_VendorBridge_Adminhtml_IntegrationController
{
    /**
     * @return Guidance_VendorBridge_Adminhtml_VendorController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('vendorbridge/vendor')
            ->_addBreadcrumb(
            Mage::helper('vendorbridge')->__('Vendor Bridge Vendor Manager'),
            Mage::helper('vendorbridge')->__('Vendor Bridge Vendor Manager')
        );

        return $this;
    }

    public function indexAction()
    {
        $this->_initAction()->renderLayout();
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        /** @var $model Guidance_VendorBridge_Model_Vendor */
        $model = Mage::getModel('vendorbridge/vendor');
        $model->load($id);

        /** @var $session Mage_Adminhtml_Model_Session */
        $session = Mage::getSingleton('adminhtml/session');

        if ($model->getId() || $id == 0) {
            $data = $session->getData('vendorbridge_vendor_data', true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('vendorbridgeVendor_data', $model);
            $this->loadLayout();
            $this->_setActiveMenu('vendorbridge/vendor');

            $this->_addBreadcrumb(
                Mage::helper('vendorbridge')->__('Vendor Manager'),
                Mage::helper('vendorbridge')->__('Vendor Manager')
            );
            $this->_addBreadcrumb(
                Mage::helper('vendorbridge')->__('Manage'),
                Mage::helper('vendorbridge')->__('Manage')
            );
            $this->renderLayout();
        } else {
            $session->addError(
                Mage::helper('vendorbridge')->__('Vendor does not exist')
            );
            $this->_redirect('*/*/');
        }
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function saveAction()
    {
        /** @var $session Mage_Adminhtml_Model_Session */
        $session = Mage::getSingleton('adminhtml/session');

        $data               = $this->getRequest()->getPost(null, false);
        $data['updated_at'] = now();
        if ($data) {
            /** @var $model Guidance_VendorBridge_Model_Vendor */
            $model = Mage::getModel('vendorbridge/vendor');
            $model->load($this->getRequest()->getParam('id'));
            if (!$model->getData('created_at')) {
                $data['created_at'] = now();
            }
            $model->addData($data)->setId($this->getRequest()->getParam('id'));
            try {
                $model->save();
                $session->addSuccess(
                    Mage::helper('vendorbridge')->__('Vendor was successfully saved')
                );
                $session->setData('vendorbridge_vendor_data', false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                $session->addError(
                    Mage::helper('vendorbridge')->__($e->getMessage())
                );
                $session->setData('vendorbridge_vendor_data', $data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $session->addError(
            Mage::helper('vendorbridge')->__('Unable to find vendor to save')
        );
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        /** @var $session Mage_Adminhtml_Model_Session */
        $session = Mage::getSingleton('adminhtml/session');

        if ($this->getRequest()->getParam('id') > 0) {
            try {
                /** @var $model Guidance_VendorBridge_Model_Vendor */
                $model = Mage::getModel('vendorbridge/vendor');
                $model->setId($this->getRequest()->getParam('id'))->delete();
                $session->addSuccess(
                    Mage::helper('vendorbridge')->__('Vendor was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                $session->addError(
                    Mage::helper('vendorbridge')->__($e->getMessage())
                );
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction()
    {
        /** @var $session Mage_Adminhtml_Model_Session */
        $session = Mage::getSingleton('adminhtml/session');

        $ids = $this->getRequest()->getParam('vendor', false);
        if (!is_array($ids)) {
            $session->addError(
                Mage::helper('vendorbridge')->__('Please select vendor(s)'));
        } else {
            try {
                $idsToDelete = array();
                foreach ($ids as $id) {
                    $idsToDelete[] = $id;
                }
                $resource = Mage::getResourceSingleton('vendorbridge/vendor');
                $resource->deleteRowsByIds($idsToDelete);

                $session->addSuccess(
                    Mage::helper('vendorbridge')->__(
                        'Total of %d vendor(s) were successfully deleted', count($ids)
                    )
                );
            } catch (Exception $e) {
                $session->addError(
                    Mage::helper('vendorbridge')->__($e->getMessage())
                );
            }
        }
        $this->_redirect('*/*/index');
    }

    public function vendorCsvAction()
    {
        $fileName = 'vendorbridge_vendor.csv';
        $layout   = $this->getLayout();
        /** @var $block Guidance_VendorBridge_Block_Adminhtml_Vendor_Grid */
        $block   = $layout->createBlock('vendorbridge/adminhtml_vendor_grid');
        $content = $block->getCsv();
        $this->_sendUploadResponse($fileName, $content);
    }

    public function vendorXmlAction()
    {
        $fileName = 'vendorbridge_vendor.xml';
        $layout   = $this->getLayout();
        /** @var $block Guidance_VendorBridge_Block_Adminhtml_Vendor_Grid */
        $block   = $layout->createBlock('vendorbridge/adminhtml_vendor_grid');
        $content = $block->getXml();
        $this->_sendUploadResponse($fileName, $content);
    }
}
