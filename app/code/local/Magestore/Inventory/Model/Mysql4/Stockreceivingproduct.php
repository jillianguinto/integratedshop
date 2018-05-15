<?php
    class Magestore_Inventory_Model_Mysql4_Stockreceivingproduct extends Mage_Core_Model_Mysql4_Abstract {

    public function _construct() {
        $this->_init('inventory/stockreceivingproduct', 'stock_receiving_product_id');
    }
}
?>
