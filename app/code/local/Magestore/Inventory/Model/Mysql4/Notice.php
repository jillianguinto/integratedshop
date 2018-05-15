<?php

class Magestore_Inventory_Model_Mysql4_Notice extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('inventory/notice', 'notice_id');
    }
}