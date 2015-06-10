<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_File_Edi_810_N1 extends Guidance_VendorBridge_Model_File_Line_Abstract
{
    /**#@+
     * Line constants
     */
    const ROW_TYPE               = 'N1';
    const ENTITY_IDENTIFIER_CODE = 'ST';

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
            'N103' => $this->_shipToName,
        );
    }
}
