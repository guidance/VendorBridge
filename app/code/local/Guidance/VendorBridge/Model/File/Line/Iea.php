<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_File_Line_Iea extends Guidance_VendorBridge_Model_File_Line_Abstract
{
    /**#@+
     * Line constants
     */
    const ROW_TYPE          = 'IEA';
    const NUM_GROUPS_LENGTH = 5;

    private $_numGroups;

    /**
     * @param array $args
     */
    public function __construct(array $args)
    {
        $this->_interchangeId = $args['interchange_id'];
        $this->_numGroups     = $args['num_groups'];
    }

    /**
     * @return string
     */
    public function getInterchangeTrailer()
    {
        $trailer = $this->_mapInterchangeTrailer();

        return join(self::ELEMENT_SEPARATOR, $trailer) . self::SEGMENT_TERMINATOR;
    }

    /**
     * @return array
     */
    protected function _mapInterchangeTrailer()
    {
        return array(
            'IEA00' => self::ROW_TYPE,
            'IEA01' => $this->_numGroups,
            'IEA02' => $this->_getInterchangeControlNumber(),
        );
    }
}
