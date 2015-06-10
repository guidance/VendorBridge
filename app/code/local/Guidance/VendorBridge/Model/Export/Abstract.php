<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
abstract class Guidance_VendorBridge_Model_Export_Abstract
{
    /**#@+
     * Export constants
     */
    const ALREADY_RUNNING_MESSAGE = 'The %s process is already running. Exiting';
    const LOCAL_EXPORT_PATH       = 'var/vendorbridge/export';
    const LOCAL_ARCHIVE_PATH      = 'var/archive/vendorbridge/exports';
    const FILENAME_SEPARATOR      = '-';
    const ID_CODE                 = '';
    const PROCESS_NAME            = '';
    const PROCESS_TYPE            = '';
    const FILENAME_PREFIX         = '';
    const FILE_EXTENSION          = '';

    protected $_processList = array();
    protected $_fileData = array();
    protected $_errors = array();
    protected $_processName;
    protected $_processLabel;

    /**
     * @var $_exportObject Guidance_VendorBridge_Model_Export
     */
    protected $_exportObject;

    /**
     * @var $_vendor Guidance_VendorBridge_Model_Vendor
     */
    protected $_vendor;

    /**
     *
     */
    public function __construct()
    {
        $this->_processName  = $this::PROCESS_TYPE;
        $this->_processLabel = $this::PROCESS_NAME;
        if ($this->_isRunning()) {
            throw new Guidance_VendorBridge_Exception(
                Mage::helper('vendorbridge')->__(self::ALREADY_RUNNING_MESSAGE, $this->_getProcessName())
            );
        }
        $this->_startRunning();
    }

    /**
     * @return string
     */
    protected function _getProcessName()
    {
        return $this->_processName;
    }

    /**
     * @return string process label
     */
    protected function _getProcessLabel()
    {
        return $this->_processLabel;
    }

    /**
     * @return string
     */
    protected function _getFileName()
    {
        return $this::FILENAME_PREFIX . $this::FILENAME_SEPARATOR . $this->_microTimeString() . $this::FILE_EXTENSION;
    }

    /**
     *
     */
    protected function process()
    {
        /** @var $vendors Guidance_VendorBridge_Model_Resource_Vendor_Collection */
        $vendors = Mage::getModel('vendorbridge/vendor')->getCollection()
            ->addFieldToFilter('status', array('eq' => Guidance_VendorBridge_Model_Source_Status::STATUS_ENABLED));
        if ($vendors->getSize() < 1) {
            throw new Guidance_VendorBridge_Exception(
                Mage::helper('vendorbridge')->__('There are no active vendors configured')
            );
        }
        /** @var $session Mage_Adminhtml_Model_Session */
        $session = Mage::getSingleton('adminhtml/session');

        foreach ($vendors as $vendor) {
            $this->_vendor = $vendor;
            try {
                $this->_instantiateExportObject();
                $this->_writeExportFile();
                $this->_archiveFiles();
                $this->_postExport();
            } catch (Guidance_VendorBridge_NofileException $e) {
                if ($this->_exportObject && $this->_exportObject->getId()) {
                    $this->_exportObject->delete();
                }
                $session->addNotice($e->getMessage());
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_failureAction($e->getMessage());
                $this->_errors[] = $e->getMessage();
            }
        }

        $this->_stopRunning();
        if (!empty($this->_errors)) {
            throw new Guidance_VendorBridge_Exception(join(', ', $this->_errors));
        }
    }

    /**
     * @return bool is running
     */
    protected function _isRunning()
    {
        /** @var $lockHelper Guidance_VendorBridge_Helper_Lock */
        $lockHelper = Mage::helper('vendorbridge/lock');

        return $lockHelper->isRunning($this->_getProcessName());
    }

    /**
     *
     */
    protected function _startRunning()
    {
        /** @var $lockHelper Guidance_VendorBridge_Helper_Lock */
        $lockHelper = Mage::helper('vendorbridge/lock');
        $lockHelper->startRunning($this->_getProcessName());
    }

    /**
     *
     */
    protected function _instantiateExportObject()
    {
        if (!$this->_canExport($this->_vendor)) {
            throw new Guidance_VendorBridge_NofileException(
                Mage::helper('vendorbridge')->__('%s not allowed for %s', $this::PROCESS_NAME, $this->_vendor->getName())
            );
        }

        /** @var $_exportObject Guidance_VendorBridge_Model_Export */
        $this->_exportObject = Mage::getModel('vendorbridge/export');
        $this->_exportObject->setData('type', $this->_vendor->getName() . ' ' . $this->_getProcessLabel());
        $this->_exportObject->setData('vendor', $this->_vendor->getEntityId());
        $this->_exportObject->setData('start_time', date(Varien_Date::DATETIME_PHP_FORMAT, time()));
        $this->_exportObject->save();
    }

    protected function _writeExportFile()
    {
        $localFolder = $this->_getLocalFolder();
        $fileName    = $this->_getFileName();
        $localFile   = $localFolder . $fileName;

        $exportHandle = fopen($localFile, 'w');
        if (!$exportHandle) {
            $message = Mage::helper('vendorbridge')->__(
                'Unable to create file for export.'
            );
            throw new Guidance_VendorBridge_Exception($message);
        }

        $fileData = $this->_fileData;
        foreach ($fileData as $line) {
            fwrite($exportHandle, $line . PHP_EOL);
        }
        fclose($exportHandle);
        chmod($localFile, 0777);

        $this->_processList[] = $localFile;
    }

    protected function _archiveFiles()
    {
        $archiveFolder = $this->_getArchiveFolder();
        $archiveFolder .= date('Y') . DS . date('m');
        $pathParts = explode('/', rtrim($archiveFolder, '/'));
        $path      = '';
        foreach ($pathParts as $name) {
            $path = ltrim($path . DS . $name, '\\');
            if (!file_exists($path)) {
                if (!mkdir($path, 0755, true)) {
                    throw new DomainException("Cannot create $path directory.");
                }
            }
        }

        foreach ($this->_processList as $file) {
            $source   = $file;
            $fileName = basename($file);
            $dest     = $archiveFolder . DS . $fileName;
            $data     = file_get_contents($source);
            $handle   = fopen($dest, 'w');
            $written  = fwrite($handle, $data);
            fclose($handle);
            chmod($file, 0755);

            if ($data === false) {
                throw new Guidance_VendorBridge_Exception('Unable to open file ' . $source . ' for archiving.');
            } else if (false === $written || 0 == $written) {
                throw new Guidance_VendorBridge_Exception('Unable to write file ' . $source . ' to archive.');
            }

            chmod($dest, 0755);
        }
    }

    /**
     * @return string
     */
    protected function _getArchiveFolder()
    {
        $basePath      = $this->_getBasePath();
        $archiveFolder = $basePath . DS . self::LOCAL_ARCHIVE_PATH . DS;

        return $archiveFolder;
    }

    /**
     * @return string
     */
    protected function _getBasePath()
    {
        return Mage::getBaseDir();
    }

    /**
     * @return string
     * @throws DomainException
     */
    protected function _getLocalFolder()
    {
        $basePath    = $this->_getBasePath();
        $localFolder = $basePath . DS . self::LOCAL_EXPORT_PATH . DS;
        $pathParts   = explode('/', rtrim($localFolder, '/'));
        $path        = '';
        foreach ($pathParts as $name) {
            $path = ltrim($path . DS . $name, '\\');
            if (!file_exists($path)) {
                if (!mkdir($path, 0755, true)) {
                    throw new DomainException("Cannot create $path directory.");
                }
            }
        }

        return $localFolder;
    }

    /**
     *
     */
    protected function _postExport()
    {
        $this->_saveResults();
    }

    /**
     * @return mixed
     */
    protected function _stopRunning()
    {
        /** @var $lockHelper Guidance_VendorBridge_Helper_Lock */
        $lockHelper = Mage::helper('vendorbridge/lock');

        $lockHelper->stopRunning($this->_getProcessName());
    }

    /**
     * @param $message
     */
    protected function _failureAction($message)
    {
        $this->_exportObject->setData('stop_time', date(Varien_Date::DATETIME_PHP_FORMAT, time()));
        $this->_exportObject->setData('status', Guidance_VendorBridge_Model_Source_Result::STATUS_FAILURE);
        $this->_exportObject->save();

        $this->_sendFailureEmail($message);
    }

    protected function _saveResults()
    {
        $this->_exportObject->setData('stop_time', date(Varien_Date::DATETIME_PHP_FORMAT, time()));
        $this->_exportObject->setData('status', Guidance_VendorBridge_Model_Source_Result::STATUS_SUCCESS);
        $this->_exportObject->save();
    }

    /**
     * @param $message
     */
    protected function _sendFailureEmail($message)
    {
        $emails = Mage::getStoreConfig('vendorbridge/email/recipients');
        if (empty($emails)) {
            return;
        }
        $emails = explode(',', $emails);
        if (empty($emails)) {
            return;
        }
        $store   = Mage::getModel('core/store')->load($this->_vendor->getStoreId());
        $storeId = $store->getId();
        $sender  = Mage::getStoreConfig('vendorbridge/email/identity');
        if (empty($sender)) {
            return;
        }
        /** @var $translate Mage_Core_Model_Translate */
        $translate = Mage::getSingleton('core/translate');
        /** @var $mailTemplate Mage_Core_Model_Email_Template */
        $mailTemplate = Mage::getModel('core/email_template');
        $senderEmail  = Mage::getStoreConfig('trans_email/ident_' . $sender . '/email');
        $templateId   = 'vendorbridge_email_notification_template';
        $subject      = 'VendorBridge failure notification for ' . $this->_getProcessName() . ' Export for ' . $this->_vendor->getName();
        $sender       = array(
            'name'  => 'VendorBridge Export Notification Mailer',
            'email' => $senderEmail,
        );
        $vars         = array('message' => $message);

        try {
            $mailTemplate->setTemplateSubject($subject)
                ->sendTransactional($templateId, $sender, $senderEmail, $subject, $vars, $storeId);
            $translate->setTranslateInline(false);
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * @return Mage_Core_Model_Resource
     */
    protected function _getResource()
    {
        /** @var $resource Mage_Core_Model_Resource */
        $resource = Mage::getSingleton('core/resource');

        return $resource;
    }

    /**
     * @return Magento_Db_Adapter_Pdo_Mysql
     */
    protected function _getWriteAdapter()
    {
        /** @var $resource Mage_Core_Model_Resource */
        $resource = $this->_getResource();
        /** @var $adapter Magento_Db_Adapter_Pdo_Mysql */
        $adapter = $resource->getConnection('write');

        return $adapter;
    }

    /**
     * @return int
     */
    protected function _microTimeString()
    {
        list($msec, $sec) = explode(' ', microtime());
        $mTime = (float)$msec + (float)$sec;

        return intval($mTime * 100000000);
    }

    /**
     * @param Guidance_VendorBridge_Model_Vendor $vendor
     * @return bool
     */
    protected function _canExport(Guidance_VendorBridge_Model_Vendor $vendor)
    {
        $allowedTypes = $vendor->getAllowedTypes();
        if (!is_array($allowedTypes)) {
            $allowedTypes = explode(',', $allowedTypes);
        }

        return in_array($this::ID_CODE, $allowedTypes);
    }
}
