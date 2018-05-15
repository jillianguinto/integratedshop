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
 * Inventory Resource Collection Model
 * 
 * @category    Magestore
 * @package     Magestore_Inventory
 * @author      Magestore Developer
 */
class Magestore_Inventory_Model_Mysql4_Warehouse_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected $_isGroupSql = false;
    public function _construct()
    {
        parent::_construct();
        $this->_init('inventory/warehouse');
    }
    
    public function joinAdminUser(){
        $resource = Mage::getSingleton('core/resource');
        $this->getSelect()
            ->joinLeft(array('admin_user'=>$resource->getTableName('admin/user')), 'main_table.created_by = admin_user.user_id',array('username'=>'admin_user.username'))
        ;
        return $this;
    }
    
    public function joinProduct(){
        $resource = Mage::getSingleton('core/resource');
        $this->getSelect()
            ->joinLeft(
                array('warehouse_product'=>$resource->getTableName('inventory/warehouseproduct')), 
                'main_table.warehouse_id = warehouse_product.warehouse_id',
                array('*')
            )
            ->group('main_table.warehouse_id')
            
        ;
        $this->getSelect()->columns(array('total_products'=>'SUM(warehouse_product.qty)'));
        return $this;
    }
    
    public function joinShipment(){
        $resource = Mage::getSingleton('core/resource');
        $this->getSelect()
            ->join(
                array('inventory_shipment'=>$resource->getTableName('inventory/inventoryshipment')), 
                'main_table.warehouse_id = inventory_shipment.warehouse_id',
                array('shipment_id'=>'inventory_shipment.shipment_id')
            )
            ->join(
                array('shipment'=>$resource->getTableName('sales_flat_shipment')),
                'inventory_shipment.shipment_id=shipment.entity_id',
                array('date_ship'=>'shipment.created_at')
            )
        ;
        return $this;
    }
    
    public function setIsGroupCountSql($value) {
        $this->_isGroupSql = $value;
        return $this;
    }
    
    public function getSelectCountSql() {
        if ($this->_isGroupSql) {
            $this->_renderFilters();
            $countSelect = clone $this->getSelect();
            $countSelect->reset(Zend_Db_Select::ORDER);
            $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
            $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
            
            $countSelect->reset(Zend_Db_Select::COLUMNS);
            if (count($this->getSelect()->getPart(Zend_Db_Select::GROUP)) > 0) {
                $countSelect->reset(Zend_Db_Select::GROUP);
                $countSelect->distinct(true);
                $group = $this->getSelect()->getPart(Zend_Db_Select::GROUP);
                $countSelect->columns("COUNT(DISTINCT " . implode(", ", $group) . ")");
            } else {
                $countSelect->columns('COUNT(*)');
            }
            return $countSelect;
        }
        return parent::getSelectCountSql();
    }
}