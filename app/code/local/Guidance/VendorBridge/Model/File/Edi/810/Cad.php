<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_File_Edi_810_Cad extends Guidance_VendorBridge_Model_File_Line_Abstract
{
    /**#@+
     * Line constants
     */
    const ROW_TYPE                   = 'CAD';
    const REFERENCE_NUMBER_QUALIFIER = 'CN';

    /**
     * @return string
     */
    public function getCarrierDetail()
    {
        $segment = $this->_mapCarrierDetail();

        return join(self::ELEMENT_SEPARATOR, $segment) . self::SEGMENT_TERMINATOR;
    }

    /**
     * @return array
     */
    protected function _mapCarrierDetail()
    {
        return array(
            'CAD00' => self::ROW_TYPE,
            'CAD01' => '',
            'CAD02' => '',
            'CAD03' => '',
            'CAD04' => Guidance_VendorBridge_Model_File_Edi_856_Td5::IDENTIFICATION_CODE,
        );
    }
}
