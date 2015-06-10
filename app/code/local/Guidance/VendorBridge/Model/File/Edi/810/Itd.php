<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_File_Edi_810_Itd extends Guidance_VendorBridge_Model_File_Line_Abstract
{
    /**#@+
     * Line constants
     */
    const ROW_TYPE              = 'ITD';
    const TERMS_TYPE_CODE_BASIC = '01';
    const TERMS_BASIS_DATE_CODE = '3';
    const TERMS_NET_DAYS        = '45';

    /**
     * @return string
     */
    public function getTermsOfSale()
    {
        $segment = $this->_mapTermsOfSale();

        return join(self::ELEMENT_SEPARATOR, $segment) . self::SEGMENT_TERMINATOR;
    }

    /**
     * @return array
     */
    protected function _mapTermsOfSale()
    {
        return array(
            'ITD00' => self::ROW_TYPE,
            'ITD01' => self::TERMS_TYPE_CODE_BASIC,
            'ITD02' => self::TERMS_BASIS_DATE_CODE,
            'ITD03' => '',
            'ITD04' => '',
            'ITD05' => '',
            'ITD06' => '',
            'ITD07' => self::TERMS_NET_DAYS,
        );
    }
}
