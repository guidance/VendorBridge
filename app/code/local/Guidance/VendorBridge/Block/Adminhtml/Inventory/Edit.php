<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Block_Adminhtml_Inventory_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->_objectId   = 'id';
        $this->_blockGroup = 'vendorbridge';
        $this->_controller = 'adminhtml_inventory';

        $this->_updateButton('save', 'label', Mage::helper('vendorbridge')->__('Save Inventory Item'));
        $this->_updateButton('delete', 'label', Mage::helper('vendorbridge')->__('Delete Inventory Item'));

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
        if (Mage::registry('vendorbridgeInventory_data') && Mage::registry('vendorbridgeInventory_data')->getSku()) {
            $sku = Mage::registry('vendorbridgeInventory_data')->getSku();
            /** @var $product Mage_Catalog_Model_Product */
            $product = Mage::getModel('catalog/product');
            $product->load($product->getIdBySku($sku));

            return Mage::helper('vendorbridge')->__('Edit Product ID \'%s\'', $product->getId());
        }

        return '';
    }
}
