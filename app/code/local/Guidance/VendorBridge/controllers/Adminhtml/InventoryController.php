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
class Guidance_VendorBridge_Adminhtml_InventoryController extends Guidance_VendorBridge_Adminhtml_IntegrationController
{
    /**
     * @return Guidance_VendorBridge_Adminhtml_InventoryController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('vendorbridge/inventory')
            ->_addBreadcrumb(
            Mage::helper('vendorbridge')->__('Vendor Bridge Inventory Manager'),
            Mage::helper('vendorbridge')->__('Vendor Bridge Inventory Manager')
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
                Mage::helper('vendorbridge')->__('You must create/enable a Vendor before you can export inventory')
            );
        }

        $this->_initAction()->renderLayout();
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        /** @var $model Guidance_VendorBridge_Model_Inventory */
        $model = Mage::getModel('vendorbridge/inventory');
        $model->load($id);

        /** @var $session Mage_Adminhtml_Model_Session */
        $session = Mage::getSingleton('adminhtml/session');

        if ($model->getId() || $id == 0) {
            $data = $session->getData('vendorbridge_inventory_data', true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('vendorbridgeInventory_data', $model);
            $this->loadLayout();
            $this->_setActiveMenu('vendorbridge/inventory');

            $this->_addBreadcrumb(
                Mage::helper('vendorbridge')->__('Inventory Manager'),
                Mage::helper('vendorbridge')->__('Inventory Manager')
            );
            $this->_addBreadcrumb(
                Mage::helper('vendorbridge')->__('Manage'),
                Mage::helper('vendorbridge')->__('Manage')
            );
            $this->renderLayout();
        } else {
            $session->addError(
                Mage::helper('vendorbridge')->__('Inventory item does not exist')
            );
            $this->_redirect('*/*/');
        }
    }

    public function newAction()
    {
        $this->_forward('noroute');
    }

    public function saveAction()
    {
        /** @var $session Mage_Adminhtml_Model_Session */
        $session = Mage::getSingleton('adminhtml/session');

        $data = $this->getRequest()->getPost(null, false);
        if ($data) {
            if (!empty($data['created_at'])) {
                $data['created_at'] = Mage::app()->getLocale()->utcDate(null, $data['created_at'], true);
            }
            if (!empty($data['updated_at'])) {
                $data['updated_at'] = Mage::app()->getLocale()->utcDate(null, $data['updated_at'], true);
            }
            /** @var $model Guidance_VendorBridge_Model_Inventory */
            $model = Mage::getModel('vendorbridge/inventory');
            $model->setData($data)->setId($this->getRequest()->getParam('id'));
            try {
                $model->save();
                $session->addSuccess(
                    Mage::helper('vendorbridge')->__('Inventory item was successfully saved')
                );
                $session->setData('vendorbridge_inventory_data', false);
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
                $session->setData('vendorbridge_inventory_data', $data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $session->addError(
            Mage::helper('vendorbridge')->__('Unable to find inventory item to save')
        );
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        /** @var $session Mage_Adminhtml_Model_Session */
        $session = Mage::getSingleton('adminhtml/session');

        if ($this->getRequest()->getParam('id') > 0) {
            try {
                /** @var $model Guidance_VendorBridge_Model_Inventory */
                $model = Mage::getModel('vendorbridge/inventory');
                $model->setId($this->getRequest()->getParam('id'))->delete();
                $session->addSuccess(
                    Mage::helper('vendorbridge')->__('Inventory item was successfully deleted'));
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

        $ids = $this->getRequest()->getParam('inventory', false);
        if (!is_array($ids)) {
            $session->addError(
                Mage::helper('vendorbridge')->__('Please select item(s)'));
        } else {
            try {
                $idsToDelete = array();
                foreach ($ids as $id) {
                    $idsToDelete[] = $id;
                }
                $resource = Mage::getResourceSingleton('vendorbridge/inventory');
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

    public function inventoryCsvAction()
    {
        $fileName = 'vendorbridge_inventory.csv';
        $layout   = $this->getLayout();
        /** @var $block Guidance_VendorBridge_Block_Adminhtml_Inventory_Grid */
        $block   = $layout->createBlock('vendorbridge/adminhtml_inventory_grid');
        $content = $block->getCsv();
        $this->_sendUploadResponse($fileName, $content);
    }

    public function inventoryXmlAction()
    {
        $fileName = 'vendorbridge_inventory.xml';
        $layout   = $this->getLayout();
        /** @var $block Guidance_VendorBridge_Block_Adminhtml_Inventory_Grid */
        $block   = $layout->createBlock('vendorbridge/adminhtml_inventory_grid');
        $content = $block->getXml();
        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportFullFeedAction()
    {
        /** @var $session Mage_Adminhtml_Model_Session */
        $session = Mage::getSingleton('adminhtml/session');
        try {
            /** @var $model Guidance_VendorBridge_Model_Export_Inventory */
            $model = Mage::getModel('vendorbridge/export_inventory');
            $model->exportFull();

            $session->addSuccess(
                Mage::helper('vendorbridge')->__(
                    'The full inventory feed was successfully sent to Vendor Bridge.'
                )
            );
        } catch (Exception $e) {
            $session->addError(
                Mage::helper('vendorbridge')->__($e->getMessage())
            );
        }
        $this->_redirect('*/*/index');
    }

    public function exportPartialFeedAction()
    {
        /** @var $session Mage_Adminhtml_Model_Session */
        $session = Mage::getSingleton('adminhtml/session');
        try {
            /** @var $model Guidance_VendorBridge_Model_Export_Inventory */
            $model = Mage::getModel('vendorbridge/export_inventory');
            $model->exportPartial();

            $session->addSuccess(
                Mage::helper('vendorbridge')->__(
                    'A partial inventory feed was successfully sent to Vendor Bridge.'
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
