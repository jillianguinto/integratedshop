<?php

class Magestore_Inventory_Adminhtml_ShipmentController extends Mage_Adminhtml_Controller_action {

    public function checkavailablebyeventAction() {
        $warehouseId = $this->getRequest()->getParam('warehouse_id');
        $productId = $this->getRequest()->getParam('product_id');
        $qty = $this->getRequest()->getParam('qty');
        $orderItemId = $this->getRequest()->getParam('order_item_id');
        $orderId = $this->getRequest()->getParam('order_id');
        $totalQtyOfProductRequest = $this->getRequest()->getParam('total_qty');
        $availableProduct = Mage::helper('inventory/warehouse')
                ->checkWarehouseAvailableProduct($warehouseId, $productId, $totalQtyOfProductRequest);
        if ($availableProduct == true || $qty == 0) {
            $this->getResponse()->setBody("available");
        } else {
            $checkWaittingForTransfer = Mage::helper('inventory/inventoryshipment')->checkOrderItemWaittingFortransfer($orderItemId, $productId, $orderId, $warehouseId);
            if ($checkWaittingForTransfer == true) {
                $this->getResponse()->setBody("waitting");
            } else {
                $this->getResponse()->setBody("notavailable");
            }
        }
    }

    public function checkavailableAction() {
        $warehouseId = $this->getRequest()->getParam('warehouse_id');
        $productId = $this->getRequest()->getParam('product_id');
        $qty = $this->getRequest()->getParam('qty');
        $orderItemId = $this->getRequest()->getParam('order_item_id');
        $orderId = $this->getRequest()->getParam('order_id');

        /* --------------------------------------------------------------------------- */
        $order = Mage::getModel('sales/order')->load($orderId);
        $items = $order->getAllItems();
        $_totalData = $order->getData();
        $_grand = $_totalData['grand_total'];
        $arrProduct = array();
        $checDulicateProductInOrder = false;
        $totalQtyOfProduct = 0;
        foreach ($items as $itemId => $item) {
            $_orderItemId = $item->getId();
            $productItemSku = $item->getSku();
            $productItemId = Mage::getModel("catalog/product")->getIdBySku($productItemSku);
            if (in_array($productItemId, $arrProduct) && $productId == $productItemId) {
                $checDulicateProductInOrder = true;
                break;
            }
            $arrProduct[$_orderItemId] = $productItemId;
        }

        /* ------------------------------------------------------------------------------ */
        if ($checDulicateProductInOrder == false) {
            //neu khong co su trung lap sku trong shipment (lien quan toi group product           
            $availableProduct = Mage::helper('inventory/warehouse')
                    ->checkWarehouseAvailableProduct($warehouseId, $productId, $qty);
            if ($availableProduct == true || $qty == 0) {
                $this->getResponse()->setBody("available");
            } else {
                $checkWaittingForTransfer = Mage::helper('inventory/inventoryshipment')->checkOrderItemWaittingFortransfer($orderItemId, $productId, $orderId, $warehouseId);
                if ($checkWaittingForTransfer == true) {
                    $this->getResponse()->setBody("waitting");
                } else {
                    $this->getResponse()->setBody("notavailable");
                }
            }
        } else { //neu ton tai dulicate product
            $ArrQty = array();
            if (Mage::getSingleton('core/session')->getArrayQtyOfProduct()) {
                $ArrQty = Mage::getSingleton('core/session')->getArrayQtyOfProduct();
                $ArrQty[$productId][$warehouseId][$orderItemId] = $qty;
                Mage::getSingleton('core/session')->setArrayQtyOfProduct($ArrQty);
            } else {
                $ArrQty = array($productId => array($warehouseId => array($orderItemId => $qty)));
                Mage::getSingleton('core/session')->setArrayQtyOfProduct($ArrQty);
            }
            $ArrQty = Mage::getSingleton('core/session')->getArrayQtyOfProduct();
            $minQty = 0;
            ksort($ArrQty[$productId][$warehouseId]);
            foreach ($ArrQty[$productId][$warehouseId] as $arrOrderItemId => $qtyOrderItem) {
                $minQty += $qtyOrderItem;
                if ($arrOrderItemId == $orderItemId) {
                    //break;
                }
            }
            $availableProduct = Mage::helper('inventory/warehouse')
                    ->checkWarehouseAvailableProduct($warehouseId, $productId, $minQty);
            if ($availableProduct == true || $qty == 0) {
                $this->getResponse()->setBody("available");
            } else {
                $checkWaittingForTransfer = Mage::helper('inventory/inventoryshipment')->checkOrderItemWaittingFortransfer($orderItemId, $productId, $orderId, $warehouseId);
                if ($checkWaittingForTransfer == true) {
                    $this->getResponse()->setBody("waitting");
                } else {
                    $this->getResponse()->setBody("notavailable");
                }
            }
        }
        /* ------------------------------------------------------------------------------ */
    }

    public function prepareneedtransferAction() {
        $block = $this->getLayout()->createBlock('adminhtml/template')->setTemplate('inventory/shipment/needtransferproduct.phtml');
        echo $block->toHtml();
    }

    public function transferproductAction() {
        $data = $this->getRequest()->getParams();

        try {
            $warehouseFromeName = Mage::helper('inventory/warehouse')->getWarehouseNameByWarehouseId($data['warehouse_from_id']);
            $warehouseToName = Mage::helper('inventory/warehouse')->getWarehouseNameByWarehouseId($data['warehouse_id']);
            $qtyProductInWarehouse = Mage::helper('inventory/warehouse')->getQtyOneProductInWarehouse($data['warehouse_from_id'], $data['product_id']);
            if ($data['qty'] > $qtyProductInWarehouse) {
                //new qty cua product trong warehouse it hon so product muon transfer
                $data['qty'] = $qtyProductInWarehouse;
            }
            $admin = Mage::getModel('admin/session')->getUser()->getUsername();
            $stockTransferingModel = Mage::getModel('inventory/stocktransfering');
            $stockTransferingModel->setWarehouseFromId($data['warehouse_from_id'])
                    ->setWarehouseFromName($warehouseFromeName)
                    ->setWarehouseToId($data['warehouse_id'])
                    ->setWarehouseToName($warehouseToName)
                    ->setTotalProducts($data['qty'])
                    ->setCreateAt(date("Y-m-d"))
                    ->setCreateBy($admin)
                    ->setReason('Need transfering to ship!')
                    ->setStatus(1)
                    ->setType(2)
                    ->save();
            ////vi luc truoc lam nguoc nen bay gio chi thay doi dc save vao db
            //cac bien van bi gan nguoc gia tri

            $transferstockHistory = Mage::getModel('inventory/transferstockhistory');
            $transferstockHistoryContent = Mage::getModel('inventory/transferstockhistorycontent');
            $transferstockHistory->setData('transfer_stock_id', $stockTransferingModel->getId())
                    ->setData('time_stamp', now())
                    ->setData('create_by', $admin)
                    ->save();
            $transferstockHistoryContent->setData('transfer_stock_history_id', $transferstockHistory->getId())
                    ->setData('field_name', $admin . ' created this stock transfering!')
                    ->save();

            $productName = Mage::helper('inventory')->getProductNameByProductId($data['product_id']);
            $productSku = Mage::helper('inventory')->getProductSkuByProductId($data['product_id']);
            $stockTransferingProductModel = Mage::getModel('inventory/stocktransferingproduct');
            $stockTransferingProductModel->setProductId($data['product_id'])
                    ->setProductName($productName)
                    ->setProductSku($productSku)
                    ->setTransferStockId($stockTransferingModel->getId())
                    ->setQtyTransfer(0)
                    ->setQtyRequest($data['qty'])
                    ->setQtyReceive(0)
                    ->save();

            $shipmentTransferModel = Mage::getModel('inventory/inventoryshipmenttransfer');
            $shipmentTransferModel->setItemId($data['item_id'])
                    ->setProductId($data['product_id'])
                    ->setOrderId($data['order_id'])
                    ->setWarehouseId($data['warehouse_id'])
                    ->setWarehouseName(Mage::helper('inventory/warehouse')->getWarehouseNameByWarehouseid($data['warehouse_id']))
                    ->setQtyNeedTransfer($data['qty'])
                    ->setTransferStockId($stockTransferingModel->getId())
                    ->setStatus(1)
                    ->save();
            $this->getResponse()->setBody("success");
            return;
        } catch (Exception $e) {
            $this->getResponse()->setBody("fail");
            return;
        }
    }

    public function noticepurchaseAction() {
        $data = $this->getRequest()->getParams();
        $url = Mage::helper("adminhtml")->getUrl('adminhtml/sales_order/view', array('order_id' => $data['order_id']));
        $productName = Mage::getModel('catalog/product')->load($data['product_id'])->getName();
        $description = "We are lacking of " . $data['qtyneedpurchase'] . " of <b>($productName )</b> item(s) to ship for an " . "<a href='" . $url . "' target='_blank' > order </a>. Please replenish stock by purchasing at least (" . $data['qtyneedpurchase'] . ") more items of this product.";
        try {
            $inventoryNoticeModel = Mage::getModel('inventory/notice')
                            ->setNoticeDate(date('Y-m-d'))
                            ->setDescription($description)
                            ->setComment($data['notice_comment'])->save();
            $this->getResponse()->setBody("success");
            return;
        } catch (Exception $e) {
            $this->getResponse()->setBody("fail");
            return;
        }
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('inventory');
    }

    public function savecreditmemoAction() {
        Zend_debug::dump($this->getRequest()->getPost());
        die('123');
    }

    public function requestProductAction() {
        $data = $this->getRequest()->getParams();
        //request data
        $requestData = array();
        $model = Mage::getModel('inventory/requeststock');
        $requestData['from_id'] = $data['warehouse_from_id'];
        $requestData['from_name'] = Mage::helper('inventory/warehouse')->getWarehouseNameByWarehouseId($data['warehouse_from_id']);
        $requestData['to_id'] = $data['warehouse_id'];
        $requestData['to_name'] = Mage::helper('inventory/warehouse')->getWarehouseNameByWarehouseId($data['warehouse_id']);
        $qtyProductInWarehouse = Mage::helper('inventory/warehouse')->getQtyOneProductInWarehouse($data['warehouse_from_id'], $data['product_id']);
        if ($data['qty'] > $qtyProductInWarehouse) {
            //new qty cua product trong warehouse it hon so product muon transfer
            $data['qty'] = $qtyProductInWarehouse;
        }
        $admin = Mage::getModel('admin/session')->getUser()->getUsername();
        $requestData['created_by'] = $admin;
        $now = now();
        $requestData['created_at'] = $now;
        $requestData['total_products'] = $data['qty'];
        $model->setData($requestData);
        //create send transaction data
        $transactionSendModel = Mage::getModel('inventory/transaction');
        $transactionSendData = array();
        $transactionSendData['type'] = '1';
        $transactionSendData['from_id'] = $requestData['from_id'];
        $transactionSendData['from_name'] = $requestData['from_name'];
        $transactionSendData['to_id'] = $requestData['to_id'];
        $transactionSendData['to_name'] = $requestData['to_name'];
        $transactionSendData['created_at'] = $requestData['created_at'];
        $transactionSendData['created_by'] = $requestData['created_by'];
        $transactionSendData['reason'] = Mage::helper('inventory')->__('Request for shipment');
        $transactionSendData['total_products'] = -$data['qty'];
        $transactionSendModel->addData($transactionSendData);
        //create receive transaction data
        $transactionReceiveModel = Mage::getModel('inventory/transaction');
        $transactionReceiveData = array();
        $transactionReceiveData['type'] = '2';
        $transactionReceiveData['from_id'] = $requestData['from_id'];
        $transactionReceiveData['from_name'] = $requestData['from_name'];
        $transactionReceiveData['to_id'] = $requestData['to_id'];
        $transactionReceiveData['to_name'] = $requestData['to_name'];
        $transactionReceiveData['created_at'] = $requestData['created_at'];
        $transactionReceiveData['created_by'] = $requestData['created_by'];
        $transactionReceiveData['reason'] = Mage::helper('inventory')->__('Request for shipment');
        $transactionReceiveData['total_products'] = $data['qty'];
        $transactionReceiveModel->addData($transactionReceiveData);
        try {
            $model->save();
            $transactionSendModel->save();
            $transactionReceiveModel->save();
            if ($model->getId()) {
                $transactionSendModel
                        ->setRequestStockId($model->getId())
                        ->save();
                $transactionReceiveModel
                        ->setRequestStockId($model->getId())
                        ->save();
                ;
            }
            //save products
            if (isset($data['qty'])) {
                $product = Mage::getModel('catalog/product')->load($data['product_id']);
                //save products to sendstock product table
                Mage::getModel('inventory/requeststockproduct')
                        ->setRequestStockId($model->getId())
                        ->setProductId($data['product_id'])
                        ->setProductSku($product->getSku())
                        ->setProductName($product->getName())
                        ->setQty(($data['qty']))
                        ->save()
                ;
                //save products to transaction product table for send transaction
                Mage::getModel('inventory/transactionproduct')
                        ->setTransactionId($transactionSendModel->getId())
                        ->setProductId($data['product_id'])
                        ->setProductSku($product->getSku())
                        ->setProductName($product->getName())
                        ->setQty(-($data['qty']))
                        ->save()
                ;
                //save products to transaction product table for receive transaction
                Mage::getModel('inventory/transactionproduct')
                        ->setTransactionId($transactionReceiveModel->getId())
                        ->setProductId($data['product_id'])
                        ->setProductSku($product->getSku())
                        ->setProductName($product->getName())
                        ->setQty(($data['qty']))
                        ->save()
                ;

                //Recalculate products for sending warehouse
                $send_warehouse_products = Mage::getModel('inventory/warehouseproduct')
                        ->getCollection()
                        ->addFieldToFilter('warehouse_id', $requestData['from_id'])
                        ->addFieldToFilter('product_id', $data['product_id'])
                        ->getFirstItem();
                $new_send_warehouse_qty = $send_warehouse_products->getQty() - $data['qty'];
                $new_send_warehouse_qty_available = $send_warehouse_products->getQtyAvailable() - $data['qty'];
                $send_warehouse_products->setQty($new_send_warehouse_qty)
                        ->setQtyAvailable($new_send_warehouse_qty_available)
                        ->save();

                //Recalculate products for receiving warehouse
                $receive_warehouse_products = Mage::getModel('inventory/warehouseproduct')
                        ->getCollection()
                        ->addFieldToFilter('warehouse_id', $requestData['to_id'])
                        ->addFieldToFilter('product_id', $data['product_id'])
                        ->getFirstItem();
                if ($receive_warehouse_products->getId()) {
                    $new_receive_warehouse_qty = $receive_warehouse_products->getQty() + $data['qty'];
                    $new_receive_warehouse_qty_available = $receive_warehouse_products->getQtyAvailable() + $data['qty'];
                    $receive_warehouse_products
                            ->setQty($new_receive_warehouse_qty)
                            ->setQtyAvailable($new_receive_warehouse_qty_available)
                            ->save();
                } else {
                    Mage::getModel('inventory/warehouseproduct')
                            ->setWarehouseId($requestData['to_id'])
                            ->setProductId($data['product_id'])
                            ->setQty($data['qty'])
                            ->setQtyAvailable($data['qty'])
                            ->save();
                }
                //send email to admin of receive warehouse
                if (Mage::getStoreConfig('inventory/transaction/transaction_notice') == 1) {
                    $stockName = "Send stock No." . $model->getId();
                    if ($data['to_id'] != '' || $data['to_id'] != '1') {
                        $warehouseTarget = Mage::getModel('inventory/warehouse')->load($data['to_id']);
                        if ($warehouseTarget && !$warehouseTarget->getIsUnwarehouse()) {
                            Mage::helper('inventory/email')->sendSendstockEmail($warehouseTarget, $model->getId(), 1, $stockName);
                        }
                    }
                }
                $this->getResponse()->setBody("success");
                return;
            }
        } catch (Exception $e) {
            $this->getResponse()->setBody("fail");
            return;
        }
    }

}
