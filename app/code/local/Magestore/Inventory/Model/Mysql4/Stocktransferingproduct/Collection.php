<?php

class Magestore_Inventory_Model_Mysql4_Stocktransferingproduct_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
    public function _construct() {
        parent::_construct();
        $this->_init('inventory/stocktransferingproduct');
    }
}

?>