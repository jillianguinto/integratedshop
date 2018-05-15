<?php

class Magestore_Inventory_Model_Mysql4_Notice_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected $_isGroupSql = false;
    
    public function _construct()
    {
        parent::_construct();
        $this->_init('inventory/notice');
    }
    
    
}