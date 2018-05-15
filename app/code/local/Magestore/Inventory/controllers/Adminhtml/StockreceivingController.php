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
class Magestore_Inventory_Adminhtml_StockreceivingController extends Mage_Adminhtml_Controller_Action {

    /**
     * init layout and set active for current menu
     *
     * @return Magestore_Inventory_Adminhtml_InventoryController
     */
    protected function _initAction() {
        $this->loadLayout()
            ->_setActiveMenu('inventory/stockreceiving')
            ->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager')
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

    /**
     * view and edit item action
     */
    public function editAction() {
        $stockReceivingId = $this->getRequest()->getParam('id');
        $model = Mage::getModel('inventory/stockreceiving')->load($stockReceivingId);

        if ($model->getId() || $stockReceivingId == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('stockreceiving_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('inventory/inventory');

            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager')
            );
            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News')
            );
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('inventory/adminhtml_stockreceiving_edit'))
                ->_addLeft($this->getLayout()->createBlock('inventory/adminhtml_stockreceiving_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('inventory')->__('Item does not exist')
            );
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {
        $this->_forward('edit');
    }
    
    public function customnewAction(){
        $this->loadLayout();
        $this->renderLayout();
    }
    public function saveAction() {
        $data = $this->getRequest()->getPost();
        if($this->getRequest()->getParam('id'))
            Mage::getSingleton('admin/session')->setData('unsave_qty_receiving',1);
        if ($data['warehouse_id_to'] == $data['warehouse_id_from']){
            $this->_redirect('*/adminhtml_warehouse/edit',array('id' => $data['warehouse_id_to']));
             Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('inventory')->__('"From warehouse" and "To Warehouse" are the same!')
            );
            return;
        }else{
            $data = $this->_filterDates($data, array('created_at'));
            if(isset($data['warehouse_id_to'])){
                $warehouseSource = Mage::getModel('inventory/warehouse')->load($data['warehouse_id_to']);
                if($warehouseSource->getName())
                    $data['warehouse_to_name'] = $warehouseSource->getName();
            }
            $model = Mage::getModel('inventory/stockreceiving')->load($this->getRequest()->getParam('id'));
            $model->addData($data);
            try {              
                $model->save();
                //save products
                if (isset($data['stockreceiving_products'])) {
                    $stockreceivingProducts = array();
                    parse_str(urldecode($data['stockreceiving_products']), $stockreceivingProducts);
                    if (count($stockreceivingProducts)) {
                        $productIds = '';
                        $total = array();
                        foreach ($stockreceivingProducts as $pId => $enCoded) {
                            $codeArr = array();
                            parse_str(base64_decode($enCoded), $codeArr);
                            if($codeArr['qty']<0)
                                $codeArr['qty'] = 0;
                            array_push($total,$codeArr['qty']);
                            Mage::getModel('inventory/stockreceivingproduct')
                                ->setProductId($pId)
                                ->setStockReceivingId($model->getId())
                                ->setQty($codeArr['qty'])
                                ->save();
                            $warehouseProduct = Mage::getModel('inventory/warehouseproduct')
                                ->getCollection()
                                ->addFieldToFilter('product_id',$pId)
                                ->addFieldToFilter('warehouse_id',$data['warehouse_id_to']);
                            if(count($warehouseProduct)){
                                $product = $warehouseProduct->getFirstItem();
                                $id = $product->getWarehouseProductId();
                                $currentQty = $product->getQty();
                                $newQty = (int)$currentQty + (int)$codeArr['qty'];
								$newQtyAvailable = (int)$product->getQtyAvailable() + (int)$codeArr['qty'];
                                Mage::getModel('inventory/warehouseproduct')
                                    ->load($id)
                                    ->setQty($newQty)
									->setQtyAvailable($newQtyAvailable)
                                    ->save();
                            }else{
                                Mage::getModel('inventory/warehouseproduct')
                                    ->setProductId($pId)
                                    ->setWarehouseId($data['warehouse_id_to'])
                                    ->setQty($codeArr['qty'])
									->setQtyAvailable($codeArr['qty'])
                                    ->save();
                            } 
                                                     
                        }
                        $productIds[] = $pId;
                        $totalProducts = array_sum($total);
                        $model->setTotalProducts($totalProducts)
                            ->setStatus(3)
                            ->setType($data['type'])
                            ->setReferenceInvoiceNumber($data['reference_invoice_number'])
                            ->save();
                        
                    }
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('inventory')->__('Stock receiving was successfully saved!')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/view', array('id' => $model->getId(),'warehouse_id' => $data['warehouse_id_to']));
                    return;
                }
                 $this->_redirect('*/*/view', array('id' => $model->getId(),'warehouse_id' => $data['warehouse_id_to']));
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $data['warehouse_id_to']));
                return;
            }

            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('inventory')->__('Unable to create stock receiving!')
            );
            $this->_redirect('*/adminhtml_warehouse/');
        }
    }

    public function viewAction() {
        $stockReceivingId = $this->getRequest()->getParam('id');
        $model = Mage::getModel('inventory/stockreceiving')->load($stockReceivingId);
        if ($model->getId() || $stockReceivingId == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('stockreceiving_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('inventory/warehouse');

            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Stock Receiving Manager'), Mage::helper('adminhtml')->__('Stock Receiving Manager')
            );
            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Stock Receiving News'), Mage::helper('adminhtml')->__('Stock Receiving News')
            );
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('inventory/adminhtml_stockreceiving_view'))
                ->_addLeft($this->getLayout()->createBlock('inventory/adminhtml_stockreceiving_view_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('inventory')->__('Item does not exist')
            );
            $this->_redirect('*/*/');
        }
    }
    
    public function getImportCsvAction() {
        if (isset($_FILES['fileToUpload']['name']) && $_FILES['fileToUpload']['name'] != '') {
            try {
                $fileName = $_FILES['fileToUpload']['tmp_name'];
                $Object = new Varien_File_Csv();
                $dataFile = $Object->getData($fileName);
                $stockreceiveProduct = array();
                $stockreceiveProducts = array();
                $fields = array();
                $count = 0;
                $helper = Mage::helper('inventory/stockreceiving');
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
                                        $stockreceiveProduct[$fields[$index]] = $cell;
                                    }
                                }
                            $stockreceiveProducts[] = $stockreceiveProduct;
                        }
                    }
                $helper->importProduct($stockreceiveProducts);
            } catch (Exception $e) {
                
            }
        }
    }
    
    public function productsAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('stockreceiving.edit.tab.products')
            ->setProducts($this->getRequest()->getPost('oproducts', null));
        $this->renderLayout();
        if(Mage::getModel('admin/session')->getData('stockreceiving_product_import'))
            Mage::getModel('admin/session')->setData('stockreceiving_product_import',null);
    }

    public function productsGridAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('stockreceiving.edit.tab.products')
            ->setProducts($this->getRequest()->getPost('oproducts', null));
        $this->renderLayout();
    }
    
    public function productGridViewAction(){
        $block = $this->getLayout()->createBlock('inventory/adminhtml_stockreceiving_view_tab_products');
        $this->getResponse()->setBody($block->toHtml());
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('inventory');
    }

}