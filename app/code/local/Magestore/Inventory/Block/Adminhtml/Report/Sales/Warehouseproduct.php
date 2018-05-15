<?php

class Magestore_Inventory_Block_Adminhtml_Report_Sales_Warehouseproduct extends Mage_Core_Block_Template {

    public function __construct() {
        
    }

    public function getType() {
        return 'warehouseproduct';
    }

    public function getGridName($filterData) {
        $period = $filterData->getData('period_type');
        if (is_null($period) || $period == '') {
            $name = 'inventory_adminhtml_report_sales_case5';
        } else {
            $name = 'inventory_adminhtml_report_sales_case4';
        }
        return $name;
    }

    public function getFilterUrl() {
        $url = $this->getUrl('inventoryadmin/adminhtml_report/warehouseproductsales', array('key' => $this->getRequest()->getParam('key')));
        return $url;
    }

}

?>
