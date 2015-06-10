<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_File_Edi_856_Sn1 extends Guidance_VendorBridge_Model_File_Line_Abstract
{
    /**#@+
     * Line constants
     */
    const ROW_TYPE = 'SN1';

    private $_qtyShipped;

    /**
     * @param array $args
     */
    public function __construct(array $args)
    {
        $this->_qtyShipped = $args['qty_shipped'];
    }

    /**
     * @return string
     */
    public function getItemDetail()
    {
        $segment = $this->_mapItemDetail();

        return join(self::ELEMENT_SEPARATOR, $segment) . self::SEGMENT_TERMINATOR;
    }

    /**
     * @return array
     */
    protected function _mapItemDetail()
    {
        return array(
            'SN100' => self::ROW_TYPE,
            'SN101' => '',
            'SN102' => $this->_qtyShipped,
            'SN103' => self::UNIT_OF_MEASUREMENT,
        );
    }
}
