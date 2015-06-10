<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Helper_Data extends Mage_Core_Helper_Data
{
    /**#@+
     * Helper constants
     */
    const ENCRYPTION_SALT            = 'VendorBridge';
    const NOT_ENABLED_MESSAGE        = 'The Vendor Bridge Integration is not enabled';
    const VENDOR_BRIDGE_ENABLED_PATH = 'vendorbridge/general/enabled';
    const LOG_FILE_NAME              = 'vendorbridge.log';

    /**
     * @return bool if enabled or not
     */
    public function isEnabled()
    {
        return (bool)Mage::getStoreConfig(self::VENDOR_BRIDGE_ENABLED_PATH);
    }

    /**
     * Encrypt a string
     *
     * @param $clear
     * @return mixed
     */
    public function encrypt($clear)
    {
        $encrypted = trim(
            base64_encode(
                mcrypt_encrypt(
                    MCRYPT_RIJNDAEL_256,
                    self::ENCRYPTION_SALT,
                    $clear,
                    MCRYPT_MODE_ECB,
                    mcrypt_create_iv(
                        mcrypt_get_iv_size(
                            MCRYPT_RIJNDAEL_256,
                            MCRYPT_MODE_ECB
                        ),
                        MCRYPT_RAND
                    )
                )
            )
        );

        $search  = array('+', '/', '=');
        $replace = array('-', '_', '!');
        $urlSafe = str_replace($search, $replace, $encrypted);

        return $urlSafe;
    }

    /**
     * Decrypt a string
     *
     * @param $urlSafe
     * @return string
     */
    public function decrypt($urlSafe)
    {
        $replace   = array('+', '/', '=');
        $search    = array('-', '_', '!');
        $encrypted = str_replace($search, $replace, $urlSafe);

        $clear = trim(
            mcrypt_decrypt(
                MCRYPT_RIJNDAEL_256,
                self::ENCRYPTION_SALT,
                base64_decode($encrypted),
                MCRYPT_MODE_ECB,
                mcrypt_create_iv(
                    mcrypt_get_iv_size(
                        MCRYPT_RIJNDAEL_256,
                        MCRYPT_MODE_ECB
                    ),
                    MCRYPT_RAND
                )
            )
        );

        return $clear;
    }

    /**
     * @param $data
     */
    public function log($data)
    {
        Mage::log(print_r($data, true), null, self::LOG_FILE_NAME, true);
    }
}
