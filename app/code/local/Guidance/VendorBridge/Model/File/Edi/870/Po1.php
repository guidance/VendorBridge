<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_File_Edi_870_Po1 extends Guidance_VendorBridge_Model_File_Line_Abstract
{
    /**#@+
     * Line constants
     */
    const ROW_TYPE = 'PO1';

    private $_lineNumber;
    private $_qty;
    private $_sku;

    /**
     * @param array $args
     */
    public function __construct(array $args)
    {
        $this->_lineNumber = $args['line_number'];
        $this->_qty        = $args['quantity'];
        $this->_sku        = $args['sku'];
    }

    /**
     * @return string
     */
    public function getBaselineItemData()
    {
        $segment = $this->_mapBaselineItemData();

        return join(self::ELEMENT_SEPARATOR, $segment) . self::SEGMENT_TERMINATOR;
    }

    /**
     * @return array
     */
    protected function _mapBaselineItemData()
    {
        return array(
            'PO100' => self::ROW_TYPE,
            'PO101' => $this->_lineNumber,
            'PO102' => $this->_qty,
            'PO103' => self::UNIT_OF_MEASUREMENT,
            'PO104' => '',
            'PO105' => '',
            'PO106' => self::PRODUCT_ID_QUALIFIER,
            'PO107' => $this->_sku,
        );
    }
}
