<?php

class Magestore_Inventory_Helper_Requeststock extends Mage_Core_Helper_Abstract {

    public function importProduct($data) {
        if (count($data)) {
            Mage::getModel('admin/session')->setData('requeststock_product_import', $data);
        }else{
            Mage::getModel('admin/session')->setData('null_requeststock_product_import', 1);
        }
    }
	
	public function checkCancelRequeststock($id)
	{
		$store = Mage::app()->getStore();
		$days = 24*60*60*Mage::getStoreConfig('inventory/transaction/cancel_time', $store->getId());
		$requestStock = Mage::getModel('inventory/requeststock')->load($id);
		$createdAt = strtotime($requestStock->getCreatedAt())+$days;
		$now = strtotime(now("y-m-d"));
		$warehouseId = $requestStock->getFromId();
		$admin = Mage::getSingleton('admin/session')->getUser();
		if($warehouseId&& Mage::helper('inventory/warehouse')->canTransfer($admin->getId(), $warehouseId)){
			if(($requestStock->getStatus() == 1)&&($createdAt >= $now))
				return true;
		}
		return false;
	}

}
