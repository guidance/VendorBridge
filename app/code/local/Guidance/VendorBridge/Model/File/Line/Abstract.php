<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_File_Line_Abstract
{
    /**#@+
     * Line constants
     */
    const TIME_FORMAT           = 'Hi';
    const DATE_FORMAT           = 'Ymd';
    const SEGMENT_TERMINATOR    = '';
    const ELEMENT_SEPARATOR     = '*';
    const SUBELEMENT_SEPARATOR  = '>';
    const INTERCHANGE_ID_LENGTH = 9;
    const UNIT_OF_MEASUREMENT   = 'EA';
    const PRODUCT_ID_QUALIFIER  = 'VN';

    protected $_interchangeId;

    /**
     * @return string
     */
    protected function _getTime()
    {
        return date(self::TIME_FORMAT, time());
    }

    /**
     * @param $date
     * @return string formatted date
     */
    protected function _getDate($date)
    {
        return date(self::DATE_FORMAT, strtotime($date));
    }

    /**
     * @return string
     */
    protected function _getInterchangeControlNumber()
    {
        return str_pad($this->_interchangeId, self::INTERCHANGE_ID_LENGTH, '0', STR_PAD_LEFT);
    }

    /**
     * @param string $tableName
     * @return string control number for st/se segments
     */
    protected function _getLastControlNumber($tableName)
    {
        /** @var $adapter Magento_Db_Adapter_Pdo_Mysql */
        $adapter = $this->_getWriteAdapter();
        $select  = $adapter->select()
            ->from($tableName, array('entity_id'))
            ->order('entity_id DESC')
            ->limit(1);

        $last = (int)$adapter->fetchOne($select);

        return str_pad($last, 4, '0', STR_PAD_LEFT);
    }

    /**
     * @return Mage_Core_Model_Resource
     */
    protected function _getResource()
    {
        /** @var $resource Mage_Core_Model_Resource */
        $resource = Mage::getSingleton('core/resource');

        return $resource;
    }

    /**
     * @return Magento_Db_Adapter_Pdo_Mysql
     */
    protected function _getWriteAdapter()
    {
        /** @var $resource Mage_Core_Model_Resource */
        $resource = $this->_getResource();
        /** @var $adapter Magento_Db_Adapter_Pdo_Mysql */
        $adapter = $resource->getConnection('write');

        return $adapter;
    }
}
