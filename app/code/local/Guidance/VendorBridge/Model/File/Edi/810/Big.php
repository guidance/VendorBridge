<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_File_Edi_810_Big extends Guidance_VendorBridge_Model_File_Line_Abstract
{
    /**#@+
     * Line constants
     */
    const ROW_TYPE    = 'BIG';
    const DATE_FORMAT = 'Ymd';

    private $_invoiceId;
    private $_poDate;
    private $_poNumber;

    /**
     * @param array $args
     */
    public function __construct(array $args)
    {
        $this->_invoiceId = $args['invoice_id'];
        $this->_poDate    = $args['po_date'];
        $this->_poNumber  = $args['po_number'];
    }

    /**
     * @return string
     */
    public function getBeginningSegmentForInvoice()
    {
        $segment = $this->_mapBeginningSegmentForInvoice();

        return join(self::ELEMENT_SEPARATOR, $segment) . self::SEGMENT_TERMINATOR;
    }

    /**
     * @return array
     */
    protected function _mapBeginningSegmentForInvoice()
    {
        return array(
            'BIG00' => self::ROW_TYPE,
            'BIG01' => date(self::DATE_FORMAT),
            'BIG02' => $this->_invoiceId,
            'BIG03' => $this->_getDate($this->_poDate),
            'BIG04' => $this->_poNumber,
            'BIG05' => '',
            'BIG06' => '',
            'BIG07' => 'DR',
        );
    }
}
