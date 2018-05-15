<?php

class Magestore_Inventory_Helper_Sendstock extends Mage_Core_Helper_Abstract {

    public function importProduct($data) {
        if (count($data)) {
            Mage::getModel('admin/session')->setData('sendstock_product_import', $data);
        }else{
            Mage::getModel('admin/session')->setData('null_sendstock_product_import', 1);
        }
    }

}
