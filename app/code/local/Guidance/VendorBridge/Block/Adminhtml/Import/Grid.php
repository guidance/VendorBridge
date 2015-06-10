<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Block_Adminhtml_Import_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->setId('importGrid');
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
        /** @var $collection Guidance_VendorBridge_Model_Resource_Import_Collection */
        $collection = Mage::getModel('vendorbridge/import')->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header' => Mage::helper('vendorbridge')->__('Transaction ID'),
            'align'  => 'left',
            'width'  => '30px',
            'index'  => 'entity_id',
        ));

        $this->addColumn('type', array(
            'header' => Mage::helper('vendorbridge')->__('Import Type'),
            'align'  => 'left',
            'width'  => '50px',
            'index'  => 'type',
        ));

        /** @var $status Guidance_VendorBridge_Model_Source_Result */
        $status = Mage::getModel('vendorbridge/source_result');
        $this->addColumn('status', array(
            'header'   => Mage::helper('vendorbridge')->__('Status'),
            'align'    => 'left',
            'width'    => '50px',
            'index'    => 'status',
            'type'     => 'options',
            'options'  => $status->toArray(),
            'renderer' => 'Guidance_VendorBridge_Block_Adminhtml_Renderer_Result',
        ));

        $this->addColumn('start_time', array(
            'header' => Mage::helper('vendorbridge')->__('Start Time'),
            'width'  => '50px',
            'index'  => 'start_time',
            'type'   => 'datetime',
        ));

        $this->addColumn('process_time', array(
            'header'   => Mage::helper('vendorbridge')->__('Process Time'),
            'align'    => 'left',
            'width'    => '50px',
            'index'    => 'stop_time',
            'filter'   => false,
            'sortable' => false,
            'renderer' => 'Guidance_VendorBridge_Block_Adminhtml_Renderer_ProcessTime',
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('vendorbridge')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('vendorbridge')->__('XML'));

        return parent::_prepareColumns();
    }

    /**
     * @return Guidance_VendorBridge_Block_Adminhtml_Import_Grid|Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setData('form_field_name', 'import');

        $this->getMassactionBlock()->addItem('delete', array(
            'label'   => Mage::helper('vendorbridge')->__('Delete import record(s)'),
            'url'     => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('vendorbridge')->__('Really delete the selected import record(s)?')
        ));

        return $this;
    }

    /**
     * @param Varien_Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return 'javascript:void(0)';
    }
}
