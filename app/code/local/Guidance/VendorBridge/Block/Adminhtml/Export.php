<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Block_Adminhtml_Export extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     *
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_export';
        $this->_blockGroup = 'vendorbridge';
        $this->_headerText = Mage::helper('vendorbridge')->__('Vendor Bridge Export Manager');

        /** @var $vendors Guidance_VendorBridge_Model_Resource_Vendor_Collection */
        $vendors  = Mage::getModel('vendorbridge/vendor')->getCollection()
            ->addFieldToFilter('status', array('eq' => Guidance_VendorBridge_Model_Source_Status::STATUS_ENABLED));
        $disabled = false;
        if ($vendors->getSize() < 1) {
            $disabled = true;
        }

        $this->_addButton('export_ship_conf', array(
            'label'   => Mage::helper('vendorbridge')->__('Export Ship Confirms'),
            'onclick' => $disabled ? 'alert(\'There are no active vendors configured\');' : 'setLocation(\'' . $this->getUrl('vendorbridge/adminhtml_export/exportShipConfirmations') . '\')',
            'class'   => $disabled ? 'scalable go disabled' : 'scalable go',
            'style'   => 'float:left',
        ), -1, 10);

        $this->_addButton('export_cancel', array(
            'label'   => Mage::helper('vendorbridge')->__('Export Cancels'),
            'onclick' => $disabled ? 'alert(\'There are no active vendors configured\');' : 'setLocation(\'' . $this->getUrl('vendorbridge/adminhtml_export/exportCancels') . '\')',
            'class'   => $disabled ? 'scalable go disabled' : 'scalable go',
            'style'   => 'float:left',
        ), -1, 20);

        $this->_addButton('export_invoice', array(
            'label'   => Mage::helper('vendorbridge')->__('Export Invoices'),
            'onclick' => $disabled ? 'alert(\'There are no active vendors configured\');' : 'setLocation(\'' . $this->getUrl('vendorbridge/adminhtml_export/exportInvoices') . '\')',
            'class'   => $disabled ? 'scalable go disabled' : 'scalable go',
            'style'   => 'float:left',
        ), -2, 30);

        $this->_addButton('export_inventory', array(
            'label'   => Mage::helper('vendorbridge')->__('Export Inventory'),
            'onclick' => $disabled ? 'alert(\'There are no active vendors configured\');' : 'setLocation(\'' . $this->getUrl('vendorbridge/adminhtml_inventory/index') . '\')',
            'class'   => $disabled ? 'scalable go disabled' : 'scalable go',
            'style'   => 'float:left',
        ), -2, 40);

        parent::__construct();

        $this->removeButton('add');
    }
}
