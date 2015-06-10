<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_File_Edi_810_Iss extends Guidance_VendorBridge_Model_File_Line_Abstract
{
    /**#@+
     * Line constants
     */
    const ROW_TYPE            = 'ISS';
    const NUM_UNITS_SHIPPED   = 1;
    const UNIT_OF_MEASUREMENT = 'PK';

    /**
     * @return string
     */
    public function getInvoiceShipmentSummary()
    {
        $segment = $this->_mapInvoiceShipmentSummary();

        return join(self::ELEMENT_SEPARATOR, $segment) . self::SEGMENT_TERMINATOR;
    }

    /**
     * @return array
     */
    protected function _mapInvoiceShipmentSummary()
    {
        return array(
            'ISS00' => self::ROW_TYPE,
            'ISS01' => self::NUM_UNITS_SHIPPED,
            'ISS02' => self::UNIT_OF_MEASUREMENT,
        );
    }
}
