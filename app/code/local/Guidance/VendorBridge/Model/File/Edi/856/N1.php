<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_File_Edi_856_N1 extends Guidance_VendorBridge_Model_File_Line_Abstract
{
    /**#@+
     * Line constants
     */
    const ROW_TYPE               = 'N1';
    const ENTITY_IDENTIFIER_CODE = 'ST';
    const ENTITY_CODE_QUALIFIER  = '92';
    const IDENTIFICATION_CODE    = '222222';

    private $_shipToName;

    /**
     * @param array $args
     */
    public function __construct(array $args)
    {
        $this->_shipToName = $args['ship_to_name'];
    }

    /**
     * @return string
     */
    public function getName()
    {
        $segment = $this->_mapName();

        return join(self::ELEMENT_SEPARATOR, $segment) . self::SEGMENT_TERMINATOR;
    }

    /**
     * @return array
     */
    protected function _mapName()
    {
        return array(
            'N100' => self::ROW_TYPE,
            'N101' => self::ENTITY_IDENTIFIER_CODE,
            'N102' => $this->_shipToName,
            'N103' => self::ENTITY_CODE_QUALIFIER,
            'N104' => self::IDENTIFICATION_CODE,
        );
    }
}
