<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Block_Adminhtml_Vendor extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     *
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_vendor';
        $this->_blockGroup = 'vendorbridge';
        $this->_headerText = Mage::helper('vendorbridge')->__('Vendor Bridge Vendor Manager');

        parent::__construct();
    }
}
