<?php

class Magestore_Inventory_Adminhtml_SendstockController extends Mage_Adminhtml_Controller_action {

    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function transferDataAction() {
        $admin = Mage::getModel('admin/session')->getUser()->getUsername();
        //Transaction type sending
        $stockIssuingCollection = Mage::getModel('inventory/stockissuing')->getCollection();
        foreach ($stockIssuingCollection as $stockIssuing) {
            //transaction model
            $model = Mage::getModel('inventory/transaction');
            $transactionData = array();
            //issuing type
            $type = $stockIssuing->getType();
            //transaction send stock type
            if ($type == 1) {
                $transactionData['type'] = '1';
                $transactionData['from_id'] = $stockIssuing->getWarehouseIdFrom();
                $transactionData['from_name'] = $stockIssuing->getWarehouseFromName();
                $transactionData['to_id'] = $stockIssuing->getWarehouseIdTo();
                $transactionData['to_name'] = $stockIssuing->getWarehouseToName();
                $transactionData['created_at'] = $stockIssuing->getCreatedAt();
                $transactionData['created_by'] = $admin;
                $transactionData['total_products'] = -$stockIssuing->getTotalProducts();
                $transactionData['reason'] = $stockIssuing->getComment();
            }
            // transaction send stock type with others
            else if ($type == 4) {
                $transactionData['type'] = '1';
                $transactionData['from_id'] = $stockIssuing->getWarehouseIdFrom();
                $transactionData['from_name'] = $stockIssuing->getWarehouseFromName();
                $transactionData['to_id'] = '';
                $transactionData['to_name'] = 'Others';
                $transactionData['created_at'] = $stockIssuing->getCreatedAt();
                $transactionData['created_by'] = $admin;
                $transactionData['total_products'] = -$stockIssuing->getTotalProducts();
                $transactionData['reason'] = $stockIssuing->getComment();
            }
            //transaction customer order type
            else if ($type == 2) {
                $transactionData['type'] = '5';
                $transactionData['from_id'] = $stockIssuing->getWarehouseIdFrom();
                $transactionData['from_name'] = $stockIssuing->getWarehouseFromName();
                $transactionData['created_at'] = $stockIssuing->getCreatedAt();
                $transactionData['created_by'] = $admin;
                $transactionData['total_products'] = -$stockIssuing->getTotalProducts();
                $transactionData['reason'] = $stockIssuing->getComment();
            }
            //transaction return stock type
            else if ($type == 3) {
                $transactionData['type'] = '4';
                $transactionData['from_id'] = $stockIssuing->getWarehouseIdFrom();
                $transactionData['from_name'] = $stockIssuing->getWarehouseFromName();
                $transactionData['created_at'] = $stockIssuing->getCreatedAt();
                $transactionData['created_by'] = $admin;
                $transactionData['total_products'] = -$stockIssuing->getTotalProducts();
                $transactionData['reason'] = $stockIssuing->getComment();
            }
            $model->addData($transactionData)->save();

            //save products for transaction
            $stockIssuingProducts = Mage::getModel('inventory/stockissuingproduct')
                    ->getCollection()
                    ->addFieldToFilter('stock_issuing_id', $stockIssuing->getId());
            foreach ($stockIssuingProducts as $issuingProduct) {
                Mage::getModel('inventory/transactionproduct')
                        ->setTransactionId($model->getId())
                        ->setProductId($issuingProduct->getProductId())
                        ->setProductName($issuingProduct->getProductName())
                        ->setProductSku($issuingProduct->getProductSku())
                        ->setQty(-$issuingProduct->getQty())
                        ->save();
            }
        }

        //Transaction for receiving
        $stockReceivingCollection = Mage::getModel('inventory/stockreceiving')->getCollection();
        foreach ($stockReceivingCollection as $stockReceiving) {
            //transaction model
            $transactionModel = Mage::getModel('inventory/transaction');
            $transactionReceivingData = array();
            //$receiving type
            $type = $stockReceiving->getType();
            //transaction receive stock type
            if ($type == 1) {
                $transactionData['type'] = '2';
                $transactionData['from_id'] = $stockReceiving->getWarehouseIdFrom();
                $transactionData['from_name'] = $stockReceiving->getWarehouseFromName();
                $transactionData['to_id'] = $stockReceiving->getWarehouseIdTo();
                $transactionData['to_name'] = $stockReceiving->getWarehouseToName();
                $transactionData['created_at'] = $stockReceiving->getCreatedAt();
                $transactionData['created_by'] = $admin;
                $transactionData['total_products'] = $stockReceiving->getTotalProducts();
                $transactionData['reason'] = $stockReceiving->getComment();
            }
            // transaction receive stock type with others
            else if ($type == 3) {
                $transactionData['type'] = '1';
                $transactionData['to_id'] = $stockReceiving->getWarehouseIdTo();
                $transactionData['to_name'] = $stockReceiving->getWarehouseToName();
                $transactionData['from_id'] = '';
                $transactionData['from_name'] = 'Others';
                $transactionData['created_at'] = $stockReceiving->getCreatedAt();
                $transactionData['created_by'] = $admin;
                $transactionData['total_products'] = $stockReceiving->getTotalProducts();
                $transactionData['reason'] = $stockReceiving->getComment();
            }
            // transaction purchase order
            else if ($type == 2) {
                $transactionData['type'] = '1';
                $transactionData['to_id'] = $stockReceiving->getWarehouseIdTo();
                $transactionData['to_name'] = $stockReceiving->getWarehouseToName();
                $transactionData['created_at'] = $stockReceiving->getCreatedAt();
                $transactionData['created_by'] = $admin;
                $transactionData['total_products'] = $stockReceiving->getTotalProducts();
                $transactionData['reason'] = $stockReceiving->getComment();
            }
            $transactionModel->addData($transactionReceivingData)->save();

            //save products for transaction
            $stockReceivingProducts = Mage::getModel('inventory/stockreceivingproduct')
                    ->getCollection()
                    ->addFieldToFilter('stock_receiving_id', $stockReceiving->getId());
            foreach ($stockReceivingProducts as $receivingProduct) {
                Mage::getModel('inventory/transactionproduct')
                        ->setTransactionId($transactionModel->getId())
                        ->setProductId($receivingProduct->getProductId())
                        ->setProductName($receivingProduct->getProductName())
                        ->setProductSku($receivingProduct->getProductSku())
                        ->setQty($receivingProduct->getQty())
                        ->save();
            }
        }
    }

    public function newAction() {
        $this->loadLayout();
        $this->_setActiveMenu('inventory/inventory');
        $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Stock Sending Manager'), Mage::helper('adminhtml')->__('Stock Sending Manager')
        );
        $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Stock Sending News'), Mage::helper('adminhtml')->__('Stock Sending News')
        );
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->_addContent($this->getLayout()->createBlock('inventory/adminhtml_sendstock_edit'))
                ->_addLeft($this->getLayout()->createBlock('inventory/adminhtml_sendstock_edit_tabs'));
        $this->renderLayout();
    }

    public function editAction() {
        $sendstock = $this->getRequest()->getParam('id');
        $model = Mage::getModel('inventory/sendstock')->load($sendstock);

        if ($model->getId() || $sendstock == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);

            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('sendstock_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('inventory/inventory');

            $this->_addBreadcrumb(
                    Mage::helper('adminhtml')->__('Stock Sending Manager'), Mage::helper('adminhtml')->__('Stock Sending Manager')
            );
            $this->_addBreadcrumb(
                    Mage::helper('adminhtml')->__('Stock Sending News'), Mage::helper('adminhtml')->__('Stock Sending News')
            );

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('inventory/adminhtml_sendstock_edit'))
                    ->_addLeft($this->getLayout()->createBlock('inventory/adminhtml_sendstock_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('inventory')->__('Item does not exist')
            );
            $this->_redirect('*/*/');
        }
    }

    public function productsAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('sendstock.edit.tab.products')
                ->setProducts($this->getRequest()->getPost('oproducts', null));
        $this->renderLayout();
        if (Mage::getModel('admin/session')->getData('sendstock_product_import'))
            Mage::getModel('admin/session')->setData('sendstock_product_import', null);
    }

    public function productsGridAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('sendstock.edit.tab.products')
                ->setProducts($this->getRequest()->getPost('oproducts', null));
        $this->renderLayout();
    }

    public function gridAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function saveAction() {
        $data = $this->getRequest()->getPost();
        if ($data) {
            //save send stock information
            $model = Mage::getModel('inventory/sendstock')->load($this->getRequest()->getParam('id'));
            if (isset($data['warehouse_source'])) {
                $data['from_id'] = $data['warehouse_source'];
            }
            if (isset($data['warehouse_target'])) {
                $data['to_id'] = $data['warehouse_target'];
            }
            $warehourseSource = Mage::getModel('inventory/warehouse')->load($data['from_id']);
            if ($data['to_id'] != 'others') {
                $warehourseTarget = Mage::getModel('inventory/warehouse')->load($data['to_id']);
                if ($warehourseTarget->getName())
                    $data['to_name'] = $warehourseTarget->getName();
            }else if ($data['to_id'] == 'others') {
                $data['to_id'] = '';
                $data['to_name'] = 'Others';
            }
            if ($warehourseSource->getName())
                $data['from_name'] = $warehourseSource->getName();
            $createdAt = date('Y-m-d', strtotime(now()));
            $data['created_at'] = $createdAt;
            $admin = Mage::getModel('admin/session')->getUser()->getUsername();
            if ($this->getRequest()->getParam('id')) {
                $data['created_by'] = $model->getData('created_by');
            } else {
                $data['created_by'] = $admin;
            }
            $data['status'] = 1;
            $model->addData($data);

            //create send transaction data
            $transactionSendModel = Mage::getModel('inventory/transaction');
            $transactionSendData = array();
            $transactionSendData['type'] = '1';
            $transactionSendData['from_id'] = $data['from_id'];
            $transactionSendData['from_name'] = $data['from_name'];
            $transactionSendData['to_id'] = $data['to_id'];
            $transactionSendData['to_name'] = $data['to_name'];
            $transactionSendData['created_at'] = $data['created_at'];
            $transactionSendData['created_by'] = $data['created_by'];
            $transactionSendData['reason'] = $data['reason'];
            $transactionSendModel->addData($transactionSendData);

            //create receive transaction data
            $transactionReceiveModel = Mage::getModel('inventory/transaction');
            if ($data['to_id'] != '') {
                $transactionReceiveData = array();
                $transactionReceiveData['type'] = '2';
                $transactionReceiveData['from_id'] = $data['from_id'];
                $transactionReceiveData['from_name'] = $data['from_name'];
                $transactionReceiveData['to_id'] = $data['to_id'];
                $transactionReceiveData['to_name'] = $data['to_name'];
                $transactionReceiveData['created_at'] = $data['created_at'];
                $transactionReceiveData['created_by'] = $data['created_by'];
                $transactionReceiveData['reason'] = $data['reason'];
                $transactionReceiveModel->addData($transactionReceiveData);
            }

            try {
                //save data
                $model->save();
                $transactionSendModel->save();
                $transactionReceiveModel->save();
                //save products
                if (isset($data['sendstock_products'])) {
                    $sendstockProducts = array();
                    $total = array();
                    parse_str(urldecode($data['sendstock_products']), $sendstockProducts);
                    if (count($sendstockProducts)) {
                        foreach ($sendstockProducts as $pId => $enCoded) {
                            $product = Mage::getModel('catalog/product')->load($pId);
                            $codeArr = array();
                            $qty = 0;
                            parse_str(base64_decode($enCoded), $codeArr);
                            $send_warehouse_products = Mage::getModel('inventory/warehouseproduct')
                                    ->getCollection()
                                    ->addFieldToFilter('warehouse_id', $data['from_id'])
                                    ->addFieldToFilter('product_id', $pId)
                                    ->getFirstItem();
                            if (!empty($codeArr['qty'])) {
                                if ((int) $codeArr['qty'] > (int) $send_warehouse_products->getQty()) {
                                    $qty = $send_warehouse_products->getQty();
                                } else {
                                    $qty = $codeArr['qty'];
                                }
                                $total[] = $qty;
                            }
                            //save products to sendstock product table
                            Mage::getModel('inventory/sendstockproduct')
                                    ->setSendStockId($model->getId())
                                    ->setProductId($pId)
                                    ->setProductSku($product->getSku())
                                    ->setProductName($product->getName())
                                    ->setQty((-$qty))
                                    ->save()
                            ;
                            //save products to transaction product table for send transaction
                            Mage::getModel('inventory/transactionproduct')
                                    ->setTransactionId($transactionSendModel->getId())
                                    ->setProductId($pId)
                                    ->setProductSku($product->getSku())
                                    ->setProductName($product->getName())
                                    ->setQty(-$qty)
                                    ->save()
                            ;
                            //save products to transaction product table for receive transaction
                            if ($transactionReceiveModel->getId()) {
                                Mage::getModel('inventory/transactionproduct')
                                        ->setTransactionId($transactionReceiveModel->getId())
                                        ->setProductId($pId)
                                        ->setProductSku($product->getSku())
                                        ->setProductName($product->getName())
                                        ->setQty($qty)
                                        ->save()
                                ;
                            }
                            //Recalculate products for sending warehouse
                            $new_send_warehouse_qty = $send_warehouse_products->getQty() - $qty;
                            $new_send_warehouse_qty_available = $send_warehouse_products->getQtyAvailable() - $qty;
                            $send_warehouse_products->setQty($new_send_warehouse_qty)
                                    ->setQtyAvailable($new_send_warehouse_qty_available)
                                    ->save();
                            //Recalculate products for receiving warehouse
                            if ($data['to_id'] != '') {
                                $receive_warehouse_products = Mage::getModel('inventory/warehouseproduct')
                                        ->getCollection()
                                        ->addFieldToFilter('warehouse_id', $data['to_id'])
                                        ->addFieldToFilter('product_id', $pId)
                                        ->getFirstItem();
                                if ($receive_warehouse_products->getId()) {
                                    $new_receive_warehouse_qty = $receive_warehouse_products->getQty() + $qty;
                                    $new_receive_warehouse_qty_available = $receive_warehouse_products->getQtyAvailable() + $qty;
                                    $receive_warehouse_products
                                            ->setQty($new_receive_warehouse_qty)
                                            ->setQtyAvailable($new_receive_warehouse_qty_available)
                                            ->save();
                                } else {
                                    Mage::getModel('inventory/warehouseproduct')
                                            ->setWarehouseId($data['to_id'])
                                            ->setProductId($pId)
                                            ->setQty($qty)
                                            ->setQtyAvailable($qty)
                                            ->save();
                                }
                            } else {
                                $stock_item = Mage::getModel('cataloginventory/stock_item')
                                        ->getCollection()
                                        ->addFieldToFilter('product_id', $pId)
                                        ->getFirstItem();
                                $stock_item_qty = $stock_item->getQty();
                                $new_stock_qty = $stock_item_qty - $qty;
                                $stock_item->setQty($new_stock_qty)->save();
                            }
                        }
                    }
                }
                //save total products for sendstock
                $totalProducts = array_sum($total);
                $model->setTotalProducts(-$totalProducts);
                $model->save();
                //save total products and send_stock id for transaction
                $transactionSendModel
                        ->setTotalProducts(-$totalProducts)
                        ->setSendStockId($model->getId());
                $transactionSendModel->save();
                if ($data['to_id'] != '') {
                    $transactionReceiveModel
                            ->setSendStockId($model->getId())
                            ->setTotalProducts($totalProducts);
                    $transactionReceiveModel->save();
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
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('inventory')->__('Stock sending was successfully created.')
                );
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('inventoryadmin/adminhtml_sendstock/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('inventoryadmin/adminhtml_sendstock/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }

            Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('inventory')->__('Unable to save')
            );
            $this->_redirect('*/*/');
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('inventory')->__('Unable to save')
            );
            $this->_redirect('*/*/');
        }
    }

    public function checkproductAction() {
        $sendstock_products = $this->getRequest()->getPost('products');
        $checkProduct = 0;
        $next = false;
        if (isset($sendstock_products)) {
            $sendstockProducts = array();
            $sendstockProductsExplodes = explode('&', urldecode($sendstock_products));
            if (count($sendstockProductsExplodes) <= 900) {
                parse_str(urldecode($sendstock_products), $sendstockProducts);
            } else {
                foreach ($sendstockProductsExplodes as $sendstockProductsExplode) {
                    $sendstockProduct = '';
                    parse_str($sendstockProductsExplode, $sendstockProduct);
                    $sendstockProducts = $sendstockProducts + $sendstockProduct;
                }
            }
            if (count($sendstockProducts)) {
                foreach ($sendstockProducts as $pId => $enCoded) {
                    $codeArr = array();
                    parse_str(base64_decode($enCoded), $codeArr);
                    if (is_numeric($codeArr['qty']) && $codeArr['qty'] > 0) {
                        $checkProduct = 1;
                        $next = true;
                        break;
                    }
                }
            }
        }
        echo $checkProduct;
    }

    public function cancelAction() {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('inventory/sendstock')->load($id);
        $send_warehouse = $model->getToId();
        $receive_warehouse = $model->getFromId();
        try {
            //change status of send stock record
            $model->setStatus(2)->save();
            //create send transaction
            $transactionSendModel = Mage::getModel('inventory/transaction');
            $transactionSendData = array();
            $transactionSendData['type'] = '1';
            $transactionSendData['from_id'] = $model->getToId();
            $transactionSendData['from_name'] = $model->getToName();
            $transactionSendData['to_id'] = $model->getFromId();
            $transactionSendData['to_name'] = $model->getFromName();
            $transactionSendData['created_at'] = $model->getCreatedAt();
            $transactionSendData['created_by'] = $model->getCreatedBy();
            $transactionSendData['reason'] = Mage::helper('inventory')->__("Cancel Stock Sending No.'%s'", $id);
            $transactionSendData['total_products'] = $model->getTotalProducts();
            $transactionSendModel->addData($transactionSendData);
            $transactionSendModel->save();
            //create receive transaction
            $transactionReceiveModel = Mage::getModel('inventory/transaction');
            $transactionReceiveData = array();
            $transactionReceiveData['type'] = '2';
            $transactionReceiveData['from_id'] = $model->getToId();
            $transactionReceiveData['from_name'] = $model->getToName();
            $transactionReceiveData['to_id'] = $model->getFromId();
            $transactionReceiveData['to_name'] = $model->getFromName();
            $transactionReceiveData['created_at'] = $model->getCreatedAt();
            $transactionReceiveData['created_by'] = $model->getCreatedBy();
            $transactionReceiveData['reason'] = Mage::helper('inventory')->__("Cancel Stock Sending No.'%s'", $id);
            $transactionReceiveData['total_products'] = -$model->getTotalProducts();
            $transactionReceiveModel->addData($transactionReceiveData);
            $transactionReceiveModel->save();

            //recalculate qty
            $sendstockProducts = Mage::getModel('inventory/sendstockproduct')
                    ->getCollection()
                    ->addFieldToFilter('send_stock_id', $id);
            foreach ($sendstockProducts as $sendstockproduct) {
                $pId = $sendstockproduct->getProductId();
                $pSku = $sendstockproduct->getProductSku();
                $pName = $sendstockproduct->getProductName();
                //get qty of product using for transaction
                //qty is negative
                $qty = $sendstockproduct->getQty();
                //save products to transaction product table for send transaction
                Mage::getModel('inventory/transactionproduct')
                        ->setTransactionId($transactionSendModel->getId())
                        ->setProductId($pId)
                        ->setProductSku($pSku)
                        ->setProductName($pName)
                        ->setQty($qty)
                        ->save()
                ;
                //save products to transaction product table for receive transaction
                Mage::getModel('inventory/transactionproduct')
                        ->setTransactionId($transactionReceiveModel->getId())
                        ->setProductId($pId)
                        ->setProductSku($pSku)
                        ->setProductName($pName)
                        ->setQty(-$qty)
                        ->save()
                ;
                //recalculate product qty for warehouse send
                if ($send_warehouse != 0) {
                    $send_warehouse_products = Mage::getModel('inventory/warehouseproduct')
                            ->getCollection()
                            ->addFieldToFilter('warehouse_id', $send_warehouse)
                            ->addFieldToFilter('product_id', $pId)
                            ->getFirstItem();
                    $newProductsQtySend = $send_warehouse_products->getQty() + $qty;
                    $newProductsQtyAvaSend = $send_warehouse_products->getQtyAvailable() + $qty;
                    $send_warehouse_products
                            ->setQty($newProductsQtySend)
                            ->setQtyAvailable($newProductsQtyAvaSend)
                            ->save();
                } else {
                    //recalculate product qty for system if other
                    $stock_item = Mage::getModel('cataloginventory/stock_item')
                            ->getCollection()
                            ->addFieldToFilter('product_id', $pId)
                            ->getFirstItem();
                    $stock_item_qty = $stock_item->getQty();
                    $new_stock_qty = $stock_item_qty - $qty;
                    $stock_item->setQty($new_stock_qty)->save();
                }
                //recalculate product qty for warehouses receive
                $receive_warehouse_products = Mage::getModel('inventory/warehouseproduct')
                        ->getCollection()
                        ->addFieldToFilter('warehouse_id', $receive_warehouse)
                        ->addFieldToFilter('product_id', $pId)
                        ->getFirstItem();
                $newProductsQtyReceive = $receive_warehouse_products->getQty() - $qty;
                $newProductsQtyAvaReceive = $receive_warehouse_products->getQtyAvailable() - $qty;
                $receive_warehouse_products
                        ->setQty($newProductsQtyReceive)
                        ->setQtyAvailable($newProductsQtyAvaReceive)
                        ->save();
            }

            //send email to admin of receive warehouse
            if (Mage::getStoreConfig('inventory/transaction/transaction_notice') == 1) {
                if ($receive_warehouse) {
                    $warehouseTarget = Mage::getModel('inventory/warehouse')->load($receive_warehouse);
                    if ($warehouseTarget && !$warehouseTarget->getIsUnwarehouse()) {
                        $stockName = "Cancel send stock No." . $model->getId();
                        Mage::helper('inventory/email')->sendSendstockEmail($warehouseTarget, $model->getId(), 1, $stockName);
                    }
                }
            }

            Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('inventory')->__('Stock Sending was successfully canceled.')
            );
            if ($this->getRequest()->getParam('warehouse_id')) {
                $this->_redirect('inventoryadmin/adminhtml_warehouse/edit', array('id' => $this->getRequest()->getParam('warehouse_id')));
            } else {
                $this->_redirect('inventoryadmin/adminhtml_sendstock/index');
            }
            return;
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_redirect('inventoryadmin/adminhtml_sendstock/edit', array('id' => $this->getRequest()->getParam('id')));
            return;
        }

        Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('inventory')->__('Unable to cancel')
        );
        if ($this->getRequest()->getParam('warehouse_id')) {
            $this->_redirect('inventoryadmin/adminhtml_warehouse/edit', array('id' => $this->getRequest()->getParam('warehouse_id')));
        } else {
            $this->_redirect('inventoryadmin/adminhtml_sendstock/index');
        }
    }

    public function getImportCsvAction() {
        if (isset($_FILES['fileToUpload']['name']) && $_FILES['fileToUpload']['name'] != '') {
            try {
                $fileName = $_FILES['fileToUpload']['tmp_name'];
                $Object = new Varien_File_Csv();
                $dataFile = $Object->getData($fileName);
                $sendstockProduct = array();
                $sendstockProducts = array();
                $fields = array();
                $count = 0;
                $helper = Mage::helper('inventory/sendstock');
                if (count($dataFile))
                    foreach ($dataFile as $col => $row) {
                        if ($col == 0) {
                            if (count($row))
                                foreach ($row as $index => $cell)
                                    $fields[$index] = (string) $cell;
                        }elseif ($col > 0) {
                            if (count($row))
                                foreach ($row as $index => $cell) {

                                    if (isset($fields[$index])) {
                                        $sendstockProduct[$fields[$index]] = $cell;
                                    }
                                }
                            $source = $this->getRequest()->getParam('source');
                            $productId = Mage::getModel('catalog/product')->getIdBySku($sendstockProduct['SKU']);
                            $warehouseproduct = Mage::getModel('inventory/warehouseproduct')
                                    ->getCollection()
                                    ->addFieldToFilter('warehouse_id', $source)
                                    ->addFieldToFilter('product_id', $productId);
                            if ($warehouseproduct->getSize()) {
                                $sendstockProducts[] = $sendstockProduct;
                            }
                        }
                    }
                $helper->importProduct($sendstockProducts);
            } catch (Exception $e) {
                
            }
        }
    }

    public function exportCsvAction() {
        $fileName = 'send_stock.csv';
        $content = $this->getLayout()
                ->createBlock('inventory/adminhtml_sendstock_grid')
                ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction() {
        $fileName = 'send_stock.xml';
        $content = $this->getLayout()
                ->createBlock('inventory/adminhtml_sendstock_grid')
                ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

}
