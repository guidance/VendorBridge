<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_File_Line_Ge extends Guidance_VendorBridge_Model_File_Line_Abstract
{
    /**#@+
     * Line constants
     */
    const ROW_TYPE = 'GE';

    private $_numSets;

    /**
     * @param array $args
     */
    public function __construct(array $args)
    {
        $this->_interchangeId = $args['interchange_id'];
        $this->_numSets       = $args['num_sets'];
    }

    /**
     * @return string
     */
    public function getGroupTrailer()
    {
        $trailer = $this->_mapGroupTrailer();

        return join(self::ELEMENT_SEPARATOR, $trailer) . self::SEGMENT_TERMINATOR;
    }

    /**
     * @return array
     */
    protected function _mapGroupTrailer()
    {
        return array(
            'GE00' => self::ROW_TYPE,
            'GE01' => $this->_numSets,
            'GE02' => $this->_interchangeId,
        );
    }
}
