<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_Export_Inventory extends Guidance_VendorBridge_Model_Export_Abstract
{
    /**#@+
     * Inventory constants
     */
    const FILENAME_PREFIX = 'Inventory';
    const FILE_EXTENSION  = '.edi';
    const MAX_NUM_RECORDS = 10000;
    const PROCESS_TYPE    = 'vendorbridge_inventory_export';
    const PROCESS_NAME    = 'Inventory Export';
    const ID_CODE         = '846';

    private $_isPartial;

    /**
     * @var array
     */
    private $_inventoryData = array();

    /**
     *
     */
    protected function _writeExportFile()
    {
        $this->_getInventoryArray($this->_isPartial);
        if (empty($this->_inventoryData)) {
            throw new Guidance_VendorBridge_NofileException(
                Mage::helper('vendorbridge')->__('There is no partial inventory data to export for %s at this time.', $this->_vendor->getName())
            );
        }
        $dataChunks = array_chunk($this->_inventoryData, self::MAX_NUM_RECORDS, true);
        /** @var $ediWriter Guidance_VendorBridge_Model_File_Edi_846 */
        $ediWriter     = Mage::getModel('vendorbridge/file_edi_846', array('vendor' => $this->_vendor));
        $interchangeId = $this->_exportObject->getEntityId();

        $this->_fileData   = array();
        $this->_fileData[] = $ediWriter->getInterchangeHeader($interchangeId);
        $this->_fileData[] = $ediWriter->getGroupHeader($interchangeId, 'IB');

        foreach ($dataChunks as $chunk) {
            $startingLineCount = count($this->_fileData);
            $this->_fileData[] = $ediWriter->getTransactionSetHeader(self::ID_CODE, self::PROCESS_TYPE, $interchangeId);
            $this->_fileData[] = $ediWriter->getBeginningSegmentInventoryInquiry();
            foreach ($chunk as $sku => $qty) {
                $this->_fileData[] = $ediWriter->getItemIdentification($sku);
                $this->_fileData[] = $ediWriter->getQuantity($qty);
                if ($qty < 1) {
                    $this->_fileData[] = $ediWriter->getDiscontinuedInactive();
                }
            }
            $numSegments       = count($this->_fileData) - $startingLineCount + 1;
            $this->_fileData[] = $ediWriter->getTransactionSetTrailer($numSegments, self::PROCESS_TYPE);
        }

        $this->_fileData[] = $ediWriter->getGroupTrailer($interchangeId);
        $this->_fileData[] = $ediWriter->getInterchangeTrailer($interchangeId);

        parent::_writeExportFile();

        if (!empty($this->_inventoryData)) {
            $this->_updateInventoryTable();
        }
    }

    /**
     *
     */
    public function exportFull()
    {
        $this->_isPartial = false;
        $this->process();
    }

    /**
     * @throws Guidance_VendorBridge_Exception
     */
    public function exportPartial()
    {
        $this->_isPartial = true;
        $this->process();
    }

    /**
     * Since the products are uploaded directly to Vendor Bridge, there is no need to apply any store filters
     * We're sending everything
     *
     * @param bool $partial
     * @return array
     */
    protected function _getInventoryArray($partial = false)
    {
        /** @var $resource Mage_Core_Model_Resource */
        $resource = $this->_getResource();
        /** @var $productCollection Mage_Catalog_Model_Resource_Product_Collection */
        $productCollection = Mage::getResourceModel('catalog/product_collection');
        $productCollection->addAttributeToFilter('status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED));
        $productCollection->addAttributeToFilter('type_id', array('eq' => 'simple'));
        $productCollection->joinField(
            'qty',
            'cataloginventory/stock_item',
            'qty',
            'product_id=entity_id',
            '{{table}}.stock_id=1',
            'left'
        );
        $productCollection->getSelect()
            ->joinInner(array('cpsl' => $resource->getTableName('catalog/product_super_link')),
            'cpsl.product_id = e.entity_id',
            array()
        );
        /** @var $eavConfig Mage_Eav_Model_Config */
        $eavConfig = Mage::getSingleton('eav/config');
        $status    = $eavConfig->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'status');
        $attrId    = $status->getAttributeId();
        $productCollection->getSelect()->joinInner(
            array('status' => $status->getBackendTable()),
            '(status.entity_id = cpsl.parent_id) AND (' . $attrId . ' = status.attribute_id) AND (status.store_id = 0)',
            array('status' => 'status.value')
        );
        $productCollection->getSelect()->where('status.value = ?', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
        /** @var $adapter Magento_Db_Adapter_Pdo_Mysql */
        $adapter    = $this->_getWriteAdapter();
        $inStockIds = $adapter->fetchCol($productCollection->getSelect());

        /** @var $select Varien_Db_Select */
        $select = $adapter->select();
        $select->from(
            array('product' => $resource->getTableName('catalog/product')),
            array('product.sku')
        )->joinInner(
            array('cisi' => $resource->getTableName('cataloginventory/stock_item')),
            'cisi.product_id = product.entity_id',
            array()
        )->where('`product`.`type_id` = ?', Mage_Catalog_Model_Product_Type::TYPE_SIMPLE)
            ->group('product.sku');

        $threshold         = intval($this->_vendor->getThreshold());
        $iStatusExpression = 'IF(' . $adapter->quoteInto('product.entity_id IN (?)', $inStockIds, 'INT') . ', GREATEST(0, (cisi.qty - ' . $threshold . ')), 0)';
        $select->joinLeft(
            array('vbi' => $resource->getTableName('vendorbridge/inventory')),
            '`product`.`sku` = `vbi`.`sku` AND `vbi`.`vendor` = \'' . $this->_vendor->getEntityId() . '\'',
            array()
        );
        if ($partial) {
            $select->where('vbi.status IS NULL OR vbi.status <> ' . $iStatusExpression);
        }
        $select->columns(array('istatus' => new Zend_Db_Expr($iStatusExpression)));

        $inventoryArray       = (array)$adapter->fetchPairs($select);
        $this->_inventoryData = $inventoryArray;

        return $inventoryArray;
    }

    protected function _updateInventoryTable()
    {
        /** @var $resource Mage_Core_Model_Resource */
        $resource = $this->_getResource();
        /** @var $adapter Magento_Db_Adapter_Pdo_Mysql */
        $adapter  = $this->_getWriteAdapter();
        $query    = "INSERT INTO " . $resource->getTableName('vendorbridge/inventory')
            . " ( `entity_id`, `sku`, `status`, `vendor`, `created_at`, `updated_at` ) VALUES ";
        $values   = array();
        $vendorId = $this->_vendor->getEntityId();
        foreach ($this->_inventoryData as $sku => $status) {
            $values[] = "( null, '$sku', '$status', '$vendorId', CONVERT_TZ(NOW(), 'US/Pacific', 'UTC'), CONVERT_TZ(NOW(), 'US/Pacific', 'UTC')) ";
        }
        $query .= implode(', ', $values) . " ON DUPLICATE KEY UPDATE `status` = VALUES(`status`), `updated_at` = CONVERT_TZ(NOW(), 'US/Pacific', 'UTC');";
        $adapter->query($query);
    }
}
