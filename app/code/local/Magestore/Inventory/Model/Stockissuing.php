<?php

class Magestore_Inventory_Model_Stockissuing extends Mage_Core_Model_Abstract {
    
    const STOCK_TRANSFERRING = 1;
    const CUSTOMER_ORDER = 2;
    const ORDER_RETURNED_TO_SUPPLIER = 3;
    const CUSTOM = 4;
    protected $_eventPrefix = 'inventory_stockissuing';
    protected $_eventObject = 'inventory_stockissuing';

    public function _construct() {
        parent::_construct();
        $this->_init('inventory/stockissuing');
    }
}

?>
