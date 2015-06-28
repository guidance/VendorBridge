<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
/* @var $installer Guidance_VendorBridge_Model_Resource_Setup */
/* @var $conn Magento_Db_Adapter_Pdo_Mysql */
$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();

//add column in vendor table
$conn->addColumn(
    $installer->getTable('vendorbridge/vendor'),
    'qualifier',
    array(
        'type'    => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'  => 64,
        'comment' => 'Interchange ID Qualifier',
    )
);

$installer->endSetup();
