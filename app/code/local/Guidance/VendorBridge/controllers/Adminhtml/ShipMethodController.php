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
class Guidance_VendorBridge_Adminhtml_ShipMethodController extends Guidance_VendorBridge_Adminhtml_IntegrationController
{
    /**
     * @return Guidance_VendorBridge_Adminhtml_VendorController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('vendorbridge/shipMethod')
            ->_addBreadcrumb(
            Mage::helper('vendorbridge')->__('Vendor Bridge Ship Method Manager'),
            Mage::helper('vendorbridge')->__('Vendor Bridge Ship Method Manager')
        );

        return $this;
    }

    public function indexAction()
    {
        /** @var $vendors Guidance_VendorBridge_Model_Resource_Vendor_Collection */
        $vendors = Mage::getModel('vendorbridge/vendor')->getCollection();
        if ($vendors->getSize() < 1) {
            /** @var $session Mage_Adminhtml_Model_Session */
            $session = Mage::getSingleton('adminhtml/session');
            $session->addError(
                Mage::helper('vendorbridge')->__('You must create a Vendor before you can add ship methods')
            );
        }

        $this->_initAction()->renderLayout();
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        /** @var $model Guidance_VendorBridge_Model_ShipMethod */
        $model = Mage::getModel('vendorbridge/shipMethod');
        $model->load($id);

        /** @var $session Mage_Adminhtml_Model_Session */
        $session = Mage::getSingleton('adminhtml/session');
        /** @var $helper Guidance_VendorBridge_Helper_Data */
        $helper = Mage::helper('vendorbridge');

        if ($model->getId() || $id == 0) {
            $data = $session->getData('vendorbridge_shipMethod_data', true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('vendorbridgeShipMethod_data', $model);
            $this->loadLayout();
            $this->_setActiveMenu('vendorbridge/shipMethod');

            $this->_addBreadcrumb(
                $helper->__('Ship Method Manager'),
                $helper->__('Ship Method Manager')
            );
            $this->_addBreadcrumb(
                $helper->__('Manage'),
                $helper->__('Manage')
            );
            $this->renderLayout();
        } else {
            $session->addError(
                $helper->__('Ship Method does not exist')
            );
            $this->_redirect('*/*/');
        }
    }

    public function newAction()
    {
        /** @var $vendors Guidance_VendorBridge_Model_Resource_Vendor_Collection */
        $vendors = Mage::getModel('vendorbridge/vendor')->getCollection();
        if ($vendors->getSize() < 1) {
            $this->_forward('index');
            return;
        }
        $this->_forward('edit');
    }

    public function saveAction()
    {
        /** @var $session Mage_Adminhtml_Model_Session */
        $session = Mage::getSingleton('adminhtml/session');

        $data               = $this->getRequest()->getPost(null, false);
        $data['updated_at'] = now();
        if ($data) {
            /** @var $model Guidance_VendorBridge_Model_ShipMethod */
            $model = Mage::getModel('vendorbridge/shipMethod');
            $model->load($this->getRequest()->getParam('id'));
            if (!$model->getData('created_at')) {
                $data['created_at'] = now();
            }
            $model->addData($data)->setId($this->getRequest()->getParam('id'));
            try {
                $model->save();
                $session->addSuccess(
                    Mage::helper('vendorbridge')->__('Ship Method was successfully saved')
                );
                $session->setData('vendorbridge_shipMethod_data', false);
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
                $session->setData('vendorbridge_shipMethod_data', $data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $session->addError(
            Mage::helper('vendorbridge')->__('Unable to find ship method to save')
        );
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        /** @var $session Mage_Adminhtml_Model_Session */
        $session = Mage::getSingleton('adminhtml/session');

        if ($this->getRequest()->getParam('id') > 0) {
            try {
                /** @var $model Guidance_VendorBridge_Model_ShipMethod */
                $model = Mage::getModel('vendorbridge/shipMethod');
                $model->setId($this->getRequest()->getParam('id'))->delete();
                $session->addSuccess(
                    Mage::helper('vendorbridge')->__('Ship method was successfully deleted'));
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

        $ids = $this->getRequest()->getParam('shipMethod', false);
        if (!is_array($ids)) {
            $session->addError(
                Mage::helper('vendorbridge')->__('Please select ship method(s)'));
        } else {
            try {
                $idsToDelete = array();
                foreach ($ids as $id) {
                    $idsToDelete[] = $id;
                }
                $resource = Mage::getResourceSingleton('vendorbridge/shipMethod');
                $resource->deleteRowsByIds($idsToDelete);

                $session->addSuccess(
                    Mage::helper('vendorbridge')->__(
                        'Total of %d ship methods(s) were successfully deleted', count($ids)
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
        $fileName = 'vendorbridge_ship_method.csv';
        $layout   = $this->getLayout();
        /** @var $block Guidance_VendorBridge_Block_Adminhtml_ShipMethod_Grid */
        $block   = $layout->createBlock('vendorbridge/adminhtml_shipMethod_grid');
        $content = $block->getCsv();
        $this->_sendUploadResponse($fileName, $content);
    }

    public function vendorXmlAction()
    {
        $fileName = 'vendorbridge_ship_method.xml';
        $layout   = $this->getLayout();
        /** @var $block Guidance_VendorBridge_Block_Adminhtml_ShipMethod_Grid */
        $block   = $layout->createBlock('vendorbridge/adminhtml_shipMethod_grid');
        $content = $block->getXml();
        $this->_sendUploadResponse($fileName, $content);
    }
}
