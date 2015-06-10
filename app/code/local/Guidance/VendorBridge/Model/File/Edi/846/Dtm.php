<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_File_Edi_846_Dtm extends Guidance_VendorBridge_Model_File_Line_Abstract
{
    /**#@+
     * Line constants
     */
    const ROW_TYPE    = 'DTM';
    const DATE_FORMAT = 'Ymd';

    /**
     * @return string
     */
    public function getDiscontinuedInactive()
    {
        $discontinuedInactive = $this->_mapDiscontinuedInactive();

        return join(self::ELEMENT_SEPARATOR, $discontinuedInactive) . self::SEGMENT_TERMINATOR;
    }

    /**
     * @return array
     */
    protected function _mapDiscontinuedInactive()
    {
        $schedule = array(
            'DTM00' => self::ROW_TYPE,
            'DTM01' => '036',
            'DTM02' => date(self::DATE_FORMAT),
        );

        return $schedule;
    }
}
