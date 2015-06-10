<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_File_Edi_846_Sch extends Guidance_VendorBridge_Model_File_Line_Abstract
{
    /**#@+
     * Line constants
     */
    const ROW_TYPE                    = 'SCH';
    const UOFM                        = 'EACH';
    const AVAILABILITY_DATE_QUALIFIER = '018';
    const DATE_FORMAT                 = 'Ymd';
    const FAR_FUTURE_DATE             = '20391231';

    private $_quantity;
    private $_date;

    /**
     * @param array $args
     */
    public function __construct(array $args)
    {
        $this->_quantity = $args['quantity'];
        $this->_date     = is_null($args['date']) ? self::FAR_FUTURE_DATE : $args['date'];
    }

    /**
     * @return string
     */
    public function getLineItemSchedule()
    {
        $lineItemSchedule = $this->_mapLineItemSchedule();

        return join(self::ELEMENT_SEPARATOR, $lineItemSchedule) . self::SEGMENT_TERMINATOR;
    }

    /**
     * @return array
     */
    protected function _mapLineItemSchedule()
    {
        $schedule = array(
            'SCH00' => self::ROW_TYPE,
            'SCH01' => (int)$this->_quantity,
            'SCH02' => self::UOFM,
            'SCH03' => '',
            'SCH04' => '',
            'SCH05' => self::AVAILABILITY_DATE_QUALIFIER,
            'SCH06' => $this->_getDate($this->_date),
        );

        return $schedule;
    }
}
