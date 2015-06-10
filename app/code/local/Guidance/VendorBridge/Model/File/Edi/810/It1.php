<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_File_Edi_810_It1 extends Guidance_VendorBridge_Model_File_Line_Abstract
{
    /**#@+
     * Line constants
     */
    const ROW_TYPE               = 'IT1';
    const PRECISION              = 2;
    const RETAILER_SKU_QUALIFIER = 'SK';

    private $_lineNumber;
    private $_qty;
    private $_price;
    private $_sku;

    /**
     * @param array $args
     */
    public function __construct(array $args)
    {
        $this->_lineNumber = $args['line_number'];
        $this->_qty        = $args['quantity_shipped'];
        $this->_price      = $args['price'];
        $this->_sku        = $args['sku'];
    }

    /**
     * @return string
     */
    public function getBaselineItemDetail()
    {
        $segment = $this->_mapBaselineItemDetail();

        return join(self::ELEMENT_SEPARATOR, $segment) . self::SEGMENT_TERMINATOR;
    }

    /**
     * @return array
     */
    protected function _mapBaselineItemDetail()
    {
        return array(
            'IT100' => self::ROW_TYPE,
            'IT101' => $this->_lineNumber,
            'IT102' => $this->_qty,
            'IT103' => self::UNIT_OF_MEASUREMENT,
            'IT104' => number_format(round($this->_price + 0, self::PRECISION, 2), 2),
            'IT105' => '',
            'IT106' => self::PRODUCT_ID_QUALIFIER,
            'IT107' => $this->_sku,
            'IT108' => self::RETAILER_SKU_QUALIFIER,
            'IT109' => $this->_sku,
        );
    }
}
