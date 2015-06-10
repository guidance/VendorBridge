<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Block_Adminhtml_ShipMethod_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
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
            'vendorbridge_shipMethod_form',
            array('legend' => $helper->__('Vendor Bridge Ship Method Information'))
        );

        /** @var $vendors Guidance_VendorBridge_Model_Source_Vendor */
        $vendors = Mage::getModel('vendorbridge/source_vendor');
        $fieldset->addField('vendor', 'select', array(
            'label'  => $helper->__('Vendor'),
            'name'   => 'vendor',
            'values' => $vendors->toOptionArray(),
        ));

        $fieldset->addField('external_ship_code', 'text', array(
            'label'    => $helper->__('External Ship Code'),
            'name'     => 'external_ship_code',
            'class'    => 'required-entry',
            'required' => true,
        ));

        $fieldset->addField('internal_ship_code', 'text', array(
            'label'    => $helper->__('Internal Ship Code'),
            'name'     => 'internal_ship_code',
            'class'    => 'required-entry',
            'required' => true,
        ));

        $data    = array();
        $session = Mage::getSingleton('adminhtml/session');
        if ($session->getData('vendorbridge_shipMethod_data')) {
            $data = $session->getData('vendorbridge_shipMethod_data');
            $session->setData('vendorbridge_shipMethod_data', null);
        } elseif (Mage::registry('vendorbridgeShipMethod_data')) {
            $data = Mage::registry('vendorbridgeShipMethod_data')->getData();
        }

        $form->setValues($data);

        return parent::_prepareForm();
    }
}
