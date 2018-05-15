<?php
class Magestore_Inventory_Model_Mysql4_Reportproductmoved extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('inventory/reportproductmoved', 'inventory_report_pm_id');
    }
}