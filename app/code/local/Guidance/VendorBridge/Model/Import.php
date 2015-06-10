<?php
/**
 * VendorBridge Integration
 * @method int getEntityId()
 * @method string getType()
 * @method int getStatus()
 * @method int getVendor()
 * @method string getStartTime()
 * @method string getStopTime()
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_Import extends Mage_Core_Model_Abstract
{
    const CACHE_TAG = 'vendorbridge_import';

    protected $_eventPrefix = 'vendorbridge_import';
    protected $_eventObject = 'vendorbridge_import';

    protected function _construct()
    {
        $this->_init('vendorbridge/import');
    }
}
