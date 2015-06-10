<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Block_Adminhtml_Vendor_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->_objectId   = 'id';
        $this->_blockGroup = 'vendorbridge';
        $this->_controller = 'adminhtml_vendor';

        $this->_updateButton('save', 'label', Mage::helper('vendorbridge')->__('Save Vendor'));
        $this->_updateButton('delete', 'label', Mage::helper('vendorbridge')->__('Delete Vendor'));

        $this->_addButton('saveandcontinue', array(
            'label'   => Mage::helper('vendorbridge')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class'   => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('form_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'edit_form');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'edit_form');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('vendorbridgeVendor_data') && Mage::registry('vendorbridgeVendor_data')->getName()) {
            $name = Mage::registry('vendorbridgeVendor_data')->getName();

            return Mage::helper('vendorbridge')->__('Edit Vendor \'%s\'', $name);
        }

        return '';
    }
}
