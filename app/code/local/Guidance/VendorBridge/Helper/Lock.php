<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Helper_Lock extends Mage_Core_Helper_Data
{
    /**#@+
     * Lock file constants
     */
    const LOCK_DIR       = 'var/locks/vendorbridge';
    const LOCK_EXTENSION = '.lock';
    const LOCK_DURATION  = 1800;

    /**
     * @param $processName
     * @return bool is process running
     */
    public function isRunning($processName)
    {
        $lockFile = $this->_getLockFile($processName);
        if (file_exists($lockFile)) {
            $lockTime = file_get_contents($lockFile);

            return $lockTime + self::LOCK_DURATION > time();
        }

        return false;
    }

    /**
     * @param $processName
     * @throws Guidance_VendorBridge_Exception
     */
    public function startRunning($processName)
    {
        $timestamp = time();
        $lockFile  = $this->_getLockFile($processName);
        if (file_exists($lockFile)) {
            @unlink($lockFile);
        }
        $handle  = fopen($lockFile, 'w');
        $written = fwrite($handle, $timestamp);
        fclose($handle);

        if (false === $written || 0 == $written) {
            throw new Guidance_VendorBridge_Exception('Unable to write lock file ' . $lockFile . ' for process ' . $processName . '.');
        }

        chmod($lockFile, 0755);
    }

    /**
     * @param $processName
     */
    public function stopRunning($processName)
    {
        @unlink($this->_getLockFile($processName));
    }

    /**
     * @param $processName
     * @return string lock file name
     */
    protected function _getLockFile($processName)
    {
        return $this->_getLockFolder() . DS . $processName . self::LOCK_EXTENSION;
    }

    /**
     * @return string lock folder
     */
    protected function _getLockFolder()
    {
        $basePath   = Mage::getBaseDir();
        $lockFolder = $basePath . DS . self::LOCK_DIR;

        if (!is_dir($lockFolder)) {
            mkdir($lockFolder, 0755, true);
        }

        return $lockFolder;
    }
}
