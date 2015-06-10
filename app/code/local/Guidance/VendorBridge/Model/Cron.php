<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_Cron
{
    /**
     *
     */
    public function __construct()
    {
        /** @var $helper Guidance_VendorBridge_Helper_Data */
        $helper = Mage::helper('vendorbridge');
        if (!$helper->isEnabled()) {
            throw new Guidance_VendorBridge_Exception(
                $helper->__(Guidance_VendorBridge_Helper_Data::NOT_ENABLED_MESSAGE)
            );
        }
    }

    public static function importOrders()
    {
        try {
            /** @var $model Guidance_VendorBridge_Model_Import_Orders */
            $model = Mage::getModel('vendorbridge/import_orders');
            $model->importOrders();
        } catch (Exception $e) {
        }
    }

    public static function exportInventoryFull()
    {
        try {
            /** @var $model Guidance_VendorBridge_Model_Export_Inventory */
            $model = Mage::getModel('vendorbridge/export_inventory');
            $model->exportFull();
        } catch (Exception $e) {
        }
    }

    public static function exportInventoryPartial()
    {
        try {
            /** @var $model Guidance_VendorBridge_Model_Export_Inventory */
            $model = Mage::getModel('vendorbridge/export_inventory');
            $model->exportPartial();
        } catch (Exception $e) {
        }
    }

    public static function exportShipments()
    {
        try {
            /** @var $model Guidance_VendorBridge_Model_Export_Shipments */
            $model = Mage::getModel('vendorbridge/export_shipments');
            $model->exportShipments();
        } catch (Exception $e) {
        }
    }

    public static function exportInvoices()
    {
        try {
            /** @var $model Guidance_VendorBridge_Model_Export_Invoices */
            $model = Mage::getModel('vendorbridge/export_invoices');
            $model->exportInvoices();
        } catch (Exception $e) {
        }
    }

    public static function exportCancels()
    {
        try {
            /** @var $model Guidance_VendorBridge_Model_Export_Cancels */
            $model = Mage::getModel('vendorbridge/export_cancels');
            $model->exportCancels();
        } catch (Exception $e) {
        }
    }
}
