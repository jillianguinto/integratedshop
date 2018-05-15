<?php

class Magestore_Inventory_Adminhtml_RequeststockController extends Mage_Adminhtml_Controller_action {

    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
            if (isset($data['warehouse_source']) && $data['warehouse_source'] != 'others') {
                $data['from_id'] = $data['warehouse_source'];
            }
            if (isset($data['warehouse_target'])) {
                $data['to_id'] = $data['warehouse_target'];
            }
            $warehourseSource = Mage::getModel('inventory/warehouse')->load($data['from_id']);
            $warehourseTarget = Mage::getModel('inventory/warehouse')->load($data['to_id']);
            if ($warehourseSource->getName() && $data['warehouse_source'] != 'others')
                $data['from_name'] = $warehourseSource->getName();
            elseif ($data['warehouse_source'] == 'others')
                $data['from_name'] = 'Others';
            if ($warehourseTarget->getName())
                $data['to_name'] = $warehourseTarget->getName();
            $admin = Mage::getModel('admin/session')->getUser()->getUsername();
            $data['created_by'] = $admin;
            $now = now();
            $data['created_at'] = $now;
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
            try {
                if ($this->getRequest()->getParam('id')) {
                    $model = Mage::getModel('inventory/requeststock')->load($this->getRequest()->getParam('id'));
                    $model->addData($data);
                    $model->save();
                } else {
                    $model = Mage::getModel('inventory/requeststock');
                    $model->setData($data);
                    $model->save();
                }
                if ($data['from_id'])
                    $transactionSendModel->setRequestStockId($model->getId())
                            ->save();
                else
                    $transactionSendModel->save();
                $transactionReceiveModel->save();
                //save product
                if (isset($data['requeststock_products'])) {
                    $requeststockProducts = array();
                    parse_str(urldecode($data['requeststock_products']), $requeststockProducts);
                    $total = array();
                    $notReceive = array();
                    $source = $data['from_id'];
                    $target = $data['to_id'];
                    if (count($requeststockProducts)) {
                        foreach ($requeststockProducts as $pId => $enCoded) {
                            $codeArr = array();
                            $qty = 0;
                            $product = Mage::getModel('catalog/product')->load($pId);
                            parse_str(base64_decode($enCoded), $codeArr);
                            $requeststockProductsItem = Mage::getModel('inventory/requeststockproduct')
                                    ->getCollection()
                                    ->addFieldToFilter('request_stock_id', $model->getId())
                                    ->addFieldToFilter('product_id', $pId)
                                    ->getFirstItem();
                            if ($requeststockProductsItem->getId()) {
                                if ($codeArr['qty_receive']) {
                                    if (!is_numeric($codeArr['qty_receive']) || $codeArr['qty_receive'] < 0)
                                        continue;
                                    $qty = (int) $codeArr['qty_receive'];
                                }elseif ($codeArr['qty_transfer']) {
                                    if (!is_numeric($codeArr['qty_transfer']) || $codeArr['qty_transfer'] < 0)
                                        continue;
                                    $qty = (int) $codeArr['qty_transfer'];
                                }
                                $requeststockProductsItem
                                        // ->setProductId($pId)
                                        ->setQtyReceive($qty)
                                        ->save();
                                array_push($total, (int) $qty);
                            }else {
                                $warehouse = Mage::getModel('inventory/warehouseproduct')
                                        ->getCollection()
                                        ->addFieldToFilter('warehouse_id', $source)
                                        ->addFieldToFilter('product_id', $pId)
                                        ->getFirstItem();
                                if (!is_numeric($codeArr['qty_request']) || (int) $codeArr['qty_request'] < 0)
                                    $codeArr['qty_request'] = 0;
                                elseif ((int) $codeArr['qty_request'] <= (int) $warehouse->getQty())
                                    $qty = (int) $codeArr['qty_request'];
                                elseif ((int) $codeArr['qty_request'] > (int) $warehouse->getQty() && $data['warehouse_source'] != 'others')
                                    $qty = (int) $warehouse->getQty();
                                elseif ((int) $codeArr['qty_request'] > (int) $warehouse->getQty() && $data['warehouse_source'] == 'others')
                                    $qty = $codeArr['qty_request'];
                                Mage::getModel('inventory/requeststockproduct')
                                        ->setProductId($pId)
                                        ->setRequestStockId($model->getId())
                                        ->setProductSku($product->getSku())
                                        ->setProductName($product->getName())
                                        ->setQty($qty)
                                        ->save();
                                $warehouseProductTarget = Mage::getModel('inventory/warehouseproduct')
                                        ->getCollection()
                                        ->addFieldToFilter('warehouse_id', $target)
                                        ->addFieldToFilter('product_id', $pId)
                                        ->getFirstItem();
                                if ($warehouseProductTarget && $warehouseProductTarget->getId()) {
                                    $qtyTarget = $warehouseProductTarget->getQty() + $qty;
                                    $qtyAvailableTarget = $warehouseProductTarget->getQtyAvailable() + $qty;
                                    $warehouseProductTarget->setQty($qtyTarget)
                                            ->setQtyAvailable($qtyAvailableTarget)
                                            ->save();
                                } else {
                                    Mage::getModel('inventory/warehouseproduct')
                                            ->setWarehouseId($target)
                                            ->setProductId($pId)
                                            ->setQty($qty)
                                            ->setQtyAvailable($qty)
                                            ->save();
                                }
                                if ($data['warehouse_source'] != 'others') {
                                    $currentQty = (int) $warehouse->getQty() - $qty;
                                    $currentQtyAvailable = (int) $warehouse->getQtyAvailable() - $qty;
                                    $warehouse->setQty($currentQty);
                                    $warehouse->setQtyAvailable($currentQtyAvailable);
                                    $warehouse->save();
                                } else {
                                    $stock_item = Mage::getModel('cataloginventory/stock_item')
                                            ->getCollection()
                                            ->addFieldToFilter('product_id', $pId)
                                            ->getFirstItem();
                                    $stock_item_qty = $stock_item->getQty();
                                    $new_stock_qty = $stock_item_qty + $qty;
                                    $stock_item->setQty($new_stock_qty)->save();
                                }
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
                                Mage::getModel('inventory/transactionproduct')
                                        ->setTransactionId($transactionReceiveModel->getId())
                                        ->setProductId($pId)
                                        ->setProductSku($product->getSku())
                                        ->setProductName($product->getName())
                                        ->setQty($qty)
                                        ->save()
                                ;
                                $qty_request = (int) $qty;
                                array_push($total, $qty_request);
                            }
                        }
                        $totalProducts = array_sum($total);
                        $model->setTotalProducts($totalProducts);
                        $model->save();
                        $transactionSendModel->setTotalProducts(-$totalProducts);
                        $transactionSendModel->save();
                        $transactionReceiveModel->setTotalProducts($totalProducts);
                        $transactionReceiveModel->save();
                    }
                    $store = Mage::app()->getStore();
                    if (Mage::getStoreConfig('inventory/transaction/transaction_notice', $store->getId())) {
                        $warehouseSource = Mage::getModel('inventory/warehouse')->load($source);
                        if ($source && !$warehouseSource->getIsUnwarehouse()) {
                            $stockName = "Request Stock";
                            Mage::helper('inventory/email')->sendSendstockEmail($warehouseSource, $model->getId(), 0, $stockName);
                        }
                    }
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('inventory')->__('Stock request was successfully created.')
                );
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('inventoryadmin/adminhtml_requeststock/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('inventoryadmin/adminhtml_requeststock/edit', array('id' => $this->getRequest()->getParam('id')));
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

    public function gridAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function newAction() {
        $this->loadLayout();
        $this->_setActiveMenu('inventory/inventory');
        $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Stock Requesting Manager'), Mage::helper('adminhtml')->__('Stock Transfering Manager')
        );
        $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Stock Requesting News'), Mage::helper('adminhtml')->__('Stock Transfering News')
        );
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->_addContent($this->getLayout()->createBlock('inventory/adminhtml_requeststock_edit'))
                ->_addLeft($this->getLayout()->createBlock('inventory/adminhtml_requeststock_edit_tabs'));
        $this->renderLayout();
    }

    public function editAction() {
        $requeststock = $this->getRequest()->getParam('id');
        $model = Mage::getModel('inventory/requeststock')->load($requeststock);
        if ($model->getId() || $requeststock == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);

            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('requeststock_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('inventory/inventory');

            $this->_addBreadcrumb(
                    Mage::helper('adminhtml')->__('Request Stock Manager'), Mage::helper('adminhtml')->__('Request Stock Manage')
            );
            $this->_addBreadcrumb(
                    Mage::helper('adminhtml')->__('Request Stock News'), Mage::helper('adminhtml')->__('Request Stock News')
            );

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('inventory/adminhtml_requeststock_edit'))
                    ->_addLeft($this->getLayout()->createBlock('inventory/adminhtml_requeststock_edit_tabs'));

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
        $this->getLayout()->getBlock('requeststock.edit.tab.products')
                ->setProducts($this->getRequest()->getPost('oproducts', null));
        $this->renderLayout();
        if (Mage::getModel('admin/session')->getData('requeststock_product_import'))
            Mage::getModel('admin/session')->setData('requeststock_product_import', null);
    }

    public function productsGridAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('requeststock.edit.tab.products')
                ->setProducts($this->getRequest()->getPost('oproducts', null));
        $this->renderLayout();
    }

    public function checkproductAction() {
        $requeststock_products = $this->getRequest()->getPost('products');
        $checkProduct = 0;
        $next = false;
        if (isset($requeststock_products)) {
            $stockrequestProducts = array();
            $stockrequestProductsExplodes = explode('&', urldecode($requeststock_products));
            if (count($stockrequestProductsExplodes) <= 900) {
                parse_str(urldecode($requeststock_products), $stockrequestProducts);
            } else {
                foreach ($stockrequestProductsExplodes as $stockrequestProductsExplode) {
                    $stockrequestProduct = '';
                    parse_str($stockrequestProductsExplode, $stockrequestProduct);
                    $stockrequestProducts = $stockrequestProducts + $stockrequestProduct;
                }
            }
            if (count($stockrequestProducts)) {
                foreach ($stockrequestProducts as $pId => $enCoded) {
                    $codeArr = array();
                    parse_str(base64_decode($enCoded), $codeArr);
                    if (is_numeric($codeArr['qty_request']) && $codeArr['qty_request'] > 0) {
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
        $model = Mage::getModel('inventory/requeststock')->load($id);
        $send_warehouse = $model->getToId();
        $receive_warehouse = $model->getFromId(); //zend_debug::dump($receive_warehouse.','.$send_warehouse);die();
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
            $transactionSendData['reason'] = Mage::helper('inventory')->__("Cancel Stock Requesting No.'%s'" . $id);
            $transactionSendData['total_products'] = -$model->getTotalProducts();
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
            $transactionReceiveData['reason'] = Mage::helper('inventory')->__("Cancel Stock Requesting No.'%s'" . $id);
            $transactionReceiveData['total_products'] = $model->getTotalProducts();
            $transactionReceiveModel->addData($transactionReceiveData);
            $transactionReceiveModel->save();

            //recalculate qty
            $requeststockProducts = Mage::getModel('inventory/requeststockproduct')
                    ->getCollection()
                    ->addFieldToFilter('request_stock_id', $id);
            foreach ($requeststockProducts as $requeststockProduct) {
                $pId = $requeststockProduct->getProductId();
                $pSku = $requeststockProduct->getProductSku();
                $pName = $requeststockProduct->getProductName();
                //get qty of product using for transaction
                //qty is negative
                $qty = $requeststockProduct->getQty();
                //save products to transaction product table for send transaction
                Mage::getModel('inventory/transactionproduct')
                        ->setTransactionId($transactionSendModel->getId())
                        ->setProductId($pId)
                        ->setProductSku($pSku)
                        ->setProductName($pName)
                        ->setQty(-$qty)
                        ->save()
                ;
                //save products to transaction product table for receive transaction
                Mage::getModel('inventory/transactionproduct')
                        ->setTransactionId($transactionReceiveModel->getId())
                        ->setProductId($pId)
                        ->setProductSku($pSku)
                        ->setProductName($pName)
                        ->setQty($qty)
                        ->save()
                ;
                //recalculate product qty for warehouse send
                if ($send_warehouse != 0) {
                    $send_warehouse_products = Mage::getModel('inventory/warehouseproduct')
                            ->getCollection()
                            ->addFieldToFilter('warehouse_id', $send_warehouse)
                            ->addFieldToFilter('product_id', $pId)
                            ->getFirstItem();
                    $newProductsQtySend = $send_warehouse_products->getQty() - $qty;
                    $newProductsQtyAvailableSend = $send_warehouse_products->getQtyAvailable() - $qty;
                    $send_warehouse_products->setQty($newProductsQtySend)
                            ->setQtyAvailable($newProductsQtyAvailableSend)
                            ->save();
                    $store = Mage::app()->getStore();
                    if (Mage::getStoreConfig('inventory/transaction/transaction_notice', $store->getId())) {
                        $warehouseTaget = Mage::getModel('inventory/warehouse')->load($send_warehouse);
                        if ($send_warehouse && !$warehouseTaget->getIsUnwarehouse()) {
                            $stockName = "Cancel Request Stock #" . Mage::helper('inventory')->getIncrementRequeststockId($id);
                            Mage::helper('inventory/email')->sendSendstockEmail($warehouseTaget, $id, 0, $stockName);
                        }
                    }
                }
                //recalculate product qty for warehouses receive
                if ($receive_warehouse) {
                    $receive_warehouse_products = Mage::getModel('inventory/warehouseproduct')
                            ->getCollection()
                            ->addFieldToFilter('warehouse_id', $receive_warehouse)
                            ->addFieldToFilter('product_id', $pId)
                            ->getFirstItem();
                    $newProductsQtyReceive = $receive_warehouse_products->getQty() + $qty;
                    $newProductsQtyAvailableReceive = $receive_warehouse_products->getQtyAvailable() + $qty;
                    $receive_warehouse_products->setQty($newProductsQtyReceive)
                            ->setQtyAvailable($newProductsQtyAvailableReceive)
                            ->save();
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
            Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('inventory')->__('Stock request was successfully canceled.')
            );
            if ($this->getRequest()->getParam('warehouse_id'))
                $this->_redirect('inventoryadmin/adminhtml_warehouse/edit', array('id' => $this->getRequest()->getParam('warehouse_id')));
            else
                $this->_redirect('inventoryadmin/adminhtml_requeststock/index');
            return;
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            if ($this->getRequest()->getParam('warehouse_id'))
                $this->_redirect('inventoryadmin/adminhtml_warehouse/edit', array('id' => $this->getRequest()->getParam('warehouse_id')));
            else
                $this->_redirect('inventoryadmin/adminhtml_requeststock/edit', array('id' => $this->getRequest()->getParam('id')));
            return;
        }

        Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('inventory')->__('Unable to cancel')
        );
        if ($this->getRequest()->getParam('warehouse_id'))
            $this->_redirect('inventoryadmin/adminhtml_warehouse/edit', array('id' => $this->getRequest()->getParam('warehouse_id')));
        else
            $this->_redirect('inventoryadmin/adminhtml_requeststock/index');
    }

    public function getImportCsvAction() {
        if (isset($_FILES['fileToUpload']['name']) && $_FILES['fileToUpload']['name'] != '') {
            try {
                $fileName = $_FILES['fileToUpload']['tmp_name'];
                $Object = new Varien_File_Csv();
                $dataFile = $Object->getData($fileName);
                $requeststockProduct = array();
                $requeststockProducts = array();
                $fields = array();
                $count = 0;
                $helper = Mage::helper('inventory/requeststock');
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
                                        $requeststockProduct[$fields[$index]] = $cell;
                                    }
                                }
                            $source = $this->getRequest()->getParam('source');
                            $productId = Mage::getModel('catalog/product')->getIdBySku($requeststockProduct['SKU']);
                            $warehouseproduct = Mage::getModel('inventory/warehouseproduct')
                                    ->getCollection()
                                    ->addFieldToFilter('warehouse_id', $source)
                                    ->addFieldToFilter('product_id', $productId);
                            if ($warehouseproduct->getSize()) {
                                $requeststockProducts[] = $requeststockProduct;
                            }
                        }
                    }
                $helper->importProduct($requeststockProducts);
            } catch (Exception $e) {
                
            }
        }
    }

    /**
     * export grid item to CSV type
     */
    public function exportCsvAction() {
        $fileName = 'stock_request.csv';
        $content = $this->getLayout()
                ->createBlock('inventory/adminhtml_requeststock_grid')
                ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction() {
        $fileName = 'stock_request.xml';
        $content = $this->getLayout()
                ->createBlock('inventory/adminhtml_requeststock_grid')
                ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

}
