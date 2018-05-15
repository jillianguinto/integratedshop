<?php

class Magestore_Inventory_Model_Reportproductmoved extends Mage_Core_Model_Abstract
{
    protected $_eventPrefix = 'inventory_reportproductmoved';
    protected $_eventObject = 'inventory_reportproductmoved';
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('inventory/reportproductmoved');
    }
}