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
 * Supplier Adminhtml Block
 * 
 * @category    Magestore
 * @package     Magestore_Inventory
 * @author      Magestore Developer
 */
class Magestore_Inventory_Block_Adminhtml_Purchaseorder extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_controller = 'adminhtml_purchaseorder';
        $this->_blockGroup = 'inventory';
        $this->_headerText = Mage::helper('inventory')->__('Purchase Order Manager');
        $this->_addButtonLabel = Mage::helper('inventory')->__('Create Purchase Order');
        parent::__construct();
    }

    public function getWarehouseIdsForPurchase() {
        $warehouseIds = $this->getRequest()->getParam('warehouse_ids', null);
        if (!$warehouseIds) {
            $warehouseIds = Mage::getModel('inventory/purchaseorder')->load($this->getRequest()->getParam('id'))->getWarehouseId();
        }
        $warehouseIds = explode(',', $warehouseIds);
        return $warehouseIds;
    }

    public function getWarehouseNameById($warehouseId) {
        return Mage::getModel('inventory/warehouse')->load($warehouseId)->getName();
    }

    public function getWarehouseList() {
        $warehouseIds = $this->getWarehouseIdsForPurchase();
        $numberWarehouses = count($warehouseIds);
        $warehouseNames = '';
        $i = 1;
        foreach ($warehouseIds as $warehouseId) {
            if (($numberWarehouses > 1) && ($i > 1)) {
                if ($i == $numberWarehouses) {
                    $warehouseNames .= Mage::helper('inventory')->__(' and ');
                } else {
                    $warehouseNames .= ', ';
                }
            }
            $warehouseNames .= '<b>' . $this->getWarehouseNameById($warehouseId) . '</b>';
            $i++;
        }
        return $warehouseNames;
    }

    public function getPurhcaseOrderHistory($id) {
        return Mage::getModel('inventory/purchaseorderhistory')->load($id);
    }

    public function getPurchaseOrderContentByHistoryId($id) {
        $collection = Mage::getModel('inventory/purchaseorderhistorycontent')
                ->getCollection()
                ->addFieldToFilter('purchase_order_history_id', $id);
        return $collection;
    }

}