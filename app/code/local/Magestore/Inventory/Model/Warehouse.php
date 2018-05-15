<?php

class Magestore_Inventory_Model_Warehouse extends Mage_Core_Model_Abstract {
    
    protected $_eventPrefix = 'inventory_warehouse';
    protected $_eventObject = 'inventory_warehouse';
    
    public function _construct() {
        parent::_construct();
        $this->_init('inventory/warehouse');
    }

}

?>
