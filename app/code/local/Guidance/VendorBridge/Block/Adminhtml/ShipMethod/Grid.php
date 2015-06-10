<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Block_Adminhtml_ShipMethod_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->setId('shipMethodGrid');
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
        /** @var $collection Guidance_VendorBridge_Model_Resource_ShipMethod_Collection */
        $collection = Mage::getModel('vendorbridge/shipMethod')->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header' => Mage::helper('vendorbridge')->__('Method ID'),
            'align'  => 'left',
            'width'  => '30px',
            'index'  => 'entity_id',
        ));

        /** @var $vendor Guidance_VendorBridge_Model_Source_Vendor */
        $vendor = Mage::getModel('vendorbridge/source_vendor');
        $this->addColumn('vendor', array(
            'header'  => Mage::helper('vendorbridge')->__('Vendor'),
            'align'   => 'left',
            'width'   => '150px',
            'index'   => 'vendor',
            'type'    => 'options',
            'options' => $vendor->toArray(),
        ));

        $this->addColumn('external_ship_code', array(
            'header' => Mage::helper('vendorbridge')->__('Ext. Ship Code'),
            'align'  => 'left',
            'width'  => '50px',
            'index'  => 'external_ship_code',
        ));

        $this->addColumn('internal_ship_code', array(
            'header' => Mage::helper('vendorbridge')->__('Int. Ship Code'),
            'align'  => 'left',
            'width'  => '50px',
            'index'  => 'internal_ship_code',
        ));

        $this->addColumn('created_at', array(
            'header' => Mage::helper('vendorbridge')->__('Created At'),
            'type'   => 'datetime',
            'width'  => '50px',
            'index'  => 'created_at',
        ));

        $this->addColumn('updated_at', array(
            'header' => Mage::helper('vendorbridge')->__('Updated At'),
            'width'  => '50px',
            'index'  => 'updated_at',
            'type'   => 'datetime',
        ));

        $this->addExportType('*/*/shipMethodCsv', Mage::helper('vendorbridge')->__('CSV'));
        $this->addExportType('*/*/shipMethodXml', Mage::helper('vendorbridge')->__('XML'));

        return parent::_prepareColumns();
    }

    /**
     * @return Guidance_VendorBridge_Block_Adminhtml_ShipMethod_Grid|Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setData('form_field_name', 'shipMethod');

        $this->getMassactionBlock()->addItem('delete', array(
            'label'   => Mage::helper('vendorbridge')->__('Delete ship method(s)'),
            'url'     => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('vendorbridge')->__('Really delete the selected ship method(s)?')
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
