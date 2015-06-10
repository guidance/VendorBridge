<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_Source_Status
{
    /**#@+
     * Status values
     */
    const STATUS_DISABLED = '0';
    const STATUS_ENABLED  = '1';

    private $_statuses = array();

    /**
     *
     */
    public function __construct()
    {
        $this->_statuses = array(
            array(
                'value' => self::STATUS_ENABLED,
                'label' => Mage::helper('vendorbridge')->__('Enabled'),
            ),
            array(
                'value' => self::STATUS_DISABLED,
                'label' => Mage::helper('vendorbridge')->__('Disabled'),
            ),
        );
    }

    /**
     * @return array $optionArray
     */
    public function toOptionArray()
    {
        return $this->_statuses;
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
