<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_File_Edi_856_Prf extends Guidance_VendorBridge_Model_File_Line_Abstract
{
    /**#@+
     * Line constants
     */
    const ROW_TYPE = 'PRF';

    private $_poNumber;

    /**
     * @param array $args
     */
    public function __construct(array $args)
    {
        $this->_poNumber = $args['po_number'];
    }

    /**
     * @return string
     */
    public function getPurchaseOrderReference()
    {
        $segment = $this->_mapPurchaseOrderReference();

        return join(self::ELEMENT_SEPARATOR, $segment) . self::SEGMENT_TERMINATOR;
    }

    /**
     * @return array
     */
    protected function _mapPurchaseOrderReference()
    {
        return array(
            'PRF00' => self::ROW_TYPE,
            'PRF01' => $this->_poNumber,
        );
    }
}
