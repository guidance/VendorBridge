<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_File_Edi_856_Bsn extends Guidance_VendorBridge_Model_File_Line_Abstract
{
    /**#@+
     * Line constants
     */
    const ROW_TYPE                     = 'BSN';
    const TRANSACTION_SET_PURPOSE_CODE = '00';
    const DATE_FORMAT                  = 'Ymd';
    const TIME_FORMAT                  = 'Hi';
    const HIERARCHICAL_STRUCTURE_CODE  = '0001';

    private $_shipmentId;

    /**
     * @param array $args
     */
    public function __construct(array $args)
    {
        $this->_shipmentId = $args['shipment_id'];
    }

    /**
     * @return string
     */
    public function getBeginningSegmentShipNotice()
    {
        $segment = $this->_mapBeginningSegmentShipNotice();

        return join(self::ELEMENT_SEPARATOR, $segment) . self::SEGMENT_TERMINATOR;
    }

    /**
     * @return array
     */
    protected function _mapBeginningSegmentShipNotice()
    {
        return array(
            'BSN00' => self::ROW_TYPE,
            'BSN01' => self::TRANSACTION_SET_PURPOSE_CODE,
            'BSN02' => $this->_shipmentId,
            'BSN03' => date(self::DATE_FORMAT),
            'BSN04' => date(self::TIME_FORMAT),
            'BSN05' => self::HIERARCHICAL_STRUCTURE_CODE,
        );
    }
}
