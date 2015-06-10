<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_File_Edi_856_Dtm extends Guidance_VendorBridge_Model_File_Line_Abstract
{
    /**#@+
     * Line constants
     */
    const ROW_TYPE            = 'DTM';
    const DATE_TIME_QUALIFIER = '011';
    const DATE_FORMAT         = 'Ymd';
    const TIME_FORMAT         = 'Hi';

    private $_date;

    /**
     * @param array $args
     */
    public function __construct(array $args)
    {
        $this->_date = $args['date'];
    }

    /**
     * @return string
     */
    public function getDateTimeMaintenance()
    {
        $segment = $this->_mapDateTimeMaintenance();

        return join(self::ELEMENT_SEPARATOR, $segment) . self::SEGMENT_TERMINATOR;
    }

    /**
     * @return array
     */
    protected function _mapDateTimeMaintenance()
    {
        return array(
            'DTM00' => self::ROW_TYPE,
            'DTM01' => self::DATE_TIME_QUALIFIER,
            'DTM02' => $this->_getDate($this->_date),
        );
    }
}
