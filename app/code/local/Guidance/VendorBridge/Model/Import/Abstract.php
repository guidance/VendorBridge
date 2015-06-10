<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
abstract class Guidance_VendorBridge_Model_Import_Abstract
{
    /**#@+
     * Import constants
     */
    const ALREADY_RUNNING_MESSAGE = 'The %s process is already running. Exiting';
    const LOCAL_IMPORT_PATH       = 'var/vendorbridge/import';
    const LOCAL_ARCHIVE_PATH      = 'var/archive/vendorbridge/imports';
    const FILENAME_SEPARATOR      = '-';
    const PROCESS_NAME            = '';
    const EDI_TYPE_CODE           = '';
    const PROCESS_TYPE            = '';

    protected $_processList = array();
    protected $_fileData = array();
    protected $_microTimeString;
    protected $_processName;
    protected $_processLabel;

    /**
     * @var $_vendor Guidance_VendorBridge_Model_Import
     */
    protected $_importObject;

    /**
     * @var $_vendor Guidance_VendorBridge_Model_Vendor
     */
    protected $_vendor;

    abstract protected function _readImportFiles();

    /**
     * @throws Guidance_VendorBridge_Exception
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
     * @return string process name
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
     *
     */
    protected function process()
    {
        /** @var $vendors Guidance_VendorBridge_Model_Resource_Vendor_Collection */
        $vendors = Mage::getModel('vendorbridge/vendor')->getCollection()
            ->addFieldToFilter('status', array('eq' => Guidance_VendorBridge_Model_Source_Status::STATUS_ENABLED));

        foreach ($vendors as $vendor) {
            $this->_vendor = $vendor;
            try {
                $this->_instantiateImportObject();
                $this->_readImportFiles();
                $this->_archiveFiles();
                $this->_postImport();
            } catch (Guidance_VendorBridge_NofileException $e) {
                //intentionally left empty
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_failureAction($e->getMessage());
            }
        }

        $this->_stopRunning();
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
    protected function _instantiateImportObject()
    {
        if (!$this->_canImport($this->_vendor)) {
            throw new Guidance_VendorBridge_NofileException(
                Mage::helper('vendorbridge')->__('%s not allowed for %s', $this::PROCESS_NAME, $this->_vendor->getName())
            );
        }

        $this->_importObject = Mage::getModel('vendorbridge/import');
        $this->_importObject->setData('type', $this->_vendor->getName() . ' ' . $this->_getProcessLabel());
        $this->_importObject->setData('vendor', $this->_vendor->getEntityId());
        $this->_importObject->setData('start_time', date(Varien_Date::DATETIME_PHP_FORMAT, time()));
    }

    /**
     * @param Guidance_VendorBridge_Model_Vendor $vendor
     * @return bool
     */
    protected function _canImport(Guidance_VendorBridge_Model_Vendor $vendor)
    {
        $allowedTypes = $vendor->getAllowedTypes();
        if (!is_array($allowedTypes)) {
            $allowedTypes = explode(',', $allowedTypes);
        }

        return in_array($this::EDI_TYPE_CODE, $allowedTypes);
    }

    /**
     * @throws Guidance_VendorBridge_Exception
     */
    protected function _archiveFiles()
    {
        $archiveFolder = $this->_getArchiveFolder();

        foreach ($this->_processList as $file) {
            $source   = $file;
            $fileName = preg_replace('/' . $this->_vendor->getEntityId() . '-' . '/', $this->_vendor->getEntityId() . '-' . $this->_microTimeString() . '-', basename($file), 1);
            $dest     = $archiveFolder . DS . $fileName;
            $data     = file_get_contents($source);
            $handle   = fopen($dest, 'w');
            $written  = fwrite($handle, $data);
            fclose($handle);

            if ($data === false) {
                throw new Guidance_VendorBridge_Exception('Unable to open file ' . $source . ' for archiving.');
            } else if (false === $written || 0 == $written) {
                throw new Guidance_VendorBridge_Exception('Unable to write file ' . $source . ' to archive.');
            }

            chmod($dest, 0755);
            @unlink($file);
        }
    }

    /**
     * @return string
     * @throws DomainException
     */
    protected function _getArchiveFolder()
    {
        $basePath      = $this->_getBasePath();
        $archiveFolder = $basePath . DS . self::LOCAL_ARCHIVE_PATH . DS . date('Y') . DS . date('m');
        $pathParts     = explode('/', rtrim($archiveFolder, '/'));
        $path          = '';
        foreach ($pathParts as $name) {
            $path = ltrim($path . DS . $name, '\\');
            if (!file_exists($path)) {
                if (!mkdir($path, 0755, true)) {
                    throw new DomainException("Cannot create $path directory.");
                }
            }
        }

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
     */
    protected function _getLocalFolder()
    {
        $basePath    = $this->_getBasePath();
        $localFolder = $basePath . DS . self::LOCAL_IMPORT_PATH . DS;

        if (!is_dir($localFolder)) {
            mkdir($localFolder, 0777, true);
        }

        return $localFolder;
    }

    /**
     *
     */
    protected function _postImport()
    {
        $this->_saveResults();
    }

    /**
     *
     */
    protected function _stopRunning()
    {
        /** @var $lockHelper Guidance_VendorBridge_Helper_Lock */
        $lockHelper = Mage::helper('vendorbridge/lock');
        $lockHelper->stopRunning($this->_getProcessName());
    }

    /**
     * @param string $message
     */
    protected function _failureAction($message)
    {
        $this->_importObject->setData('stop_time', date(Varien_Date::DATETIME_PHP_FORMAT, time()));
        $this->_importObject->setData('status', Guidance_VendorBridge_Model_Source_Result::STATUS_FAILURE);
        $this->_importObject->save();

        $this->_sendFailureEmail($message);
    }

    /**
     *
     */
    protected function _saveResults()
    {
        $this->_importObject->setData('stop_time', date(Varien_Date::DATETIME_PHP_FORMAT, time()));
        $this->_importObject->setData('status', Guidance_VendorBridge_Model_Source_Result::STATUS_SUCCESS);
        $this->_importObject->save();
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
        $subject      = 'VendorBridge failure notification for ' . $this->_getProcessName() . ' Import for ' . $this->_vendor->getName();
        $sender       = array(
            'name'  => 'VendorBridge Import Notification Mailer',
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
     * @return Mage_Core_Model_Abstract
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
        /** @var $conn Magento_Db_Adapter_Pdo_Mysql */
        $conn = $resource->getConnection('write');

        return $conn;
    }

    /**
     * @return int
     */
    protected function _microTimeString()
    {
        if (isset($this->_microTimeString)) {
            return $this->_microTimeString;
        }
        list($msec, $sec) = explode(' ', microtime());
        $mTime                  = (float)$msec + (float)$sec;
        $this->_microTimeString = intval($mTime * 100000000);

        return $this->_microTimeString;
    }

    /**
     * @param $xml
     * @return array
     */
    protected function _simpleXmlToArray($xml)
    {
        $array = (array)$xml;
        foreach ($array as $key => $value) {
            if (strpos(@get_class($value), "SimpleXML") !== false) {
                $array[$key] = $this->_simpleXmlToArray($value);
            }
        }

        return $array;
    }

    /**
     * @param      $xml
     * @param bool $html_output
     * @return string
     */
    protected function _xmlpp($xml, $html_output = false)
    {
        if (is_string($xml)) {
            try {
                $xml_obj = new SimpleXMLElement($xml);
            } catch (Exception $e) {
                Mage::logException($e);
                return $xml;
            }
        } else {
            $xml_obj = $xml;
        }

        $level  = 4;
        $indent = 0; // current indentation level
        $pretty = array();

        // get an array containing each XML element
        $xml = explode("\n", preg_replace('/>\s*</', ">\n<", $xml_obj->asXML()));

        // shift off opening XML tag if present
        if (count($xml) && preg_match('/^<\?\s*xml/', $xml[0])) {
            $pretty[] = array_shift($xml);
        }

        foreach ($xml as $el) {
            if (preg_match('/^<([\w])+[^>\/]*>$/U', $el)) {
                // opening tag, increase indent
                $pretty[] = str_repeat(' ', $indent) . $el;
                $indent += $level;
            } else {
                if (preg_match('/^<\/.+>$/', $el)) {
                    $indent -= $level; // closing tag, decrease indent
                }
                if ($indent < 0) {
                    $indent += $level;
                }
                $pretty[] = str_repeat(' ', $indent) . $el;
            }
        }
        $xml = implode("\n", $pretty);
        return ($html_output) ? htmlentities($xml) : $xml;
    }
}
