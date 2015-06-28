<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_File_Edi
{
    /**
     * @var $_vendor Guidance_VendorBridge_Model_Vendor
     */
    protected $_vendor;

    /**
     * @param array $args
     */
    public function __construct(array $args)
    {
        $this->_vendor = $args['vendor'];
        $this->_construct();
    }

    /**
     *
     */
    public function _construct()
    {
    }

    /**
     * @param int $interchangeId
     * @return string
     */
    public function getInterchangeHeader($interchangeId)
    {
        $args = array(
            'interchange_id'           => (int)$interchangeId,
            'interchange_sender_id'    => (string)$this->_vendor->getInterchangeSenderId(),
            'interchange_receiver_id'  => (string)$this->_vendor->getInterchangeReceiverId(),
            'interchange_id_qualifier' => (string)$this->_vendor->getQualifier(),
        );
        /** @var $isa Guidance_VendorBridge_Model_File_Line_Isa */
        $isa = Mage::getModel('vendorbridge/file_line_isa', $args);

        return $isa->getInterchangeHeader();
    }

    /**
     * @param int    $interchangeId
     * @param string $functionalId
     * @return string
     */
    public function getGroupHeader($interchangeId, $functionalId)
    {
        $args = array(
            'interchange_id'          => (int)$interchangeId,
            'functional_id'           => (string)$functionalId,
            'interchange_sender_id'   => (string)$this->_vendor->getInterchangeSenderId(),
            'interchange_receiver_id' => (string)$this->_vendor->getInterchangeReceiverId(),
        );
        /** @var $gs Guidance_VendorBridge_Model_File_Line_Gs */
        $gs = Mage::getModel('vendorbridge/file_line_gs', $args);

        return $gs->getGroupHeader();
    }

    /**
     * @param int    $idCode
     * @param string $tableName
     * @param int    $interchangeId
     * @return string
     */
    public function getTransactionSetHeader($idCode, $tableName, $interchangeId)
    {
        $args = array(
            'id_code'        => (int)$idCode,
            'table_name'     => (string)$tableName,
            'interchange_id' => (int)$interchangeId,
        );
        /** @var $st Guidance_VendorBridge_Model_File_Line_St */
        $st = Mage::getModel('vendorbridge/file_line_st', $args);

        return $st->getTransactionSetHeader();
    }

    /**
     * @param $numSegments
     * @param $tableName
     * @return string
     */
    public function getTransactionSetTrailer($numSegments, $tableName)
    {
        $args = array(
            'num_segments' => (int)$numSegments,
            'table_name'   => (string)$tableName,
        );
        /** @var $se Guidance_VendorBridge_Model_File_Line_Se */
        $se = Mage::getModel('vendorbridge/file_line_se', $args);

        return $se->getTransactionSetTrailer();
    }

    /**
     * @param     $interchangeId
     * @param int $numSets
     * @return string
     */
    public function getGroupTrailer($interchangeId, $numSets = 1)
    {
        $args = array(
            'interchange_id' => (int)$interchangeId,
            'num_sets'       => (int)$numSets,
        );
        /** @var $ge Guidance_VendorBridge_Model_File_Line_Ge */
        $ge = Mage::getModel('vendorbridge/file_line_ge', $args);

        return $ge->getGroupTrailer();
    }

    /**
     * @param     $interchangeId
     * @param int $numGroups
     * @return string
     */
    public function getInterchangeTrailer($interchangeId, $numGroups = 1)
    {
        $args = array(
            'interchange_id' => (int)$interchangeId,
            'num_groups'     => (int)$numGroups,
        );
        /** @var $iea Guidance_VendorBridge_Model_File_Line_Iea */
        $iea = Mage::getModel('vendorbridge/file_line_iea', $args);

        return $iea->getInterchangeTrailer();
    }
}
