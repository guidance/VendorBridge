<?php
/**
 * VendorBridge Integration
 * @method int getEntityId()
 * @method string getSku()
 * @method int getStatus()
 * @method int getVendor()
 * @method string getCreatedAt()
 * @method string getUpdatedAt()
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_Inventory extends Mage_Core_Model_Abstract
{
    const CACHE_TAG = 'vendorbridge_inventory';
    /**#@+
     * Inventory status values
     */
    const OUT_OF_STOCK = '0';
    const IN_STOCK     = '1';

    protected $_eventPrefix = 'vendorbridge_inventory';
    protected $_eventObject = 'vendorbridge_inventory';

    protected function _construct()
    {
        $this->_init('vendorbridge/inventory');
    }
}
