<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_Export_Invoices extends Guidance_VendorBridge_Model_Export_Abstract
{
    /**#@+
     * Invoice constants
     */
    const FILENAME_PREFIX = 'Invoice';
    const FILE_EXTENSION  = '.edi';
    const PROCESS_TYPE    = 'vendorbridge_invoice_export';
    const PROCESS_NAME    = 'Invoice Export';
    const EXPORTED_FLAG   = 1;
    const ID_CODE         = '810';

    /**
     * @var array
     */
    private $_invoiceData = array();

    /**
     *
     */
    protected function _writeExportFile()
    {
        $exportData = $this->_getInvoiceArray();
        if (empty($exportData)) {
            throw new Guidance_VendorBridge_NofileException(
                Mage::helper('vendorbridge')->__('There are no invoices to export for %s at this time.', $this->_vendor->getName())
            );
        }
        /** @var $ediWriter Guidance_VendorBridge_Model_File_Edi_810 */
        $ediWriter     = Mage::getModel('vendorbridge/file_edi_810', array('vendor' => $this->_vendor));
        $interchangeId = $this->_exportObject->getEntityId();

        $this->_fileData   = array();
        $this->_fileData[] = $ediWriter->getInterchangeHeader($interchangeId);
        $this->_fileData[] = $ediWriter->getGroupHeader($interchangeId, 'IN');

        foreach ($exportData as $invoice) {
            $startingLineCount = count($this->_fileData);
            $this->_fileData[] = $ediWriter->getTransactionSetHeader(self::ID_CODE, self::PROCESS_TYPE, $interchangeId);
            $this->_fileData[] = $ediWriter->getBeginningSegmentForInvoice($invoice['increment_id'], $invoice['invoice_date'], $invoice['po_number']);
            $this->_fileData[] = $ediWriter->getReferenceNumber(
                Guidance_VendorBridge_Model_File_Edi_810_Ref::INTERNAL_VENDOR_NUMBER,
                $invoice['ref_3_x']
            );
            $this->_fileData[] = $ediWriter->getReferenceNumber(
                Guidance_VendorBridge_Model_File_Edi_810_Ref::DEPARTMENT_NUMBER,
                $invoice['tracking_number']
            );
            $this->_fileData[] = $ediWriter->getName($invoice['ship_to_name']);
            $this->_fileData[] = $ediWriter->getTermsOfSale();
            $this->_fileData[] = $ediWriter->getDateTimeReference($invoice['ship_date']);

            $totalAmountInvoiced = 0;
            $lineItemsCount      = 0;
            foreach ($invoice['items'] as $lineNumber => $item) {
                $this->_fileData[] = $ediWriter->getBaselineItemDetail($lineNumber, $item['qty'], $item['price'], $item['sku']);
                $totalAmountInvoiced += $item['row_total'];
                $lineItemsCount++;
            }

            $this->_fileData[] = $ediWriter->getTotalMonetaryValueSummary($totalAmountInvoiced);
            $this->_fileData[] = $ediWriter->getCarrierDetail();
            $this->_fileData[] = $ediWriter->getTransactionTotals($lineItemsCount);

            $numSegments       = count($this->_fileData) - $startingLineCount + 1;
            $this->_fileData[] = $ediWriter->getTransactionSetTrailer($numSegments, self::PROCESS_TYPE);
        }

        $this->_fileData[] = $ediWriter->getGroupTrailer($interchangeId, count($exportData));
        $this->_fileData[] = $ediWriter->getInterchangeTrailer($interchangeId);

        parent::_writeExportFile();

        if (!empty($this->_invoiceData)) {
            $this->_updateShipmentTable();
        }
    }

    /**
     *
     */
    public function exportInvoices()
    {
        $this->process();
    }

    /**
     * @return array
     */
    protected function _getInvoiceArray()
    {
        /** @var $shipments Mage_Sales_Model_Resource_Order_Shipment_Collection */
        $shipments = Mage::getModel('sales/order_shipment')->getCollection();
        $shipments->addFieldToFilter('reseller_exported', array('eq' => Guidance_VendorBridge_Model_Export_Shipments::EXPORTED_FLAG));
        $shipments->addFieldToFilter('is_invoice_exported', array('neq' => self::EXPORTED_FLAG));
        $shipments->addFieldToFilter('main_table.store_id', array('eq' => $this->_vendor->getStoreId()));
        $shipments->getSelect()->joinInner(
            array('order' => $shipments->getTable('sales/order')),
            '(order.entity_id = main_table.order_id)',
            array(
                'ref_3_x'           => 'order.ref_3_x',
                'reseller_order_id' => 'order.reseller_order_id',
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
            array('track_number' => 'UPPER(track.track_number)')
        );
        $shipments->getSelect()->joinLeft(
            array('sitem' => $shipments->getTable('sales/shipment_item')),
            '(sitem.parent_id = main_table.entity_id)',
            array('qty' => 'sitem.qty', 'sku' => 'sitem.sku')
        );
        $shipments->getSelect()->joinInner(
            array('oitem' => $shipments->getTable('sales/order_item')),
            '(oitem.item_id = sitem.order_item_id AND oitem.product_type = \'simple\')',
            array(
                'merchant_line_number' => 'oitem.merchantLineNumber',
                'price'                => 'oitem.price',
                'row_total'            => 'oitem.row_total'
            )
        );

        $invoiceArray = array();
        if ($shipments->getSize() < 1) {
            return $invoiceArray;
        }

        $stmt = $this->_getWriteAdapter()->query($shipments->getSelect());
        $stmt->bindColumn('entity_id', $entityId);
        $stmt->bindColumn('track_number', $trackNumber);
        $stmt->bindColumn('created_at', $createdAt);
        $stmt->bindColumn('updated_at', $updatedAt);
        $stmt->bindColumn('po_number', $poNumber);
        $stmt->bindColumn('created_at', $createdAt);
        $stmt->bindColumn('ref_3_x', $ref3x);
        $stmt->bindColumn('reseller_order_id', $resellerOrderId);
        $stmt->bindColumn('increment_id', $incrementId);
        $stmt->bindColumn('ship_to_name', $shipToName);
        $stmt->bindColumn('sku', $sku);
        $stmt->bindColumn('qty', $qty);
        $stmt->bindColumn('merchant_line_number', $merchantLineNumber);
        $stmt->bindColumn('price', $price);
        $stmt->bindColumn('row_total', $rowTotal);
        while ($stmt->fetch()) {
            if (!isset($invoiceArray[$entityId])) {
                $invoiceArray[$entityId] = array(
                    'tracking_number' => $trackNumber,
                    'ship_date'       => $createdAt,
                    'invoice_date'    => $updatedAt,
                    'po_number'       => $poNumber,
                    'ref_3_x'         => $ref3x,
                    'order_number'    => $resellerOrderId,
                    'increment_id'    => $incrementId,
                    'ship_to_name'    => $shipToName,
                    'items'           => array(),
                );
            }
            $invoiceArray[$entityId]['items'][$merchantLineNumber] = array(
                'sku'       => $sku,
                'qty'       => $qty,
                'price'     => $price,
                'row_total' => $rowTotal,
            );
        }
        $this->_invoiceData = $invoiceArray;

        return $invoiceArray;
    }

    /**
     *
     */
    protected function _updateShipmentTable()
    {
        $entityIds = array_keys($this->_invoiceData);
        $resource  = $this->_getResource();
        $adapter   = $this->_getWriteAdapter();
        $adapter->update(
            $resource->getTableName('sales/shipment'),
            array('is_invoice_exported' => self::EXPORTED_FLAG),
            array($adapter->quoteInto('entity_id IN(?) ', $entityIds))
        );
    }
}
