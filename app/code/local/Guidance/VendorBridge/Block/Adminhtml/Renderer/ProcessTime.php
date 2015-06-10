<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Block_Adminhtml_Renderer_ProcessTime extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $stopTime = $row->getData('stop_time');
        if (empty($stopTime)) {
            return '--';
        }

        $startTime = $row->getData('start_time');
        $diff      = strtotime($stopTime) - strtotime($startTime);

        if ($diff <= 1) {
            return Mage::helper('vendorbridge')->__('1 Second');
        }

        $seconds = $diff;
        if ($seconds > 59) {
            $minutes = floor($seconds / 60);
            $seconds = $seconds % 60;
        } else {
            return Mage::helper('vendorbridge')->__('%s Seconds', $seconds);
        }

        return Mage::helper('vendorbridge')->__('%s Minutes %s Seconds', $minutes, $seconds);
    }
}
