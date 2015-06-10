<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_File_Edi_846_Bia extends Guidance_VendorBridge_Model_File_Line_Abstract
{
    /**#@+
     * Line constants
     */
    const ROW_TYPE                     = 'BIA';
    const TRANSACTION_SET_PURPOSE_CODE = '00';
    const REPORT_TYPE_CODE             = 'PI';
    const REFERENCE_NUMBER             = '1';
    const DATE_FORMAT                  = 'Ymd';

    /**
     * @return string
     */
    public function getBeginningSegmentInventoryInquiry()
    {
        $segment = $this->_mapBeginningSegmentInventoryInquiry();

        return join(self::ELEMENT_SEPARATOR, $segment) . self::SEGMENT_TERMINATOR;
    }

    /**
     * @return array
     */
    protected function _mapBeginningSegmentInventoryInquiry()
    {
        return array(
            'BIA00' => self::ROW_TYPE,
            'BIA01' => self::TRANSACTION_SET_PURPOSE_CODE,
            'BIA02' => self::REPORT_TYPE_CODE,
            'BIA03' => self::REFERENCE_NUMBER,
            'BIA04' => date(self::DATE_FORMAT),
        );
    }
}
