<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Block_Adminhtml_Vendor_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->setId('vendorGrid');
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
        /** @var $collection Guidance_VendorBridge_Model_Resource_Vendor_Collection */
        $collection = Mage::getModel('vendorbridge/vendor')->getCollection();
        /** @var $select Varien_Db_Select */
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        /** @var $helper Guidance_VendorBridge_Helper_Data */
        $helper = Mage::helper('vendorbridge');
        $this->addColumn('entity_id', array(
            'header' => $helper->__('Vendor ID'),
            'align'  => 'left',
            'width'  => '30px',
            'index'  => 'entity_id',
        ));

        $this->addColumn('name', array(
            'header' => $helper->__('Vendor Name'),
            'align'  => 'left',
            'width'  => '150px',
            'index'  => 'name',
        ));

        $this->addColumn('interchange_sender_id', array(
            'header' => $helper->__('Sender ID'),
            'align'  => 'left',
            'width'  => '50px',
            'index'  => 'interchange_sender_id',
        ));

        $this->addColumn('interchange_receiver_id', array(
            'header' => $helper->__('Receiver ID'),
            'align'  => 'left',
            'width'  => '50px',
            'index'  => 'interchange_receiver_id',
        ));

        $this->addColumn('vendor_number', array(
            'header' => $helper->__('Vendor Number'),
            'align'  => 'left',
            'width'  => '50px',
            'index'  => 'vendor_number',
        ));

        /** @var $status Guidance_VendorBridge_Model_Source_Status */
        $status = Mage::getModel('vendorbridge/source_status');
        $this->addColumn('status', array(
            'header'  => $helper->__('Status'),
            'width'   => '50px',
            'index'   => 'status',
            'type'    => 'options',
            'options' => $status->toArray(),
        ));

        /** @var $status Guidance_VendorBridge_Model_Source_Stores */
        $stores = Mage::getModel('vendorbridge/source_stores');
        $this->addColumn('store_id', array(
            'header'  => $helper->__('Store'),
            'width'   => '50px',
            'index'   => 'store_id',
            'type'    => 'options',
            'options' => $stores->toArray(),
        ));

        $this->addColumn('reseller_id', array(
            'header' => $helper->__('Reseller ID'),
            'align'  => 'left',
            'width'  => '50px',
            'index'  => 'reseller_id',
        ));

        $this->addColumn('threshold', array(
            'header' => $helper->__('Inv. Threshold'),
            'align'  => 'left',
            'width'  => '50px',
            'index'  => 'threshold',
        ));

        $this->addColumn('allowed_types', array(
            'header'   => $helper->__('Allowed Types'),
            'align'    => 'left',
            'width'    => '50px',
            'index'    => 'allowed_types',
            'renderer' => 'Guidance_VendorBridge_Block_Adminhtml_Renderer_AllowedTypes',
        ));

        $this->addExportType('*/*/vendorCsv', $helper->__('CSV'));
        $this->addExportType('*/*/vendorXml', $helper->__('XML'));

        return parent::_prepareColumns();
    }

    /**
     * @return Guidance_VendorBridge_Block_Adminhtml_Vendor_Grid|Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setData('form_field_name', 'vendor');

        /** @var $helper Guidance_VendorBridge_Helper_Data */
        $helper = Mage::helper('vendorbridge');

        $this->getMassactionBlock()->addItem('delete', array(
            'label'   => $helper->__('Delete vendor(s)'),
            'url'     => $this->getUrl('*/*/massDelete'),
            'confirm' => $helper->__('Really delete the selected vendor(s)?')
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
