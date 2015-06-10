<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_File_Edi_846 extends Guidance_VendorBridge_Model_File_Edi
{
    /**#@+
     * Type constants
     */
    const UPC_QUALIFIER = 'UP';

    /**
     * @return string
     */
    public function getBeginningSegmentInventoryInquiry()
    {
        /** @var $bia Guidance_VendorBridge_Model_File_Edi_846_Bia */
        $bia = Mage::getModel('vendorbridge/file_edi_846_bia');

        return $bia->getBeginningSegmentInventoryInquiry();
    }

    /**
     * @return string
     */
    public function getReferenceIdentification()
    {
        $args = array(
            'vendor' => $this->_vendor,
        );
        /** @var $ref Guidance_VendorBridge_Model_File_Edi_846_Ref */
        $ref = Mage::getModel('vendorbridge/file_edi_846_ref', $args);

        return $ref->getReferenceIdentification();
    }

    /**
     * @param $sku
     * @param $qualifier
     * @return string line
     */
    public function getItemIdentification($sku, $qualifier = 'SK')
    {
        $args = array(
            'qualifier' => (string)$qualifier,
            'id'        => (string)$sku,
        );
        /** @var $lin Guidance_VendorBridge_Model_File_Edi_846_Lin */
        $lin = Mage::getModel('vendorbridge/file_edi_846_lin', $args);

        return $lin->getItemIdentification();
    }

    /**
     * @param $quantity
     * @return string line
     */
    public function getQuantity($quantity)
    {
        $args = array(
            'quantity' => (int)$quantity,
        );
        /** @var $qty Guidance_VendorBridge_Model_File_Edi_846_Qty */
        $qty = Mage::getModel('vendorbridge/file_edi_846_qty', $args);

        return $qty->getQuantity();
    }

    /**
     * @param int   $quantity
     * @param mixed $date
     * @return string line
     */
    public function getLineItemSchedule($quantity, $date = null)
    {
        $args = array(
            'quantity' => (int)$quantity,
            'date'     => $date,
        );
        /** @var $sch Guidance_VendorBridge_Model_File_Edi_846_Sch */
        $sch = Mage::getModel('vendorbridge/file_edi_846_sch', $args);

        return $sch->getLineItemSchedule();
    }

    /**
     * @return string
     */
    public function getDiscontinuedInactive()
    {
        /** @var $dtm Guidance_VendorBridge_Model_File_Edi_846_Dtm */
        $dtm = Mage::getModel('vendorbridge/file_edi_846_dtm');

        return $dtm->getDiscontinuedInactive();
    }
}
