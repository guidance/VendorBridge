<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_File_Edi_870 extends Guidance_VendorBridge_Model_File_Edi
{
    /**
     * @param int    $cancelId
     * @param string $cancelDate
     * @return string
     */
    public function getBeginningSegmentOrderStatus($cancelId, $cancelDate)
    {
        $args = array(
            'cancel_id'   => (int)$cancelId,
            'cancel_date' => (string)$cancelDate,
        );
        /** @var $bsr Guidance_VendorBridge_Model_File_Edi_870_Bsr */
        $bsr = Mage::getModel('vendorbridge/file_edi_870_bsr', $args);

        return $bsr->getBeginningSegmentOrderStatus();
    }

    /**
     * @param $qualifier
     * @param $id
     * @return mixed
     */
    public function getReferenceIdentification($qualifier, $id)
    {
        $args = array(
            'reference_id_qualifier' => (string)$qualifier,
            'reference_id'           => (string)$id,
        );
        /** @var $ref Guidance_VendorBridge_Model_File_Edi_870_Ref */
        $ref = Mage::getModel('vendorbridge/file_edi_870_ref', $args);

        return $ref->getReferenceIdentification();
    }

    /**
     * @param        $idNumber
     * @param string $parentIdNumber
     * @param        $levelCode
     * @return string
     */
    public function getHierarchicalLevel($idNumber, $parentIdNumber = '', $levelCode)
    {
        $args = array(
            'id_number'        => (int)$idNumber,
            'parent_id_number' => (string)$parentIdNumber,
            'level_code'       => (int)$levelCode,
        );
        /** @var $hl Guidance_VendorBridge_Model_File_Edi_870_Hl */
        $hl = Mage::getModel('vendorbridge/file_edi_870_hl', $args);

        return $hl->getHierarchicalLevel();
    }

    /**
     * @param $poNumber
     * @return string
     */
    public function getPurchaseOrderReference($poNumber)
    {
        $args = array(
            'po_number' => (string)$poNumber,
        );
        /** @var $prf Guidance_VendorBridge_Model_File_Edi_870_Prf */
        $prf = Mage::getModel('vendorbridge/file_edi_870_prf', $args);

        return $prf->getPurchaseOrderReference();
    }

    /**
     * @param $lineNumber
     * @param $qty
     * @param $sku
     * @return string
     */
    public function getBaselineItemData($lineNumber, $qty, $sku)
    {
        $args = array(
            'line_number' => (int)$lineNumber,
            'quantity'    => (int)$qty,
            'sku'         => (string)$sku,
        );
        /** @var $po1 Guidance_VendorBridge_Model_File_Edi_870_Po1 */
        $po1 = Mage::getModel('vendorbridge/file_edi_870_po1', $args);

        return $po1->getBaselineItemData();
    }

    /**
     * @return string
     */
    public function getItemStatusReport()
    {
        /** @var $isr Guidance_VendorBridge_Model_File_Edi_870_Isr */
        $isr = Mage::getModel('vendorbridge/file_edi_870_isr');

        return $isr->getItemStatusReport();
    }
}
