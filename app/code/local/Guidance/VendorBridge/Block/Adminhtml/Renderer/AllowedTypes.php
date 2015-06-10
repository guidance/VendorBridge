<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Block_Adminhtml_Renderer_AllowedTypes extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $value = $row->getData('allowed_types');

        if (empty($value)) {
            return '';
        }
        if (!is_array($value)) {
            $value = explode(',', $value);
        }
        $newArray = array();
        /** @var $allowedTypes Guidance_VendorBridge_Model_Source_AllowedTypes */
        $allowedTypes = Mage::getModel('vendorbridge/source_allowedTypes');
        $options      = $allowedTypes->toArray();
        foreach ($value as $type) {
            $newArray[] = $options[$type];
        }

        return join(', ', $newArray);
    }
}
