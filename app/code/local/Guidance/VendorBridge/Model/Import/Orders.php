<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_Import_Orders extends Guidance_VendorBridge_Model_Import_Abstract
{
    /**#@+
     * Order import constants
     */
    const ORDER_EXTENSION = '.order';
    const EDI_EXTENSION   = '.edi';
    const PROCESS_TYPE    = 'vendorbridge_order_import';
    const PROCESS_NAME    = 'Order Import';
    const EDI_LOGFILE     = 'edireader.log';
    const EDI_TYPE_CODE   = '850';

    /**
     * Do a quick check to see if there are orders to process before we actually start
     *
     * @throws Guidance_VendorBridge_Exception
     */
    public function importOrders()
    {
        $files = array();
        foreach (glob($this->_getLocalFolder() . '*' . self::ORDER_EXTENSION) as $file) {
            $files[] = realpath($file);
        }
        foreach (glob($this->_getLocalFolder() . '*' . self::EDI_EXTENSION) as $file) {
            $files[] = realpath($file);
        }
        if (count($files) < 1) {
            $this->_stopRunning();
            throw new Guidance_VendorBridge_NofileException(
                Mage::helper('vendorbridge')->__('There are no orders to import.')
            );
        }
        $this->process();
    }

    protected function _convertOrderFiles()
    {
        $edireaderPath = Mage::getBaseDir() . DS . 'edireader';
        $styleSheet    = $edireaderPath . DS . 'po.xslt';
        $archiveFolder = $this->_getArchiveFolder();

        foreach (glob($this->_getLocalFolder() . '*' . self::EDI_EXTENSION) as $file) {
            $ediString = file_get_contents($file);
            $ediParts  = explode(Guidance_VendorBridge_Model_File_Line_Abstract::ELEMENT_SEPARATOR, $ediString);
            //check ISA07 for interchange receiver id
            if (trim($ediParts[6]) != $this->_vendor->getInterchangeReceiverId()) {
                continue;
            }
            $filePath = realpath($file);
            $destPath = str_replace(array(self::EDI_EXTENSION, self::LOCAL_IMPORT_PATH . DS), array(self::ORDER_EXTENSION, self::LOCAL_IMPORT_PATH . DS . $this->_vendor->getData('entity_id') . '-'), $filePath);
            $output   = shell_exec("bash $edireaderPath/scripts/EDITransform.sh $filePath -x $styleSheet -o $destPath 2>&1");

            if (file_exists($destPath) && trim(file_get_contents($destPath)) != '') {
                $dest   = $archiveFolder . DS . $this->_vendor->getData('entity_id') . '-' . $this->_microTimeString() . '-' . basename($filePath);
                $data   = file_get_contents($filePath);
                $handle = fopen($dest, 'w');
                if ($handle) {
                    fwrite($handle, $data);
                    fclose($handle);
                    chmod($dest, 0755);
                    @unlink($filePath);
                }

                $xml   = file_get_contents($destPath);
                $ppxml = $this->_xmlpp($xml);
                file_put_contents($destPath, $ppxml);
            } else {
                Mage::log(print_r($output, true), null, self::EDI_LOGFILE, true);
                @unlink($destPath);
            }
        }
    }

    protected function _readImportFiles()
    {
        $this->_convertOrderFiles();
        $this->_processList = array();
        foreach (glob($this->_getLocalFolder() . DS . '*' . self::ORDER_EXTENSION) as $file) {
            $file      = realpath($file);
            $fileParts = explode('-', basename($file));
            if (reset($fileParts) != $this->_vendor->getEntityId()) {
                continue;
            }
            $xml     = file_get_contents($file);
            $element = new SimpleXMLElement($xml);
            foreach ($element->order as $order) {
                $this->_fileData[$this->_vendor->getEntityId()][] = $order;
            }
            $this->_processList[] = $file;
        }

        if (empty($this->_processList)) {
            throw new Guidance_VendorBridge_NofileException(
                Mage::helper('vendorbridge')->__('No orders to import for %s', $this->_vendor->getName())
            );
        }
        $this->_createOrders();
    }

    /**
     *
     */
    protected function _createOrders()
    {
        foreach ($this->_fileData[$this->_vendor->getEntityId()] as $order) {
            if ($this->_orderExists($order)) {
                continue;
            }
            $orderData = $this->_prepareOrderData($order);
            /** @var $quote Mage_Sales_Model_Quote */
            $quote = $this->_createQuote($orderData);
            if ($quote instanceof Mage_Sales_Model_Quote) {
                $this->_saveOrder($quote);
            }
        }
    }

    /**
     * @param SimpleXMLElement $order
     * @return bool
     */
    protected function _orderExists(SimpleXMLElement $order)
    {
        /** @var $helper Guidance_VendorBridge_Helper_Data */
        $helper = Mage::helper('vendorbridge');
        /** @var $collection Mage_Sales_Model_Resource_Order_Collection */
        $collection = Mage::getModel('sales/order')->getCollection();
        $collection->addFieldToFilter('store_id', array('eq' => $this->_vendor->getStoreId()));
        $collection->addFieldToFilter('reseller_order_id', array('eq' => $order->order_id));

        $exists = $collection->getSize() > 0;
        if ($exists) {
            $message = $helper->__('Skipping order with reseller Order Number %s that already exists.', $order->order_id);
            $helper->log($message);
        }

        return $exists;
    }

    /**
     * @param SimpleXMLElement $order
     * @return array
     */
    protected function _prepareOrderData(SimpleXMLElement $order)
    {
        $name      = explode(' ', $order->shipping_address->name);
        $firstname = reset($name);
        $lastname  = end($name);
        //emails will not be received
        $email = $this->_microTimeString() . '@vendorbridge-import.com';
        if (empty($lastname)) {
            $lastname = '&nbsp;';
        }

        $orderData = array(
            'session'  => array(
                'customer_id' => '0',
                'store_id'    => $this->_vendor->getStoreId(),
                'group_id'    => '1',
            ),
            'payment'  => array(
                'method'    => 'purchaseorder',
                'po_number' => (string)$order->po_number,
            ),
            'customer' => array(
                'email' => $email,
            ),
            'order'    => array(
                'reseller'             => $this->_vendor->getResellerId(),
                'reseller_order_id'    => (string)$order->order_id,
                'ref_3_x'              => (string)$order->ref_3_x,
                'cancel_after'         => date(Varien_Date::DATE_PHP_FORMAT, strtotime($order->cancel_after)),
                'reseller_order_date'  => date(Varien_Date::DATETIME_PHP_FORMAT, strtotime($order->date)),
                'currency'             => 'USD',
                'account'              => array(
                    'group_id' => '1',
                    'email'    => $email,
                ),
                'billing_address'      => array(
                    'prefix'     => '',
                    'firstname'  => $firstname,
                    'middlename' => '',
                    'lastname'   => $lastname,
                    'suffix'     => '',
                    'company'    => '',
                    'street'     => array(
                        (string)$order->shipping_address->address_line_1,
                        (string)$order->shipping_address->address_line_2
                    ),
                    'city'       => (string)$order->shipping_address->city,
                    'country_id' => 'US',
                    'country'    => 'US',
                    'region'     => (string)$order->shipping_address->state,
                    'region_id'  => (string)$order->shipping_address->state,
                    'postcode'   => (string)$order->shipping_address->postal_code,
                    'telephone'  => (string)$order->shipping_address->telephone,
                    'fax'        => '',
                ),
                'shipping_address'     => array(
                    'prefix'     => '',
                    'firstname'  => $firstname,
                    'middlename' => '',
                    'lastname'   => $lastname,
                    'suffix'     => '',
                    'company'    => '',
                    'street'     => array(
                        (string)$order->shipping_address->address_line_1,
                        (string)$order->shipping_address->address_line_2
                    ),
                    'city'       => (string)$order->shipping_address->city,
                    'country_id' => 'US',
                    'country'    => 'US',
                    'region'     => (string)$order->shipping_address->state,
                    'region_id'  => (string)$order->shipping_address->state,
                    'postcode'   => (string)$order->shipping_address->postal_code,
                    'telephone'  => (string)$order->shipping_address->telephone,
                    'fax'        => '',
                ),
                'shipping_method'      => 'freeshipping_freeshipping',
                'shipping_description' => $this->_mapShippingMethod($order->shipping_method),
                'send_confirmation'    => false,
            ),
        );

        foreach ($order->items->item as $item) {
            $orderData['products'][] = array(
                'sku'      => (string)$item->sku,
                'line'     => (string)$item->line_number,
                'price'    => (string)$item->price,
                'quantity' => (string)$item->quantity,
            );
        }

        return $orderData;
    }

    /**
     * @param string $methodCode
     * @return string
     */
    protected function _mapShippingMethod($methodCode)
    {
        /** @var $shipMethods Guidance_VendorBridge_Model_Resource_ShipMethod_Collection */
        $shipMethods = $this->_vendor->getShippingMethods();
        foreach ($shipMethods as $method) {
            /**@var $method Guidance_VendorBridge_Model_ShipMethod */
            if ($method->getExternalShipCode() == $methodCode) {
                return $method->getInternalShipCode();
            }
        }

        return Guidance_VendorBridge_Model_ShipMethod::DEFAULT_METHOD;
    }

    /**
     * @param array $orderData
     * @return bool false|Mage_Core_Model_Abstract
     */
    protected function _createQuote(array $orderData)
    {
        /** @var $helper Guidance_VendorBridge_Helper_Data */
        $helper = Mage::helper('vendorbridge');
        /** @var $session Mage_Core_Model_Session */
        $session = Mage::getSingleton('core/session');
        $session->setData('order_import', true);
        /** @var $quote Mage_Sales_Model_Quote */
        $quote = Mage::getModel('sales/quote');
        $quote->setData('reseller', $this->_vendor->getResellerId());
        $quote->setData('reseller_order_id', $orderData['order']['reseller_order_id']);
        $quote->setData('reseller_order_date', $orderData['order']['reseller_order_date']);
        $quote->setData('cancel_after', $orderData['order']['cancel_after']);
        $quote->setData('ref_3_x', $orderData['order']['ref_3_x']);
        $quote->setIsMultiShipping(false);
        $quote->setCheckoutMethod('guest');
        $quote->setCustomerEmail($orderData['customer']['email']);
        $quote->setCustomerIsGuest(true);
        $quote->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);
        $quote->setStore($this->_vendor->getStore());
        $quote->setData('ignore_old_qty', true);

        try {
            $quote->save();
        } catch (Exception $e) {
            $helper->log($e->getMessage());
            Mage::logException($e);
            return false;
        }

        /** @var $productModel Mage_Catalog_Model_Product */
        $productModel = Mage::getModel('catalog/product');
        $subTotal     = 0;
        $productIds   = array();
        foreach ($orderData['products'] as $data) {
            $productId = $productModel->getIdBySku($data['sku']);
            if ($productId) {
                $session->setData('product_id', $productId);
                $productModel->load($productId);
                $productIds[] = $productId;

                try {
                    /** @var $item Mage_Sales_Model_Quote_Item */
                    $item = Mage::getModel('sales/quote_item');
                    $item->setQuote($quote);
                    $item->setProduct($productModel);
                    $qty = number_format($data['quantity'], 2);
                    $item->setData('qty', $qty);
                    $item->setCustomPrice($data['price']);
                    $item->setOriginalCustomPrice($data['price']);
                    $item->setData('merchantLineNumber', $data['line']);
                    $item->setNoDiscount(true);
                    $quote->addItem($item);
                    $rowTotal = (float)($qty * $data['price']);
                    $subTotal += $rowTotal;
                } catch (Exception $e) {
                    Mage::logException($e);
                }
                $session->unsetData('product_id');
            } else {
                $message = $helper->__('Order import error: SKU %s does not exist in Vendor Bridge order id %s.', $data['sku'], $orderData['order']['reseller_order_id']);
                $helper->log($message);
            }
        }
        $quote->setSubtotal($subTotal)
            ->setBaseSubtotal($subTotal)
            ->setGrandTotal($subTotal)
            ->setBaseGrandTotal($subTotal);

        /** @var $quoteShippingAddress Mage_Sales_Model_Quote_Address */
        $quoteShippingAddress = new Mage_Sales_Model_Quote_Address();
        $quoteShippingAddress->setData($orderData['order']['shipping_address']);
        /** @var $quoteBillingAddress Mage_Sales_Model_Quote_Address */
        $quoteBillingAddress = new Mage_Sales_Model_Quote_Address();
        $quoteBillingAddress->setData($orderData['order']['billing_address']);
        $quote->setShippingAddress($quoteShippingAddress);
        $quote->setBillingAddress($quoteBillingAddress);
        $quote->getShippingAddress()->setShippingMethod($orderData['order']['shipping_method']);
        $quote->setData('shipping_description', $orderData['order']['shipping_description']);
        $quote->getShippingAddress()->setCollectShippingRates(true);
        /** @var $payment Mage_Sales_Model_Quote_Payment */
        $payment = $quote->getPayment();
        $payment->setMethod($orderData['payment']['method']);
        /** @var $paymentData Varien_Object */
        $paymentData = new Varien_Object();
        $paymentData->setData('po_number', $orderData['payment']['po_number']);
        $payment->getMethodInstance()->assignData($paymentData);
        $session->setData('product_ids', $productIds);
        $quote->collectTotals();
        $quote->save();

        return $quote;
    }

    /**
     * @param Mage_Sales_Model_Quote $quote
     */
    protected function _saveOrder(Mage_Sales_Model_Quote $quote)
    {
        try {
            /** @var $helper Guidance_VendorBridge_Helper_Data */
            $helper = Mage::helper('vendorbridge');
            /** @var $service Mage_Sales_Model_Service_Quote */
            $service = Mage::getModel('sales/service_quote', $quote);
            /** @var $order Mage_Sales_Model_Order */
            $order = $service->submitOrder();
            $order->setShippingDescription($quote->getData('shipping_description'));
            //do our own convert
            $order->setData('ref_3_x', $quote->getData('ref_3_x'));
            $order->setData('reseller', $quote->getData('reseller'));
            $order->setData('reseller_order_id', $quote->getData('reseller_order_id'));
            $order->setData('cancel_after', $quote->getData('cancel_after'));
            $order->save();
            $quoteItems = $quote->getAllItems();

            foreach ($quoteItems as $quoteItem) {
                /** @var $quoteItem Mage_Sales_Model_Quote_Item */
                /** @var $orderItem Mage_Sales_Model_Order_Item */
                $orderItem = $order->getItemByQuoteItemId($quoteItem->getData('item_id'));
                $orderItem->setData('merchantLineNumber', $quoteItem->getData('merchantLineNumber'));
                $orderItem->save();
            }

            /** @var $api Mage_Sales_Model_Order_Api */
            $api         = Mage::getModel('sales/order_api');
            $comment     = $helper->__('Order successfully imported from %s', $this->_vendor->getName());
            $incrementId = $order->getIncrementId();
            $status      = 'vendorbridge_imported';
            $api->addComment($incrementId, $status, $comment);

            /** @var $session Mage_Core_Model_Session */
            $session = Mage::getSingleton('core/session');
            $session->unsetData('order_import');
            $session->unsetData('product_ids');
        } catch (Exception $e) {
            $message = $helper->__('Order import error: error occurred saving order - %s', $e->getMessage());
            $helper->log($message);
            Mage::logException($e);
        }
    }
}
