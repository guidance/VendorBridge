<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_Source_Result
{
    /**#@+
     * Status values
     */
    const STATUS_FAILURE = '0';
    const STATUS_SUCCESS = '1';

    private $_statuses = array();

    /**
     *
     */
    public function __construct()
    {
        $this->_statuses = array(
            array(
                'value' => self::STATUS_SUCCESS,
                'label' => Mage::helper('vendorbridge')->__('Success'),
            ),
            array(
                'value' => self::STATUS_FAILURE,
                'label' => Mage::helper('vendorbridge')->__('Failure'),
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
