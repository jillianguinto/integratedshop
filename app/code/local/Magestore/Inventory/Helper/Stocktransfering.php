<?php

class Magestore_Inventory_Helper_Stocktransfering extends Mage_Core_Helper_Abstract {

    public function getWarehouseByAdmin() {
        $adminId = Mage::getSingleton('admin/session')->getUser()->getId();
        $adminCollection = Mage::getModel('inventory/assignment')->getCollection()
            ->addFieldToFilter('admin_id', $adminId);
        $warehouses = array();
        foreach ($adminCollection as $admin) {
            $warehouseId = $admin->getWarehouseId();
            $can_transfer = $admin->getCanTransfer();
        }
        if ($can_transfer == 1) {
            $warehouse = Mage::getModel('inventory/warehouse')->load($warehouseId);
            $warehouses[$warehouseId] = $warehouse->getName();
            return $warehouses;
        } else {
            return $warehouses;
        }
    }

    public function checkEditStocktransfering($transferstockId) {
        $adminId = Mage::getSingleton('admin/session')->getUser()->getId();
        $stocktransferingModel = Mage::getModel('inventory/stocktransfering')->load($transferstockId);
        $targetWarehouseId = $stocktransferingModel->getWarehouseToId();
        $adminCollection = Mage::getModel('inventory/assignment')->getCollection()
            ->addFieldToFilter('admin_id', $adminId)
            ->addFieldToFilter('warehouse_id', $targetWarehouseId);
        foreach ($adminCollection as $admin) {
            $can_transfer = $admin->getCanTransfer();
        }
        return $can_transfer;
    }
    
    public function canSaveAndApply($source, $target){
        $adminId = Mage::getSingleton('admin/session')->getUser()->getId();
        $targetCollection = Mage::getModel('inventory/assignment')->getCollection()
            ->addFieldToFilter('admin_id', $adminId)
            ->addFieldToFilter('warehouse_id', $target);
        $sourceCollection = Mage::getModel('inventory/assignment')->getCollection()
            ->addFieldToFilter('admin_id', $adminId)
            ->addFieldToFilter('warehouse_id', $source);
        $result = false;
        if($targetCollection->getSize() && $sourceCollection->getSize()){
            if($targetCollection->getFirstItem()->getCanTransfer() && $sourceCollection->getFirstItem()->getCanTransfer()){
                $result = true;
            }
        }
        return $result;
    }

    public function importProduct($data) {
        if (count($data)) {
            Mage::getModel('admin/session')->setData('stocktransfering_product_import', $data);
        }
    }

    public function getStatusOptions() {
        return array(
            array(
                'value' => 1,
                'label' => 'Pending'
            ),
            array(
                'value' => 2,
                'label' => 'Proccessing'
            ),
            array(
                'value' => 3,
                'label' => 'Complete'
            ),
            array(
                'value' => 4,
                'label' => 'Cancel'
            )
        );
    }

    public function getTypeOptions() {
        return array(
            array(
                'value' => 1,
                'label' => 'Stock Issuing'
            ),
            array(
                'value' => 2,
                'label' => 'Stock Receiving'
            )
        );
    }

    public function autoCreateStockReceving($id, $data, $createAll = false) {
        if ($id) {
            $receivingModel = Mage::getModel('inventory/stockreceiving');
            $receivingProductModel = Mage::getModel('inventory/stockreceivingproduct');
            $transferModel = Mage::getModel('inventory/stocktransfering')->load($id);
            $transferProductModel = Mage::getModel('inventory/stocktransferingproduct')
                ->getCollection()
                ->addFieldToFilter('transfer_stock_id', $id);
            $total_product = array();
            $warehouse_from = $transferModel->getWarehouseFromId();
            $warehouse_to = $transferModel->getWarehouseToId();
            $warehourseSource = Mage::getModel('inventory/warehouse')->load($warehouse_from);
            $warehourseTarget = Mage::getModel('inventory/warehouse')->load($warehouse_to);
            if($warehourseSource->getName())
                $data['warehouse_from_name'] = $warehourseSource->getName();
            if($warehourseTarget->getName())
                $data['warehouse_to_name'] = $warehourseTarget->getName();
            if($data['stockreceiving_created_at']){
                $created_at = $data['stockreceiving_created_at'];
            }else{
                $created_at = now();
            }
            $reference_invoice_number = $data['reference_invoice_number_receiving'];
            $receivingModel->setData($data);
            $receivingModel->setType(1)
                ->setWarehouseIdFrom($warehouse_from)                              
                ->setWarehouseIdTo($warehouse_to)                
                ->setWarehouseFromName($warehourseSource->getName())  
                ->setWarehouseToName($warehourseTarget->getName())
                ->setStatus(3)
                ->setReferenceInvoiceNumber($reference_invoice_number)
                ->setReferenceId($id)
                ->setCreatedAt($created_at);
            $receivingModel->save();

            foreach ($transferProductModel as $item) {
                $receivingProductModel = Mage::getModel('inventory/stockreceivingproduct');
                $product_id = $item->getProductId();
                $product_name = Mage::getModel('catalog/product')->load($product_id)->getName();
                $product_sku = Mage::getModel('catalog/product')->load($product_id)->getSku();
                $qty = $item->getQtyReceive();
                if($createAll)
                    $qty = $item->getQtyTransfer();
                
                //check transfer unWarehouse
                                        
                if($warehourseSource->getIsUnwarehouse()=='1'){
                    $qty_unwarehouse = Mage::getModel('inventory/warehouseproduct')
                                    ->getCollection()
                                    ->addFieldToFilter('warehouse_id', $warehourseSource->getId())
                                    ->addFieldToFilter('product_id', $product_id)
                                    ->getFirstItem()
                                    ->getQty();
                    if($qty > $qty_unwarehouse)
                        $qty = $qty_unwarehouse;
                }
                if(!is_numeric($qty) || $qty < 0)
                    $qty = 0;
                array_push($total_product,$qty);
                $stockReceivingId = $receivingModel->getId();
                $warehouseProduct = Mage::getModel('inventory/warehouseproduct')->getCollection()
                    ->addFieldToFilter('product_id', $product_id)
                    ->addFieldToFilter('warehouse_id', $warehouse_to);
                if (count($warehouseProduct)) {
                    $warehouseQty = $warehouseProduct->getFirstItem()->getQty();
					$warehouseQtyAvailable = $warehouseProduct->getFirstItem()->getQtyAvailable();
                    $warehouseQtyAfter = (int) $warehouseQty + (int) $qty;
					$warehouseQtyAvailableAfter = (int) $warehouseQtyAvailable + (int) $qty;
                    $warehouseProduct->getFirstItem()->setQty($warehouseQtyAfter)
													->setQtyAvailable($warehouseQtyAvailableAfter)
													->save();
                } else {
                    Mage::getModel('inventory/warehouseproduct')
                        ->setProductId($product_id)
                        ->setWarehouseId($warehouse_to)
                        ->setQty((int)$qty)
						->setQtyAvailable((int)$qty)
                        ->save();
                }
                $receivingProductModel
                    ->setStockReceivingId($stockReceivingId)
                    ->setProductId($product_id)
                    ->setProductName($product_name)
                    ->setProductSku($product_sku)
                    ->setQty($qty)
                    ->setId(null)
                    ->save();
                Mage::getModel('inventory/stocktransferingproduct')
                ->getCollection()
                ->addFieldToFilter('transfer_stock_id', $id)
                ->addFieldToFilter('product_id',$product_id)
                ->getFirstItem()
                ->setQtyReceive($qty)
                ->save();
                
            }
            $receivingModel->setTotalProducts((int) array_sum($total_product))->save();
            $transferModel->setStatus(3)->save();
            return;
        } else {
            return;
        }
    }

    public function createStockIssuing($source, $target, $data, $referenceId, $type = null) {
        if (is_null($type))
            $type = 'qty_transfer';
        $issuing = Mage::getModel('inventory/stockissuing');
        $issuing->setData($data);
        $warehouseFromName = '';
        $warehouseToName = '';
        if(isset($source))
            $warehouseFromName = Mage::getModel('inventory/warehouse')->load($source)->getName();
        if(isset($target))
            $warehouseToName = Mage::getModel('inventory/warehouse')->load($target)->getName();
        $issuing->setType(1)
            ->setWarehouseIdFrom($source)
            ->setWarehouseFromName($warehouseFromName)            
            ->setWarehouseIdTo($target)
            ->setWarehouseToName($warehouseToName)
            ->setReferenceId($referenceId)
            ->setComment($data['comment'])
            ->setId(null);
        if(isset($data['reference_invoice_number_issuing']))
            $issuing->setReferenceInvoiceNumber($data['reference_invoice_number_issuing']);
        if (isset($data['stockissuing_created_at']))
            $issuing->setCreatedAt($data['stockissuing_created_at']);
        else
            $issuing->setCreatedAt(now());
        try {
            $issuing->save();
        } catch (Exception $e) {
            
        }
        $totalProducts = 0;

        if (isset($data['stocktransfering_products'])) {
            $issuingProducts = array();
            parse_str(urldecode($data['stocktransfering_products']), $issuingProducts);
            foreach ($issuingProducts as $pId => $enCoded) {
                $product = Mage::getModel('catalog/product')->load($pId);
                $codeArr = array();
                parse_str(base64_decode($enCoded), $codeArr);
                $productsItem = Mage::getModel('inventory/stockissuingproduct')
                    ->getCollection()
                    ->addFieldToFilter('stock_issuing_id', $issuing->getId())
                    ->addFieldToFilter('product_id', $pId)
                    ->getFirstItem();
                
                if (!$productsItem->getId()) {
                    $qty_warehouse = Mage::getModel('inventory/warehouseproduct')
                        ->getCollection()
                        ->addFieldToFilter('warehouse_id', $source)
                        ->addFieldToFilter('product_id', $pId)
                        ->getFirstItem()
                        ->getQty();
                    $qtySave = (int)$codeArr[$type];
                    if((int)$qty_warehouse < (int)$codeArr[$type]){
                        $qtySave = (int)$qty_warehouse;
                    }
                    if(!is_numeric($qtySave) || $qtySave < 0)
                        $qtySave = 0;
                    try{
                        $productsItem->setStockIssuingId($issuing->getId())
                            ->setProductId($pId)
                            ->setProductName($product->getName())
                            ->setProductSku($product->getSku())
                            ->setQty($qtySave)
                            ->save();

                        $totalProducts += $qtySave;
                        $unWarehouse = $this->getUnWarehouse();
                        if($unWarehouse && $issuing->getWarehouseIdFrom() != $unWarehouse->getId()){
                            $warehouseproduct = Mage::getModel('inventory/warehouseproduct')
                                ->getCollection()
                                ->addFieldToFilter('warehouse_id', $unWarehouse->getId())
                                ->addFieldToFilter('product_id', $pId)
                                ->getFirstItem()
                                ;
                            if($warehouseproduct->getId()){
                                $warehouseproduct->setQty($warehouseproduct->getQty() + $qtySave)
												->setQtyAvailable($warehouseproduct->getQtyAvailable() + $qtySave)
												->save();
                            }else{
                                $warehouseproduct->setWarehouseId($unWarehouse->getId())
                                        ->setProductId($pId)
                                        ->setQty($qtySave)
										->setQtyAvailable($qtySave)
                                        ->save();
                            }
                        }
                    }  catch (Exception $e){
                        Mage::getSingleton('adminhtml/session')->addError(
                            $e->getMessage()
                        );  
                    }
                }
                
            }
        }
        $issuing->setTotalProducts($totalProducts)->save();
    }
    
    public function getUnWarehouse(){
        $warehouseCollection = Mage::getModel('inventory/warehouse')
                ->getCollection()
                ->addFieldToFilter('is_unwarehouse',array('eq'=>'1'))
                ;
        if($warehouseCollection->getSize())
            return $warehouseCollection->getFirstItem();
        return null;
    }
    
    public function autoCreateReturnReceiving($id,$data){
        if($id){
            $receivingModel = Mage::getModel('inventory/stockreceiving');
            $receivingProductModel = Mage::getModel('inventory/stockreceivingproduct');
            $transferModel = Mage::getModel('inventory/stocktransfering')->load($id);
            $warehouse_from = $transferModel->getWarehouseFromId();
            $warehouse_to = $transferModel->getWarehouseToId();
            $total_product = array();
            $comment = 'Return from transfer No.'.$id;
            $receivingModel->setType(1)
                ->setWarehouseIdFrom($warehouse_to)
                ->setWarehouseIdTo($warehouse_from)
                ->setWarehouseToName(Mage::getModel('inventory/warehouse')->load($warehouse_from)->getName())
                ->setWarehouseFromName(Mage::getModel('inventory/warehouse')->load($warehouse_to)->getName())  
                ->setStatus(3)
                ->setReferenceId($id)
                ->setComment($comment)
                ->setCreatedAt(now())
                ->save();
            foreach($data as $pId=>$qty){
                array_push($total_product,$qty);
                $product_name = Mage::getModel('catalog/product')->load($pId)->getName();
                $product_sku = Mage::getModel('catalog/product')->load($pId)->getSku();
                $stockReceivingId = $receivingModel->getId();
                $warehouseProduct = Mage::getModel('inventory/warehouseproduct')->getCollection()
                    ->addFieldToFilter('product_id', $pId)
                    ->addFieldToFilter('warehouse_id', $warehouse_from);
                if (count($warehouseProduct)) {
                    $warehouseQty = $warehouseProduct->getFirstItem()->getQty();
                    $warehouseQtyAfter = (int) $warehouseQty + (int) $qty;
					$warehouseQtyAvailable = $warehouseProduct->getFirstItem()->getQtyAvailable();
                    $warehouseQtyAvailableAfter = (int) $warehouseQtyAvailable + (int) $qty;
                    $warehouseProduct->getFirstItem()->setQty($warehouseQtyAfter)
													->setQtyAvailable($warehouseQtyAvailableAfter)
													->save();
                } else {
                    Mage::getModel('inventory/warehouseproduct')
                        ->setProductId($pId)
                        ->setWarehouseId($warehouse_from)
                        ->setQty((int)$qty)
						->setQtyAvailable((int)$qty)
                        ->save();
                }
                $receivingProductModel
                    ->setStockReceivingId($stockReceivingId)
                    ->setProductId($pId)
                    ->setProductName($product_name)
                    ->setProductSku($product_sku)
                    ->setQty($qty)
                    ->setId(null)
                    ->save();
            }
            $receivingModel->setTotalProducts((int) array_sum($total_product))->save();
        }
    }

}

?>
