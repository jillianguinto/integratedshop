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
class Magestore_Inventory_Block_Adminhtml_Report_Sales_Header extends Mage_Core_Block_Template
{
    public function __construct()
    {
    }
    
    public function getGridName(){
        $warehouseId = $this->getRequest()->getParam('warehouse_select');
        $period = $this->getRequest()->getParam('period');
        $name = '';
        if(is_null($period) || $period == ''){
            if(is_null($warehouseId) || $warehouseId == '')
                $name = 'inventory_adminhtml_report_sales_case2';
            else
                $name = 'inventory_adminhtml_report_sales_case1';
        }else{
            if(is_null($warehouseId) || $warehouseId == '')
                $name = 'inventory_adminhtml_report_sales_case3';
            else
                $name = 'inventory_adminhtml_report_sales_case4';
        }
        return $name;
    }
}