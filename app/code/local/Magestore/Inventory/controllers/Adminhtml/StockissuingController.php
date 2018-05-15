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
 * Stock Issuing Adminhtml Controller
 * 
 * @category    Magestore
 * @package     Magestore_Inventory
 * @author      Magestore Developer
 */
class Magestore_Inventory_Adminhtml_StockissuingController extends Mage_Adminhtml_Controller_Action
{
    /**
     * init layout and set active for current menu
     *
     * @return Magestore_Inventory_Adminhtml_InventoryController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('inventory/inventory')
            ->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Items Manager'),
                Mage::helper('adminhtml')->__('Item Manager')
            );
        return $this;
    }
 
    /**
     * index action
     */
    public function indexAction()
    {
        $this->_initAction()
            ->renderLayout();
    }

    /**
     * view and edit item action
     */
    public function editAction()
    {
        $admin = Mage::getSingleton('admin/session')->getUser();
        $stockIssuingId     = $this->getRequest()->getParam('id');
        $warehouseId     = $this->getRequest()->getParam('warehouse_id');
        if(Mage::helper('inventory/warehouse')->canTransfer($admin->getId(), $warehouseId)){
            $model  = Mage::getModel('inventory/stockissuing')->load($stockIssuingId);

            if ($model->getId() || $stockIssuingId == 0) {
                $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
                if (!empty($data)) {
                    $model->setData($data);
                }
                Mage::register('stockissuing_data', $model);

                $this->loadLayout();
                $this->_setActiveMenu('inventory/inventory');

                $this->_addBreadcrumb(
                    Mage::helper('adminhtml')->__('Item Manager'),
                    Mage::helper('adminhtml')->__('Item Manager')
                );
                $this->_addBreadcrumb(
                    Mage::helper('adminhtml')->__('Item News'),
                    Mage::helper('adminhtml')->__('Item News')
                );
                $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
                $this->_addContent($this->getLayout()->createBlock('inventory/adminhtml_stockissuing_edit'))
                    ->_addLeft($this->getLayout()->createBlock('inventory/adminhtml_stockissuing_edit_tabs'));

                $this->renderLayout();
            } else {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('inventory')->__('Item does not exist')
                );
                $this->_redirect('*/*/');
            }
        }else{
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('inventory')->__('Admin cannot create stock issuing!')
            );
            $this->_redirect('inventoryadmin/adminhtml_warehouse/edit',array('id'=>$warehouseId));
        }
    }
 
    public function newAction()
    {
        $this->_forward('edit');
    }
    public function customnewAction(){
        $this->loadLayout();
        $this->renderLayout();
    }
 
    /**
     * delete item action
     */
    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('inventory/inventory');
                $model->setId($this->getRequest()->getParam('id'))
                    ->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Item was successfully deleted')
                );
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * mass delete item(s) action
     */
    public function massDeleteAction()
    {
        $inventoryIds = $this->getRequest()->getParam('inventory');
        if (!is_array($inventoryIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($inventoryIds as $inventoryId) {
                    $inventory = Mage::getModel('inventory/inventory')->load($inventoryId);
                    $inventory->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted',
                    count($inventoryIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
    
    /**
     * mass change status for item(s) action
     */
    public function massStatusAction()
    {
        $inventoryIds = $this->getRequest()->getParam('inventory');
        if (!is_array($inventoryIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($inventoryIds as $inventoryId) {
                    Mage::getSingleton('inventory/inventory')
                        ->load($inventoryId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($inventoryIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
     /**
     * get import csv
     */
    public function getImportCsvAction() {
        if (isset($_FILES['fileToUpload']['name']) && $_FILES['fileToUpload']['name'] != '') {
            try {
                $fileName = $_FILES['fileToUpload']['tmp_name'];
                $Object = new Varien_File_Csv();
                $dataFile = $Object->getData($fileName);
                $stockissueProduct = array();
                $stockissueProducts = array();
                $fields = array();
                $count = 0;
                $helper = Mage::helper('inventory/stockissuing');
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
                                        $stockissueProduct[$fields[$index]] = $cell;
                                    }
                                }
                            $stockissueProducts[] = $stockissueProduct;
                        }
                    }
                $helper->importProduct($stockissueProducts);
            } catch (Exception $e) {
                
            }
        }
    }
    
    /**
     * export grid item to CSV type
     */
    public function exportCsvAction()
    {
        $fileName   = 'inventory.csv';
        $content    = $this->getLayout()
                           ->createBlock('inventory/adminhtml_inventory_grid')
                           ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction()
    {
        $fileName   = 'inventory.xml';
        $content    = $this->getLayout()
                           ->createBlock('inventory/adminhtml_inventory_grid')
                           ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('inventory');
    }
    
    public function viewAction(){
        $stockIssuingId     = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('inventory/stockissuing')->load($stockIssuingId);
        if ($model->getId() || $stockIssuingId == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('stockissuing_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('inventory/warehouse');

            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Item Manager'),
                Mage::helper('adminhtml')->__('Item Manager')
            );
            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Item News'),
                Mage::helper('adminhtml')->__('Item News')
            );
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('inventory/adminhtml_stockissuing_view'))
                ->_addLeft($this->getLayout()->createBlock('inventory/adminhtml_stockissuing_view_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('inventory')->__('Item does not exist')
            );
            $this->_redirect('*/*/');
        }
    }
    
    public function productsAction(){
        $this->loadLayout();
        $this->getLayout()->getBlock('stockissuing.edit.tab.products')
            ->setProducts($this->getRequest()->getPost('oproducts', null));
        $this->renderLayout();
        if(Mage::getModel('admin/session')->getData('stockissuing_product_import'))
            Mage::getModel('admin/session')->setData('stockissuing_product_import',null);
    }
    
    public function productGridAction(){
        $this->loadLayout();
        $this->getLayout()->getBlock('stockissuing.edit.tab.products')
            ->setProducts($this->getRequest()->getPost('oproducts', null));
        $this->renderLayout();
    }
    
    public function productGridViewAction(){
        $block = $this->getLayout()->createBlock('inventory/adminhtml_stockissuing_view_tab_products');
        $this->getResponse()->setBody($block->toHtml());
    }
    
    /**
     * save item action
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $data = $this->_filterDates($data,array('created_at'));
            if(isset($data['warehouse_id_from'])){
                $warehouseSource = Mage::getModel('inventory/warehouse')->load($data['warehouse_id_from']);
                if($warehouseSource->getName())
                    $data['warehouse_from_name'] = $warehouseSource->getName();
            }
            $model = Mage::getModel('inventory/stockissuing');
            if($this->getRequest()->getParam('id'))
                Mage::getSingleton('admin/session')->setData('unsave_qty',1);
            $model->setData($data)->setId($this->getRequest()->getParam('id'));
            if(!$this->getRequest()->getParam('id')){
            $model->setType(4);
            }
            try {
                $model->save();
                //save issuing product
                if(!$this->getRequest()->getParam('id')){
                    $deleteStockissuing = 1;
                    if(isset($data['stockissuing_products'])){
                        $issuingProducts = array();
                        parse_str(urldecode($data['stockissuing_products']),$issuingProducts);

                        if(count($issuingProducts)){
                            $warehouseSourceId = $model->getWarehouseIdFrom();
                            $productIds = '';
                            $totalQty = 0;
                            foreach ($issuingProducts as $pId => $enCoded){
                                $codeArr = array(); 
                                parse_str(base64_decode($enCoded),$codeArr);
                                if($codeArr['qty_issuing']<0)
                                    $codeArr['qty_issuing'] = 0;
                                $issuingProductsItem = Mage::getModel('inventory/stockissuingproduct')
                                    ->getCollection()
                                    ->addFieldToFilter('stock_issuing_id', $model->getId())
                                    ->addFieldToFilter('product_id', $pId)
                                    ->getFirstItem();
                                $warehouseProduct = Mage::getModel('inventory/warehouseproduct')
                                        ->getCollection()
                                        ->addFieldToFilter('warehouse_id', $warehouseSourceId)
                                        ->addFieldToFilter('product_id',$pId)
                                        ->getFirstItem();
                                if($warehouseProduct->getQty() >= $codeArr['qty_issuing']){
                                    $qty = $codeArr['qty_issuing'];
                                    $deleteStockissuing = 0;
                                }else{
                                    if($warehouseProduct->getQty() > 0){
                                        $qty = $warehouseProduct->getQty();
                                        $deleteStockissuing = 0;
                                    }else{
                                        $qty = 0;
                                    }
                                    $product = Mage::getModel('catalog/product')->load($pId);
                                    $message = Mage::helper('inventory')->__($product->getName().' not enough qty. System only use all of warehouse has.');
                                    Mage::getSingleton('adminhtml/session')->addNotice($message);
                                }
                                if($issuingProductsItem->getId()){

                                    $issuingProductsItem->setStockIssuingId($model->getId())
                                    ->setProductId($pId)
                                    ->setQty($qty)
                                    ->save();
                                    $totalQty += $qty;
                                }else{
                                    Mage::getModel('inventory/stockissuingproduct')
                                        ->setProductId($pId)
                                        ->setStockIssuingId($model->getId())
                                        ->setQty($qty)
                                        ->save();
                                    $totalQty += $qty;
                                }
                                $productIds[] = $pId;
                            }
                            $model->setTotalProducts($totalQty)->save();
                        }
                    }

                    if($deleteStockissuing == 1){
                        $model->delete();
                        $this->_redirect('inventoryadmin/adminhtml_stockissuing/new');
                        return;                    
                    }
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('inventory')->__('Stock issuing was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/view', array('id' => $model->getId(),'warehouse_id'=>$data['warehouse_id_from']));
                    return;
                }
//                $this->_redirect('*/*/view', array('id' => $model->getId(),'warehouse_id'=>$data['warehouse_id_from']));
                $this->_redirect('*/adminhtml_warehouse/edit',array('id'=>$data['warehouse_id_from']));
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/adminhtml_warehouse/edit',array('id'=>$data['warehouse_id_from']));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('inventory')->__('Unable to find stock issuing to save')
        );
        $this->_redirect('*/adminhtml_warehouse/');
    }
}