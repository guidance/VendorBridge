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

$conn->dropTable($installer->getTable('vendorbridge/vendor'));
$table = $conn->newTable($installer->getTable('vendorbridge/vendor'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary'  => true,
), 'Entity ID')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(
    'nullable' => false,
), 'Vendor Name')
    ->addColumn('interchange_sender_id', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(
    'nullable' => false,
), 'Interchange Sender ID')
    ->addColumn('interchange_receiver_id', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(
    'nullable' => false,
), 'Interchange Receiver ID')
    ->addColumn('vendor_number', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(
    'nullable' => false,
), 'Vendor Number')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
    'unsigned' => true,
    'nullable' => true,
), 'Status')
    ->addColumn('threshold', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
    'unsigned' => true,
    'nullable' => false,
    'default'  => '0',
), 'Threshold')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
    'unsigned' => true,
    'nullable' => false,
), 'Store ID')
    ->addColumn('reseller_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => false,
), 'Reseller ID')
    ->addColumn('allowed_types', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(
    'nullable' => true,
), 'Allowed Types')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Creation Time')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Update Time')
    ->addIndex($installer->getIdxName('vendorbridge/import', array('status')),
    array('status'))
    ->addForeignKey(
    $installer->getFkName(
        array('vendorbridge/vendor'),
        'store_id',
        'core/store',
        'store_id'
    ),
    'store_id', $installer->getTable('core/store'), 'store_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_NO_ACTION)
    ->setComment('Vendor Bridge Vendors');
$conn->createTable($table);

$conn->dropTable($installer->getTable('vendorbridge/shipMethod'));
$table = $conn->newTable($installer->getTable('vendorbridge/shipMethod'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary'  => true,
), 'Entity ID')
    ->addColumn('vendor', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => false,
), 'Vendor')
    ->addColumn('external_ship_code', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(
    'nullable' => false,
), 'External Ship Code')
    ->addColumn('internal_ship_code', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(
    'nullable' => false,
), 'Internal Ship Code')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Creation Time')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Update Time')
    ->addForeignKey(
    $installer->getFkName(
        array('vendorbridge/shipMethod'),
        'vendor',
        'vendorbridge/vendor',
        'entity_id'
    ),
    'vendor', $installer->getTable('vendorbridge/vendor'), 'entity_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_NO_ACTION)
    ->setComment('Vendor Bridge Ship Methods');
$conn->createTable($table);

$conn->dropTable($installer->getTable('vendorbridge/import'));
$table = $conn->newTable($installer->getTable('vendorbridge/import'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary'  => true,
), 'Entity ID')
    ->addColumn('type', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(
    'nullable' => false,
), 'Import Type')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
    'unsigned' => true,
    'nullable' => true,
), 'Status')
    ->addColumn('vendor', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => false,
), 'Vendor')
    ->addColumn('start_time', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(), 'Start Time')
    ->addColumn('stop_time', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(), 'Stop Time')
    ->addIndex($installer->getIdxName('vendorbridge/import', array('type')),
    array('type'))
    ->addIndex($installer->getIdxName('vendorbridge/import', array('start_time')),
    array('start_time'))
    ->addIndex($installer->getIdxName('vendorbridge/import', array('stop_time')),
    array('stop_time'))
    ->addIndex($installer->getIdxName('vendorbridge/import', array('status')),
    array('status'))
    ->addForeignKey(
    $installer->getFkName(
        array('vendorbridge/import'),
        'vendor',
        'vendorbridge/vendor',
        'entity_id'
    ),
    'vendor', $installer->getTable('vendorbridge/vendor'), 'entity_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_NO_ACTION)
    ->setComment('Vendor Bridge Import Logs');
$conn->createTable($table);

$conn->dropTable($installer->getTable('vendorbridge/export'));
$table = $conn->newTable($installer->getTable('vendorbridge/export'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary'  => true,
), 'Entity ID')
    ->addColumn('type', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(
    'nullable' => false,
), 'Import Type')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
    'unsigned' => true,
    'nullable' => true,
), 'Status')
    ->addColumn('vendor', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => false,
), 'Vendor')
    ->addColumn('start_time', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(), 'Start Time')
    ->addColumn('stop_time', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(), 'Stop Time')
    ->addIndex($installer->getIdxName('vendorbridge/export', array('type')),
    array('type'))
    ->addIndex($installer->getIdxName('vendorbridge/export', array('start_time')),
    array('start_time'))
    ->addIndex($installer->getIdxName('vendorbridge/export', array('stop_time')),
    array('stop_time'))
    ->addIndex($installer->getIdxName('vendorbridge/export', array('status')),
    array('status'))
    ->addForeignKey(
    $installer->getFkName(
        array('vendorbridge/export'),
        'vendor',
        'vendorbridge/vendor',
        'entity_id'
    ),
    'vendor', $installer->getTable('vendorbridge/vendor'), 'entity_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_NO_ACTION)
    ->setComment('Vendor Bridge Export Logs');
$conn->createTable($table);

$conn->dropTable($installer->getTable('vendorbridge/inventory'));
$table = $conn->newTable($installer->getTable('vendorbridge/inventory'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary'  => true,
), 'Entity ID')
    ->addColumn('sku', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(
    'nullable' => false,
), 'Product SKU')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => false,
    'default'  => 0,
), 'Status')
    ->addColumn('vendor', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => false,
), 'Vendor')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(), 'Start Time')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(), 'Stop Time')
    ->addIndex(
    $installer->getIdxName(
        array('vendorbridge/inventory'),
        array('sku', 'vendor'),
        'unique'
    ),
    array('sku', 'vendor'),
    array('type' => 'unique')
)
    ->addIndex($installer->getIdxName('vendorbridge/import', array('created_at')),
    array('created_at'))
    ->addIndex($installer->getIdxName('vendorbridge/import', array('updated_at')),
    array('updated_at'))
    ->addIndex($installer->getIdxName('vendorbridge/import', array('status')),
    array('status'))
    ->addForeignKey(
    $installer->getFkName(
        array('vendorbridge/inventory'),
        'sku',
        'catalog/product',
        'sku'
    ),
    'sku', $installer->getTable('catalog/product'), 'sku',
    Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_NO_ACTION)
    ->addForeignKey(
    $installer->getFkName(
        array('vendorbridge/inventory'),
        'vendor',
        'vendorbridge/vendor',
        'entity_id'
    ),
    'vendor', $installer->getTable('vendorbridge/vendor'), 'entity_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_NO_ACTION)
    ->setComment('Vendor Bridge Inventory');
$conn->createTable($table);

//control tables
$conn->dropTable($conn->getTableName(Guidance_VendorBridge_Model_Export_Inventory::PROCESS_TYPE));
$table = $conn->newTable($conn->getTableName(Guidance_VendorBridge_Model_Export_Inventory::PROCESS_TYPE))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary'  => true,
), 'Control Number')
    ->addColumn('export_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => false,
), 'Export ID')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(), 'Created At')
    ->setComment(Guidance_VendorBridge_Model_Export_Inventory::PROCESS_NAME);
$conn->createTable($table);

$conn->dropTable($conn->getTableName(Guidance_VendorBridge_Model_Export_Shipments::PROCESS_TYPE));
$table = $conn->newTable($conn->getTableName(Guidance_VendorBridge_Model_Export_Shipments::PROCESS_TYPE))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary'  => true,
), 'Control Number')
    ->addColumn('export_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => false,
), 'Export ID')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(), 'Created At')
    ->setComment(Guidance_VendorBridge_Model_Export_Shipments::PROCESS_NAME);
$conn->createTable($table);

$conn->dropTable($conn->getTableName(Guidance_VendorBridge_Model_Export_Invoices::PROCESS_TYPE));
$table = $conn->newTable($conn->getTableName(Guidance_VendorBridge_Model_Export_Invoices::PROCESS_TYPE))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary'  => true,
), 'Control Number')
    ->addColumn('export_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => false,
), 'Export ID')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(), 'Created At')
    ->setComment(Guidance_VendorBridge_Model_Export_Invoices::PROCESS_NAME);
$conn->createTable($table);

$conn->dropTable($conn->getTableName(Guidance_VendorBridge_Model_Export_Cancels::PROCESS_TYPE));
$table = $conn->newTable($conn->getTableName(Guidance_VendorBridge_Model_Export_Cancels::PROCESS_TYPE))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary'  => true,
), 'Control Number')
    ->addColumn('export_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => false,
), 'Export ID')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(), 'Created At')
    ->setComment(Guidance_VendorBridge_Model_Export_Cancels::PROCESS_NAME);
$conn->createTable($table);

if ($conn->tableColumnExists($installer->getTable('sales/order'), 'reseller_order_id')) {
    //modify column in order table
    $conn->modifyColumn(
        $installer->getTable('sales/order'),
        'reseller_order_id',
        array(
            'type'     => Varien_Db_Ddl_Table::TYPE_BIGINT,
            'length'   => 20,
            'nullable' => true,
            'default'  => null,
            'comment'  => 'Reseller Order ID',
        )
    );
} else {
    //add column in order table
    $conn->addColumn(
        $installer->getTable('sales/order'),
        'reseller_order_id',
        array(
            'type'     => Varien_Db_Ddl_Table::TYPE_BIGINT,
            'length'   => 20,
            'nullable' => true,
            'default'  => null,
            'comment'  => 'Reseller ID',
        )
    );
}

if ($conn->tableColumnExists($installer->getTable('sales/order'), 'reseller')) {
    //modify column in order table
    $conn->modifyColumn(
        $installer->getTable('sales/order'),
        'reseller',
        array(
            'type'     => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'length'   => 10,
            'nullable' => false,
            'comment'  => 'Reseller',
        )
    );
} else {
    //add column in order table
    $conn->addColumn(
        $installer->getTable('sales/order'),
        'reseller',
        array(
            'type'     => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'length'   => 10,
            'nullable' => false,
            'comment'  => 'Reseller',
        )
    );
}

//create new order status
$statusTable = $installer->getTable('sales/order_status');
$stateTable  = $installer->getTable('sales/order_status_state');
$conn->insertOnDuplicate(
    $statusTable,
    array(
        'status' => 'vendorbridge_imported',
        'label'  => 'Imported from VendorBridge',
    )
);
$conn->insertOnDuplicate(
    $stateTable,
    array(
        'status'     => 'vendorbridge_imported',
        'state'      => 'pending',
        'is_default' => 0,
    )
);

//add column to sales order and sales quote tables for cancel after date
$conn->addColumn(
    $installer->getTable('sales/order'),
    'cancel_after',
    array(
        'type'     => Varien_Db_Ddl_Table::TYPE_DATE,
        'nullable' => true,
        'default'  => null,
        'comment'  => 'Cancel After',
    )
);
$conn->addColumn(
    $installer->getTable('sales/quote'),
    'cancel_after',
    array(
        'type'     => Varien_Db_Ddl_Table::TYPE_DATE,
        'nullable' => true,
        'default'  => null,
        'comment'  => 'Cancel After',
    )
);

//add column to sales order and sales quote tables for ref 3x value
$conn->addColumn(
    $installer->getTable('sales/order'),
    'ref_3_x',
    array(
        'type'     => Varien_Db_Ddl_Table::TYPE_TEXT, 32,
        'nullable' => true,
        'default'  => null,
        'comment'  => 'Ref 3X',
    )
);
$conn->addColumn(
    $installer->getTable('sales/quote'),
    'ref_3_x',
    array(
        'type'     => Varien_Db_Ddl_Table::TYPE_TEXT, 32,
        'nullable' => true,
        'default'  => null,
        'comment'  => 'Ref 3X',
    )
);

//ensure reseller exported flags are present
$conn->addColumn(
    $installer->getTable('sales/creditmemo'),
    'is_exported',
    array(
        'type'     => Varien_Db_Ddl_Table::TYPE_SMALLINT, null,
        'nullable' => false,
        'default'  => '0',
        'comment'  => 'Is Exported',
    )
);
$conn->addColumn(
    $installer->getTable('sales/shipment'),
    'is_invoice_exported',
    array(
        'type'     => Varien_Db_Ddl_Table::TYPE_SMALLINT, null,
        'nullable' => false,
        'default'  => '0',
        'comment'  => 'Is Invoice Exported',
    )
);
$conn->addColumn(
    $installer->getTable('sales/shipment'),
    'reseller_exported',
    array(
        'type'     => Varien_Db_Ddl_Table::TYPE_SMALLINT, null,
        'nullable' => false,
        'default'  => '0',
        'comment'  => 'Is Exported',
    )
);
$conn->addColumn(
    $installer->getTable('sales/quote_item'),
    'merchantLineNumber',
    array(
        'type'     => Varien_Db_Ddl_Table::TYPE_TEXT, 32,
        'nullable' => true,
        'default'  => null,
        'comment'  => 'Merchant Line Number',
    )
);
$conn->addColumn(
    $installer->getTable('sales/order_item'),
    'merchantLineNumber',
    array(
        'type'     => Varien_Db_Ddl_Table::TYPE_TEXT, 32,
        'nullable' => true,
        'default'  => null,
        'comment'  => 'Merchant Line Number',
    )
);

$installer->endSetup();
