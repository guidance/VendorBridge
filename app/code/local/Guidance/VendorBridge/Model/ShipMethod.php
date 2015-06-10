<?php
/**
 * VendorBridge Integration
 * @method int getEntityId()
 * @method int getVendor()
 * @method string getExternalShipCode()
 * @method string getInternalShipCode()
 * @method string getCreatedAt()
 * @method string getUpdatedAt()
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_ShipMethod extends Mage_Core_Model_Abstract
{
    /**#@+
     * Model constants
     */
    const CACHE_TAG          = 'vendorbridge_shipmethod';
    const DEFAULT_METHOD     = 'Standard Shipping';
    const DEFAULT_EXT_METHOD = 'SI';

    protected $_eventPrefix = 'vendorbridge_shipMethod';
    protected $_eventObject = 'vendorbridge_shipMethod';

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('vendorbridge/shipMethod');
    }
}
