<?php

class Magestore_Inventory_Model_Mysql4_Warehouseproduct extends Mage_Core_Model_Mysql4_Abstract {

    public function _construct() {
        $this->_init('inventory/warehouseproduct', 'warehouse_product_id');
    }

}
?>