<?php

class Magestore_Inventory_Helper_Stockreceiving extends Mage_Core_Helper_Abstract {
    public function importProduct($data) {
        if (count($data)) {
            Mage::getModel('admin/session')->setData('stockreceiving_product_import', $data);
        }
    } 
}

?>
