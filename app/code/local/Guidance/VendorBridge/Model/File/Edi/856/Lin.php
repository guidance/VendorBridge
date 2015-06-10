<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_File_Edi_856_Lin extends Guidance_VendorBridge_Model_File_Line_Abstract
{
    /**#@+
     * Line constants
     */
    const ROW_TYPE      = 'LIN';
    const UPC_QUALIFIER = 'EA';

    private $_lineNumber;
    private $_sku;

    /**
     * @param array $args
     */
    public function __construct(array $args)
    {
        $this->_lineNumber = $args['line_number'];
        $this->_sku        = $args['sku'];
    }

    /**
     * @return string
     */
    public function getItemIdentification()
    {
        $segment = $this->_mapItemIdentification();

        return join(self::ELEMENT_SEPARATOR, $segment) . self::SEGMENT_TERMINATOR;
    }

    /**
     * @return array
     */
    protected function _mapItemIdentification()
    {
        return array(
            'LIN00' => self::ROW_TYPE,
            'LIN01' => $this->_lineNumber,
            'LIN02' => self::PRODUCT_ID_QUALIFIER,
            'LIN03' => $this->_sku,
            'LIN04' => 'SK',
            'LIN05' => $this->_sku,
        );
    }
}
