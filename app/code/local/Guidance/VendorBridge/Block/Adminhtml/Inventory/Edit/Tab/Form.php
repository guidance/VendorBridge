<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Block_Adminhtml_Inventory_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'vendorbridge_inventory_form',
            array('legend' => Mage::helper('vendorbridge')->__('Vendor Bridge Inventory Information'))
        );

        $fieldset->addField('sku', 'text', array(
            'label'    => Mage::helper('vendorbridge')->__('SKU'),
            'readonly' => true,
            'name'     => 'sku',
            'style'    => 'background-color: #d3d3d3;',
        ));

        $fieldset->addField('status', 'text', array(
            'label' => Mage::helper('vendorbridge')->__('Qty Available'),
            'name'  => 'status',
        ));

        /** @var $vendors Guidance_VendorBridge_Model_Source_Vendor */
        $vendors = Mage::getModel('vendorbridge/source_vendor');
        $fieldset->addField('vendor', 'select', array(
            'label'  => Mage::helper('vendorbridge')->__('Vendor'),
            'name'   => 'vendor',
            'values' => $vendors->toOptionArray(),
        ));

        $fieldset->addField('created_at', 'date', array(
            'label'    => Mage::helper('vendorbridge')->__('First Sent'),
            'name'     => 'created_at',
            'format'   => Varien_Date::DATETIME_INTERNAL_FORMAT,
            'time'     => true,
            'readonly' => true,
            'style'    => 'width: 130px !important; background-color: #d3d3d3;',
        ));

        $fieldset->addField('updated_at', 'date', array(
            'label'    => Mage::helper('vendorbridge')->__('Last Sent'),
            'name'     => 'updated_at',
            'format'   => Varien_Date::DATETIME_INTERNAL_FORMAT,
            'time'     => true,
            'readonly' => true,
            'style'    => 'width: 130px !important; background-color: #d3d3d3;',
        ));

        $data    = array();
        $session = Mage::getSingleton('adminhtml/session');
        if ($session->getData('vendorbridge_inventory_data')) {
            $data = $session->getData('vendorbridge_inventory_data');
            $session->setData('vendorbridge_inventory_data', null);
        } elseif (Mage::registry('vendorbridgeInventory_data')) {
            $data = Mage::registry('vendorbridgeInventory_data')->getData();
        }

        if (isset($data['updated_at']) && !empty($data['updated_at'])) {
            $data['updated_at'] = Mage::app()->getLocale()->date($data['updated_at'], null);
        }
        if (isset($data['created_at']) && !empty($data['created_at'])) {
            $data['created_at'] = Mage::app()->getLocale()->date($data['created_at'], null);
        }

        $form->setValues($data);

        return parent::_prepareForm();
    }
}
