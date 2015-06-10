<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_File_Edi_856_Hl extends Guidance_VendorBridge_Model_File_Line_Abstract
{
    /**#@+
     * Line constants
     */
    const ROW_TYPE       = 'HL';
    const LEVEL_SHIPMENT = 'S';
    const LEVEL_ORDER    = 'O';
    const LEVEL_PACK     = 'P';
    const LEVEL_ITEM     = 'I';

    private $_idNumber;
    private $_parentIdNumber;
    private $_levelCode;

    /**
     * @param array $args
     */
    public function __construct(array $args)
    {
        $this->_idNumber       = $args['id_number'];
        $this->_parentIdNumber = $args['parent_id_number'];
        $this->_levelCode      = $args['level_code'];
    }

    /**
     * @return string
     */
    public function getHierarchicalLevel()
    {
        $segment = $this->_mapHierarchicalLevel();

        return join(self::ELEMENT_SEPARATOR, $segment) . self::SEGMENT_TERMINATOR;
    }

    /**
     * @return array
     */
    protected function _mapHierarchicalLevel()
    {
        return array(
            'HL00' => self::ROW_TYPE,
            'HL01' => $this->_idNumber,
            'HL02' => empty($this->_parentIdNumber) ? '' : $this->_parentIdNumber,
            'HL03' => $this->_levelCode,
        );
    }
}
