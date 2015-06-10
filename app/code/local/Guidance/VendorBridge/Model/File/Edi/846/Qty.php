<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_File_Edi_846_Qty extends Guidance_VendorBridge_Model_File_Line_Abstract
{
    /**#@+
     * Line constants
     */
    const ROW_TYPE           = 'QTY';
    const QUANTITY_QUALIFIER = '36';
    const UOFM               = 'EA';

    private $_quantity;

    /**
     * @param array $args
     */
    public function __construct(array $args)
    {
        $this->_quantity = $args['quantity'];
    }

    /**
     * @return string
     */
    public function getQuantity()
    {
        $quantity = $this->_mapQuantity();

        return join(self::ELEMENT_SEPARATOR, $quantity) . self::SEGMENT_TERMINATOR;
    }

    /**
     * @return array
     */
    protected function _mapQuantity()
    {
        return array(
            'QTY00' => self::ROW_TYPE,
            'QTY01' => self::QUANTITY_QUALIFIER,
            'QTY02' => $this->_quantity,
            'QTY03' => self::UOFM,
        );
    }
}
