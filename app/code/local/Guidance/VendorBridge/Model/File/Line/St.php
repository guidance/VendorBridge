<?php
/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
class Guidance_VendorBridge_Model_File_Line_St extends Guidance_VendorBridge_Model_File_Line_Abstract
{
    /**#@+
     * Line constants
     */
    const ROW_TYPE = 'ST';

    private $_idCode;
    private $_tableName;

    /**
     * @param array $args
     */
    public function __construct(array $args)
    {
        $this->_idCode        = $args['id_code'];
        $this->_tableName     = $args['table_name'];
        $this->_interchangeId = $args['interchange_id'];
    }

    /**
     * @return string
     */
    public function getTransactionSetHeader()
    {
        $header = $this->_mapTransactionSetHeader();

        return join(self::ELEMENT_SEPARATOR, $header) . self::SEGMENT_TERMINATOR;
    }

    /**
     * @return array
     */
    protected function _mapTransactionSetHeader()
    {
        return array(
            'ST00' => self::ROW_TYPE,
            'ST01' => $this->_idCode,
            'ST02' => $this->_getControlNumber(),
        );
    }

    /**
     * @return string
     */
    protected function _getControlNumber()
    {
        /** @var $adapter Magento_Db_Adapter_Pdo_Mysql */
        $adapter = $this->_getWriteAdapter();
        $bind    = array(
            'export_id'  => (int)$this->_interchangeId,
            'created_at' => (string)now(),
        );
        $adapter->insert($this->_tableName, $bind);

        return $this->_getLastControlNumber($this->_tableName);
    }
}
