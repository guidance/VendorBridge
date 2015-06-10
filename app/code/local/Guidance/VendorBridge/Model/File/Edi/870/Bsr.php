<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_File_Edi_870_Bsr extends Guidance_VendorBridge_Model_File_Line_Abstract
{
    /**#@+
     * Line constants
     */
    const ROW_TYPE           = 'BSR';
    const DATE_FORMAT        = 'Ymd';
    const STATUS_REPORT_CODE = '2';
    const ORDER_CODE         = 'PP';

    private $_cancelId;
    private $_cancelDate;

    /**
     * @param array $args
     */
    public function __construct(array $args)
    {
        $this->_cancelId   = $args['cancel_id'];
        $this->_cancelDate = $args['cancel_date'];
    }

    /**
     * @return string
     */
    public function getBeginningSegmentOrderStatus()
    {
        $segment = $this->_mapBeginningSegmentOrderStatus();

        return join(self::ELEMENT_SEPARATOR, $segment) . self::SEGMENT_TERMINATOR;
    }

    /**
     * @return array
     */
    protected function _mapBeginningSegmentOrderStatus()
    {
        return array(
            'BSR00' => self::ROW_TYPE,
            'BSR01' => self::STATUS_REPORT_CODE,
            'BSR02' => self::ORDER_CODE,
            'BSR03' => $this->_cancelId,
            'BSR04' => $this->_getDate($this->_cancelDate),
        );
    }
}
