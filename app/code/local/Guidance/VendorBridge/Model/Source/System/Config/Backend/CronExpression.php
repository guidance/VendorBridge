<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_Source_System_Config_Backend_CronExpression extends Mage_Core_Model_Config_Data
{
    const INVALID_CRON_ERROR = 'The cron expression you entered is not valid';

    /**
     * @return Mage_Core_Model_Abstract
     * @throws Guidance_VendorBridge_Exception
     */
    public function save()
    {
        $cronExpr = $this->getValue();

        if (!$this->_validate($cronExpr)) {
            $error = self::INVALID_CRON_ERROR;
            throw new Guidance_VendorBridge_Exception($error);
        }

        return parent::save();
    }

    /**
     * @param string $cron
     * @return bool whether valid or not
     */
    protected function _validate($cron)
    {
        $regexp = $this->_buildRegexp();
        $parts  = explode(' ', $cron);
        foreach ($parts as $part) {
            if (!preg_match($regexp, $part)) {
                return false;
            }
        }

        return (count($parts) == 5);
    }

    /**
     * @return string reg expression
     */
    protected function _buildRegexp()
    {
        return '/^(?:[1-9]?\d|\*)(?:(?:[\/-][1-9]?\d)|(?:,[1-9]?\d)+)?$/';
    }
}
