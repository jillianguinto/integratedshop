<?php

class Magestore_Inventory_Model_Mysql4_Stocktransferingproduct extends Mage_Core_Model_Mysql4_Abstract {

    public function _construct() {
        $this->_init('inventory/stocktransferingproduct', 'tranfer_stock_product_id');
    }
}
?>
