<?php
class Magestore_Inventory_Model_Mysql4_Reportproductreceived extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('inventory/reportproductreceived', 'inventory_report_pr_id');
    }
}