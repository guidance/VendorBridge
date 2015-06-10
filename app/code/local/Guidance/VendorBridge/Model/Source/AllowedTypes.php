<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_Source_AllowedTypes
{
    /**#@+
     * Allowed type values
     */
    const TYPE_810 = '810';
    const TYPE_846 = '846';
    const TYPE_850 = '850';
    const TYPE_856 = '856';
    const TYPE_870 = '870';

    const LABEL_810 = 'Invoice';
    const LABEL_846 = 'Inventory';
    const LABEL_850 = 'Order';
    const LABEL_856 = 'Shipment Confirmation';
    const LABEL_870 = 'Cancellation';

    private $_allowedTypes;

    /**
     *
     */
    public function __construct()
    {
        $this->_allowedTypes = array(
            array(
                'value' => self::TYPE_810,
                'label' => self::LABEL_810,
            ),
            array(
                'value' => self::TYPE_846,
                'label' => self::LABEL_846,
            ),
            array(
                'value' => self::TYPE_850,
                'label' => self::LABEL_850,
            ),
            array(
                'value' => self::TYPE_856,
                'label' => self::LABEL_856,
            ),
            array(
                'value' => self::TYPE_870,
                'label' => self::LABEL_870,
            ),
        );
    }

    /**
     * @return array $optionArray
     */
    public function toOptionArray()
    {
        return $this->_allowedTypes;
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
