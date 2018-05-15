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
class Magestore_Inventory_Block_Adminhtml_Report_Purchases_Header extends Mage_Core_Block_Template
{
    public function __construct()
    {
    }
    public function getGridName(){
        $supplierId = $this->getRequest()->getParam('supplier_select');
        $req = Mage::app()->getRequest();
        $actionName = $req->getActionName();
        $period = $this->getRequest()->getParam('period');
        $name = '';
        if(is_null($period) || $period == ''){
            if(is_null($supplierId) || $supplierId == ''){
                if($actionName == 'purchases'){
                    $name = 'inventory_adminhtml_report_purchases_caseoneall';//one supplier - all period
                }else{
                $name = 'inventory_report_purchases';//all supplier - all period
                }
            }
            else
                $name = 'inventory_adminhtml_report_purchases_caseoneall';//one supplier - all period
        }else{
            if(is_null($supplierId) || $supplierId == '')
                $name = 'inventory_report_purchases';//all supplier - one period
            else
                $name = 'inventory_adminhtml_report_purchases_caseoneone';//one supplier - one period
        }
        return $name;
    }
}