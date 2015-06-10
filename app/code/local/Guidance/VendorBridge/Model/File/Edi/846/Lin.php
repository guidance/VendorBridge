<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_File_Edi_846_Lin extends Guidance_VendorBridge_Model_File_Line_Abstract
{
    /**#@+
     * Line constants
     */
    const ROW_TYPE = 'LIN';

    private $_qualifier;
    private $_id;

    /**
     * @param array $args
     */
    public function __construct(array $args)
    {
        $this->_qualifier = $args['qualifier'];
        $this->_id        = $args['id'];
    }

    /**
     * @return string
     */
    public function getItemIdentification()
    {
        $id = $this->_mapItemIdentification();

        return join(self::ELEMENT_SEPARATOR, $id) . self::SEGMENT_TERMINATOR;
    }

    /**
     * @return array
     */
    protected function _mapItemIdentification()
    {
        return array(
            'LIN00' => self::ROW_TYPE,
            'LIN01' => '',
            'LIN02' => $this->_qualifier,
            'LIN03' => $this->_id,
        );
    }
}
