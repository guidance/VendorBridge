<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_Source_Stores
{
    private $_stores = array();

    /**
     *
     */
    public function __construct()
    {
        /** @var $stores Mage_Core_Model_Resource_Store_Collection */
        $stores = Mage::getModel('core/store')->getCollection();
        $stores->setOrder('website_id', $stores::SORT_ORDER_ASC);
        foreach ($stores as $store) {
            /** @var $store Mage_Core_Model_Store */
            if ($store->getData('code') == Mage_Core_Model_Store::ADMIN_CODE) {
                continue;
            }
            /** @var $website Mage_Core_Model_Website */
            $website         = $store->getWebsite();
            $this->_stores[] = array(
                'value' => $store->getData('store_id'),
                'label' => $website->getData('name') . ' - ' . $store->getData('name'),
            );
        }
    }

    /**
     * @return array $optionArray
     */
    public function toOptionArray()
    {
        return $this->_stores;
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
