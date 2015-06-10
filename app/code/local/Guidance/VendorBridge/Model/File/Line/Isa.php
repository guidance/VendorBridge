<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_File_Line_Isa extends Guidance_VendorBridge_Model_File_Line_Abstract
{
    /**#@+
     * Line constants
     */
    const ROW_TYPE                  = 'ISA';
    const NO_INFORMATION_PRESENT    = '00';
    const INTERCHANGE_ID_QUALIFIER  = 'ZZ';
    const SENDER_ID_LENGTH          = 15;
    const RECEIVER_ID_LENGTH        = 15;
    const NOT_USED_LENGTH           = 10;
    const DATE_FORMAT               = 'ymd';
    const INTERCHANGE_STANDARDS_ID  = 'U';
    const INTERCHANGE_VERSION_ID    = '00401';
    const ACKNOWLEDGEMENT_REQUESTED = '1';
    const TEST_DATA                 = 'T';
    const PRODUCTION_DATA           = 'P';

    private $_interchangeSenderId;
    private $_interchangeReceiverId;

    /**
     * @param array $args
     */
    public function __construct(array $args)
    {
        $this->_interchangeId         = $args['interchange_id'];
        $this->_interchangeReceiverId = $args['interchange_receiver_id'];
        $this->_interchangeSenderId   = $args['interchange_sender_id'];
    }

    /**
     * @return string
     */
    public function getInterchangeHeader()
    {
        $header = $this->_mapInterchangeHeader();

        return join(self::ELEMENT_SEPARATOR, $header) . self::SEGMENT_TERMINATOR;
    }

    /**
     * @return array
     */
    protected function _mapInterchangeHeader()
    {
        return array(
            'ISA00' => self::ROW_TYPE,
            'ISA01' => self::NO_INFORMATION_PRESENT,
            'ISA02' => $this->_getNotUsedString(),
            'ISA03' => self::NO_INFORMATION_PRESENT,
            'ISA04' => $this->_getNotUsedString(),
            'ISA05' => self::INTERCHANGE_ID_QUALIFIER,
            'ISA06' => $this->_getInterchangeSenderId(),
            'ISA07' => self::INTERCHANGE_ID_QUALIFIER,
            'ISA08' => $this->_getInterchangeReceiverId(),
            'ISA09' => date(self::DATE_FORMAT, time()),
            'ISA10' => $this->_getTime(),
            'ISA11' => self::INTERCHANGE_STANDARDS_ID,
            'ISA12' => self::INTERCHANGE_VERSION_ID,
            'ISA13' => $this->_getInterchangeControlNumber(),
            'ISA14' => self::ACKNOWLEDGEMENT_REQUESTED,
            'ISA15' => self::PRODUCTION_DATA,
            'ISA16' => self::SUBELEMENT_SEPARATOR,
        );
    }

    /**
     * @return string
     */
    protected function _getNotUsedString()
    {
        return str_pad('', self::NOT_USED_LENGTH, ' ', STR_PAD_RIGHT);
    }

    /**
     * @return string
     */
    protected function _getInterchangeSenderId()
    {
        return str_pad($this->_interchangeSenderId, self::SENDER_ID_LENGTH, ' ', STR_PAD_RIGHT);
    }

    /**
     * @return string
     */
    protected function _getInterchangeReceiverId()
    {
        return str_pad($this->_interchangeReceiverId, self::RECEIVER_ID_LENGTH, ' ', STR_PAD_RIGHT);
    }
}
