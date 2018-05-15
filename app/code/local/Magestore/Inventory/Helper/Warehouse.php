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
 * Warehouse Helper
 * 
 * @category    Magestore
 * @package     Magestore_Inventory
 * @author      Magestore Developer
 */
class Magestore_Inventory_Helper_Warehouse extends Mage_Core_Helper_Abstract {

    /**
     * check admin can edit a warehouse or no
     * @param type $adminId
     * @param type $warehouseId
     * @return boolean
     */
    public function canEdit($adminId, $warehouseId) {
        if ($warehouseId) {
            $assignmentCollection = Mage::getModel('inventory/assignment')
                    ->getCollection()
                    ->addFieldToFilter('warehouse_id', $warehouseId)
                    ->addFieldToFilter('admin_id', $adminId);
            if ($assignmentCollection->getSize()) {
                $assignment = $assignmentCollection->getFirstItem();
                if ($assignment->getCanEditWarehouse()) {
                    return true;
                }
            }
        } else {
            return true;
        }
        return false;
    }

    public function getAllWarehouseName() {
        $warehouses = array();
        $model = Mage::getModel('inventory/warehouse');
        $collection = $model->getCollection();
        foreach ($collection as $warehouse) {
            $warehouses[$warehouse->getId()] = $warehouse->getName();
        }
        return $warehouses;
    }
    
    public function getWarehouseNames() {
        $warehouses = array();
        $model = Mage::getModel('inventory/warehouse');
        $collection = $model->getCollection();
        foreach ($collection as $warehouse) {
            $warehouses[$warehouse->getName()] = $warehouse->getName();
        }
        return $warehouses;
    }

    public function getAllWarehouseTarget() {
        $warehouses = array();
        $model = Mage::getModel('inventory/warehouse');
        $collection = $model->getCollection()->addFieldToFilter('is_unwarehouse', array('neq' => '1'));
        foreach ($collection as $warehouse) {
            $warehouses[$warehouse->getId()] = $warehouse->getName();
        }
        return $warehouses;
    }
    
    public function getAllWarehouseSendstock(){
        $warehouses = array();
        $model = Mage::getModel('inventory/warehouse');
        $collection = $model->getCollection();
        foreach ($collection as $warehouse) {
            $warehouses[$warehouse->getName()] = $warehouse->getName();
        }
        if(empty($warehouses['Others']))
            $warehouses['Others']  = 'Others';
        return $warehouses;
    }
	
    public function getAllWarehouseSendstockWithId(){
        $warehouses = array();
        $model = Mage::getModel('inventory/warehouse');
        $collection = $model->getCollection();
        foreach ($collection as $warehouse) {
            $warehouses[$warehouse->getId()] = $warehouse->getName();
        }
        if(empty($warehouses['others']))
            $warehouses['others']  = 'Others';
        return $warehouses;
    }
    
	public function getAllWarehouseRequeststock(){
        $warehouses = array();
        $model = Mage::getModel('inventory/warehouse');
        $collection = $model->getCollection();
        foreach ($collection as $warehouse) {
            $warehouses[$warehouse->getId()] = $warehouse->getName();
        }
        if(empty($warehouses['others']))
            $warehouses['others']  = 'Others';
        return $warehouses;
    }
	
	public function getAllWarehouseRequeststockGird(){
        $warehouses = array();
        $model = Mage::getModel('inventory/warehouse');
        $collection = $model->getCollection();
        foreach ($collection as $warehouse) {
            $warehouses[$warehouse->getName()] = $warehouse->getName();
        }
        if(empty($warehouses['Others']))
            $warehouses['Others']  = 'Others';
        return $warehouses;
    }
    
    public function loadTransferAbleWarehouses($admin) {
        $warehouses = array();
        $collection = Mage::getModel('inventory/assignment')->getCollection()
                ->addFieldToFilter('admin_id', $admin->getId())
                ->addFieldToFilter('can_transfer', 1);
        foreach ($collection as $assignment) {
            $warehouses[] = $assignment->getWarehouseId();
        }
        $warehouseCollection = Mage::getModel('inventory/warehouse')->getCollection()
                ->addFieldToFilter('warehouse_id', array('in' => $warehouses));
        return $warehouseCollection;
    }

    public function getHtmlWarehouses() {
        $warehouses = $this->getAllWarehouseName();
        $html = '';
        foreach ($warehouses as $id => $name) {
            $html .= '<option value = "' . $id . '" >' . $name . '</option>';
        }
        return $html;
    }

    public function getHtmlWarehousesTarget() {
        $model = Mage::getModel('inventory/warehouse');
        $collection = $model->getCollection()
                ->addFieldToFilter('is_unwarehouse',array('neq'=>1));
        $html = '';
        foreach ($collection as $warehouse) {
            $html .= '<option value = "' . $warehouse->getId() . '" >' . $warehouse->getName() . '</option>';
        }
        return $html;
    }

    public function getWarehouseByAdmin() {
        $admin = Mage::getSingleton('admin/session')->getUser();
        $collection = $this->loadTransferAbleWarehouses($admin);
        $warehouses = array();
        foreach ($collection as $warehouse) {
            $warehouses[$warehouse->getId()] = $warehouse->getName();
        }
        return $warehouses;
    }

    public function getHtmlWarehouseByAdmin() {
        $admin = Mage::getSingleton('admin/session')->getUser();
        $collection = $this->loadTransferAbleWarehouses($admin);
        $html = '';
        foreach ($collection as $warehouse) {
            $html .= '<option value="' . $warehouse->getId() . '">' . $warehouse->getName() . '</option>';
        }
        return $html;
    }

    public function canTransfer($adminId, $warehouseId) {
        $collection = Mage::getModel('inventory/assignment')->getCollection()
                ->addFieldToFilter('admin_id', $adminId)
                ->addFieldToFilter('warehouse_id', $warehouseId)
        ;
        if ($collection->getSize()) {
            if ($collection->getFirstItem()->getCanTransfer() == 1) {
                return true;
            }
        }
        return false;
    }

    public function getAllWarehouseNameEnable() {
        $warehouses = array();
        $model = Mage::getModel('inventory/warehouse');
        $collection = $model->getCollection()
                ->addFieldToFilter('status', 1);
        foreach ($collection as $warehouse) {
            $warehouses[$warehouse->getId()] = $warehouse->getName();
        }
        return $warehouses;
    }

    public function selectboxWarehouseShipmentByPid($productId, $minQty, $orderItemId) {
        $allWarehouse = Mage::helper('inventory/warehouse')->getAllWarehouseNameEnable();
        $warehouseProductModel = Mage::getModel('inventory/warehouseproduct')->getCollection()
                ->addFieldToFilter('product_id', $productId)
                ->setOrder('qty', 'DESC');
        $warehouseHaveProduct = array();
        $return = "<select class='warehouse-shipment' name='warehouse-shipment[items][$orderItemId]' onchange='changeviewwarehouse(this,$orderItemId);' id='warehouse-shipment[items][$orderItemId]'>";
        $firstWarehouse = '';
        foreach ($warehouseProductModel as $model) {
            $warehouseId = $model->getwarehouseId();
            $warehouseName = $allWarehouse[$warehouseId];
            $productQty = $model->getQty();
            if ($warehouseName != '' && $warehouseId != $this->getIdOfunWarehouse()) {
                if(!$firstWarehouse)
                    $firstWarehouse = $warehouseId;
                $return .= "<option value='$warehouseId' ";
                $return .= ">$warehouseName($productQty product(s))</option>";
                $warehouseHaveProduct[] = $allWarehouse[$warehouseId];
            }
        }
        foreach ($allWarehouse as $warehouseIdKey => $warehouseNameValue) {
            if ($warehouseNameValue != '' && $warehouseIdKey != $this->getIdOfunWarehouse()) {
                if (in_array($allWarehouse[$warehouseIdKey], $warehouseHaveProduct) == false) {
                    if(!$firstWarehouse)
                        $firstWarehouse = $warehouseIdKey;
                    $return .= "<option value='$warehouseIdKey' ";
                    $return .= ">$warehouseNameValue(0 product(s))</option>";
                }
            }
        }
        $warehouseModel = Mage::getModel('inventory/warehouse')->getCollection();
        if(count($warehouseModel)==1){
            $qtyInUnwarehouse = $this->getQtyOneProductInWarehouse($this->getIdOfunWarehouse(), $productId);
            $warehouseIdUnwarehouse = $this->getIdOfunWarehouse();
            if(!$firstWarehouse)
                $firstWarehouse = $warehouseIdUnwarehouse;
            $return .= "<option value='$warehouseIdUnwarehouse' ";
                    $return .= ">unWarehouse( $qtyInUnwarehouse product(s))</option>";
        }
        $return .= "</select><br />";
        $return .= "<div style='float:right;'><a id='view_warehouse-shipment[items][$orderItemId]' target='_blank' href='".Mage::getBlockSingleton('inventory/adminhtml_warehouse')->getUrl('inventoryadmin/adminhtml_warehouse/edit').'id/'.$firstWarehouse."'>".$this->__('view')."</a></div>";
        return $return;
    }

    public function selectboxWarehouseNeedTransfer($productId, $minQty) {
        $allWarehouse = Mage::helper('inventory/warehouse')->getAllWarehouseNameEnable();
        $warehouseProductModel = Mage::getModel('inventory/warehouseproduct')->getCollection()
                ->addFieldToFilter('product_id', $productId)
                ->setOrder('qty', 'DESC');
        $return = "<select class='warehouse-can-transfer' name='warehouse-can-transfer' id='warehouse-can-transfer' >";
        foreach ($warehouseProductModel as $model) {
            $warehouseId = $model->getwarehouseId();
            $warehouseName = $allWarehouse[$warehouseId];
            $productQty = $model->getQty();
            if ($warehouseName != '' && $warehouseId != $this->getIdOfunWarehouse()) {
                $return .= "<option value='$warehouseId' ";
                $return .= ">$warehouseName( $productQty product(s))</option>";
            }
        }
        $return .= "</select>";
        return $return;
    }

    public function selectboxWarehouseToTransfer($productId, $minQty, $wahouseTargetId) {
        $allWarehouse = Mage::helper('inventory/warehouse')->getAllWarehouseNameEnable();
        $warehouseProductModel = Mage::getModel('inventory/warehouseproduct')->getCollection()
                ->addFieldToFilter('product_id', $productId)
                ->setOrder('qty', 'DESC');
        $return = "<select class='warehouse-can-transfer' name='warehouse-can-transfer' id='warehouse-can-transfer' >";
        foreach ($warehouseProductModel as $model) {
            $warehouseId = $model->getwarehouseId();
            $warehouseName = $allWarehouse[$warehouseId];
            $productQty = $model->getQty();
            if ($warehouseName != '' && $warehouseId != $wahouseTargetId) {
                $return .= "<option value='$warehouseId' ";
                $return .= ">$warehouseName( $productQty product(s))</option>";
            }
        }
//        if(count($warehouseHaveProduct)==0){
//            $qtyInUnwarehouse = $this->getQtyOneProductInWarehouse($this->getIdOfunWarehouse(), $productId);
//            $warehouseIdUnwarehouse = $this->getIdOfunWarehouse();
//            $return .= "<option value='$warehouseIdUnwarehouse' ";
//                    $return .= ">unWarehouse( $qtyInUnwarehouse product(s))</option>";
//        }
        $return .= "</select>";
        return $return;
    }

    public function checkWarehouseAvailableProduct($warehouseId, $productId, $qty) {
        $warehouseProductModel = Mage::getModel('inventory/warehouseproduct')->getCollection()
                ->addFieldToFilter('warehouse_id', $warehouseId)
                ->addFieldToFilter('product_id', $productId)
                ->addFieldToFilter('qty', array('gteq' => $qty));
        if ($warehouseProductModel->getFirstItem()->getData()) {
            return true;
        } else {
            return false;
        }
    }

    function getIdOfunWarehouse() {
        $warehouseModel = Mage::getModel('inventory/warehouse')->getCollection()
                ->addFieldToFilter('is_unwarehouse', '1');
        if ($warehouseModel->getFirstItem()->getData()) {
            $unWarehouseId = $warehouseModel->getFirstItem()->getId();
        }
        return $unWarehouseId;
    }

    public function checkTheFirstWarehouseAvailableProduct($productId, $minQty) {
        /*
         * check 1 san pham co trong warehouse nao do ko va qty lon hon minQty
         */
        $unWarehouseId = Mage::helper('inventory/warehouse')
                ->getIdOfunWarehouse();
        $warehouseModel = Mage::getModel('inventory/warehouse')->getCollection();
        if(count($warehouseModel)==1){
            $warehouseProductModel = Mage::getModel('inventory/warehouseproduct')->getCollection()
                ->addFieldToFilter('product_id', $productId)
                ->addFieldToFilter('qty', array('gteq' => $minQty))
                ->setOrder('qty', 'DESC');
        }else{
            $warehouseProductModel = Mage::getModel('inventory/warehouseproduct')->getCollection()
                    ->addFieldToFilter('product_id', $productId)
                    ->addFieldToFilter('warehouse_id', array('nin' => $unWarehouseId))
                    ->addFieldToFilter('qty', array('gteq' => $minQty))
                    ->setOrder('qty', 'DESC');
        }
        if ($warehouseProductModel->getFirstItem()->getData()) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * get the firrst warehouse ha most this product but not unWarehouse
     */

    public function getFirstWarehouseHaveMostOfAProduct($productId) {
        $unWarehouseId = Mage::helper('inventory/warehouse')
                ->getIdOfunWarehouse();
        $warehouseProductModel = Mage::getModel('inventory/warehouseproduct')->getCollection()
                ->addFieldToFilter('product_id', $productId)
                ->setOrder('qty', 'DESC');
        if(count($warehouseProductModel) > 1){
        $warehouseProductModel = Mage::getModel('inventory/warehouseproduct')->getCollection()
                ->addFieldToFilter('product_id', $productId)
                ->addFieldToFilter('warehouse_id', array('nin' => $unWarehouseId))
                ->setOrder('qty', 'DESC');
        }
        if ($warehouseProductModel->getFirstItem()->getData()) {
            $warehouseId = $warehouseProductModel->getFirstItem()->getWarehouseId();
        }else{
            $allWarehouse = Mage::helper('inventory/warehouse')->getAllWarehouseNameEnable();
            foreach($allWarehouse as $warehouseIdKey => $warehouseNameValue){
                if($warehouseNameValue != '' && $warehouseIdKey != Mage::helper('inventory/warehouse')->getIdOfunWarehouse()){
                    $warehouseId = $warehouseIdKey;
                    break;
                }
            }
        }
        return $warehouseId;
    }

    /*
     * get warehouse name by warehouse id in model inventory/warehouse
     */

    public function getWarehouseNameByWarehouseId($warehouseId) {
        $warehouseModel = Mage::getModel('inventory/warehouse')->load($warehouseId);
        $warehouseName = $warehouseModel->getName();
        return $warehouseName;
    }

    /*
     * get product qty of one product in  warehouse by warehouse id and product 
     * id in model inventory/warehouseproduct
     */

    public function getQtyOneProductInWarehouse($warehouseId, $productId) {
        $warehouseProductModel = Mage::getModel('inventory/warehouseproduct')->getCollection()
                ->addFieldToFilter('warehouse_id', $warehouseId)
                ->addFieldToFilter('product_id', $productId);
        if ($warehouseProductModel->getFirstItem()->getQty()) {
            $qty = $warehouseProductModel->getFirstItem()->getQty();
            return $qty;
        }
        return 0;
    }

    /*
     * get all other column in  erp_inventorywarehouse table by warehouse id 
     * id in model inventory/warehouse
     */

    public function getAColumnValueInWarehouse($warehouseId, $column) {
        $warehouseIdModel = Mage::getModel('inventory/warehouse')->load($warehouseId);
        if ($warehouseIdModel->getData()) {
            $return = $warehouseIdModel->getData($column);
            return $return;
        }
        return 0;
    }

    /**
     * get warehouses by order
     */
    public function getWarehousesByOrder($order) {
        
    }

    /**
     * get all warehouses have product by product id
     */
    public function getWarehouseByProductId($productId,$checkqtyZero = true) {
        if($checkqtyZero == true){
            $warehouseProducts = Mage::getModel('inventory/warehouseproduct')
                ->getCollection()
                ->addFieldToFilter('product_id', $productId)
                ->addFieldToFilter('qty', array('gt' => 0));
        }
        else{
            $warehouseProducts = Mage::getModel('inventory/warehouseproduct')
                ->getCollection()
                ->addFieldToFilter('product_id', $productId);
        }
        if (count($warehouseProducts)) {
            return $warehouseProducts;
        } else {
            return null;
        }
    }

    public function createNewAdjustStock($warehouse, $products) {
        $model = Mage::getModel('inventory/adjuststock');
        $admin = Mage::getModel('admin/session')->getUser()->getUsername();
        $model->setWarehouseId($warehouse->getId())
                ->setWarehouseName($warehouse->getName())
                ->setCreateBy($admin)
                ->setCreatedAt(now());
        try {
            $model->save();
            //create adjust stock product
            if (count($products)){
                foreach($products as $productId=>$p){
                    $adjustproduct = Mage::getModel('inventory/adjuststockproduct');
                    $adjustproduct->setAdjuststockId($model->getId())
                            ->setProductId($productId)
                            ->setOldQty($p['old'])
                            ->setAdjustQty($p['new'])
                            ->setUpdatedQty($p['new']);
                    try{
                        $adjustproduct->save();
                    }  catch (Exception $e){
                         Mage::getSingleton('adminhtml/session')->addSuccess(
                                Mage::helper('inventory')->__($e->getMessage())
                        );
                    }
                }
            }
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('inventory')->__($e->getMessage())
            );
        }
    }
    
    public function deleteWarehouseProducts($warehouse, $list){
        $warehouseProducts = Mage::getModel('inventory/warehouseproduct')
                ->getCollection()
                ->addFieldToFilter('warehouse_id', $warehouse->getId())
                ->addFieldToFilter('product_id', array('nin'=>$list));
        $warehouseProductSkus = '';
        if($warehouseProducts->getSize()){
            $i = 0;
            foreach($warehouseProducts as $wp){
                if($i!=0)
                    $warehouseProductSkus .= ', ';
                $warehouseProductSkus .= $this->getProductSkuByProductId($wp->getId());
                if($wp->getQty() == 0)
                    $wp->delete();
            }
        }
        return $warehouseProductSkus;
    }
    
    public function canDelete($warehouseId){
        $warehouseProducts = Mage::getModel('inventory/warehouseproduct')
                ->getCollection()
                ->addFieldToFilter('warehouse_id',$warehouseId)
                ->addFieldToFilter('qty', array('gt'=>'0'));
        if($warehouseProducts->getSize()){
            return false;
        }
        return true;
    }
    
    public function getProductSkuByProductId($productId)
    {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $sql = 'SELECT distinct(`sku`) from ' . $resource->getTableName("catalog_product_entity") . ' WHERE (entity_id = '.$productId.')';
        $result = $readConnection->fetchOne($sql);
        return $result;
    }

}