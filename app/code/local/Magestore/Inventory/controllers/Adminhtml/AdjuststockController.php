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
 * Inventory Adminhtml Controller
 * 
 * @category    Magestore
 * @package     Magestore_Inventory
 * @author      Magestore Developer
 */
class Magestore_Inventory_Adminhtml_AdjuststockController extends Mage_Adminhtml_Controller_Action
{
    
    protected function _initAction() {
        $this->loadLayout()
            ->_setActiveMenu('inventory/adjuststock')
            ->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Adjust stock Manager'), Mage::helper('adminhtml')->__('Adjust stock Manager')
        );
        return $this;
    }

    /**
     * index action
     */
    public function indexAction() {
       $this->_initAction()
            ->renderLayout();

    }
    
    public function importproductAction() {
        if (isset($_FILES['fileToUpload']['name']) && $_FILES['fileToUpload']['name'] != '') {
            try {
                $fileName = $_FILES['fileToUpload']['tmp_name'];
                $Object = new Varien_File_Csv();
                $dataFile = $Object->getData($fileName);
                $adjuststockProduct = array();
                $adjuststockProducts = array();
                $fields = array();
                $count = 0;
                $adjuststockHelper = Mage::helper('inventory/adjuststock');
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
                                        $adjuststockProduct[$fields[$index]] = $cell;
                                    }
                                }
                            $adjuststockProducts[] = $adjuststockProduct;
                        }
                    }
                $adjuststockHelper->importProduct($adjuststockProducts);
            } catch (Exception $e) {
                
            }
        }
    }
    
    public function gridAction() {
            $this->loadLayout();
            $this->getLayout()->getBlock('inventory_listadjuststock_grid');
            $this->renderLayout();
    }

    
    public function newAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function editAction()
    {
        $adjustStockId = $this->getRequest()->getParam('id');
        $model = Mage::getModel('inventory/adjuststock')->load($adjustStockId);
        if($model->getId() || $adjustStockId==0){
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if(!empty($data)){
                $model->setData($data);
            }
            Mage::register('adjuststock_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('inventory/adjuststock');
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true)
                                                ->removeItem('js','mage/adminhtml/grid.js')
                                                ->addItem('js','magestore/adminhtml/inventory/grid.js');
            $this->_addContent($this->getLayout()->createBlock('inventory/adminhtml_adjuststock_edit'))
                ->_addLeft($this->getLayout()->createBlock('inventory/adminhtml_adjuststock_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('inventory')->__('Adjust Stock does not exist')
            );
            $this->_redirect('*/*/');
        }
    }
    
    public function prepareAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            if (isset($_FILES['fileToUpload']['name']) && $_FILES['fileToUpload']['name'] != '') {
                try {
                    /* Starting upload */    
                    $uploader = new Varien_File_Uploader('fileToUpload');
                    
                    // Any extention would work
                    $uploader->setAllowedExtensions(array('csv'));
                    $uploader->setAllowRenameFiles(false);
                    
                    $uploader->setFilesDispersion(false);
                    
                    try {
                        $fileName   = $_FILES['fileToUpload']['tmp_name'];
                        $Object  	= new Varien_File_Csv();
                        $dataFile 	= $Object->getData($fileName);
                        $adjustStockProduct = array();
                        $adjustStockProducts = array();
                        $fields = array();
                        if(count($dataFile))
                        foreach($dataFile as $col => $row){
                            if($col == 0){
                                    if(count($row))
                                    foreach($row as $index=>$cell)
                                            $fields[$index] = (string)$cell;			
                            }elseif($col>0){
                                    if(count($row))
                                    foreach($row as $index=>$cell){
                                            if(isset($fields[$index])){
                                                    $adjustStockProduct[$fields[$index]] = $cell;
                                            }
                                    }
                                    $adjustStockProducts[] = $adjustStockProduct;
                            }		
                        }
                        if(count($adjustStockProducts)){
                            $adjustStockProducts['warehouse_id'] = $this->getRequest()->getPost('warehouse_id');
                            Mage::getModel('admin/session')->setData('adjuststock_product_import',$adjustStockProducts);
                            $this->loadLayout();
                            $this->_setActiveMenu('inventory/adjuststock');
                            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true)
                                                                ->removeItem('js','mage/adminhtml/grid.js')
                                                                ->addItem('js','magestore/adminhtml/inventory/grid.js');
                            $this->_addContent($this->getLayout()->createBlock('inventory/adminhtml_adjuststock_edit')->setAdjustStockProducts($adjustStockProducts))
                                 ->_addLeft($this->getLayout()->createBlock('inventory/adminhtml_adjuststock_edit_tabs')->setAdjustStockProducts($adjustStockProducts));
                            $this->renderLayout();
                        }else{
                            Mage::getSingleton('adminhtml/session')->addError(
                                Mage::helper('inventory')->__('Unable to find item to save')
                            );
                            $this->_redirect('*/*/new');
                        }
                    } catch (Exception $e) {
                    }       
                } catch (Exception $e) {
                }
            }else{
                $adjustStockProducts['warehouse_id'] = $this->getRequest()->getPost('warehouse_id');
                Mage::getModel('admin/session')->setData('adjuststock_product_warehouse',$adjustStockProducts);
                $this->loadLayout();
                $this->_setActiveMenu('inventory/adjuststock');
                $this->getLayout()->getBlock('head')->setCanLoadExtJs(true)
                                                    ->removeItem('js','mage/adminhtml/grid.js')
                                                    ->addItem('js','magestore/adminhtml/inventory/grid.js');
                $this->_addContent($this->getLayout()->createBlock('inventory/adminhtml_adjuststock_edit')->setAdjustStockProducts($adjustStockProducts))
                     ->_addLeft($this->getLayout()->createBlock('inventory/adminhtml_adjuststock_edit_tabs')->setAdjustStockProducts($adjustStockProducts));
                $this->renderLayout();
            }
        }else{
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('inventory')->__('Unable to find item to save')
            );
            $this->_redirect('*/*/new');
        }
    }
    
    public function productAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('inventory.adjuststock.edit.tab.products')
                ->setProducts($this->getRequest()->getPost('adjuststock_products',null));
        $this->renderLayout();
        if (Mage::getModel('admin/session')->getData('adjuststock_product_import')) {
            Mage::getModel('admin/session')->setData('adjuststock_product_import', null);
        }
    }
    
    public function productGridAction()
    {
        $this->loadLayout();
            $this->getLayout()->getBlock('inventory.adjuststock.edit.tab.products')
                    ->setProducts($this->getRequest()->getPost('adjuststock_products',null));
            $this->renderLayout();
    }
    
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {        
            $model = Mage::getModel('inventory/adjuststock');
            $model->addData($data);
            $warehouse_id = $data['warehouse_id'];
            $warehouse = Mage::getModel('inventory/warehouse')->load($warehouse_id);
            try {
                $model->setWarehouseId($warehouse_id)
                      ->setWarehouseName($warehouse->getName())
                      ->setCreatedAt(now())
                      ->setReason($data['reason']);
                $admin = Mage::getModel('admin/session')->getUser()->getUsername();
                if(!$this->getRequest()->getParam('id')){
                    $model->setData('create_by',$admin);
                }
                $model->save();
                $resource = Mage::getSingleton('core/resource');
                $writeConnection = $resource->getConnection('core_write');
                $readConnection = $resource->getConnection('core_read');
                $installer = Mage::getModel('core/resource_setup');
                $sqlNews = array();
                $sqlOlds = '';
				$sqlOldsAvailable = '';
                $sqlAdjustNew = array();
                $countSqlOlds = 0;
                if (isset($data['adjuststock_products'])){
                    $adjuststockProducts = array();
                    $adjuststockProductsExplodes = explode('&',urldecode($data['adjuststock_products']));
                    if(count($adjuststockProductsExplodes) <=900){
                        parse_str(urldecode($data['adjuststock_products']),$adjuststockProducts);
                    }else{
                        foreach($adjuststockProductsExplodes as $adjuststockProductsExplode){
                            $adjuststockProduct = '';
                            parse_str($adjuststockProductsExplode,$adjuststockProduct);
                            $adjuststockProducts = $adjuststockProducts + $adjuststockProduct;
                        }
                    }
                    if (count($adjuststockProducts)){
                        $productIds = '';
                        $qtys = '';
                        $count = 0;
                        foreach ($adjuststockProducts as $pId => $enCoded){
                            $codeArr = array(); 
                            parse_str(base64_decode($enCoded),$codeArr);
                            $warehouseProductItem = Mage::getModel('inventory/warehouseproduct')
                                ->getCollection()
                                ->addFieldToFilter('warehouse_id',$warehouse_id)
                                ->addFieldToFilter('product_id',$pId)
                                ->getFirstItem();
                            $qtyAddMore = 0;
                            if($warehouseProductItem->getId()){
                                $qtyAddMore = $codeArr['adjust_qty'] - $warehouseProductItem->getQty();
                                $oldQty = $warehouseProductItem->getQty();
								$oldQtyAvailable = $warehouseProductItem->getQtyAvailable();
                                $newQty = $codeArr['adjust_qty'];
								$newQtyAvailable = $oldQtyAvailable + ($newQty - $oldQty);
                                if($qtyAddMore != 0){
                                    $countSqlOlds++;
                                    $sqlOlds .= 'UPDATE '.$installer->getTable('inventory/warehouseproduct').' 
                                                                            SET `qty` = \''.$codeArr['adjust_qty'].'\'
                                                                                    WHERE `warehouse_product_id` ='.$warehouseProductItem->getId().';';
                                    if ($countSqlOlds == 900) {
                                        $writeConnection->query($sqlOlds);
                                        //$countSqlOlds = 0;
                                    }
									
									$sqlOldsAvailable .= 'UPDATE '.$installer->getTable('inventory/warehouseproduct').' 
                                                                            SET `qty_available` = \''.$newQtyAvailable.'\'
                                                                                    WHERE `warehouse_product_id` ='.$warehouseProductItem->getId().';';
                                    if ($countSqlOlds == 900) {
                                        $writeConnection->query($sqlOldsAvailable);
                                        $countSqlOlds = 0;
                                    }
                                }
                            }else{
                                $qtyAddMore = $codeArr['adjust_qty'];
                                $oldQty = 0;
                                $newQty = $codeArr['adjust_qty'];
								$newQtyAvailable = $codeArr['adjust_qty'];
                                $sqlNews[] = array(
                                                'product_id' => $pId,
                                                'warehouse_id' => $warehouse_id,
                                                'qty' => $codeArr['adjust_qty'],
												'qty_available' => $newQtyAvailable
                                            );
                                if(count($sqlNews) == 1000) {
                                    $writeConnection->insertMultiple($installer->getTable('inventory/warehouseproduct'), $sqlNews);
                                    $sqlNews = array();
                                }
                            }
                            $sqlAdjustNew[] = array(
                                                'adjuststock_id'=> $model->getId(),
                                                'product_id' => $pId,
                                                'old_qty' => $oldQty,
                                                'adjust_qty' => $newQty
                                            );
                            if(count($sqlAdjustNew) == 1000) {
                                $writeConnection->insertMultiple($installer->getTable('inventory/adjuststockproduct'), $sqlAdjustNew);
                                $sqlAdjustNew = array();
                            }
                            /*update product qty for system*/
                            if($qtyAddMore != 0){
                                if(Mage::getStoreConfig('inventory/general/updatestock')){
                                    $product = Mage::getModel('catalog/product')->load($pId);
                                    $sqlSelect = 'Select qty from '.$installer->getTable("cataloginventory_stock_item").' WHERE (product_id = '.$pId.')';
                                    $results = $readConnection->fetchAll($sqlSelect);
                                    foreach($results as $result){
                                        $oldQtyProduct = $result['qty'];
                                    }
                                    $minToChangeStatus = Mage::getStoreConfig('cataloginventory/item_options/min_qty');
                                    if(($oldQtyProduct + $qtyAddMore) > $minToChangeStatus){
                                        $sqlUpdateProduct = 'UPDATE '.$installer->getTable("cataloginventory_stock_item") .' SET qty = qty + '.$qtyAddMore.', is_in_stock = 1 WHERE (product_id = '.$pId.')';
                                        $sqlUpdateProductStatus = 'UPDATE '.$installer->getTable("cataloginventory_stock_status") .' SET qty = qty + '.$qtyAddMore.', stock_status = 1 WHERE (product_id = '.$pId.')';
                                    }else{
                                        if($product->getTypeId() != 'configurable'){
                                            $sqlUpdateProduct = 'UPDATE '.$installer->getTable("cataloginventory_stock_item") .' SET qty = qty + '.$qtyAddMore.', is_in_stock = 0 WHERE (product_id = '.$pId.')';
                                        }else{
                                            $sqlUpdateProduct = 'UPDATE '.$installer->getTable("cataloginventory_stock_item") .' SET qty = qty + '.$qtyAddMore.' WHERE (product_id = '.$pId.')';
                                        }
                                        $sqlUpdateProductStatus = 'UPDATE '.$installer->getTable("cataloginventory_stock_status") .' SET qty = qty + '.$qtyAddMore.', stock_status = 0 WHERE (product_id = '.$pId.')';
                                    }
                                }else{
                                    $sqlUpdateProduct = 'UPDATE '.$installer->getTable("cataloginventory_stock_item") .' SET qty = qty + '.$qtyAddMore.' WHERE (product_id = '.$pId.')';
                                    $sqlUpdateProductStatus = 'UPDATE '.$installer->getTable("cataloginventory_stock_status") .' SET qty = qty + '.$qtyAddMore.' WHERE (product_id = '.$pId.')';
                                }
//                                $sqlUpdateProduct = 'UPDATE '.$installer->getTable("cataloginventory_stock_item") .' SET qty = qty + '.$qtyAddMore.' WHERE (product_id = '.$pId.')';
                                $writeConnection->query($sqlUpdateProduct);
                                $writeConnection->query($sqlUpdateProductStatus);
                            }
                        }
                        if(!empty($sqlNews)){
                            $writeConnection->insertMultiple($installer->getTable('inventory/warehouseproduct'), $sqlNews);
                        }
                        if(!empty($sqlOlds)){
                                $writeConnection->query($sqlOlds);
                        }
						if(!empty($sqlOldsAvailable)){
                                $writeConnection->query($sqlOldsAvailable);
                        }
                        if(!empty($sqlAdjustNew)){
                            $writeConnection->insertMultiple($installer->getTable('inventory/adjuststockproduct'), $sqlAdjustNew);
                        }
                    }
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('inventory')->__('Stock adjustment was successfully applied.')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $model->getId()));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('inventory')->__('Unable to find adjust stock to create')
        );
        $this->_redirect('*/*/');
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('inventory');
    }
	
	/**
     * export grid item to CSV type
     */
    public function exportCsvAction() {
        $fileName = 'adjustment.csv';
        $content = $this->getLayout()
            ->createBlock('inventory/adminhtml_adjuststock_listadjuststock_grid')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction() {
        $fileName = 'adjustment.xml';
        $content = $this->getLayout()
            ->createBlock('inventory/adminhtml_adjuststock_listadjuststock_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }
}