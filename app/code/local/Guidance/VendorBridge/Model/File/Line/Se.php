<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_File_Line_Se extends Guidance_VendorBridge_Model_File_Line_Abstract
{
    /**#@+
     * Line constants
     */
    const ROW_TYPE = 'SE';

    private $_numSegments;
    private $_tableName;

    /**
     * @param array $args
     */
    public function __construct(array $args)
    {
        $this->_tableName   = $args['table_name'];
        $this->_numSegments = $args['num_segments'];
    }

    /**
     * @return string
     */
    public function getTransactionSetTrailer()
    {
        $trailer = $this->_mapTransactionSetTrailer();

        return join(self::ELEMENT_SEPARATOR, $trailer) . self::SEGMENT_TERMINATOR;
    }

    /**
     * @return array
     */
    protected function _mapTransactionSetTrailer()
    {
        return array(
            'SE00' => self::ROW_TYPE,
            'SE01' => $this->_numSegments,
            'SE02' => $this->_getLastControlNumber($this->_tableName),
        );
    }
}
