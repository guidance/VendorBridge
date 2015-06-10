<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Block_Adminhtml_Inventory extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     *
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_inventory';
        $this->_blockGroup = 'vendorbridge';
        $this->_headerText = Mage::helper('vendorbridge')->__('Vendor Bridge Inventory Manager');

        /** @var $vendors Guidance_VendorBridge_Model_Resource_Vendor_Collection */
        $vendors  = Mage::getModel('vendorbridge/vendor')->getCollection()
            ->addFieldToFilter('status', array('eq' => Guidance_VendorBridge_Model_Source_Status::STATUS_ENABLED));
        $disabled = false;
        if ($vendors->getSize() < 1) {
            $disabled = true;
        }

        $this->_addButton('exportfullinventory', array(
            'label'   => Mage::helper('vendorbridge')->__('Export Full Feed'),
            'onclick' => $disabled ? 'alert(\'There are no active vendors configured\');' : 'setLocation(\'' . $this->getUrl('*/*/exportFullFeed') . '\')',
            'class'   => $disabled ? 'export disabled' : 'export',
            'style'   => 'float:left',
        ), -1, 10);
        $this->_addButton('exportpartialinventory', array(
            'label'   => Mage::helper('vendorbridge')->__('Export Partial Feed'),
            'onclick' => $disabled ? 'alert(\'There are no active vendors configured\');' : 'setLocation(\'' . $this->getUrl('*/*/exportPartialFeed') . '\')',
            'class'   => $disabled ? 'export disabled' : 'export',
            'style'   => 'float:left',
        ), -1, 20);

        parent::__construct();

        $this->removeButton('add');
    }
}
