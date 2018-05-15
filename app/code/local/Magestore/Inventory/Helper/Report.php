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
 * Report Helper
 * 
 * @category    Magestore
 * @package     Magestore_Inventory
 * @author      Magestore Developer
 */
class Magestore_Inventory_Helper_Report extends Mage_Core_Helper_Abstract {

    public function prepareIntervalsCollection($collection, $from, $to, $periodType = self::REPORT_PERIOD_TYPE_DAY, $column = 'created_time') {
        $intervals = $this->getIntervals($from, $to, $periodType);
        foreach ($intervals as $interval) {
            $item = Mage::getModel('adminhtml/report_item');
            $item->setData($column, $interval);
            $item->setIsEmpty();
            $collection->addItem($item);
        }
    }
    
    public function getBlockName($filterData){
        $warehouseId = $filterData->getData('warehouse_select');
        $period = $filterData->getData('period_type');
        $name = '';
        if(is_null($period) || $period == ''){
            if(is_null($warehouseId) || $warehouseId == '')
                $name = 'inventory_adminhtml_report_sales_case2';
            else
                $name = 'inventory_adminhtml_report_sales_case5';
        }else{
            if(is_null($warehouseId) || $warehouseId == '')
                $name = 'inventory_adminhtml_report_sales_case3';
            else
                $name = 'inventory_adminhtml_report_sales_case4';
        }
        return $name;
    }
    public function getSalesRateValue($item, $filterData){
        $period = $filterData->getData('period_type');
        $dateForm = $filterData->getData('date_from');
        $dateTo = $filterData->getData('date_to');
        $from = strtotime($dateForm);
        $to = strtotime($dateTo);
        $dateDiff = $to - $from;
        
        $fullDays = ceil($dateDiff/(60*60*24))+1;
        $rate = 0;
        if($fullDays){
            $rate = number_format($item->getTotalShipment()/$fullDays, 2);
        }
        return $rate;
    }
    public function getProductAmountValue($item, $filterData){
        $productAmount = $filterData->getData('product_amount');
        return $productAmount;
    }
    public function _tempCollection(){
        $collection = new Varien_Data_Collection();
        return $collection;
    }
    
}