<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_Export_Shipments extends Guidance_VendorBridge_Model_Export_Abstract
{
    /**#@+
     * Shipment constants
     */
    const FILENAME_PREFIX = 'Shipment';
    const FILE_EXTENSION  = '.edi';
    const PROCESS_TYPE    = 'vendorbridge_shipment_export';
    const PROCESS_NAME    = 'Shipment Export';
    const MAX_SHIPMENTS   = 200000;
    const EXPORTED_FLAG   = 1;
    const ID_CODE         = '856';

    /**
     * @var array
     */
    private $_shipData = array();

    /**
     *
     */
    protected function _writeExportFile()
    {
        $exportData = $this->_getShipArray();
        if (empty($exportData)) {
            $this->_stopRunning();
            throw new Guidance_VendorBridge_NofileException(
                Mage::helper('vendorbridge')->__('There are no shipments to export for %s at this time.', $this->_vendor->getName())
            );
        }
        /** @var $ediWriter Guidance_VendorBridge_Model_File_Edi_856 */
        $ediWriter     = Mage::getModel('vendorbridge/file_edi_856', array('vendor' => $this->_vendor));
        $interchangeId = $this->_exportObject->getEntityId();

        $this->_fileData   = array();
        $this->_fileData[] = $ediWriter->getInterchangeHeader($interchangeId);
        $this->_fileData[] = $ediWriter->getGroupHeader($interchangeId, 'SH');

        foreach ($exportData as $shipmentId => $ship) {
            $startingLineCount = count($this->_fileData);
            $this->_fileData[] = $ediWriter->getTransactionSetHeader(self::ID_CODE, self::PROCESS_TYPE, $interchangeId);
            $this->_fileData[] = $ediWriter->getBeginningSegmentShipNotice($shipmentId);
            $this->_fileData[] = $ediWriter->getHierarchicalLevel(1, null, Guidance_VendorBridge_Model_File_Edi_856_Hl::LEVEL_SHIPMENT);
            $this->_fileData[] = $ediWriter->getCarrierDetails($ship['shipping_method'], $ship['service_level_code']);
            $this->_fileData[] = $ediWriter->getDateTimeMaintenance($ship['date']);
            $this->_fileData[] = $ediWriter->getName($ship['ship_to_name']);
            $this->_fileData[] = $ediWriter->getHierarchicalLevel(2, 1, Guidance_VendorBridge_Model_File_Edi_856_Hl::LEVEL_ORDER);
            $this->_fileData[] = $ediWriter->getPurchaseOrderReference($ship['po_number']);
            $this->_fileData[] = $ediWriter->getReferenceIdentification('IV', $ship['invoice_number']);
            $this->_fileData[] = $ediWriter->getHierarchicalLevel(3, 2, Guidance_VendorBridge_Model_File_Edi_856_Hl::LEVEL_PACK);
            $this->_fileData[] = $ediWriter->getTrackingLine($ship['tracking_number']);

            foreach ($ship['items'] as $lineNumber => $item) {
                $this->_fileData[] = $ediWriter->getHierarchicalLevel(4, 3, Guidance_VendorBridge_Model_File_Edi_856_Hl::LEVEL_ITEM);
                $this->_fileData[] = $ediWriter->getItemIdentification($lineNumber, $item['sku']);
                $this->_fileData[] = $ediWriter->getItemDetail($item['qty']);
            }

            $endingLineCount   = count($this->_fileData);
            $numSegments       = $endingLineCount - $startingLineCount + 1;
            $this->_fileData[] = $ediWriter->getTransactionSetTrailer($numSegments, self::PROCESS_TYPE);
        }

        $this->_fileData[] = $ediWriter->getGroupTrailer($interchangeId);
        $this->_fileData[] = $ediWriter->getInterchangeTrailer($interchangeId);

        parent::_writeExportFile();

        if (!empty($this->_shipData)) {
            $this->_updateShipmentTable();
        }
    }

    public function exportShipments()
    {
        $this->process();
    }

    /**
     * @return array
     */
    protected function _getShipArray()
    {
        /** @var $shipments Mage_Sales_Model_Resource_Order_Shipment_Collection */
        $shipments = Mage::getModel('sales/order_shipment')->getCollection();
        $shipments->addFieldToFilter('reseller_exported', array('neq' => self::EXPORTED_FLAG));
        $shipments->addFieldToFilter('main_table.store_id', array('eq' => $this->_vendor->getStoreId()));
        $shipments->getSelect()->joinInner(
            array('order' => $shipments->getTable('sales/order')),
            '(order.entity_id = main_table.order_id)',
            array(
                'reseller_order_id'  => 'order.reseller_order_id',
                'shipping_method'    => 'order.shipping_description',
                'service_level_code' => new Zend_Db_Expr("{$this->_shippingDbExpr()}"),
            )
        );
        $shipments->getSelect()->joinInner(
            array('address' => $shipments->getTable('sales/quote_address')),
            '(order.quote_id = address.quote_id AND address.address_type = \'shipping\')',
            array('ship_to_name' => 'CONCAT_WS(" ", address.firstname, address.lastname)')
        );
        $shipments->getSelect()->joinInner(
            array('payment' => $shipments->getTable('sales/order_payment')),
            '(payment.parent_id = order.entity_id)',
            array('po_number' => 'payment.po_number')
        );
        $shipments->getSelect()->joinLeft(
            array('track' => $shipments->getTable('sales/shipment_track')),
            '(track.parent_id = main_table.entity_id)',
            array('track_number' => 'track.track_number')
        );
        $shipments->getSelect()->joinLeft(
            array('sitem' => $shipments->getTable('sales/shipment_item')),
            '(sitem.parent_id = main_table.entity_id)',
            array('qty' => 'sitem.qty', 'sku' => 'sitem.sku')
        );
        $shipments->getSelect()->joinInner(
            array('oitem' => $shipments->getTable('sales/order_item')),
            '(oitem.item_id = sitem.order_item_id AND oitem.product_type = \'simple\')',
            array('merchant_line_number' => 'oitem.merchantLineNumber')
        );
        $shipments->getSelect()->limit(self::MAX_SHIPMENTS);

        $shipArray = array();
        if ($shipments->getSize() < 1) {
            return $shipArray;
        }

        /** @var $stmt Varien_Db_Statement_Pdo_Mysql */
        $stmt = $this->_getWriteAdapter()->query($shipments->getSelect());
        $stmt->bindColumn('entity_id', $entityId);
        $stmt->bindColumn('service_level_code', $serviceLevelCode);
        $stmt->bindColumn('shipping_method', $shippingMethod);
        $stmt->bindColumn('ship_to_name', $shipToName);
        $stmt->bindColumn('track_number', $trackNumber);
        $stmt->bindColumn('created_at', $createdAt);
        $stmt->bindColumn('po_number', $poNumber);
        $stmt->bindColumn('increment_id', $incrementId);
        $stmt->bindColumn('reseller_order_id', $resellerOrderId);
        $stmt->bindColumn('merchant_line_number', $merchantLineNumber);
        $stmt->bindColumn('sku', $sku);
        $stmt->bindColumn('qty', $qty);
        while ($stmt->fetch()) {
            if (!isset($shipArray[$entityId])) {
                $shipArray[$entityId] = array(
                    'service_level_code' => $serviceLevelCode,
                    'shipping_method'    => $shippingMethod,
                    'ship_to_name'       => $shipToName,
                    'tracking_number'    => $trackNumber,
                    'date'               => $createdAt,
                    'po_number'          => $poNumber,
                    'invoice_number'     => $incrementId,
                    'order_number'       => $resellerOrderId,
                    'items'              => array(),
                );
            }
            $shipArray[$entityId]['items'][$merchantLineNumber] = array(
                'sku' => $sku,
                'qty' => $qty,
            );
        }
        $this->_shipData = $shipArray;

        return $shipArray;
    }

    /**
     * @return string
     */
    protected function _shippingDbExpr()
    {
        $defMethod = Guidance_VendorBridge_Model_ShipMethod::DEFAULT_EXT_METHOD;
        /** @var $shipMethods Guidance_VendorBridge_Model_Resource_ShipMethod_Collection */
        $shipMethods = $this->_vendor->getShippingMethods();
        if ($shipMethods->getSize() < 1) {
            return '\'' . $defMethod . '\'';
        }

        $caseLogic = 'CASE `order`.`shipping_description` ' . PHP_EOL;
        foreach ($shipMethods as $method) {
            /**@var $method Guidance_VendorBridge_Model_ShipMethod */
            $caseLogic .= 'WHEN \'' . $method->getInternalShipCode() . '\' THEN \'' . $method->getExternalShipCode() . '\' ' . PHP_EOL;
        }
        $caseLogic .= ' ELSE \'' . $defMethod . '\' END';

        return $caseLogic;
    }

    /**
     *
     */
    protected function _updateShipmentTable()
    {
        $entityIds = array_keys($this->_shipData);
        $resource  = $this->_getResource();
        $adapter   = $this->_getWriteAdapter();
        $adapter->update(
            $resource->getTableName('sales/shipment'),
            array('reseller_exported' => self::EXPORTED_FLAG, 'updated_at' => now()),
            array($adapter->quoteInto('entity_id IN(?) ', $entityIds))
        );
    }
}
