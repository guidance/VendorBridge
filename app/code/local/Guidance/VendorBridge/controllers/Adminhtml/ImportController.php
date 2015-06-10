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
class Guidance_VendorBridge_Adminhtml_ImportController extends Guidance_VendorBridge_Adminhtml_IntegrationController
{
    /**
     * @return Guidance_VendorBridge_Adminhtml_ImportController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('vendorbridge/imports')
            ->_addBreadcrumb(
            Mage::helper('vendorbridge')->__('Vendor Bridge Import Manager'),
            Mage::helper('vendorbridge')->__('Vendor Bridge Import Manager')
        );

        return $this;
    }

    public function indexAction()
    {
        /** @var $vendors Guidance_VendorBridge_Model_Resource_Vendor_Collection */
        $vendors = Mage::getModel('vendorbridge/vendor')->getCollection()
            ->addFieldToFilter('status', array('eq' => Guidance_VendorBridge_Model_Source_Status::STATUS_ENABLED));
        if ($vendors->getSize() < 1) {
            /** @var $session Mage_Adminhtml_Model_Session */
            $session = Mage::getSingleton('adminhtml/session');
            $session->addError(
                Mage::helper('vendorbridge')->__('You must create/enable a Vendor before you can import files')
            );
        }

        $this->_initAction()->renderLayout();
    }

    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('import', false);
        /** @var $session Mage_Adminhtml_Model_Session */
        $session = Mage::getSingleton('adminhtml/session');
        if (!is_array($ids)) {
            $session->addError(Mage::helper('vendorbridge')->__('Please select item(s)'));
        } else {
            try {
                $idsToDelete = array();
                foreach ($ids as $id) {
                    $idsToDelete[] = $id;
                }
                $resource = Mage::getResourceSingleton('vendorbridge/import');
                $resource->deleteRowsByIds($idsToDelete);

                $session->addSuccess(
                    Mage::helper('vendorbridge')->__(
                        'Total of %d record(s) were successfully deleted', count($ids)
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

    public function exportCsvAction()
    {
        $fileName = 'vendorbridge_import.csv';
        $layout   = $this->getLayout();
        /** @var $block Guidance_VendorBridge_Block_Adminhtml_Import_Grid */
        $block   = $layout->createBlock('vendorbridge/adminhtml_import_grid');
        $content = $block->getCsv();
        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName = 'vendorbridge_import.xml';
        $layout   = $this->getLayout();
        /** @var $block Guidance_VendorBridge_Block_Adminhtml_Import_Grid */
        $block   = $layout->createBlock('vendorbridge/adminhtml_import_grid');
        $content = $block->getXml();
        $this->_sendUploadResponse($fileName, $content);
    }

    public function importOrdersAction()
    {
        /** @var $session Mage_Adminhtml_Model_Session */
        $session = Mage::getSingleton('adminhtml/session');
        try {
            /** @var $model Guidance_VendorBridge_Model_Import_Orders */
            $model = Mage::getModel('vendorbridge/import_orders');
            $model->importOrders();

            $session->addSuccess(
                Mage::helper('vendorbridge')->__(
                    'The Vendor Bridge order import process has been run.'
                )
            );
        } catch (Exception $e) {
            $session->addError(
                Mage::helper('vendorbridge')->__($e->getMessage())
            );
        }
        $this->_redirect('*/*/index');
    }
}
