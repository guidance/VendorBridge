<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_File_Edi_856_Td5 extends Guidance_VendorBridge_Model_File_Line_Abstract
{
    /**#@+
     * Line constants
     */
    const ROW_TYPE                      = 'TD5';
    const IDENTIFICATION_CODE_QUALIFIER = '2';
    const IDENTIFICATION_CODE           = 'UPSN';

    private $_serviceLevelCode;
    private $_shippingMethod;

    /**
     * @param array $args
     */
    public function __construct(array $args)
    {
        $this->_serviceLevelCode = $args['service_level_code'];
        $this->_shippingMethod   = $args['shipping_method'];
    }

    /**
     * @return string
     */
    public function getCarrierDetails()
    {
        $segment = $this->_mapCarrierDetails();

        return join(self::ELEMENT_SEPARATOR, $segment) . self::SEGMENT_TERMINATOR;
    }

    /**
     * @return array
     */
    protected function _mapCarrierDetails()
    {
        return array(
            'TD500' => self::ROW_TYPE,
            'TD501' => '',
            'TD502' => self::IDENTIFICATION_CODE_QUALIFIER,
            'TD503' => self::IDENTIFICATION_CODE,
            'TD504' => '',
            'TD505' => $this->_shippingMethod,
            'TD506' => '',
            'TD507' => '',
            'TD508' => '',
            'TD509' => '',
            'TD510' => '',
            'TD511' => '',
            'TD512' => $this->_serviceLevelCode,
        );
    }
}
