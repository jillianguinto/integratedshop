<?php

class Magestore_Inventory_Model_Transactionproduct extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('inventory/transactionproduct');
    }
}

?>
