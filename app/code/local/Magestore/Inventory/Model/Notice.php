<?php

class Magestore_Inventory_Model_Notice extends Mage_Core_Model_Abstract
{
    protected $_eventPrefix = 'inventory_notice';
    protected $_eventObject = 'inventory_notice';
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('inventory/notice');
    }
}