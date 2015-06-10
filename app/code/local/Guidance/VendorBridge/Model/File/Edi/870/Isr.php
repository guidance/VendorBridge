<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_File_Edi_870_Isr extends Guidance_VendorBridge_Model_File_Line_Abstract
{
    /**#@+
     * Line constants
     */
    const ROW_TYPE          = 'ISR';
    const ORDER_STATUS_CODE = 'IC';

    /**
     * @return string
     */
    public function getItemStatusReport()
    {
        $segment = $this->_mapItemStatusReport();

        return join(self::ELEMENT_SEPARATOR, $segment) . self::SEGMENT_TERMINATOR;
    }

    /**
     * @return array
     */
    protected function _mapItemStatusReport()
    {
        return array(
            'ISR00' => self::ROW_TYPE,
            'ISR01' => self::ORDER_STATUS_CODE,
        );
    }
}
