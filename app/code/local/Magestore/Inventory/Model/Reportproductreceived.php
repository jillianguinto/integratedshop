<?php

class Magestore_Inventory_Model_Reportproductreceived extends Mage_Core_Model_Abstract
{
    protected $_eventPrefix = 'inventory_reportproductreceived';
    protected $_eventObject = 'inventory_reportproductreceived';
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('inventory/reportproductreceived');
    }
}