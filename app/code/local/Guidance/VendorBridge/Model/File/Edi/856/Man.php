<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_File_Edi_856_Man extends Guidance_VendorBridge_Model_File_Line_Abstract
{
    /**#@+
     * Line constants
     */
    const ROW_TYPE = 'MAN';

    private $_trackingNumber;

    /**
     * @param array $args
     */
    public function __construct(array $args)
    {
        $this->_trackingNumber = $args['tracking_number'];
    }

    /**
     * @return string
     */
    public function getTrackingLine()
    {
        $segment = $this->_mapItemDetail();

        return join(self::ELEMENT_SEPARATOR, $segment) . self::SEGMENT_TERMINATOR;
    }

    /**
     * @return array
     */
    protected function _mapItemDetail()
    {
        return array(
            'MAN00' => self::ROW_TYPE,
            'MAN01' => 'CP',
            'MAN02' => $this->_trackingNumber,
        );
    }
}
