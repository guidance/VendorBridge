<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_Source_Vendor
{
    private $_vendors = array();

    /**
     *
     */
    public function __construct()
    {
        /** @var $stores Guidance_Vendorbridge_Model_Resource_Vendor_Collection */
        $vendors = Mage::getModel('vendorbridge/vendor')->getCollection();
        $vendors->setOrder('name', $vendors::SORT_ORDER_ASC);
        foreach ($vendors as $vendor) {
            /** @var $vendor Guidance_VendorBridge_Model_Vendor */
            $this->_vendors[] = array(
                'value' => $vendor->getData('entity_id'),
                'label' => $vendor->getData('name'),
            );
        }
    }

    /**
     * @return array $optionArray
     */
    public function toOptionArray()
    {
        return $this->_vendors;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $array = array();
        foreach ($this->toOptionArray() as $option) {
            $array[$option['value']] = $option['label'];
        }

        return $array;
    }
}
