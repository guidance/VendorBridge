<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Block_Adminhtml_Inventory_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->setId('inventoryGrid');
        $this->_defaultLimit = 200;
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('desc');
        $this->setData('no_filter_massaction_column', true);
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        /** @var $collection Guidance_VendorBridge_Model_Resource_Inventory_Collection */
        $collection = Mage::getModel('vendorbridge/inventory')->getCollection();
        /** @var $select Varien_Db_Select */
        $select = $collection->getSelect();
        $select->joinInner(
            array('product' => $collection->getTable('catalog/product')),
            '(product.sku = main_table.sku)',
            array('product_id' => 'entity_id')
        );
        $this->_joinAttributeToSelect($select, 'name');
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @param Varien_Db_Select$select
     * @param                 $attrCode
     * @return Varien_Db_Select
     */
    protected function _joinAttributeToSelect(Varien_Db_Select $select, $attrCode)
    {
        /** @var $eav Mage_Eav_Model_Config */
        $eav       = Mage::getSingleton('eav/config');
        $attribute = $eav->getAttribute(Mage_Catalog_Model_Product::ENTITY, $attrCode);
        $attrId    = $attribute->getAttributeId();

        $select->joinLeft(
            array($attrCode => $attribute->getBackendTable()),
            '(' . $attrCode . '.entity_id = product.entity_id) AND (' . $attrId . ' = ' . $attrCode . '.attribute_id) AND (' . $attrCode . '.store_id = 0) ',
            array($attrCode => $attrCode . '.value')
        );

        return $select;
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('product_id', array(
            'header'       => Mage::helper('vendorbridge')->__('Product ID'),
            'align'        => 'left',
            'width'        => '30px',
            'index'        => 'product_id',
            'filter_index' => 'product.entity_id',
        ));

        $this->addColumn('product_name', array(
            'header'       => Mage::helper('vendorbridge')->__('Product Name'),
            'align'        => 'left',
            'width'        => '150px',
            'index'        => 'name',
            'filter_index' => 'name.value',
        ));

        $this->addColumn('sku', array(
            'header'       => Mage::helper('vendorbridge')->__('SKU'),
            'align'        => 'left',
            'width'        => '50px',
            'index'        => 'sku',
            'filter_index' => 'product.sku',
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('vendorbridge')->__('Stock Qty'),
            'align'  => 'left',
            'width'  => '50px',
            'index'  => 'status',
        ));

        /** @var $vendor Guidance_VendorBridge_Model_Source_Vendor */
        $vendor = Mage::getModel('vendorbridge/source_vendor');
        $this->addColumn('vendor', array(
            'header'  => Mage::helper('vendorbridge')->__('Vendor'),
            'align'   => 'left',
            'width'   => '100px',
            'index'   => 'vendor',
            'type'    => 'options',
            'options' => $vendor->toArray(),
        ));

        $this->addColumn('created_at', array(
            'header'       => Mage::helper('vendorbridge')->__('First Sent'),
            'type'         => 'datetime',
            'width'        => '50px',
            'index'        => 'created_at',
            'filter_index' => 'main_table.created_at',
        ));

        $this->addColumn('updated_at', array(
            'header'       => Mage::helper('vendorbridge')->__('Last Sent'),
            'width'        => '50px',
            'index'        => 'updated_at',
            'type'         => 'datetime',
            'filter_index' => 'main_table.updated_at',
        ));

        $this->addExportType('*/*/inventoryCsv', Mage::helper('vendorbridge')->__('CSV'));
        $this->addExportType('*/*/inventoryXml', Mage::helper('vendorbridge')->__('XML'));

        return parent::_prepareColumns();
    }

    /**
     * @return Guidance_VendorBridge_Block_Adminhtml_Inventory_Grid|Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setData('form_field_name', 'inventory');

        $this->getMassactionBlock()->addItem('delete', array(
            'label'   => Mage::helper('vendorbridge')->__('Delete inventory item(s)'),
            'url'     => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('vendorbridge')->__('Really delete the selected inventory item(s)?')
        ));

        return $this;
    }

    /**
     * @param Varien_Object $row
     * @return string row url
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}
