<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_File_Line_Gs extends Guidance_VendorBridge_Model_File_Line_Abstract
{
    /**#@+
     * Line constants
     */
    const ROW_TYPE                = 'GS';
    const DATE_FORMAT             = 'Ymd';
    const RESPONSIBLE_AGENCY_CODE = 'X';
    const VERSION                 = '004010';

    private $_functionalId;
    private $_interchangeSenderId;
    private $_interchangeReceiverId;

    /**
     * @param array $args
     */
    public function __construct(array $args)
    {
        $this->_interchangeId         = $args['interchange_id'];
        $this->_functionalId          = $args['functional_id'];
        $this->_interchangeReceiverId = $args['interchange_receiver_id'];
        $this->_interchangeSenderId   = $args['interchange_sender_id'];
    }

    /**
     * @return string
     */
    public function getGroupHeader()
    {
        $header = $this->_mapGroupHeader();

        return join(self::ELEMENT_SEPARATOR, $header) . self::SEGMENT_TERMINATOR;
    }

    /**
     * @return array
     */
    protected function _mapGroupHeader()
    {
        return array(
            'GS00' => self::ROW_TYPE,
            'GS01' => $this->_functionalId,
            'GS02' => $this->_interchangeSenderId,
            'GS03' => $this->_interchangeReceiverId,
            'GS04' => $this->_getDataInterchangeDate(),
            'GS05' => $this->_getDataInterchangeTime(),
            'GS06' => $this->_interchangeId,
            'GS07' => self::RESPONSIBLE_AGENCY_CODE,
            'GS08' => self::VERSION,
        );
    }

    /**
     * @return string
     */
    protected function _getDataInterchangeDate()
    {
        return date(self::DATE_FORMAT, time());
    }

    /**
     * @return string
     */
    protected function _getDataInterchangeTime()
    {
        return $this->_getTime();
    }
}
