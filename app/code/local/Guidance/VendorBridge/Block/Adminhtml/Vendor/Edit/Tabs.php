<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Block_Adminhtml_Vendor_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('vendorbridge_vendor_tabs');
        $this->setDestElementId('edit_form');
        $this->setData('title', Mage::helper('vendorbridge')->__('Vendor Bridge Vendor'));
    }

    /**
     * @return Mage_Core_Block_Abstract
     */
    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'   => Mage::helper('vendorbridge')->__('Vendor Bridge Vendor'),
            'title'   => Mage::helper('vendorbridge')->__('Vendor Bridge Vendor'),
            'content' => $this->getLayout()->createBlock('vendorbridge/adminhtml_vendor_edit_tab_form')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }
}
