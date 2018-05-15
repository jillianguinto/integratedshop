<?php
class Magestore_Inventory_Helper_Purchaseorder extends Mage_Core_Helper_Abstract
{
	public function importProduct($data)
	{
            if(count($data)){
                Mage::getModel('admin/session')->setData('purchaseorder_product_import',$data);
            }
	}
        
        public function importDeliveryProduct($data)
	{
            if(count($data)){
                Mage::getModel('admin/session')->setData('delivery_purchaseorder_product_import',$data);
            }
	}
        
        public function importReturnOrderProduct($data)
	{
            if(count($data)){
                Mage::getModel('admin/session')->setData('returnorder_product_import',$data);
            }
	}
	public function getReturnOrderStatus(){
            return array(
                1 => Mage::helper('inventory')->__('New'),
                2 => Mage::helper('inventory')->__('Inquiry'),
                3 => Mage::helper('inventory')->__('Awaiting payment'),
                4 => Mage::helper('inventory')->__('Awaiting supplier'),
                5 => Mage::helper('inventory')->__('Awaiting delivery'),
                6 => Mage::helper('inventory')->__('Complete'),
                7 => Mage::helper('inventory')->__('Cancelled')
           );
	}
        public function getPurchaseOrderStatus(){
            return array(
                1 => 'New',
                2 => 'Inquiry',
                3 => 'Awaiting payment',
                4 => 'Awaiting supplier',
                5 => 'Awaiting delivery',
                6 => 'Complete',
                7 => 'Cancelled'
            );
	}
	public function getSupplierInfoByPurchaseOrderId($purchaseOrderId){
            $purchaseOrderModel = Mage::getModel('inventory/purchaseorder')->load($purchaseOrderId);
            $supplierId = $purchaseOrderModel->getSupplierId();
            $supplierModel = Mage::getModel('inventory/supplier')->load($supplierId);
            $supplierField = '';
            if($supplierModel->getId()){
                $data = $supplierModel->getData();
                $supplierField = "<br/>".$data['street'];
                if(isset($data['state'])){
                        $supplierField .=  " - ".$data['state'];
                }
                $supplierField .= " - ".$data['city'];
                $supplierField .= "<br />".$this->__('Telephone: ').$data['telephone'];
                $supplierField .= "<br/>".$this->__('Email: ').$data['email'];
            }
            return  $supplierField;
	}
	
	public function getBilingAddressByPurchaseOrderId($purchaseOrderId){
            $purchaseOrderModel = Mage::getModel('inventory/purchaseorder')->load($purchaseOrderId);
            $supplierId = $purchaseOrderModel->getSupplierId();
            $supplierModel = Mage::getModel('inventory/supplier')->load($supplierId);
            $supplierField = '';
            if($supplierModel->getId()){
                $data = $supplierModel->getData();
				$countryLists = Mage::getModel('directory/country')->getResourceCollection()->loadByStore() ->toOptionArray(true);
				$countryList=array();
				foreach($countryLists as $county)
				{
					$countryList[$county['value']]=$county['label'];
				}
                $supplierField = "<br/>".$data['street'];
				$supplierField .= "<br/>".$data['city'];
                if(isset($data['state'])){
                        $supplierField .=  ", ".$data['state'];
                }
                $supplierField .= ", ".$data['postcode'];
				$supplierField .= "<br/>".$countryList[$data['country_id']];
                $supplierField .= "<br />".$this->__('T: ').$data['telephone'];
            }
            return  $supplierField;
	}
        public function getDataByPurchaseOrderId($purchaseOrderId,$column){
            $purchaseOrderModel = Mage::getModel('inventory/purchaseorder')->load($purchaseOrderId);
            $return = $purchaseOrderModel->getData($column);
            return $return;
        }
    
	/* check this purchase order has delivery or not */
	public function haveDelivery()
	{
            if($purchaseOrderId = Mage::app()->getRequest()->getParam('id')){
                $delivery = Mage::getModel('inventory/delivery')->getCollection()
                                                ->addFieldToFilter('purchase_order_id',$purchaseOrderId)
                                                ->getFirstItem();
                if($delivery->getId())
                    return true;
            }
            return false;
	}	
	
	/*update product qty for warehouse and for system when create delivery*/
	public function updateQtyProduct($purchaseOrderId,$pId,$qtyDelivery)
	{
            try{
                $purchaseOrder = Mage::getModel('inventory/purchaseorder')->load($purchaseOrderId);
                $product = Mage::getModel('catalog/product')->load($pId);
                $warehouseId = $purchaseOrder->getWarehouseId();
                $warehouseProduct = Mage::getModel('inventory/warehouseproduct')
                                            ->getCollection()
                                            ->addFieldToFilter('warehouse_id',$warehouseId)
                                            ->addFieldToFilter('product_id',$pId)
                                            ->getFirstItem();
                if($warehouseProduct->getId()){
                    $qty = (int)$warehouseProduct->getQty() + (int)$qtyDelivery;
					$newQtyAvailable = (int)$warehouseProduct->getQtyAvailable() +  (int)$qtyDelivery;
                    $warehouseProduct->setQty($qty)
									->setQtyAvailable($newQtyAvailable)
									->save();
                }else{
                    $warehouseProduct = Mage::getModel('inventory/warehouseproduct');
                    $warehouseProduct->setWarehouseId($warehouseId)
                                            ->setProductId($pId)
                                            ->setQty($qtyDelivery)
											->setQtyAvailable($qtyDelivery)
                                            ->save();
                }
                $qtyStock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty();
                $product->setStockData(array(
                            'qty' => $qtyStock + $qtyDelivery
                    ))
                    ->save();
            }catch(Exception $e){
            }
	}
      public function updateQtyProductForReturnOrder($purchaseOrderId,$pId,$qtyReturn)
	{
            try{
                $purchaseOrder = Mage::getModel('inventory/purchaseorder')->load($purchaseOrderId);
                $product = Mage::getModel('catalog/product')->load($pId);
                $warehouseId = $purchaseOrder->getWarehouseId();
                $warehouseProduct = Mage::getModel('inventory/warehouseproduct')
                                            ->getCollection()
                                            ->addFieldToFilter('warehouse_id',$warehouseId)
                                            ->addFieldToFilter('product_id',$pId)
                                            ->getFirstItem();
                if($warehouseProduct->getId()){
                    $warehouseProductQty = $warehouseProduct->getQty() - $qtyReturn;
					$warehouseProductQtyAvailable = $warehouseProduct->getQtyAvailable() - $qtyReturn;
                    if($warehouseProductQty > 0){
                        $warehouseProduct->setQty($warehouseProductQty)
										->setQtyAvailable($warehouseProductQtyAvailable)
										->save();
                    }
                    else{
                        $warehouseProduct->setQty(0)
										->setQtyAvailable(0)
										->save();				 
                    }
                }
                $qtyStock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty();
                $product->setStockData(array(
                                        'qty' => $qtyStock - $qtyReturn
                                ))
                                ->save();
            }catch(Exception $e){
                
            }
	}  
        
        public function getWarehouseOption()
        {
            $adminId = Mage::getModel('admin/session')->getUser()->getId();
            if(!$adminId) return null;
            $warehouseAssigneds = Mage::getModel('inventory/assignment')->getCollection()
                                    ->addFieldToFilter('admin_id',$adminId)
                                    ;
            $warehouseIds = array();
            foreach($warehouseAssigneds as $warehouseAssigned){
                if($warehouseAssigned->getData('can_edit_warehouse')
                        || $warehouseAssigned->getData('can_transfer')
                        || $warehouseAssigned->getData('can_adjust'))
                    $warehouseIds[] = $warehouseAssigned->getWarehouseId();
            }
            if(!count($warehouseIds) || count($warehouseIds) <= 0) return null;
            $warehouseCollections = Mage::getModel('inventory/warehouse')->getCollection()
                                        ->addFieldToFilter('warehouse_id',array('in'=>$warehouseIds));
            $warehouseArr = array();
            foreach($warehouseCollections as $warehouse){
                $warehouseArr[] = array('value'=>$warehouse->getId(), 'label'=>$warehouse->getName());
            }
            return $warehouseArr;
        }
        
        /**
         * get product ids by array of sku
         * @param type $data
         * @return array of product id
         */
        public function getProductsBySkuArray($data){
            $products = array();
            $productModel = Mage::getModel('catalog/product');
            foreach($data as $productImport){
                    $productId = $productModel->getIdBySku($productImport['SKU']);
                    if($productId){
                        foreach($productImport as $pImport => $p){
                            if($pImport != 'SKU'){
                                $pImport = explode('_',$pImport);
                                if($pImport[1]){
                                    $products[$productId]['warehouse_'.$pImport[1]] = $p;
                                }
                            }    
                        }
                    }
            }
            return $products;
        }
        
        public function getOrderPlaced()
        {
            return array(
                    1 => Mage::helper('inventory')->__('Email'),
                    2 => Mage::helper('inventory')->__('Fax'),
                    3 => Mage::helper('inventory')->__('N/A'),
                    4 => Mage::helper('inventory')->__('Phone'),
                    5 => Mage::helper('inventory')->__('Vender website')
            );
        }
        
        public function getShippingMethod()
        {
            $shippingMethods = Mage::getModel('inventory/shippingmethod')
                                            ->getCollection()
                                            ->addFieldToFilter('status',1);
            if(count($shippingMethods)){
                $shippingArray = array();
                foreach($shippingMethods as $shipping){
                    $shippingArray[$shipping->getId()] = $shipping->getName();
                }
                return $shippingArray;
            }else{
                return '';
            }
        }
        
        public function getPaymentTerms()
        {
            $paymentTerms = Mage::getModel('inventory/paymentterm')
                                            ->getCollection()
                                            ->addFieldToFilter('status',1);
            if(count($paymentTerms)){
                $paymentArray = array();
                foreach($paymentTerms as $payment){
                    $paymentArray[$payment->getId()] = $payment->getName();
                }
                return $paymentArray;
            }else{
                return '';
            }
        }
}