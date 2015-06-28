<?php
/**
 * VendorBridge Integration
 * @method int getEntityId()
 * @method string getName()
 * @method string getInterchangeSenderId()
 * @method string getInterchangeReceiverId()
 * @method string getVendorNumber()
 * @method int getStatus()
 * @method int getThreshold()
 * @method int getStoreId()
 * @method int getResellerId()
 * @method string|array getAllowedTypes()
 * @method string getCreatedAt()
 * @method string getUpdatedAt()
 * @method string getQualifier()
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_Vendor extends Mage_Core_Model_Abstract
{
    const CACHE_TAG = 'vendorbridge_vendor';

    protected $_eventPrefix = 'vendor';
    protected $_eventObject = 'vendor';

    protected function _construct()
    {
        $this->_init('vendorbridge/vendor');
    }

    /**
     * @return Guidance_VendorBridge_Model_Resource_ShipMethod_Collection
     */
    public function getShippingMethods()
    {
        /** @var $methods Guidance_VendorBridge_Model_Resource_ShipMethod_Collection */
        $methods = Mage::getModel('vendorbridge/shipMethod')->getCollection();
        $methods->addFieldToFilter('vendor', array('eq' => $this->getEntityId()));

        return $methods;
    }

    /**
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        /** @var $store Mage_Core_Model_Store */
        $store = Mage::getModel('core/store')->load($this->getStoreId());

        return $store;
    }

    /**
     * @return Mage_Core_Model_Abstract|void
     */
    protected function _beforeSave()
    {
        $allowedTypes = $this->getData('allowed_types');
        if (is_array($allowedTypes)) {
            $allowedTypes = join(',', $allowedTypes);
        }
        $this->setData('allowed_types', $allowedTypes);
    }

    /**
     * @return Mage_Core_Model_Abstract|void
     */
    protected function _afterLoad()
    {
        $allowedTypes = $this->getData('allowed_types');
        if (!is_array($allowedTypes)) {
            $allowedTypes = explode(',', $allowedTypes);
        }
        $this->setData('allowed_types', $allowedTypes);
    }
}
