<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_File_Edi_810_Ref extends Guidance_VendorBridge_Model_File_Line_Abstract
{
    /**#@+
     * Line constants
     */
    const ROW_TYPE               = 'REF';
    const INTERNAL_VENDOR_NUMBER = '3X';
    const DEPARTMENT_NUMBER      = 'CN';

    private $_referenceIdQualifier;
    private $_referenceId;

    /**
     * @param array $args
     */
    public function __construct(array $args)
    {
        $this->_referenceIdQualifier = $args['reference_id_qualifier'];
        $this->_referenceId          = $args['reference_id'];
    }

    /**
     * @return string
     */
    public function getReferenceNumber()
    {
        $segment = $this->_mapReferenceNumber();

        return join(self::ELEMENT_SEPARATOR, $segment) . self::SEGMENT_TERMINATOR;
    }

    /**
     * @return array
     */
    protected function _mapReferenceNumber()
    {
        return array(
            'REF00' => self::ROW_TYPE,
            'REF01' => $this->_referenceIdQualifier,
            'REF02' => $this->_referenceId,
        );
    }
}
