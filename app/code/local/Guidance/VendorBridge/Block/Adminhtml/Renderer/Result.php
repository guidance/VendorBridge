<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Block_Adminhtml_Renderer_Result extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $value = $row->getData('status');
        /** @var $source Guidance_VendorBridge_Model_Source_Result */
        $source   = Mage::getModel('vendorbridge/source_result');
        $statuses = $source->toArray();
        $status   = isset($statuses[$value]) ? $statuses[$value] : '';
        $class    = strtolower($status);

        return '<span class="' . $class . '">' . $status . '</span>';
    }
}
