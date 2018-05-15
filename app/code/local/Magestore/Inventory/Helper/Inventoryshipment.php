<?php
class Magestore_Inventory_Helper_Inventoryshipment extends Mage_Core_Helper_Abstract
{
	public function getWarehouseIdByShipmentIdAndOrderitemId($shipmentId,$OrderItemId){
		$inventoryShipmentModel = Mage::getModel('inventory/inventoryshipment');
		$warehouse = $inventoryShipmentModel->getCollection()
						->addFieldToFilter('shipment_id',$shipmentId)
						->addFieldToFilter('item_id',$OrderItemId)
						->getFirstItem();
		$warehouseId = $warehouse->getWarehouseId();
		return $warehouseId;
	}
	
	public function getWarehouseNameByShipmentIdAndOrderitemId($shipmentId,$orderItemId){
		$inventoryShipmentModel = Mage::getModel('inventory/inventoryshipment');
                $warehouse = $inventoryShipmentModel->getCollection()
                                                    ->addFieldToFilter('shipment_id',$shipmentId)
                                                    ->addFieldToFilter('item_id',$orderItemId)
                                                    ->getFirstItem();
                if($warehouseName = $warehouse->getWarehouseName())
                    return $warehouseName;
                elseif($supplierName = $warehouse->getSupplierName())
                    return $supplierName;
//		$warehouseName = $warehouse->getWarehouseName();
	}
        
        /*
         * kiem tra 1 product co dang o trang thai waitting for transfer hay khong
         * in page create shipment
         */
        public function checkOrderItemWaittingFortransfer($orderItemId,$productId,$orderId,$warehouseId){
            $shipmentTransferModel = Mage::getModel('inventory/inventoryshipmenttransfer')
                                            ->getCollection()
                                            ->addFieldToFilter('item_id',$orderItemId)
                                            ->addFieldToFilter('product_id',$productId)
                                            ->addFieldToFilter('order_id',$orderId)
                                            ->addFieldToFilter('warehouse_id',$warehouseId);
            $data = $shipmentTransferModel->getFirstItem()->getData();
            if($data){
                $stocTransferingModel = Mage::getModel('inventory/stocktransfering')->load($data['transfer_stock_id']);
                if($stocTransferingModel->getStatus() != 3){
                    return true;
                }
                else{
                    return false;
                }
            }
            else{
                return false;
            }
                                            
        }

}