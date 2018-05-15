<?php

class Magestore_Inventory_Model_Mysql4_Stocktransfering extends Mage_Core_Model_Mysql4_Abstract {
    public function _construct() {
        $this->_init('inventory/stocktransfering', 'transfer_stock_id');
    }
}
?>