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
class Guidance_VendorBridge_Adminhtml_ExportController extends Guidance_VendorBridge_Adminhtml_IntegrationController
{
    /**
     * @return Guidance_VendorBridge_Adminhtml_ExportController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('vendorbridge/exports')
            ->_addBreadcrumb(
            Mage::helper('vendorbridge')->__('Vendor Bridge Export Manager'),
            Mage::helper('vendorbridge')->__('Vendor Bridge Export Manager')
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
                Mage::helper('vendorbridge')->__('You must create/enable a Vendor before you can export files')
            );
        }

        $this->_initAction()->renderLayout();
    }

    public function viewAction()
    {
        $id    = $this->getRequest()->getParam('id');
        $model = Mage::getModel('vendorbridge/export')->load($id);

        if ($model->getId()) {
            Mage::register('current_vendorbridge_export', $model, true);
            $this->loadLayout();
            $this->_setActiveMenu('vendorbridge/exports');
            $this->getLayout();
            $this->renderLayout();
        } else {
            /** @var $session Mage_Adminhtml_Model_Session */
            $session = Mage::getSingleton('adminhtml/session');
            $session->addError(Mage::helper('vendorbridge')->__('Export record does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('export', false);
        /** @var $session Mage_Adminhtml_Model_Session */
        $session = Mage::getSingleton('adminhtml/session');
        if (!is_array($ids)) {
            $session->addError(
                Mage::helper('vendorbridge')->__('Please select item(s)'));
        } else {
            try {
                $idsToDelete = array();
                foreach ($ids as $id) {
                    $idsToDelete[] = $id;
                }
                $resource = Mage::getResourceSingleton('vendorbridge/export');
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
        $fileName = 'vendorbridge_export.csv';
        $layout   = $this->getLayout();
        /** @var $block Guidance_VendorBridge_Block_Adminhtml_Export_Grid */
        $block   = $layout->createBlock('vendorbridge/adminhtml_export_grid');
        $content = $block->getCsv();
        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName = 'vendorbridge_export.xml';
        $layout   = $this->getLayout();
        /** @var $block Guidance_VendorBridge_Block_Adminhtml_Export_Grid */
        $block   = $layout->createBlock('vendorbridge/adminhtml_export_grid');
        $content = $block->getXml();
        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportShipConfirmationsAction()
    {
        /** @var $session Mage_Adminhtml_Model_Session */
        $session = Mage::getSingleton('adminhtml/session');
        try {
            /** @var $model Guidance_VendorBridge_Model_Export_Shipments */
            $model = Mage::getModel('vendorbridge/export_shipments');
            $model->exportShipments();

            $session->addSuccess(
                Mage::helper('vendorbridge')->__(
                    'Any pending shipment confirmations were successfully sent to Vendor Bridge.'
                )
            );
        } catch (Exception $e) {
            $session->addError(
                Mage::helper('vendorbridge')->__($e->getMessage())
            );
        }
        $this->_redirect('*/*/index');
    }

    public function exportInvoicesAction()
    {
        /** @var $session Mage_Adminhtml_Model_Session */
        $session = Mage::getSingleton('adminhtml/session');
        try {
            /** @var $model Guidance_VendorBridge_Model_Export_Invoices */
            $model = Mage::getModel('vendorbridge/export_invoices');
            $model->exportInvoices();

            $session->addSuccess(
                Mage::helper('vendorbridge')->__(
                    'Any pending invoice feeds were successfully sent to Vendor Bridge.'
                )
            );
        } catch (Exception $e) {
            $session->addError(
                Mage::helper('vendorbridge')->__($e->getMessage())
            );
        }
        $this->_redirect('*/*/index');
    }

    public function exportCancelsAction()
    {
        /** @var $session Mage_Adminhtml_Model_Session */
        $session = Mage::getSingleton('adminhtml/session');
        try {
            /** @var $model Guidance_VendorBridge_Model_Export_Cancels */
            $model = Mage::getModel('vendorbridge/export_cancels');
            $model->exportCancels();

            $session->addSuccess(
                Mage::helper('vendorbridge')->__(
                    'Any pending cancellations were successfully sent to Vendor Bridge.'
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
