<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_File_Edi_810_Tds extends Guidance_VendorBridge_Model_File_Line_Abstract
{
    /**#@+
     * Line constants
     */
    const ROW_TYPE  = 'TDS';
    const PRECISION = 2;

    private $_amountInvoiced;

    /**
     * @param array $args
     */
    public function __construct(array $args)
    {
        $this->_amountInvoiced = $args['amount_invoiced'];
    }

    /**
     * @return string
     */
    public function getTotalMonetaryValueSummary()
    {
        $segment = $this->_mapTotalMonetaryValueSummary();

        return join(self::ELEMENT_SEPARATOR, $segment) . self::SEGMENT_TERMINATOR;
    }

    /**
     * @return array
     */
    protected function _mapTotalMonetaryValueSummary()
    {
        return array(
            'TDS00' => self::ROW_TYPE,
            'TDS01' => str_replace('.', '', number_format(round($this->_amountInvoiced + 0, self::PRECISION), 2)),
        );
    }
}
