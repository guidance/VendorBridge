<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Block_Adminhtml_Vendor_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $helper   = Mage::helper('vendorbridge');
        $fieldset = $form->addFieldset(
            'vendorbridge_vendor_form',
            array('legend' => $helper->__('Vendor Bridge Vendor Information'))
        );

        $fieldset->addField('name', 'text', array(
            'label'    => $helper->__('Vendor Name'),
            'name'     => 'name',
            'class'    => 'required-entry validate-alphanum',
            'required' => true,
        ));

        $fieldset->addField('interchange_sender_id', 'text', array(
            'label'    => $helper->__('Interchange Sender ID'),
            'name'     => 'interchange_sender_id',
            'class'    => 'required-entry',
            'required' => true,
        ));

        $fieldset->addField('interchange_receiver_id', 'text', array(
            'label'    => $helper->__('Interchange Receiver ID'),
            'name'     => 'interchange_receiver_id',
            'class'    => 'required-entry',
            'required' => true,
        ));

        $fieldset->addField('vendor_number', 'text', array(
            'label'    => $helper->__('Vendor Number'),
            'name'     => 'vendor_number',
            'class'    => 'required-entry validate-number',
            'required' => true,
        ));

        /** @var $statuses Guidance_VendorBridge_Model_Source_Status */
        $statuses = Mage::getModel('vendorbridge/source_status');
        $fieldset->addField('status', 'select', array(
            'label'  => $helper->__('Vendor Status'),
            'name'   => 'status',
            'values' => $statuses->toOptionArray(),
        ));

        /** @var $stores Guidance_VendorBridge_Model_Source_Stores */
        $stores = Mage::getModel('vendorbridge/source_stores');
        $fieldset->addField('store_id', 'select', array(
            'label'              => $helper->__('Store ID'),
            'name'               => 'store_id',
            'values'             => $stores->toOptionArray(),
            'after_element_html' => '<p><small>This will determine which store orders are associated with</small></p>',
        ));

        $fieldset->addField('reseller_id', 'text', array(
            'label'              => $helper->__('Reseller ID'),
            'name'               => 'reseller_id',
            'class'              => 'required-entry validate-number',
            'required'           => true,
            'after_element_html' => '<p><small>Internal ID for the vendor that will be added to order table</small></p>',
        ));

        $fieldset->addField('threshold', 'text', array(
            'label'              => $helper->__('Inventory Threshold'),
            'name'               => 'threshold',
            'class'              => 'required-entry validate-not-negative-number',
            'required'           => true,
            'after_element_html' => '<p><small>This amount will be deducted from inventory quantity for all products for this vendor</small></p>',
        ));

        /** @var $allowedTypes Guidance_VendorBridge_Model_Source_AllowedTypes */
        $allowedTypes = Mage::getModel('vendorbridge/source_allowedTypes');
        $fieldset->addField('allowed_types', 'multiselect', array(
            'label'  => $helper->__('Allowed Types'),
            'name'   => 'allowed_types',
            'values' => $allowedTypes->toOptionArray(),
        ));

        $data    = array();
        $session = Mage::getSingleton('adminhtml/session');
        if ($session->getData('vendorbridge_vendor_data')) {
            $data = $session->getData('vendorbridge_vendor_data');
            $session->setData('vendorbridge_vendor_data', null);
        } elseif (Mage::registry('vendorbridgeVendor_data')) {
            $data = Mage::registry('vendorbridgeVendor_data')->getData();
        }

        $form->setValues($data);

        return parent::_prepareForm();
    }
}
