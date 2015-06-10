<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_File_Edi_856 extends Guidance_VendorBridge_Model_File_Edi
{
    /**
     * @param int $shipmentId
     * @return string
     */
    public function getBeginningSegmentShipNotice($shipmentId)
    {
        $args = array(
            'shipment_id' => (int)$shipmentId,
        );
        /** @var $bsn Guidance_VendorBridge_Model_File_Edi_856_Bsn */
        $bsn = Mage::getModel('vendorbridge/file_edi_856_bsn', $args);

        return $bsn->getBeginningSegmentShipNotice();
    }

    /**
     * @param        $idNumber
     * @param string $parentIdNumber
     * @param        $levelCode
     * @return mixed
     */
    public function getHierarchicalLevel($idNumber, $parentIdNumber = '', $levelCode)
    {
        $args = array(
            'id_number'        => (int)$idNumber,
            'parent_id_number' => (int)$parentIdNumber,
            'level_code'       => (string)$levelCode,
        );
        /** @var $hl Guidance_VendorBridge_Model_File_Edi_856_Hl */
        $hl = Mage::getModel('vendorbridge/file_edi_856_hl', $args);

        return $hl->getHierarchicalLevel();
    }

    /**
     * @param $shippingMethod
     * @param $serviceLevelCode
     * @return mixed
     */
    public function getCarrierDetails($shippingMethod, $serviceLevelCode)
    {
        $args = array(
            'shipping_method'    => (string)$shippingMethod,
            'service_level_code' => (string)$serviceLevelCode,
        );
        /** @var $td5 Guidance_VendorBridge_Model_File_Edi_856_Td5 */
        $td5 = Mage::getModel('vendorbridge/file_edi_856_td5', $args);

        return $td5->getCarrierDetails();
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
        /** @var $ref Guidance_VendorBridge_Model_File_Edi_856_Ref */
        $ref = Mage::getModel('vendorbridge/file_edi_856_ref', $args);

        return $ref->getReferenceIdentification();
    }

    /**
     * @param $date
     * @return mixed
     */
    public function getDateTimeMaintenance($date)
    {
        $args = array(
            'date' => (string)$date,
        );
        /** @var $dtm Guidance_VendorBridge_Model_File_Edi_856_Dtm */
        $dtm = Mage::getModel('vendorbridge/file_edi_856_dtm', $args);

        return $dtm->getDateTimeMaintenance();
    }

    /**
     * @param $shipToName
     * @return string
     */
    public function getName($shipToName)
    {
        $args = array(
            'ship_to_name' => (string)$shipToName,
        );
        /** @var $n1 Guidance_VendorBridge_Model_File_Edi_856_N1 */
        $n1 = Mage::getModel('vendorbridge/file_edi_856_n1', $args);

        return $n1->getName();
    }

    /**
     * @param $poNumber
     * @return mixed
     */
    public function getPurchaseOrderReference($poNumber)
    {
        $args = array(
            'po_number' => (string)$poNumber,
        );
        /** @var $prf Guidance_VEndorBridge_Model_File_Edi_856_Prf */
        $prf = Mage::getModel('vendorbridge/file_edi_856_prf', $args);

        return $prf->getPurchaseOrderReference();
    }

    /**
     * @param $lineNumber
     * @param $sku
     * @return mixed
     */
    public function getItemIdentification($lineNumber, $sku)
    {
        $args = array(
            'line_number' => (int)$lineNumber,
            'sku'         => (string)$sku,
        );
        /** @var $lin Guidance_VendorBridge_Model_File_Edi_856_Lin */
        $lin = Mage::getModel('vendorbridge/file_edi_856_lin', $args);

        return $lin->getItemIdentification();
    }

    /**
     * @param $qty
     * @return mixed
     */
    public function getItemDetail($qty)
    {
        $args = array(
            'qty_shipped' => (int)$qty,
        );
        /** @var $sn1 Guidance_VendorBridge_Model_File_Edi_856_Sn1 */
        $sn1 = Mage::getModel('vendorbridge/file_edi_856_sn1', $args);

        return $sn1->getItemDetail();
    }

    /**
     * @param string $trackingNumber
     * @return string
     */
    public function getTrackingLine($trackingNumber)
    {
        $args = array(
            'tracking_number' => (string)$trackingNumber,
        );
        /** @var $man Guidance_VendorBridge_Model_File_Edi_856_Man */
        $man = Mage::getModel('vendorbridge/file_edi_856_man', $args);

        return $man->getTrackingLine();
    }
}
