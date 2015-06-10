<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_Resource_ShipMethod extends Guidance_VendorBridge_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('vendorbridge/shipMethod', 'entity_id');
    }
}
