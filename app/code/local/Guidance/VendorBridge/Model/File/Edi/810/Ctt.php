<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_File_Edi_810_Ctt extends Guidance_VendorBridge_Model_File_Line_Abstract
{
    /**#@+
     * Line constants
     */
    const ROW_TYPE = 'CTT';

    private $_lineItemsCount;

    /**
     * @param array $args
     */
    public function __construct(array $args)
    {
        $this->_lineItemsCount = $args['line_items_count'];
    }

    /**
     * @return string
     */
    public function getTransactionTotals()
    {
        $segment = $this->_mapTransactionTotals();

        return join(self::ELEMENT_SEPARATOR, $segment) . self::SEGMENT_TERMINATOR;
    }

    /**
     * @return array
     */
    protected function _mapTransactionTotals()
    {
        return array(
            'CTT00' => self::ROW_TYPE,
            'CTT01' => $this->_lineItemsCount,
        );
    }
}
