<?php

class Magestore_Inventory_Helper_Stockissuing extends Mage_Core_Helper_Abstract {
    public function importProduct($data) {
        if (count($data)) {
            Mage::getModel('admin/session')->setData('stockissuing_product_import', $data);
        }
    } 
}

?>
