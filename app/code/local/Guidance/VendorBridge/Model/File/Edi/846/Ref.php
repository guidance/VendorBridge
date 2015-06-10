<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_File_Edi_846_Ref extends Guidance_VendorBridge_Model_File_Line_Abstract
{
    /**#@+
     * Line constants
     */
    const ROW_TYPE               = 'REF';
    const REFERENCE_ID_QUALIFIER = 'IA';

    /**
     * @var $_vendor Guidance_VendorBridge_Model_Vendor
     */
    protected $_vendor;

    /**
     * @param array $args
     */
    public function __construct(array $args)
    {
        $this->_vendor = $args['vendor'];
    }

    /**
     * @return string
     */
    public function getReferenceIdentification()
    {
        $id = $this->_mapReferenceIdentification();

        return join(self::ELEMENT_SEPARATOR, $id) . self::SEGMENT_TERMINATOR;
    }

    /**
     * @return array
     */
    protected function _mapReferenceIdentification()
    {
        return array(
            'REF00' => self::ROW_TYPE,
            'REF01' => self::REFERENCE_ID_QUALIFIER,
            'REF02' => $this->_vendor->getVendorNumber(),
        );
    }
}
