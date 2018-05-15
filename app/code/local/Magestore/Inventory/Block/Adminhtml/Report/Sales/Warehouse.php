<?php

/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Inventory
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Customer Orders Report Adminhtml Block
 * 
 * @category    Magestore
 * @package     Magestore_Inventory
 * @author      Magestore Developer
 */
class Magestore_Inventory_Block_Adminhtml_Report_Sales_Warehouse extends Mage_Core_Block_Template {

    public function __construct() {
        
    }

    public function getType() {
        return 'warehouse';
    }

    public function getGridName($filterData) {
        $period = $filterData->getData('period_type');
        $name = '';
        if (is_null($period) || $period == '') {
            $name = 'inventory_adminhtml_report_sales_case2';
        } else {
            $name = 'inventory_adminhtml_report_sales_case3';
        }
        return $name;
    }

    public function getFilterUrl() {
        $url = $this->getUrl('inventoryadmin/adminhtml_report/warehousesales', array('key' => $this->getRequest()->getParam('key')));
        return $url;
    }

}