<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_File_Edi_810 extends Guidance_VendorBridge_Model_File_Edi
{
    /**
     * @param int    $invoiceId
     * @param string $poDate
     * @param string $poNumber
     * @return string
     */
    public function getBeginningSegmentForInvoice($invoiceId, $poDate, $poNumber)
    {
        $args = array(
            'invoice_id' => (int)$invoiceId,
            'po_date'    => (string)$poDate,
            'po_number'  => (string)$poNumber,
        );
        /** @var $big Guidance_VendorBridge_Model_File_Edi_810_Big */
        $big = Mage::getModel('vendorbridge/file_edi_810_big', $args);

        return $big->getBeginningSegmentForInvoice();
    }

    /**
     * @param string $qualifier
     * @param string $id
     * @return string
     */
    public function getReferenceNumber($qualifier, $id)
    {
        $args = array(
            'reference_id_qualifier' => (string)$qualifier,
            'reference_id'           => (string)$id,
        );
        /** @var $ref Guidance_VendorBridge_Model_File_Edi_810_Ref */
        $ref = Mage::getModel('vendorbridge/file_edi_810_ref', $args);

        return $ref->getReferenceNumber();
    }

    /**
     * @param string $name
     * @return string
     */
    public function getName($name)
    {
        $args = array(
            'ship_to_name' => (string)$name,
        );
        /** @var $n1 Guidance_VendorBridge_Model_File_Edi_810_N1 */
        $n1 = Mage::getModel('vendorbridge/file_edi_810_n1', $args);

        return $n1->getName();
    }

    /**
     * @return string
     */
    public function getTermsOfSale()
    {
        /** @var $itd Guidance_VendorBridge_Model_File_Edi_810_Itd */
        $itd = Mage::getModel('vendorbridge/file_edi_810_itd');

        return $itd->getTermsOfSale();
    }

    /**
     * @param $date
     * @return mixed
     */
    public function getDateTimeReference($date)
    {
        $args = array(
            'date' => (string)$date,
        );
        /** @var $dtm Guidance_VendorBridge_Model_File_Edi_810_Dtm */
        $dtm = Mage::getModel('vendorbridge/file_edi_810_dtm', $args);

        return $dtm->getDateTimeReference();
    }

    /**
     * @param $lineNumber
     * @param $qty
     * @param $price
     * @param $sku
     * @return mixed
     */
    public function getBaselineItemDetail($lineNumber, $qty, $price, $sku)
    {
        $args = array(
            'line_number'      => (int)$lineNumber,
            'quantity_shipped' => (int)$qty,
            'price'            => (float)$price,
            'sku'              => (string)$sku,
        );
        /** @var $it1 Guidance_VendorBridge_Model_File_Edi_810_It1 */
        $it1 = Mage::getModel('vendorbridge/file_edi_810_it1', $args);

        return $it1->getBaselineItemDetail();
    }

    /**
     * @param $amountInvoiced
     * @return mixed
     */
    public function getTotalMonetaryValueSummary($amountInvoiced)
    {
        $args = array(
            'amount_invoiced' => (float)$amountInvoiced,
        );
        /** @var $tds Guidance_VendorBridge_Model_File_Edi_810_Tds */
        $tds = Mage::getModel('vendorbridge/file_edi_810_tds', $args);

        return $tds->getTotalMonetaryValueSummary();
    }

    /**
     * @return mixed
     */
    public function getCarrierDetail()
    {
        /** @var $cad Guidance_VendorBridge_Model_File_Edi_810_Cad */
        $cad = Mage::getModel('vendorbridge/file_edi_810_cad');

        return $cad->getCarrierDetail();
    }

    /**
     * @return string
     */
    public function getInvoiceShipmentSummary()
    {
        /** @var $iss Guidance_VendorBridge_Model_File_Edi_810_Iss */
        $iss = Mage::getModel('vendorbridge/file_edi_810_iss');

        return $iss->getInvoiceShipmentSummary();
    }

    /**
     * @param $lineItemsCount
     * @return mixed
     */
    public function getTransactionTotals($lineItemsCount)
    {
        $args = array(
            'line_items_count' => (int)$lineItemsCount,
        );
        /** @var $ctt Guidance_VendorBridge_Model_File_Edi_810_Ctt */
        $ctt = Mage::getModel('vendorbridge/file_edi_810_ctt', $args);

        return $ctt->getTransactionTotals();
    }
}
