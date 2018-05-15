<?php

class Magestore_Inventory_Adminhtml_StocktransferingController extends Mage_Adminhtml_Controller_action {

    public function indexAction() {
        $this->loadLayout()
            ->_setActiveMenu('inventory/stocktransfering');
        $this->renderLayout();
    }

    public function gridAction() {
        return $this->getResponse()->setBody($this->getLayout()->createBlock('inventory/adminhtml_stocktransfering_grid')->toHtml());
    }

    /**
     * Create new transfer and create issuing receipt
     */
    public function newAction() {
        $this->loadLayout();
        $this->_setActiveMenu('inventory/inventory');
        $this->_addBreadcrumb(
            Mage::helper('adminhtml')->__('Stock Transfering Manager'), Mage::helper('adminhtml')->__('Stock Transfering Manager')
        );
        $this->_addBreadcrumb(
            Mage::helper('adminhtml')->__('Stock Transfering News'), Mage::helper('adminhtml')->__('Stock Transfering News')
        );
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->_addContent($this->getLayout()->createBlock('inventory/adminhtml_stocktransfering_edit'))
            ->_addLeft($this->getLayout()->createBlock('inventory/adminhtml_stocktransfering_edit_tabs'));
        $this->renderLayout();
    }

    /**
     * Edit a transfer 
     */
    public function editAction() {
        $stocktransfering = $this->getRequest()->getParam('id');
        $model = Mage::getModel('inventory/stocktransfering')->load($stocktransfering);

        if ($model->getId() || $stocktransfering == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);

            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('stocktransfering_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('inventory/inventory');

            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Stock Transfering Manager'), Mage::helper('adminhtml')->__('Stock Transfering Manager')
            );
            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Stock Transfering News'), Mage::helper('adminhtml')->__('Stock Transfering News')
            );

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('inventory/adminhtml_stocktransfering_edit'))
                ->_addLeft($this->getLayout()->createBlock('inventory/adminhtml_stocktransfering_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('inventory')->__('Item does not exist')
            );
            $this->_redirect('*/*/');
        }
    }

    public function viewAction() {
        $stocktransfering = $this->getRequest()->getParam('id');
        $model = Mage::getModel('inventory/stocktransfering')->load($stocktransfering);

        if ($model->getId() || $stocktransfering == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);

            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('stocktransfering_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('inventory/inventory');

            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Stock Transfering Manager'), Mage::helper('adminhtml')->__('Stock Transfering Manager')
            );
            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Stock Transfering News'), Mage::helper('adminhtml')->__('Stock Transfering News')
            );

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('inventory/adminhtml_stocktransfering_view'))
                ->_addLeft($this->getLayout()->createBlock('inventory/adminhtml_stocktransfering_view_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('inventory')->__('Item does not exist')
            );
            $this->_redirect('*/*/');
        }
    }

    public function loadWarehouseAction() {
        $type = $this->getRequest()->getParam('type');
        $admin = Mage::getSingleton('admin/session')->getUser();
        $collection = Mage::helper('inventory/warehouse')->loadTransferAbleWarehouses($admin);
        if ($type == 2) {
            $collection->addFieldToFilter('is_unwarehouse', array('neq' => '1'));
        }
        $html = '';
        foreach ($collection as $warehouse) {
            $html .= '<option value="' . $warehouse->getId() . '">' . $warehouse->getName() . '</option>';
        }
        $this->getResponse()->setBody($html);
    }

    /**
     * Save transfer after create or edit
     */
    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
            $custom = $this->getRequest()->getParam('custom');
            if (isset($data['transfer_type'])) {
                $data['type'] = $data['transfer_type'];
            }
            if (isset($data['warehouse_source'])) {
                $data['warehouse_from_id'] = $data['warehouse_source'];
            }
            if (isset($data['warehouse_target'])) {
                $data['warehouse_to_id'] = $data['warehouse_target'];
            }
            $data = $this->_filterDates($data, array('stockreceiving_created_at','stockissuing_created_at'));
            $warehourseSource = Mage::getModel('inventory/warehouse')->load($data['warehouse_from_id']);
            $warehourseTarget = Mage::getModel('inventory/warehouse')->load($data['warehouse_to_id']);
            if($warehourseSource->getName())
                $data['warehouse_from_name'] = $warehourseSource->getName();
            if($warehourseTarget->getName())
                $data['warehouse_to_name'] = $warehourseTarget->getName();
            $model = Mage::getModel('inventory/stocktransfering')->load($this->getRequest()->getParam('id'));
            if ($model->getStatus() == 1) {
                $warehouseSourceId = $model->getData('warehouse_from_id');
                $warehouseTargetId = $model->getData('warehouse_to_id');
                Mage::helper('inventory/stocktransfering')->createStockIssuing($warehouseSourceId, $warehouseTargetId, $data, $model->getId());
                $model->setStatus(2)->save();
                $this->_redirect('inventoryadmin/adminhtml_warehouse/edit', array('id' => $warehouseSourceId));
                return;
            }
            $admin = Mage::getModel('admin/session')->getUser()->getUsername();
            if($this->getRequest()->getParam('id')){
                $data['create_by'] = $model->getData('create_by');
            }else{
                $model->setData('create_by',$admin);
            }
            $model->addData($data);
            if (!$model->getStatus())
                $model->setStatus(1);
            try {
                $model->save();
                
                if(!$this->getRequest()->getParam('id')){
                    $transferstockHistory = Mage::getModel('inventory/transferstockhistory');
                    $transferstockHistoryContent = Mage::getModel('inventory/transferstockhistorycontent');
                    $transferstockHistory->setData('transfer_stock_id',$model->getId())
                                         ->setData('time_stamp',now())
                                         ->setData('create_by',$admin)
                                         ->save();
                    $transferstockHistoryContent->setData('transfer_stock_history_id',$transferstockHistory->getId())
                                                ->setData('field_name',$admin.' created this stock transfering!')
                                                ->save();
                }
                //save products
                if (isset($data['stocktransfering_products'])) {
                    $stocktransferingProducts = array();
                    parse_str(urldecode($data['stocktransfering_products']), $stocktransferingProducts);
                    $total = array();
                    $notReceive = array();
                    $source = $data['warehouse_from_id'];
                    if (count($stocktransferingProducts)) {
                        $productIds = '';
                        foreach ($stocktransferingProducts as $pId => $enCoded) {
                            $codeArr = array();
                            parse_str(base64_decode($enCoded), $codeArr);
                            $stocktransferingProductsItem = Mage::getModel('inventory/stocktransferingproduct')
                                ->getCollection()
                                ->addFieldToFilter('transfer_stock_id', $model->getId())
                                ->addFieldToFilter('product_id', $pId)
                                ->getFirstItem();
                            if ($stocktransferingProductsItem->getId()) {
                                if ($codeArr['qty_receive']) {
                                    if(!is_numeric($codeArr['qty_receive']) || $codeArr['qty_receive'] < 0)
                                        continue;
                                    if ((int) $stocktransferingProductsItem->getQtyTransfer() >= (int) $codeArr['qty_receive']) {
                                        $stocktransferingProductsItem
                                            ->setProductId($pId)
                                            ->setQtyReceive($codeArr['qty_receive'])
                                            ->save();
                                        $qty_receive = (int) $codeArr['qty_receive'];
                                                                              
                                        array_push($total, $qty_receive);
                                        $qty_left = (int) $stocktransferingProductsItem->getQtyTransfer() - (int) $codeArr['qty_receive'];
                                        if ($qty_left > 0) {
                                            $notReceive[$pId] = $qty_left;
                                        }
                                    }elseif((int) $stocktransferingProductsItem->getQtyTransfer() <= (int) $codeArr['qty_receive']){
                                        $stocktransferingProductsItem
                                            ->setProductId($pId)
                                            ->setQtyReceive($stocktransferingProductsItem->getQtyTransfer())
                                            ->save();
                                        $qty_receive = (int) $stocktransferingProductsItem->getQtyTransfer();
                                        
                                        array_push($total, $qty_receive);
                                    }
                                } elseif ($codeArr['qty_transfer']) {
                                    if(!is_numeric($codeArr['qty_transfer']) || $codeArr['qty_transfer'] < 0)
                                        continue;
                                    if ((int) $stocktransferingProductsItem->getQtyRequest() >= (int) $codeArr['qty_transfer']) {
                                        $stocktransferingProductsItem
                                            ->setProductId($pId)
                                            ->setQtyTransfer($codeArr['qty_transfer']);
                                        if (isset($data['stockreceiving_created_at']) && $data['stockreceiving_created_at'] && Mage::helper('inventory/stocktransfering')->canSaveAndApply($model->getWarehouseFromId(), $model->getWarehouseToId()))
                                            $stocktransferingProductsItem->setQtyReceive($codeArr['qty_transfer']);
                                        $stocktransferingProductsItem->save();
                                        $qty_transfer = (int) $codeArr['qty_transfer'];
                                        array_push($total, $qty_transfer);
                                    }elseif((int) $stocktransferingProductsItem->getQtyRequest() <= (int) $codeArr['qty_transfer']){
                                        $stocktransferingProductsItem
                                            ->setProductId($pId)
                                            ->setQtyTransfer($stocktransferingProductsItem->getQtyRequest());
                                        if (isset($data['stockreceiving_created_at']) && $data['stockreceiving_created_at'] && Mage::helper('inventory/stocktransfering')->canSaveAndApply($model->getWarehouseFromId(), $model->getWarehouseToId()))
                                            $stocktransferingProductsItem->setQtyReceive($stocktransferingProductsItem->getQtyRequest());
                                        $stocktransferingProductsItem->save();
                                        $qty_transfer = (int) $stocktransferingProductsItem->getQtyRequest();
                                        array_push($total, $qty_transfer);
                                    }
                                }
                            } else {
                                $qty_warehouse = Mage::getModel('inventory/warehouseproduct')
                                    ->getCollection()
                                    ->addFieldToFilter('warehouse_id', $source)
                                    ->addFieldToFilter('product_id', $pId)
                                    ->getFirstItem()
                                    ->getQty();
                                if ((int) $codeArr['qty_request'] <= (int) $qty_warehouse) {
                                    if(!is_numeric($codeArr['qty_request']) || (int)$codeArr['qty_request'] < 0)
                                        $codeArr['qty_request'] = 0;
//                                        continue;
                                    Mage::getModel('inventory/stocktransferingproduct')
                                        ->setProductId($pId)
                                        ->setTransferStockId($model->getId())
                                        ->setQtyRequest($codeArr['qty_request'])
                                        ->setQtyTransfer(0)
                                        ->setQtyReceive(0)
                                        ->save();
                                    $qty_request = (int) $codeArr['qty_request'];
                                    array_push($total, $qty_request);
                                }elseif((int) $codeArr['qty_request'] >= (int) $qty_warehouse){
                                    if(!is_numeric($codeArr['qty_request']) || (int)$codeArr['qty_request'] < 0)
                                        $codeArr['qty_request'] = 0;
//                                        continue;
                                    Mage::getModel('inventory/stocktransferingproduct')
                                        ->setProductId($pId)
                                        ->setTransferStockId($model->getId())
                                        ->setQtyRequest($qty_warehouse)
                                        ->setQtyTransfer(0)
                                        ->setQtyReceive(0)
                                        ->save();
                                    $qty_request = (int) $qty_warehouse;
                                    array_push($total, $qty_request);
                                }
                            }
                            $productIds[] = $pId;
                        }
                        $now = now();
                        $totalProducts = array_sum($total);
                        $model->setTotalProducts($totalProducts);
                        $model->setCreateAt($now);
                        $model->save();
                        if ($model->getStatus() == 1) {
                            if ($custom == '1') {
                                Mage::helper('inventory/stocktransfering')->createStockIssuing($model->getWarehouseFromId(), $model->getWarehouseToId(), $data, $model->getId(), 'qty_request');
                                $model->setStatus(2);
                                Mage::helper('inventory/stocktransfering')->autoCreateStockReceving($model->getId(), $data, true);
                                $model->setStatus(3);
                                $model->save();
                            } else {
                                if ($model->getType() == 1) {
                                    Mage::helper('inventory/stocktransfering')->createStockIssuing($model->getWarehouseFromId(), $model->getWarehouseToId(), $data, $model->getId(), 'qty_request');
                                    $model->setStatus(2);
                                    $model->save();
                                } else {
                                    if ($warehourseSource->getIsUnwarehouse()) {
                                        Mage::helper('inventory/stocktransfering')->createStockIssuing($model->getWarehouseFromId(), $model->getWarehouseToId(), $data, $model->getId(), 'qty_request');
                                        $model->setStatus(2);
                                        $model->save();
                                    }
                                }
                            }
                        }
                    }
                    $collection = Mage::getResourceModel('inventory/stocktransferingproduct_collection')
                        ->addFieldToFilter('transfer_stock_id', $model->getId())
                        ->addFieldToFilter('product_id', array('nin' => array_keys($stocktransferingProducts)));
                    if (count($collection)) {
                        foreach ($collection as $item) {
                            $item->delete();
                        }
                    }
                }

                if ($model->getStatus() == 2) {
                    $id = $this->getRequest()->getParam('id');
                    Mage::helper('inventory/stocktransfering')->autoCreateStockReceving($id, $data);
                    if (count($notReceive)) {
                        Mage::helper('inventory/stocktransfering')->autoCreateReturnReceiving($id, $notReceive, $source);
                    }
                }
                
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('inventory')->__('Stock transfer was successfully saved.')
                );
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('inventoryadmin/adminhtml_stocktransfering/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('inventoryadmin/adminhtml_stocktransfering/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }

            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('inventory')->__('Unable to save')
            );
            $this->_redirect('*/*/');
        }else{
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('inventory')->__('Unable to save')
            );
            $this->_redirect('*/*/');
        }
    }

    /**
     * Cancel a transfer
     */
    public function cancelAction() {
        $id = $this->getRequest()->getParam('id');
        $stocktransfering = Mage::getModel('inventory/stocktransfering')->load($id);
        $admin = Mage::getSingleton('admin/session')->getUser();
        if (Mage::helper('inventory/stocktransfering')->checkEditStocktransfering($id)) {
            try {
                $stocktransfering->setStatus(4)->save();
                Mage::getSingleton('adminhtml/session')->addSuccess('Stock transfering canceled successfully!');
                $this->_redirect('inventoryadmin/adminhtml_stocktransfering/edit', array('id' => $this->getRequest()->getParam('id')));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('inventoryadmin/adminhtml_stocktransfering/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
    }

    /**
     * Create new transfer 
     */
    public function transferAction() {
        
    }

    /**
     * Show products and products's qty of a transfer  
     */
    public function productsAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('stocktransfering.edit.tab.products')
            ->setProducts($this->getRequest()->getPost('oproducts', null));
        $this->renderLayout();
        if(Mage::getModel('admin/session')->getData('stocktransfering_product_import'))
            Mage::getModel('admin/session')->setData('stocktransfering_product_import',null);
    }

    public function productsGridAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('stocktransfering.edit.tab.products')
            ->setProducts($this->getRequest()->getPost('oproducts', null));
        $this->renderLayout();
    }

    /**
     * Get Import CSV file content
     */
    public function getImportCsvAction() {
        if (isset($_FILES['fileToUpload']['name']) && $_FILES['fileToUpload']['name'] != '') {
            try {
                $fileName = $_FILES['fileToUpload']['tmp_name'];
                $Object = new Varien_File_Csv();
                $dataFile = $Object->getData($fileName);
                $stocktransferProduct = array();
                $stocktransferProducts = array();
                $fields = array();
                $count = 0;
                $helper = Mage::helper('inventory/stocktransfering');
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
                                        $stocktransferProduct[$fields[$index]] = $cell;
                                    }
                                }
                            $source = $this->getRequest()->getParam('source');
                            $productId = Mage::getModel('catalog/product')->getIdBySku($stocktransferProduct['SKU']);
                            $warehouseproduct = Mage::getModel('inventory/warehouseproduct')
                                ->getCollection()
                                ->addFieldToFilter('warehouse_id', $source)
                                ->addFieldToFilter('product_id', $productId);
                            if ($warehouseproduct->getSize()) {
                                $stocktransferProducts[] = $stocktransferProduct;
                            }
                        }
                    }
                $helper->importProduct($stocktransferProducts);
            } catch (Exception $e) {
                
            }
        }
    }

    /**
     * 
     */
    public function selectTypeAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * load layout to select source warehouse and target warehouse
     */
    public function selectWarehousesAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * load layout to select products after select warehouse
     */
    public function selectProductsAction() {
        
    }

    /**
     * Only admin of target warehouse use this function
     * 
     * Select product and fill real qty that receive
     */
    public function approveTransferingAction() {
        
    }

    /**
     * Create receive stock receipt and complete transfer
     */
    public function completeTransferingAction() {
        
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('inventory');
    }
    
    public function historyAction() {
        $this->loadLayout();
	$this->getLayout()->getBlock('inventory.transferstock.edit.tab.history');
        $this->renderLayout();
    }	
    public function historyGridAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('inventory.transferstock.edit.tab.history');
        $this->renderLayout();
    }
    
    public function showhistoryAction() {
        $form_html = $this->getLayout()
            ->createBlock('inventory/adminhtml_stocktransfering')
            ->setTemplate('inventory/stocktransfering/showhistory.phtml')
            ->toHtml();
        $this->getResponse()->setBody($form_html);
    }
    
    public  function checkproductAction()
    {
        $stocktransfering_products = $this->getRequest()->getPost('products');
        $checkProduct = 0;
        $next = false;
        if (isset($stocktransfering_products)) {
            $stocktransferingProducts = array();
            $stocktransferingProductsExplodes = explode('&', urldecode($stocktransfering_products));
            if (count($stocktransferingProductsExplodes) <= 900) {
                parse_str(urldecode($stocktransfering_products), $stocktransferingProducts);
            } else {
                foreach ($stocktransferingProductsExplodes as $stocktransferingProductsExplode) {
                    $stocktransferingProduct = '';
                    parse_str($stocktransferingProductsExplode, $stocktransferingProduct);
                    $stocktransferingProducts = $stocktransferingProducts + $stocktransferingProduct;
                }
            }
            if (count($stocktransferingProducts)) {   
                foreach ($stocktransferingProducts as $pId => $enCoded) {
                    $codeArr = array();
                    parse_str(base64_decode($enCoded), $codeArr);                             
                    if (is_numeric($codeArr['qty_request']) && $codeArr['qty_request'] >=0) {
                        $checkProduct = 1;
                        $next = true;
                        break;  
                    }
                }
            }
        }
        echo $checkProduct;
    }
	
	/**
     * export grid item to CSV type
     */
    public function exportCsvAction() {
        $fileName = 'stock_transfer.csv';
        $content = $this->getLayout()
            ->createBlock('inventory/adminhtml_stocktransfering_grid')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction() {
        $fileName = 'stock_transfer.xml';
        $content = $this->getLayout()
            ->createBlock('inventory/adminhtml_stocktransfering_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }
}

?>
