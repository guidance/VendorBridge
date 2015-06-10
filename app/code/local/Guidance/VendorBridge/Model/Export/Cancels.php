<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_Export_Cancels extends Guidance_VendorBridge_Model_Export_Abstract
{
    /**#@+
     * Cancellation constants
     */
    const FILENAME_PREFIX = 'Cancel';
    const FILE_EXTENSION  = '.edi';
    const PROCESS_TYPE    = 'vendorbridge_cancel_export';
    const PROCESS_NAME    = 'Cancel Export';
    const EXPORTED_FLAG   = 1;
    const ID_CODE         = '870';

    /**
     * @var array
     */
    private $_cancelData = array();

    /**
     * @throws Guidance_VendorBridge_NofileException
     */
    protected function _writeExportFile()
    {
        $exportData = $this->_getCancelArray();
        if (empty($exportData)) {
            throw new Guidance_VendorBridge_NofileException(
                Mage::helper('vendorbridge')->__('There are no cancellations to export for %s at this time.', $this->_vendor->getName())
            );
        }
        /** @var $ediWriter Guidance_VendorBridge_Model_File_Edi_870 */
        $ediWriter     = Mage::getModel('vendorbridge/file_edi_870', array('vendor' => $this->_vendor));
        $interchangeId = $this->_exportObject->getEntityId();

        $this->_fileData   = array();
        $this->_fileData[] = $ediWriter->getInterchangeHeader($interchangeId);
        $this->_fileData[] = $ediWriter->getGroupHeader($interchangeId, 'CA');

        foreach ($exportData as $cancelId => $cancel) {
            $startingLineCount = count($this->_fileData);
            $this->_fileData[] = $ediWriter->getTransactionSetHeader(self::ID_CODE, self::PROCESS_TYPE, $interchangeId);
            $this->_fileData[] = $ediWriter->getBeginningSegmentOrderStatus($cancelId, $cancel['date']);
            $this->_fileData[] = $ediWriter->getReferenceIdentification(
                Guidance_VendorBridge_Model_File_Edi_870_Ref::INTERNAL_VENDOR_NUMBER,
                $this->_vendor->getVendorNumber()
            );
            $this->_fileData[] = $ediWriter->getHierarchicalLevel(1, null, Guidance_VendorBridge_Model_File_Edi_870_Hl::LEVEL_ORDER);
            $this->_fileData[] = $ediWriter->getPurchaseOrderReference($cancel['po_number']);
            $this->_fileData[] = $ediWriter->getReferenceIdentification(
                Guidance_VendorBridge_Model_File_Edi_870_Ref::VENDORS_ORDER_NUMBER,
                $cancel['order_increment_id']
            );
            $this->_fileData[] = $ediWriter->getReferenceIdentification(
                Guidance_VendorBridge_Model_File_Edi_870_Ref::CUSTOMER_ORDER_NUMBER,
                $cancel['order_number']
            );
            foreach ($cancel['items'] as $lineNumber => $item) {
                $this->_fileData[] = $ediWriter->getHierarchicalLevel(2, 1, Guidance_VendorBridge_Model_File_Edi_856_Hl::LEVEL_ITEM);
                $this->_fileData[] = $ediWriter->getBaselineItemData($lineNumber, $item['qty'], $item['sku']);
                $this->_fileData[] = $ediWriter->getItemStatusReport();
            }
            $numSegments       = count($this->_fileData) - $startingLineCount + 1;
            $this->_fileData[] = $ediWriter->getTransactionSetTrailer($numSegments, self::PROCESS_TYPE);
        }

        $this->_fileData[] = $ediWriter->getGroupTrailer($interchangeId);
        $this->_fileData[] = $ediWriter->getInterchangeTrailer($interchangeId);

        parent::_writeExportFile();

        if (!empty($this->_cancelData)) {
            $this->_updateCreditmemoTable();
        }
    }

    /**
     *
     */
    public function exportCancels()
    {
        $this->process();
    }

    /**
     * @return array
     */
    protected function _getCancelArray()
    {
        /** @var $creditmemos Mage_Sales_Model_Resource_Order_Creditmemo_Collection */
        $creditmemos = Mage::getModel('sales/order_creditmemo')->getCollection();
        $creditmemos->addFieldToFilter('main_table.is_exported', array('neq' => self::EXPORTED_FLAG));
        $creditmemos->addFieldToFilter('main_table.store_id', array('eq' => $this->_vendor->getStoreId()));
        $creditmemos->getSelect()->joinInner(
            array('order' => $creditmemos->getTable('sales/order')),
            '(order.entity_id = main_table.order_id)',
            array('reseller_order_id' => 'order.reseller_order_id', 'order_increment_id' => 'order.increment_id')
        );
        $creditmemos->getSelect()->joinInner(
            array('payment' => $creditmemos->getTable('sales/order_payment')),
            '(payment.parent_id = order.entity_id)',
            array('po_number' => 'payment.po_number')
        );
        $creditmemos->getSelect()->joinLeft(
            array('citem' => $creditmemos->getTable('sales/creditmemo_item')),
            '(citem.parent_id = main_table.entity_id)',
            array('qty' => 'citem.qty', 'sku' => 'citem.sku')
        );
        $creditmemos->getSelect()->joinInner(
            array('oitem' => $creditmemos->getTable('sales/order_item')),
            '(oitem.item_id = citem.order_item_id AND oitem.product_type = \'simple\' AND citem.qty <= (oitem.qty_ordered - oitem.qty_shipped))',
            array('merchant_line_number' => 'oitem.merchantLineNumber')
        );

        $cancelArray = array();
        if ($creditmemos->getSize() < 1) {
            return $cancelArray;
        }

        $stmt = $this->_getWriteAdapter()->query($creditmemos->getSelect());
        $stmt->bindColumn('increment_id', $incrementId);
        $stmt->bindColumn('created_at', $createdAt);
        $stmt->bindColumn('po_number', $poNumber);
        $stmt->bindColumn('order_increment_id', $orderIncrementId);
        $stmt->bindColumn('reseller_order_id', $resellerOrderId);
        $stmt->bindColumn('merchant_line_number', $merchantLineNumber);
        $stmt->bindColumn('sku', $sku);
        $stmt->bindColumn('qty', $qty);
        while ($stmt->fetch()) {
            if (!isset($cancelArray[$incrementId])) {
                $cancelArray[$incrementId] = array(
                    'date'               => $createdAt,
                    'po_number'          => $poNumber,
                    'order_increment_id' => $orderIncrementId,
                    'order_number'       => $resellerOrderId,
                    'items'              => array(),
                );
            }
            $cancelArray[$incrementId]['items'][$merchantLineNumber] = array(
                'sku' => $sku,
                'qty' => $qty,
            );
        }
        $this->_cancelData = $cancelArray;

        return $cancelArray;
    }

    protected function _updateCreditmemoTable()
    {
        $entityIds = array_keys($this->_cancelData);
        $resource  = $this->_getResource();
        $adapter   = $this->_getWriteAdapter();
        $adapter->update(
            $resource->getTableName('sales/creditmemo'),
            array('is_exported' => self::EXPORTED_FLAG, 'updated_at' => now()),
            array($adapter->quoteInto('increment_id IN(?) ', $entityIds))
        );
    }
}
