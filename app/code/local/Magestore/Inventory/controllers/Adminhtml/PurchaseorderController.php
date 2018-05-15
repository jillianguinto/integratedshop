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
class Magestore_Inventory_Adminhtml_PurchaseorderController extends Mage_Adminhtml_Controller_action {

    public function indexAction() {
        $this->loadLayout()
            ->_setActiveMenu('inventory/purchaseorder');
        $this->renderLayout();
    }

    public function editAction() {
        $purchaseOrderId = $this->getRequest()->getParam('id');
        $model = Mage::getModel('inventory/purchaseorder')->load($purchaseOrderId);
        if ($model->getId() || $purchaseOrderId == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (is_null($data)) {
                $post = Mage::getModel('admin/session')->getData('supplyneeds_products_prepare_purchaseorder');
                if ($post) {
                    $list = array();
                    $list = explode(';', $post);
                    $list = Mage::helper('inventory/supplyneeds')->filterList($list);

                    if (count($list)) {
                        $warehouseIds = $this->getRequest()->getParam('warehouse_ids');
                        $warehouseIds = explode(',', $warehouseIds);
                        $first = 0;
                        if (isset($warehouseIds[0]))
                            $first = $warehouseIds[0];
                        $result = array();
                        $products = Mage::getModel('catalog/product')->getCollection()
                            ->addFieldToFilter('entity_id', array('in' => array_keys($list)));
                        foreach ($products as $product) {
                            $result[] = array(
                                'SKU' => $product->getSku(),
                                'warehouse_' . $first => $list[$product->getId()]
                            );
                        }
                        Mage::getModel('admin/session')->setData('supplyneeds_products_prepare_purchaseorder', null);
                        if (count($result)) {
                            Mage::getModel('admin/session')->setData('purchaseorder_product_import', $result);
                        }
                    }
                }
            }
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('purchaseorder_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('inventory/purchaseorder');
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true)
                ->removeItem('js', 'mage/adminhtml/grid.js')
                ->addItem('js', 'magestore/adminhtml/inventory/grid.js');
            $this->_addContent($this->getLayout()->createBlock('inventory/adminhtml_purchaseorder_edit'))
                ->_addLeft($this->getLayout()->createBlock('inventory/adminhtml_purchaseorder_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('inventory')->__('Purchase Order does not exist!')
            );
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {
        $export = $this->getRequest()->getParam('export');
        if ($export == 1) {
            $data = $this->getRequest()->getPost();
            $this->exportPostAction($data);
        } else {
            $supplier_id = $this->getRequest()->getParam('supplier_id');
            if ($supplier_id) {
                $this->_forward('edit');
            } else {
                $dt = $this->getRequest()->getPost();
                if (isset($dt['product_list']))
                    Mage::getModel('admin/session')->setData('supplyneeds_products_prepare_purchaseorder', $dt['product_list']);
                $this->loadLayout();
                $this->_setActiveMenu('inventory/purchaseorder');
                $this->_addBreadcrumb(
                    Mage::helper('adminhtml')->__('Purchase Order'), Mage::helper('adminhtml')->__('Purchase Order')
                );
                $this->_addBreadcrumb(
                    Mage::helper('adminhtml')->__('Purchase Order News'), Mage::helper('adminhtml')->__('Purchase Order News')
                );
                $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
                $this->_addContent($this->getLayout()->createBlock('inventory/adminhtml_purchaseorder_new'))
                    ->_addLeft($this->getLayout()->createBlock('inventory/adminhtml_purchaseorder_new_tabs'));
                $this->renderLayout();
            }
        }
    }

    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
            if (array_key_exists('send_mail', $data)) {
                $data['send_mail'] = 1;
            }
            $model = Mage::getModel('inventory/purchaseorder');
            $data = $this->_filterDateTime($data, array('purchase_on'));
            $data = $this->_filterDates($data, array('start_date', 'cancel_date', 'expected_date', 'payment_date'));
            if ($this->getRequest()->getParam('supplier_id'))
                $data['supplier_id'] = $this->getRequest()->getParam('supplier_id');
            if ($this->getRequest()->getParam('warehouse_ids'))
                $data['warehouse_id'] = $this->getRequest()->getParam('warehouse_ids');
            if ($this->getRequest()->getParam('currency')) {
                $data['currency'] = $this->getRequest()->getParam('currency');
            }
            if ($this->getRequest()->getParam('change_rate')) {
                $data['change_rate'] = $this->getRequest()->getParam('change_rate');
            }

            $model = Mage::getModel('inventory/purchaseorder')->load($this->getRequest()->getParam('id'));
            if ($this->getRequest()->getParam('id')) {
                $data['create_by'] = $model->getData('create_by');
            }
            $model->addData($data);
            if ($data['paid_more']) {
                if ($this->getRequest()->getParam('id')) {
                    $purchaseOrderModel = Mage::getModel('inventory/purchaseorder')->load($this->getRequest()->getParam('id'));
                    $data['paid'] = $purchaseOrderModel->getPaid() + $data['paid_more'];
                } else {
                    $data['paid'] = $data['paid_more'];
                }
                $model->setPaid($data['paid']);
            } else {
                if (!$this->getRequest()->getParam('id')) {
                    $data['paid'] = 0;
                }
            }
            $supplier_id = $data['supplier_id'];
            $supplierProducts = Mage::getModel('inventory/supplierproduct')
                ->getCollection()
                ->addFieldToFilter('supplier_id', $supplier_id);
            $supplierProductIds = array();
            foreach ($supplierProducts as $supplierProduct) {
                $supplierProductIds[] = $supplierProduct->getProductId();
            }
            try {
                if (!Mage::helper('inventory/purchaseorder')->haveDelivery()) {
                    $supplierModel = Mage::getModel('inventory/supplier')->load($supplier_id);
                    $warehouse_id = $data['warehouse_id'];
                    $warehouseModel = Mage::getModel('inventory/warehouse')->load($warehouse_id);
                    if ($supplierModel->getId())
                        $model->setSupplierName($supplierModel->getName());
                    if ($warehouseModel->getId())
                        $model->setWarehouseName($warehouseModel->getName());
                }
                //check field changed
                if ($this->getRequest()->getParam('id')) {
                    $oldData = Mage::getModel('inventory/purchaseorder')->load($this->getRequest()->getParam('id'));
                    $changeArray = array();
                    $changeData = 0;
                    foreach ($data as $key => $value) {
                        if (!in_array($key, $this->getFiledSaveHistory()))
                            continue;
                        if ($oldData->getData($key) != $value) {
                            $changeArray[$key]['old'] = $oldData->getData($key);
                            $changeArray[$key]['new'] = $value;
                            $changeData = 1;
                        }
                    }
                }

                $admin = Mage::getModel('admin/session')->getUser()->getUsername();
                if (!$this->getRequest()->getParam('id')) {
                    $model->setData('create_by', $admin);
                }
                $purchaseOrderId = $model->save()->getId();
                //save histoty change
                if (!$this->getRequest()->getParam('id')) {
                    $purchaseOrderHistory = Mage::getModel('inventory/purchaseorderhistory');
                    $purchaseOrderHistoryContent = Mage::getModel('inventory/purchaseorderhistorycontent');
                    $purchaseOrderHistory->setData('purchase_order_id', $model->getId())
                        ->setData('time_stamp', now())
                        ->setData('create_by', $admin)
                        ->save();
                    $purchaseOrderHistoryContent->setData('purchase_order_history_id', $purchaseOrderHistory->getId())
                        ->setData('field_name', $admin . ' created this purchase order!')
                        ->save();
                } else {
                    if ($changeData == 1) {
                        $purchaseOrderHistory = Mage::getModel('inventory/purchaseorderhistory');
                        $purchaseOrderHistory->setData('purchase_order_id', $model->getId())
                            ->setData('time_stamp', now())
                            ->setData('create_by', $admin)
                            ->save();
                        foreach ($changeArray as $field => $filedValue) {
                            $fileTitle = $this->getTitleByField($field);
                            if ($field == 'status') {
                                $statusArray = Mage::helper('inventory/purchaseorder')->getReturnOrderStatus();
                                $filedValue['old'] = $statusArray[$filedValue['old']];
                                $filedValue['new'] = $statusArray[$filedValue['new']];
                            } elseif ($field == 'ship_via') {
                                $shipArray = Mage::helper('inventory/purchaseorder')->getShippingMethod();
                                $filedValue['old'] = $shipArray[$filedValue['old']];
                                $filedValue['new'] = $shipArray[$filedValue['new']];
                            } elseif ($field == 'payment_term') {
                                $paymentArray = Mage::helper('inventory/purchaseorder')->getPaymentTerms();
                                $filedValue['old'] = $paymentArray[$filedValue['old']];
                                $filedValue['new'] = $paymentArray[$filedValue['new']];
                            } elseif ($field == 'order_placed') {
                                $placedArray = Mage::helper('inventory/purchaseorder')->getOrderPlaced();
                                $filedValue['old'] = $placedArray[$filedValue['old']];
                                $filedValue['new'] = $placedArray[$filedValue['new']];
                            }
                            $purchaseOrderHistoryContent = Mage::getModel('inventory/purchaseorderhistorycontent');
                            $purchaseOrderHistoryContent->setData('purchase_order_history_id', $purchaseOrderHistory->getId())
                                ->setData('field_name', $fileTitle)
                                ->setData('old_value', $filedValue['old'])
                                ->setData('new_value', $filedValue['new'])
                                ->save();
                        }
                    }
                }

                $purchaseOrder = Mage::getModel('inventory/purchaseorder')->load($model->getId());
                $resource = Mage::getSingleton('core/resource');
                $writeConnection = $resource->getConnection('core_write');
                $installer = Mage::getModel('core/resource_setup');
                $sqlNews = array();
                $sqlWarehouseNew = array();
                $sqlOlds = '';
                $countSqlOlds = 0;
                if (isset($data['purchaseorder_products'])) {
                    $purchaseorderProducts = array();
                    $purchaseorderProductsExplodes = explode('&', urldecode($data['purchaseorder_products']));
                    if (count($purchaseorderProductsExplodes) <= 900) {
                        parse_str(urldecode($data['purchaseorder_products']), $purchaseorderProducts);
                    } else {
                        foreach ($purchaseorderProductsExplodes as $purchaseorderProductsExplode) {
                            $purchaseorderProduct = '';
                            parse_str($purchaseorderProductsExplode, $purchaseorderProduct);
                            $purchaseorderProducts = $purchaseorderProducts + $purchaseorderProduct;
                        }
                    }
                    if (count($purchaseorderProducts)) {
                        $productIds = '';
                        $qtys = '';
                        $count = 0;
                        $totalProducts = 0;
                        $totalAmounts = 0;
                        $sqlCount = 0;
                        $sqlNews = array();
                        $changeRateNow = $model->getChangeRate();
                        foreach ($purchaseorderProducts as $pId => $enCoded) {
                            if (in_array($pId, $supplierProductIds)) {
                                $count = 0;
                                $codeArr = array();
                                parse_str(base64_decode($enCoded), $codeArr);
                                $purchaseorderProductItem = Mage::getModel('inventory/purchaseorderproduct')
                                    ->getCollection()
                                    ->addFieldToFilter('purchase_order_id', $model->getId())
                                    ->addFieldToFilter('product_id', $pId)
                                    ->getFirstItem();
                                $productInfo = Mage::getModel('inventory/supplierproduct')
                                    ->getCollection()
                                    ->addFieldToFilter('supplier_id', $purchaseOrder->getSupplierId())
                                    ->addFieldToFilter('product_id', $pId)
                                    ->getFirstItem();
                                $productModel = Mage::getModel('catalog/product')->load($pId);
                                $productIds[] = $pId;
                                if ($purchaseorderProductItem->getId()) {
                                    $countSqlOlds++;
                                    $totalAmounts += $codeArr['qty_order'] * $purchaseorderProductItem->getCost() * (1 - $purchaseorderProductItem->getDiscount() / 100 + $purchaseorderProductItem->getTax() / 100);
                                    $totalProducts += $codeArr['qty_order'];
                                    if ($codeArr['qty_order'] == $purchaseorderProductItem->getQty()) {
                                        continue;
                                    }
                                    $sqlOlds .= 'UPDATE ' . $installer->getTable('inventory/purchaseorderproduct') . ' 
                                                                            SET `qty` = \'' . $codeArr['qty_order'] . '\'
                                                                                    WHERE `purchase_order_product_id` =' . $purchaseorderProductItem->getId() . ';';
                                    if ($countSqlOlds == 900) {
                                        $writeConnection->query($sqlOlds);
                                        $countSqlOlds = 0;
                                    }
                                } else {
                                    $sqlCount++;
                                    $product_id = $pId;
                                    $product_name = $productModel->getName();
                                    $product_sku = $productModel->getSku();
                                    $purchase_order_id = $model->getId();
                                    $qty = 0;
                                    $codeArr['qty_order'] = 0;
                                    $cost = 0;
                                    $discount = 0;
                                    $tax = 0;
                                    foreach ($codeArr as $codeId => $code) {
//                                        if ($codeId != 'qty_order') {
                                        if (!in_array($codeId, array('qty_order', 'cost_product', 'tax', 'discount'))) {
                                            $codeId = explode('_', $codeId);
                                            if ($codeId[1]) {
                                                if (!$code || $code < 0)
                                                    $code = 0;
                                                $sqlWarehouseNew[] = array(
                                                    'purchase_order_id' => $purchase_order_id,
                                                    'product_id' => $product_id,
                                                    'product_name' => $product_name,
                                                    'product_sku' => $product_sku,
                                                    'warehouse_id' => $codeId[1],
                                                    'warehouse_name' => Mage::getModel('inventory/warehouse')->load($codeId[1])->getName(),
                                                    'qty_order' => $code
                                                );
                                                $qty += $code;
                                                $codeArr['qty_order'] += $code;
                                                if (count($sqlWarehouseNew) == 1000) {
                                                    $writeConnection->insertMultiple($installer->getTable('erp_inventory_purchase_order_product_warehouse'), $sqlWarehouseNew);
                                                    $sqlWarehouseNew = array();
                                                }
                                            }
                                        }
                                    }

                                    $cost = $codeArr['cost_product'];
                                    $discount = $codeArr['discount'];
                                    $tax = $codeArr['tax'];
                                    $totalAmounts += $codeArr['qty_order'] * $cost * (1 - $discount / 100 + $tax / 100);
                                    if (!$this->getRequest()->getParam('id')) {
                                        if ($productInfo->getId()) {
                                            if (Mage::getStoreConfig('inventory/supplier/update_auto')) {
                                                if (Mage::getStoreConfig('inventory/supplier/update_method') == 'last_purchase_order') {
                                                    try {
                                                        $productInfo->setCost($cost / $changeRateNow)
                                                            ->setTax($tax)
                                                            ->setDiscount($discount)
                                                            ->save();
                                                    } catch (Exception $e) {
                                                        
                                                    }
                                                } elseif (Mage::getStoreConfig('inventory/supplier/update_method') == 'average') {
                                                    $oldPurchaseorderProducts = Mage::getModel('inventory/purchaseorderproduct')
                                                        ->getCollection();
                                                    $supplierIdNow = $productInfo->getSupplierId();
                                                    $productIdNow = $productInfo->getProductId();
                                                    $oldPurchaseorderProducts->getSelect()
                                                        ->join(array('purchaseorder' => $oldPurchaseorderProducts->getTable('inventory/purchaseorder')), "main_table.purchase_order_id=purchaseorder.purchase_order_id AND purchaseorder.supplier_id=$supplierIdNow
                                                                        AND main_table.product_id = $productIdNow", array(''));
                                                    $times = 0;
                                                    $totalCost = 0;
                                                    $totalDiscount = 0;
                                                    $totalTax = 0;
                                                    foreach ($oldPurchaseorderProducts as $oldPurchaseorderProduct) {
                                                        $times++;
                                                        $purchaseOrderOld = Mage::getModel('inventory/purchaseorder')->load($oldPurchaseorderProduct->getData('purchase_order_id'));
                                                        $totalCost += $oldPurchaseorderProduct->getCost() / ($purchaseOrderOld->getChangeRate());
//                                                        $totalCost += $oldPurchaseorderProduct->getCost();
                                                        $totalDiscount += $oldPurchaseorderProduct->getDiscount();
                                                        $totalTax += $oldPurchaseorderProduct->getTax();
                                                    }
                                                    try {
                                                        $productInfo->setCost(($totalCost + $cost / $changeRateNow) / ($times + 1))
                                                            ->setTax(($totalTax + $tax) / ($times + 1))
                                                            ->setDiscount(($totalDiscount + $discount) / ($times + 1))
                                                            ->save();
                                                    } catch (Exception $e) {
                                                        
                                                    }
                                                }
                                            }
                                            //                                        $cost = $productInfo->getCost();
                                            //                                        $discount = $productInfo->getDiscount();
                                            //                                        $tax = $productInfo->getTax();
                                            //                                        $totalAmounts += $codeArr['qty_order'] * $productInfo->getCost() * (1 - $productInfo->getDiscount() / 100 + $productInfo->getTax() / 100);
                                        }
                                    }
                                    $sqlNews[] = array(
                                        'product_id' => $product_id,
                                        'product_name' => $product_name,
                                        'product_sku' => $product_sku,
                                        'purchase_order_id' => $purchase_order_id,
                                        'qty' => $qty,
                                        'cost' => $cost,
                                        'discount' => $discount,
                                        'tax' => $tax
                                    );

                                    if (count($sqlNews) == 1000) {
                                        $writeConnection->insertMultiple($installer->getTable('inventory/purchaseorderproduct'), $sqlNews);
                                        $sqlNews = array();
                                    }

                                    $totalProducts += $codeArr['qty_order'];
                                }
                            }
                        }
                        if (!empty($sqlNews)) {
                            $writeConnection->insertMultiple($installer->getTable('inventory/purchaseorderproduct'), $sqlNews);
                        }
                        if (!empty($sqlWarehouseNew)) {
                            $writeConnection->insertMultiple($installer->getTable('erp_inventory_purchase_order_product_warehouse'), $sqlWarehouseNew);
                        }
                        if (!empty($sqlOlds)) {
                            $writeConnection->query($sqlOlds);
                        }
                        $writeConnection->commit();
                        $productDeletes = Mage::getModel('inventory/purchaseorderproduct')->getCollection()
                            ->addFieldToFilter('purchase_order_id', $model->getId())
                            ->addFieldToFilter('product_id', array('nin' => $productIds));
                        if (count($productDeletes) > 0) {
                            foreach ($productDeletes as $productDelete)
                                $productDelete->delete();
                        }
                    }

                    $model->setTotalProducts($totalProducts)
                        ->setTotalAmount($totalAmounts)
                        ->save();
                }

                if (array_key_exists('send_mail', $data)) {
                    $this->sendEmail($data['supplier_id'], $sqlNews, $purchaseOrderId);
                }

                if (!$this->getRequest()->getParam('id')) {
                    if ($totalProducts <= 0) {
                        $model->delete();
                        Mage::getSingleton('adminhtml/session')->addError(
                            Mage::helper('inventory')->__('Please fill qty for product(s) to purchase order!')
                        );
                        $this->_redirect('*/*/new');
                        return;
                    }
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('inventory')->__('Purchase order was successfully saved!')
                );
                if ($data['status'] == 6 && !$this->getRequest()->getParam('id')) {
                    $this->_redirect('*/*/allDelivery', array('purchaseorder_id' => $model->getId()));
                    return;
                }
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
            Mage::helper('inventory')->__('Unable to find Purchase order to save!')
        );
        $this->_redirect('*/*/');
    }

    public function massStatusAction() {
        $purchaseOrderIds = $this->getRequest()->getParam('inventory');
        if (!is_array($purchaseOrderIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select purchase order(s)!'));
        } else {
            try {
                foreach ($purchaseOrderIds as $purchaseOrderId) {
                    Mage::getSingleton('inventory/purchaseorder')
                        ->load($purchaseOrderId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total %d record(s) were successfully updated', count($purchaseOrderIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function exportCsvAction() {
        $fileName = 'purchase_order.csv';
        $content = $this->getLayout()
            ->createBlock('inventory/adminhtml_purchaseorder_grid')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function exportcsvpurchaseorderAction() {
        //new version - pdf
        require("lib/Magestore/Pdf/html2fpdf.php");
        $purchaseOrderId = $this->getRequest()->getParam('id');
        $purchaseOrderProducts = Mage::getModel('inventory/purchaseorderproduct')->getCollection()
            ->addFieldToFilter('purchase_order_id', $purchaseOrderId);

        $sqlNews = $purchaseOrderProducts->getData();
        $img = Mage::getDesign()->getSkinUrl('images/logo_email.gif',array('_area'=>'frontend'));
        $contents = '<div><img src="'.$img.'" /></div>';
        $contents .= $this->getLayout()->createBlock('inventory/adminhtml_email')
                                      ->setPurchaseorderid($purchaseOrderId)
                                      ->setSqlnews($sqlNews)
                                      ->setTemplate('inventory/email/sendtosupplier.phtml')
                                      ->toHtml();	
	$pdf = new HTML2FPDF('P', 'mm', 'Legal'); 
	$pdf->AddPage(); 
	$pdf->WriteHTML($contents);
        $fileName = 'PurchaseOrder #'.$purchaseOrderId;
	$pdf->Output($fileName.'.pdf', 'D'); 
        
        //old version
        /* die();
          $resource = Mage::getSingleton('core/resource');
          $readConnection = $resource->getConnection('core_read');
          $installer = Mage::getModel('core/resource_setup');
          $purchaseOrderId = $this->getRequest()->getParam('id');
          $purchaseOrder = Mage::getModel('inventory/purchaseorder')->load($purchaseOrderId);
          $supplierId = $purchaseOrder->getSupplierId();
          $supplierName = $purchaseOrder->getSupplierName();
          $supplier = Mage::getModel('inventory/supplier')->load($supplierId);
          $content = '';
          $headers_temp = '"{{header}}"';
          $header_content = new Varien_Object(
          array(
          'header' => 'Purchase order on ' . $purchaseOrder->getPurchaseOn()
          )
          );
          $content .= $header_content->toString($headers_temp) . "\n\n";
          $supplierName_temp = '"{{SupplierName}}"';
          $supplierName_content = new Varien_Object(
          array(
          'SupplierName' => Mage::helper('inventory')->__('Supplier Name: ') . $supplierName
          )
          );
          $content .= $supplierName_content->toString($supplierName_temp) . "\n";
          if ($supplier) {
          $supplierField = '';
          $data = $supplier->getData();
          $supplierField = "\n" . $data['street'];
          if (isset($data['state'])) {
          $supplierField .= " - " . $data['state'];
          }
          $supplierField .= " - " . $data['city'];
          $supplierField .= "\n" . $this->__('Telephone: ') . $data['telephone'];
          $supplierField .= "\n" . $this->__('Email: ') . $data['email'];
          $sAddress_temp = '"{{SupplierAddr}}"';
          $sAddress_content = new Varien_Object(
          array(
          'SupplierAddr' => Mage::helper('inventory')->__('Supplier Address: ') . $supplierField
          )
          );
          $content .= $sAddress_content->toString($sAddress_temp) . "\n";
          }
          $warehouseIds = $purchaseOrder->getWarehouseId();
          $warehouseIds = explode(',', $warehouseIds);
          $data = array(
          'ID' => Mage::helper('inventory')->__('ID'),
          'Name' => Mage::helper('inventory')->__('Name'),
          'SKU' => Mage::helper('inventory')->__('SKU'),
          'Cost' => Mage::helper('inventory')->__('Cost'),
          'Tax' => Mage::helper('inventory')->__('Tax'),
          'Discount' => Mage::helper('inventory')->__('Discount'),
          'TotalQty' => Mage::helper('inventory')->__('Total Qty Order'),
          );
          foreach ($warehouseIds as $warehouseId) {
          $data['warehose_' . $warehouseId] = 'Order Qty for ' . Mage::getModel('inventory/warehouse')->load($warehouseId)->getName();
          }

          $productList_content = new Varien_Object($data);
          $productList_temp = '';
          $count = 0;
          foreach ($data as $dataId => $code) {
          if ($count != 0)
          $productList_temp .= ',';
          $productList_temp .= '"{{' . $dataId . '}}"';
          $count++;
          }
          $content .= $productList_content->toString($productList_temp);
          $purchaseOrderProducts = Mage::getModel('inventory/purchaseorderproduct')
          ->getCollection()
          ->addFieldToFilter('purchase_order_id', $purchaseOrderId);
          foreach ($purchaseOrderProducts as $purchaseOrderProduct) {
          $info = array();
          $info['ID'] = $purchaseOrderProduct->getProductId();
          $info['Name'] = $purchaseOrderProduct->getProductName();
          $info['SKU'] = $purchaseOrderProduct->getProductSku();
          $info['Cost'] = $purchaseOrderProduct->getCost();
          $info['Tax'] = $purchaseOrderProduct->getTax();
          $info['Discount'] = $purchaseOrderProduct->getDiscount();
          $info['TotalQty'] = $purchaseOrderProduct->getQty();
          foreach ($warehouseIds as $warehouseId) {
          $sql = 'SELECT qty_order from ' . $installer->getTable("erp_inventory_purchase_order_product_warehouse") . ' WHERE (purchase_order_id = ' . $purchaseOrderId . ') AND (product_id = ' . $purchaseOrderProduct->getProductId() . ') AND (warehouse_id = ' . $warehouseId . ')';
          $results = $readConnection->fetchAll($sql);
          foreach ($results as $result) {
          $info['warehose_' . $warehouseId] = $result['qty_order'];
          }
          }
          $csv_content = new Varien_Object($info);
          $content .= "\n";
          $content .= $csv_content->toString($productList_temp);
          }
          $this->_prepareDownloadResponse('purchaseorder.csv', $content);

         */
    }

    public function exportXmlAction() {
        $fileName = 'purchase_order.xml';
        $content = $this->getLayout()
            ->createBlock('inventory/adminhtml_purchaseorder_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('inventory');
    }

    public function productAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('inventory.purchaseorder.edit.tab.products')
            ->setProducts($this->getRequest()->getPost('purchaseorder_products', null));
        if ($warehouseIds = $this->getRequest()->getParam('warehouse_ids')) {
            $warehouseIds = explode(',', $warehouseIds);
            $addmore = '';
            foreach ($warehouseIds as $warehouseId) {
                $this->getLayout()->getBlock('related_grid_serializer')->addColumnInputName('warehouse_' . $warehouseId);
            }
        } elseif ($this->getRequest()->getParam('id')) {
            $resource = Mage::getSingleton('core/resource');
            $readConnection = $resource->getConnection('core_read');
            $installer = Mage::getModel('core/resource_setup');
            $sql = 'SELECT distinct(`warehouse_id`) from ' . $installer->getTable("erp_inventory_purchase_order_product_warehouse") . ' WHERE (purchase_order_id = ' . $this->getRequest()->getParam("id") . ')';
            $results = $readConnection->fetchAll($sql);
            foreach ($results as $result) {
                $this->getLayout()->getBlock('related_grid_serializer')->addColumnInputName('warehouse_' . $result['warehouse_id']);
            }
        }
        $this->getLayout()->getBlock('related_grid_serializer')->addColumnInputName('cost_product');
        $this->getLayout()->getBlock('related_grid_serializer')->addColumnInputName('tax');
        $this->getLayout()->getBlock('related_grid_serializer')->addColumnInputName('discount');
        $this->renderLayout();
        if (Mage::getModel('admin/session')->getData('purchaseorder_product_import')) {
            Mage::getModel('admin/session')->setData('purchaseorder_product_import', null);
        }
    }

    public function productGridAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('inventory.purchaseorder.edit.tab.products')
            ->setProducts($this->getRequest()->getPost('purchaseorder_products', null));
        $this->renderLayout();
    }

    public function deliveryAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('inventory.purchaseorder.edit.tab.delivery')
            ->setDeliveries($this->getRequest()->getPost('isdeliveries', null));
        $this->renderLayout();
    }

    public function deliveryGridAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('inventory.purchaseorder.edit.tab.delivery')
            ->setDeliveries($this->getRequest()->getPost('isdeliveries', null));
        $this->renderLayout();
    }

    public function newDeliveryAction() {
        $purchaseOrderId = $this->getRequest()->getParam('purchaseorder_id');
        $model = Mage::getModel('inventory/purchaseorder')->load($purchaseOrderId);

        if ($model->getId() || $purchaseOrderId == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('purchaseorder_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('inventory/purchaseorder');

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('inventory/adminhtml_purchaseorder_editdelivery'))
                ->_addLeft($this->getLayout()->createBlock('inventory/adminhtml_purchaseorder_editdelivery_tabs'));
            $this->renderLayout();
            if (Mage::getModel('admin/session')->getData('delivery_purchaseorder_product_import')) {
                Mage::getModel('admin/session')->setData('delivery_purchaseorder_product_import', null);
            }
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('inventory')->__('Item does not exist')
            );
            $this->_redirect('*/*/');
        }
    }

    public function prepareDeliveryAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('inventory.purchaseorder.edit.tab.preparedelivery')
            ->setProducts($this->getRequest()->getPost('isproducts', null));
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $installer = Mage::getModel('core/resource_setup');
        $sql = 'SELECT distinct(`warehouse_id`) from ' . $installer->getTable("erp_inventory_purchase_order_product_warehouse") . ' WHERE (purchase_order_id = ' . $this->getRequest()->getParam("purchaseorder_id") . ')';
        $results = $readConnection->fetchAll($sql);
        foreach ($results as $result) {
            $this->getLayout()->getBlock('related_grid_serializer')->addColumnInputName('warehouse_' . $result['warehouse_id']);
        }
        $this->renderLayout();
        if (Mage::getModel('admin/session')->getData('delivery_purchaseorder_product_import')) {
            Mage::getModel('admin/session')->setData('delivery_purchaseorder_product_import', null);
        }
    }

    public function prepareDeliveryGridAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('inventory.purchaseorder.edit.tab.preparedelivery')
            ->setProducts($this->getRequest()->getPost('isproducts', null));
        $this->renderLayout();
    }

    public function checktimedeliveryAction() {
        $delivery_date = $this->getRequest()->getParam('delivery_date');
        if (!$delivery_date) {
            echo 'error';
        } else {
            $timestamp = Mage::getModel('core/date')->timestamp(time());
            $datestamp = strtotime(date('Y-m-d', $timestamp));
            $deliverydate = strtotime($delivery_date);
            if ($datestamp < $deliverydate) {
                echo 'error';
            }
        }
    }

    public function saveDeliveryAction() {
        $purchaseOrderId = $this->getRequest()->getParam('purchaseorder_id');
        if (!$purchaseOrderId) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('inventory')->__('Unable to find delivery to save!')
            );
            $this->_redirect('*/*/');
        }
        try {
            if ($data = $this->getRequest()->getPost()) {
                $purchaseOrder = Mage::getModel('inventory/purchaseorder')->load($purchaseOrderId);
                if (isset($data['delivery_products'])) {
                    $deliveryProducts = array();
                    parse_str(urldecode($data['delivery_products']), $deliveryProducts);
                    if (count($deliveryProducts)) {
                        $productIds = '';
                        $totalProductDelivery = 0;
                        $resource = Mage::getSingleton('core/resource');
                        $writeConnection = $resource->getConnection('core_write');
                        $installer = Mage::getModel('core/resource_setup');
                        $sqlDeliveryWarehouseNew = array();
                        $receivingData = array();
                        $deliveryIds = array();
                        foreach ($deliveryProducts as $pId => $enCoded) {
                            $codeArr = array();
                            parse_str(base64_decode($enCoded), $codeArr);
                            $purchaseorderProductItem = Mage::getModel('inventory/purchaseorderproduct')
                                ->getCollection()
                                ->addFieldToFilter('purchase_order_id', $purchaseOrderId)
                                ->addFieldToFilter('product_id', $pId)
                                ->getFirstItem();
                            if ($purchaseorderProductItem->getId()) {
                                $sametime = strtotime(now());
                                $maxQtyReceive = $purchaseorderProductItem->getQty() - $purchaseorderProductItem->getQtyRecieved();
                                $codeArr['qty_delivery'] = 0;
                                foreach ($codeArr as $codeId => $code) {
                                    if ($codeId != 'qty_delivery') {
                                        $codeId = explode('_', $codeId);
                                        if ($codeId[1]) {
                                            if (!$code || $code < 0)
                                                $code = 0;
                                            if (($codeArr['qty_delivery'] + $code) > $maxQtyReceive) {
                                                $code = $maxQtyReceive - $codeArr['qty_delivery'];
                                                $codeArr['qty_delivery'] = $maxQtyReceive;
                                            } else {
                                                $codeArr['qty_delivery'] += $code;
                                            }
                                            if ($code > 0) {
                                                $receivingData[$codeId[1]][$pId] = $code;
                                                $sqlDeliveryWarehouseNew[] = array(
                                                    'delivery_date' => $data['delivery_date'],
                                                    'purchase_order_id' => $purchaseOrderId,
                                                    'product_id' => $pId,
                                                    'product_sku' => $purchaseorderProductItem->getProductSku(),
                                                    'product_name' => $purchaseorderProductItem->getProductName(),
                                                    'warehouse_id' => $codeId[1],
                                                    'warehouse_name' => Mage::getModel('inventory/warehouse')->load($codeId[1])->getName(),
                                                    'qty_delivery' => $code,
                                                    'sametime' => $sametime
                                                );

                                                $warehouseId = $codeId[1];
                                                $warehouseProduct = Mage::getModel('inventory/warehouseproduct')
                                                    ->getCollection()
                                                    ->addFieldToFilter('warehouse_id', $warehouseId)
                                                    ->addFieldToFilter('product_id', $pId)
                                                    ->getFirstItem();
                                                try {
                                                    if ($warehouseProduct->getId()) {
                                                        $qty = $warehouseProduct->getQty() + $code;
                                                        $newQtyAvailable = $warehouseProduct->getQtyAvailable() + $code;
                                                        $warehouseProduct->setQty($qty)
                                                            ->setQtyAvailable($newQtyAvailable)
                                                            ->save();
                                                    } else {
                                                        $warehouseProduct = Mage::getModel('inventory/warehouseproduct');
                                                        $warehouseProduct->setWarehouseId($warehouseId)
                                                            ->setProductId($pId)
                                                            ->setQty($code)
                                                            ->setQtyAvailable($code)
                                                            ->save();
                                                    }
                                                } catch (Exception $e) {
                                                    
                                                }
                                                $pName = $purchaseorderProductItem->getProductName();
                                                $pName = mysql_escape_string($pName);
                                                $pSku = $purchaseorderProductItem->getProductSku();
                                                $amountReceived = ($purchaseorderProductItem->getCost()) * (100 + $purchaseorderProductItem->getTax() - $purchaseorderProductItem->getDiscount()) / 100;
                                                $sqlReportProductReceived = 'INSERT INTO ' . $installer->getTable("erp_inventory_report_products_received") . '
                                                    (product_id,product_name,product_sku,amount_received,qty_received,received_at,received_type)' . '
                                                    VALUES ("' . $pId . '","' . $pName . '","' . $pSku . '","' . $amountReceived . '","' . $code . '","' . $data['delivery_date'] . '","1")'; //1 for delivery
                                                $writeConnection->query($sqlReportProductReceived);

                                                $sqlWarehouseProductReceived = 'UPDATE ' . $installer->getTable("erp_inventory_purchase_order_product_warehouse") . ' SET qty_received = qty_received + ' . $code . ' 
                                                                            WHERE (product_id = ' . $pId . ') AND (purchase_order_id = ' . $purchaseOrderId . ') AND (warehouse_id = ' . $codeId[1] . ')';
                                                $writeConnection->query($sqlWarehouseProductReceived);
                                                if (count($sqlDeliveryWarehouseNew) == 1000) {
                                                    $writeConnection->insertMultiple($installer->getTable('erp_inventory_delivery_warehouse'), $sqlDeliveryWarehouseNew);
                                                    $sqlDeliveryWarehouseNew = array();
                                                }
                                            }
                                        }
                                    }
                                }
                                if ($codeArr['qty_delivery'] > $maxQtyReceive)
                                    $codeArr['qty_delivery'] = $maxQtyReceive;
                                if (!$codeArr['qty_delivery'] || $codeArr['qty_delivery'] <= 0)
                                    continue;
                                $purchaseorderProductItem->setQtyRecieved($purchaseorderProductItem->getQtyRecieved() + $codeArr['qty_delivery'])
                                    ->save();
                                $admin = Mage::getModel('admin/session')->getUser()->getUsername();
                                $delivery = Mage::getModel('inventory/delivery');
                                $delivery->setDeliveryDate($data['delivery_date'])
                                    ->setQtyDelivery($codeArr['qty_delivery'])
                                    ->setPurchaseOrderId($purchaseOrderId)
                                    ->setProductId($pId)
                                    ->setProductName($purchaseorderProductItem->getProductName())
                                    ->setProductSku($purchaseorderProductItem->getProductSku())
                                    ->setSametime($sametime)
                                    ->setCreateBy($admin)
                                    ->save();
                                $totalProductDelivery += $codeArr['qty_delivery'];
                                $deliveryIds[] = $delivery->getId();
                            }
                        }
                        $admin = Mage::getModel('admin/session')->getUser()->getUsername();
                        if (count($deliveryIds)) {
                            $purchaseOrderHistory = Mage::getModel('inventory/purchaseorderhistory');
                            $purchaseOrderHistoryContent = Mage::getModel('inventory/purchaseorderhistorycontent');
                            $purchaseOrderHistory->setData('purchase_order_id', $purchaseOrderId)
                                ->setData('time_stamp', now())
                                ->setData('create_by', $admin)
                                ->save();
                            $purchaseOrderHistoryContent->setData('purchase_order_history_id', $purchaseOrderHistory->getId())
                                ->setData('field_name', $admin . ' created delivery for this purchase order!')
                                ->setData('new_value', 'Delivery id(s): ' . implode(",", $deliveryIds))
                                ->save();
                        }
                        if (!empty($sqlDeliveryWarehouseNew)) {
                            $writeConnection->insertMultiple($installer->getTable('erp_inventory_delivery_warehouse'), $sqlDeliveryWarehouseNew);
                        }
                        if ($totalProductDelivery == 0) {
                            Mage::getSingleton('adminhtml/session')->addError(
                                Mage::helper('inventory')->__('Please select product and enter Qty greater than 0 to create delivery!')
                            );

                            $this->_redirect('*/*/newdelivery', array(
                                'purchaseorder_id' => $purchaseOrderId,
                                'action' => 'newdelivery',
                                'active' => 'delivery'
                            ));
                            return;
                        }
                    } else {
                        Mage::getSingleton('adminhtml/session')->addError(
                            Mage::helper('inventory')->__(' Please select product(s) to create delivery!')
                        );

                        $this->_redirect('*/*/newdelivery', array(
                            'purchaseorder_id' => $purchaseOrderId,
                            'action' => 'newdelivery',
                            'active' => 'delivery'
                        ));
                        return;
                    }
                } else {
                    Mage::getSingleton('adminhtml/session')->addError(
                        Mage::helper('inventory')->__('Please select product(s) to create delivery!')
                    );
                    $this->_redirect('*/*/newdelivery', array(
                        'purchaseorder_id' => $purchaseOrderId,
                        'action' => 'newdelivery',
                        'active' => 'delivery'
                    ));
                    return;
                }
                /* auto create receiving */
                if (count($receivingData)) {
                    foreach ($receivingData as $rData => $rCode) {
                        $stockReceiving = Mage::getModel('inventory/stockreceiving');
                        $stockReceiving->setWarehouseIdTo($rData)
                            ->setType('2')
                            ->setWarehouseNameTo(Mage::getModel('inventory/warehouse')->load($rData)->getName())
                            ->setCreatedAt($data['delivery_date'])
                            ->save();
                        $totalProductDeliveryWarehouse = 0;
                        $sqlStockReceivingNew = array();
                        foreach ($rCode as $rPId => $rPQty) {
                            $totalProductDeliveryWarehouse += $rPQty;
                            $sqlStockReceivingNew[] = array(
                                'stock_receiving_id' => $stockReceiving->getId(),
                                'product_id' => $rPId,
                                'qty' => $rPQty
                            );
                            if (count($sqlStockReceivingNew) == 1000) {
                                $writeConnection->insertMultiple($installer->getTable('inventory/stockreceivingproduct'), $sqlStockReceivingNew);
                                $sqlStockReceivingNew = array();
                            }
                        }
                        if (!empty($sqlStockReceivingNew)) {
                            $writeConnection->insertMultiple($installer->getTable('inventory/stockreceivingproduct'), $sqlStockReceivingNew);
                        }
                        $stockReceiving->setTotalProducts($totalProductDeliveryWarehouse)->save();

                        //auto create transaction
                        $transaction = Mage::getModel('inventory/transaction');
                        $transaction->setType('3')
                            ->setFromId($purchaseOrder->getSupplierId())
                            ->setFromName($purchaseOrder->getSupplierName())
                            ->setToId($rData)
                            ->setToName(Mage::getModel('inventory/warehouse')->load($rData)->getName())
                            ->setCreatedAt(now())
                            ->setCreatedBy($admin)
                            ->setReason('Receive from purchase order #' . $purchaseOrderId)
                            ->save();
                        //transaction products
                        $totalProductTransaction = 0;
                        $sqlTransactionNew = array();
                        foreach ($rCode as $rPId => $rPQty) {
                            $product = Mage::getModel('catalog/product')->load($rPId);
                            $totalProductTransaction += $rPQty;
                            $sqlTransactionNew[] = array(
                                'transaction_id' => $transaction->getId(),
                                'product_id' => $rPId,
                                'product_sku' => $product->getSku(),
                                'product_name' => $product->getName(),
                                'qty' => $rPQty
                            );
                            if (count($sqlTransactionNew) == 1000) {
                                $writeConnection->insertMultiple($installer->getTable('inventory/transactionproduct'), $sqlTransactionNew);
                                $sqlTransactionNew = array();
                            }
                        }
                        if (!empty($sqlTransactionNew)) {
                            $writeConnection->insertMultiple($installer->getTable('inventory/transactionproduct'), $sqlTransactionNew);
                        }
                        $transaction->setTotalProducts($totalProductTransaction)->save();
                    }
                }
                $totalProductOrder = 0;
                $totalProductReceived = 0;
                $purchaseOrderProducts = Mage::getModel('inventory/purchaseorderproduct')->getCollection()
                    ->addFieldToFilter('purchase_order_id', $purchaseOrderId);
                $purchaseOrder->setStatus(6);


                foreach ($purchaseOrderProducts as $purchaseOrderProduct) {
                    if ($purchaseOrderProduct->getQtyRecieved() < $purchaseOrderProduct->getQty()) {
                        $purchaseOrder->setStatus(5);
                    }
                    $totalProductOrder += $purchaseOrderProduct->getQty();
                    $totalProductReceived += $purchaseOrderProduct->getQtyRecieved();
                }
                $process = round(($totalProductReceived / $totalProductOrder) * 100, 2);
                $purchaseOrder->setDeliveryProcess($process)->save();
                $totalProduct_Recieved = $purchaseOrder->getData('total_products_recieved') + $totalProductDelivery;
                $purchaseOrder->setTotalProductsRecieved($totalProduct_Recieved);
                $purchaseOrder->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('inventory')->__('Delivery was successfully saved!')
                );


                Mage::getSingleton('adminhtml/session')->setFormData(false);
                $this->_redirect('inventoryadmin/adminhtml_purchaseorder/edit', array('id' => $this->getRequest()->getParam('purchaseorder_id'), 'active' => 'delivery'));
                return;
            }
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            Mage::getSingleton('adminhtml/session')->setFormData($data);
            $this->_redirect('*/*/');
            return;
        }

        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('inventory')->__('Unable to find delivery to save!')
        );
        $this->_redirect('*/*/');
    }

    public function allDeliveryAction() {
        $purchaseOrderId = $this->getRequest()->getParam('purchaseorder_id');
        if (!$purchaseOrderId) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('inventory')->__('Unable to find delivery to save!')
            );
            $this->_redirect('*/*/');
        }
        try {
            $purchaseOrder = Mage::getModel('inventory/purchaseorder')->load($purchaseOrderId);
            $purchaseOrderProducts = Mage::getResourceModel('inventory/purchaseorderproduct_collection')
                ->addFieldToFilter('purchase_order_id', $this->getRequest()->getParam('purchaseorder_id'));
            $totalProductDelivery = 0;
            if (count($purchaseOrderProducts)) {
                $resource = Mage::getSingleton('core/resource');
                $writeConnection = $resource->getConnection('core_write');
                $readConnection = $resource->getConnection('core_read');
                $installer = Mage::getModel('core/resource_setup');
                $sqlDeliveryWarehouseNew = array();
                $receivingData = array();
                $deliveryIds = array();
                $productInfo = array();
                foreach ($purchaseOrderProducts as $product) {
                    $sametime = strtotime(now());
                    $sql = 'SELECT warehouse_id,qty_order,qty_received from ' . $installer->getTable("erp_inventory_purchase_order_product_warehouse") . ' WHERE (purchase_order_id = ' . $this->getRequest()->getParam("purchaseorder_id") . ') AND (product_id = ' . $product->getProductId() . ')';
                    $results = $readConnection->fetchAll($sql);
                    $qtyDeliveries = 0;
                    $maxQtyReceive = $product->getQty() - $product->getQtyRecieved();
                    foreach ($results as $result) {
                        $qtyDefault = $result['qty_order'] - $result['qty_received'];
                        if ($qtyDefault < 0)
                            $qtyDefault = 0;
                        if ($qtyDefault > 0) {
                            if ($qtyDeliveries + $qtyDefault > $maxQtyReceive) {
                                $qtyDefault = $maxQtyReceive - $qtyDeliveries;
                                $qtyDeliveries = $maxQtyReceive;
                            } else {
                                $qtyDeliveries += $qtyDefault;
                            }
                            $totalProductDelivery += $qtyDefault;
                            if ($qtyDefault > 0) {
                                $receivingData[$result['warehouse_id']][$product->getProductId()] = $qtyDefault;
                                $productInfo[$product->getProductId()]['sku'] = $product->getProductSku();
                                $productInfo[$product->getProductId()]['name'] = $product->getProductName();
                                $sqlDeliveryWarehouseNew[] = array(
                                    'delivery_date' => now(),
                                    'purchase_order_id' => $purchaseOrderId,
                                    'product_id' => $product->getProductId(),
                                    'product_sku' => $product->getProductSku(),
                                    'product_name' => $product->getProductName(),
                                    'warehouse_id' => $result['warehouse_id'],
                                    'warehouse_name' => Mage::getModel('inventory/warehouse')->load($result['warehouse_id'])->getName(),
                                    'qty_delivery' => $qtyDefault,
                                    'sametime' => $sametime
                                );

                                $warehouseId = $result['warehouse_id'];
                                $warehouseProduct = Mage::getModel('inventory/warehouseproduct')
                                    ->getCollection()
                                    ->addFieldToFilter('warehouse_id', $warehouseId)
                                    ->addFieldToFilter('product_id', $product->getProductId())
                                    ->getFirstItem();
                                try {
                                    if ($warehouseProduct->getId()) {
                                        $qty = $warehouseProduct->getQty() + $qtyDefault;
                                        $newQtyAvailable = $warehouseProduct->getQtyAvailable() + $qtyDefault;
                                        $warehouseProduct->setQty($qty)
                                            ->setQtyAvailable($newQtyAvailable)
                                            ->save();
                                    } else {
                                        $warehouseProduct = Mage::getModel('inventory/warehouseproduct');
                                        $warehouseProduct->setWarehouseId($warehouseId)
                                            ->setProductId($product->getProductId())
                                            ->setQty($qtyDefault)
                                            ->setQtyAvailable($qtyDefault)
                                            ->save();
                                    }
                                } catch (Exception $e) {
                                    
                                }

                                $pName = $product->getProductName();
                                $pName = mysql_escape_string($pName);
                                $pSku = $product->getProductSku();
                                $amountReceived = ($product->getCost()) * (100 + $product->getTax() - $product->getDiscount()) / 100;
                                $sqlReportProductReceived = 'INSERT INTO ' . $installer->getTable("erp_inventory_report_products_received") . '
                                    (product_id,product_name,product_sku,amount_received,qty_received,received_at,received_type)' . '
                                    VALUES ("' . $product->getProductId() . '","' . $pName . '","' . $pSku . '","' . $amountReceived . '","' . $qtyDefault . '","' . now() . '","1")'; //1 for delivery
                                $writeConnection->query($sqlReportProductReceived);

                                $sqlWarehouseProductReceived = 'UPDATE ' . $installer->getTable("erp_inventory_purchase_order_product_warehouse") . ' SET qty_received = qty_received + ' . $qtyDefault . ' WHERE (product_id = ' . $product->getProductId() . ') AND (purchase_order_id = ' . $purchaseOrderId . ') AND (warehouse_id = ' . $result['warehouse_id'] . ')';
                                $writeConnection->query($sqlWarehouseProductReceived);
                                if (count($sqlDeliveryWarehouseNew) == 1000) {
                                    $writeConnection->insertMultiple($installer->getTable('erp_inventory_delivery_warehouse'), $sqlDeliveryWarehouseNew);
                                    $sqlDeliveryWarehouseNew = array();
                                }
                            }
                        }
                    }
                    if ($qtyDeliveries > $maxQtyReceive)
                        $qtyDeliveries = $maxQtyReceive;
                    if (!$qtyDeliveries || $qtyDeliveries <= 0)
                        continue;
                    $product->setQtyRecieved($product->getQtyRecieved() + $qtyDeliveries)
                        ->save();
                    $admin = Mage::getModel('admin/session')->getUser()->getUsername();
                    $delivery = Mage::getModel('inventory/delivery');
                    $delivery->setDeliveryDate(now())
                        ->setQtyDelivery($qtyDeliveries)
                        ->setPurchaseOrderId($purchaseOrderId)
                        ->setProductId($product->getProductId())
                        ->setProductName($product->getProductName())
                        ->setProductSku($product->getProductSku())
                        ->setSametime($sametime)
                        ->setCreateBy($admin)
                        ->save();
                    $deliveryIds[] = $delivery->getId();
                }

                //history change
                $admin = Mage::getModel('admin/session')->getUser()->getUsername();
                if (count($deliveryIds)) {
                    $purchaseOrderHistory = Mage::getModel('inventory/purchaseorderhistory');
                    $purchaseOrderHistoryContent = Mage::getModel('inventory/purchaseorderhistorycontent');
                    $purchaseOrderHistory->setData('purchase_order_id', $purchaseOrderId)
                        ->setData('time_stamp', now())
                        ->setData('create_by', $admin)
                        ->save();
                    $purchaseOrderHistoryContent->setData('purchase_order_history_id', $purchaseOrderHistory->getId())
                        ->setData('field_name', $admin . ' created delivery for this purchase order!')
                        ->setData('new_value', 'Delivery id(s): ' . implode(",", $deliveryIds))
                        ->save();
                }

                if (!empty($sqlDeliveryWarehouseNew)) {
                    $writeConnection->insertMultiple($installer->getTable('erp_inventory_delivery_warehouse'), $sqlDeliveryWarehouseNew);
                }
                if ($totalProductDelivery == 0) {
                    Mage::getSingleton('adminhtml/session')->addError(
                        Mage::helper('inventory')->__('Please select product and enter qty delivery for product to create delivery')
                    );

                    $this->_redirect('inventoryadmin/adminhtml_purchaseorder/edit', array('id' => $this->getRequest()->getParam('purchaseorder_id'), 'active' => 'delivery'));
                    return;
                }

                /* auto create receiving */
                if (count($receivingData)) {

                    foreach ($receivingData as $rData => $rCode) {
                        $stockReceiving = Mage::getModel('inventory/stockreceiving');
                        $stockReceiving->setWarehouseIdTo($rData)
                            ->setType('2')
                            ->setWarehouseNameTo(Mage::getModel('inventory/warehouse')->load($rData)->getName())
                            ->setCreatedAt(now())
                            ->save();
                        $totalProductDeliveryWarehouse = 0;
                        $sqlStockReceivingNew = array();
                        foreach ($rCode as $rPId => $rPQty) {
                            $totalProductDeliveryWarehouse += $rPQty;
                            $sqlStockReceivingNew[] = array(
                                'stock_receiving_id' => $stockReceiving->getId(),
                                'product_id' => $rPId,
                                'qty' => $rPQty
                            );
                            if (count($sqlStockReceivingNew) == 1000) {
                                $writeConnection->insertMultiple($installer->getTable('inventory/stockreceivingproduct'), $sqlStockReceivingNew);
                                $sqlStockReceivingNew = array();
                            }
                        }
                        if (!empty($sqlStockReceivingNew)) {
                            $writeConnection->insertMultiple($installer->getTable('inventory/stockreceivingproduct'), $sqlStockReceivingNew);
                        }
                        $stockReceiving->setTotalProducts($totalProductDeliveryWarehouse)->save();

                        //auto create transaction
                        $transaction = Mage::getModel('inventory/transaction');
                        $transaction->setType('3')
                            ->setFromId($purchaseOrder->getSupplierId())
                            ->setFromName($purchaseOrder->getSupplierName())
                            ->setToId($rData)
                            ->setToName(Mage::getModel('inventory/warehouse')->load($rData)->getName())
                            ->setCreatedAt(now())
                            ->setCreatedBy($admin)
                            ->setReason('Receive from purchase order #' . $purchaseOrderId)
                            ->save();
                        //transaction products
                        $totalProductTransaction = 0;
                        $sqlTransactionNew = array();
                        foreach ($rCode as $rPId => $rPQty) {
                            $totalProductTransaction += $rPQty;
                            $sqlTransactionNew[] = array(
                                'transaction_id' => $transaction->getId(),
                                'product_id' => $rPId,
                                'product_sku' => $productInfo[$rPId]['sku'],
                                'product_name' => $productInfo[$rPId]['name'],
                                'qty' => $rPQty
                            );
                            if (count($sqlTransactionNew) == 1000) {
                                $writeConnection->insertMultiple($installer->getTable('inventory/transactionproduct'), $sqlTransactionNew);
                                $sqlTransactionNew = array();
                            }
                        }
                        if (!empty($sqlTransactionNew)) {
                            $writeConnection->insertMultiple($installer->getTable('inventory/transactionproduct'), $sqlTransactionNew);
                        }
                        $transaction->setTotalProducts($totalProductTransaction)->save();
                    }
                }
                $totalProductOrder = 0;
                $totalProductReceived = 0;
                $purchaseOrderProducts = Mage::getModel('inventory/purchaseorderproduct')->getCollection()
                    ->addFieldToFilter('purchase_order_id', $purchaseOrderId);
                $purchaseOrder->setStatus(6);

                foreach ($purchaseOrderProducts as $purchaseOrderProduct) {
                    if ($purchaseOrderProduct->getQtyRecieved() < $purchaseOrderProduct->getQty()) {
                        $purchaseOrder->setStatus(5);
                    }
                    $totalProductOrder += $purchaseOrderProduct->getQty();
                    $totalProductReceived += $purchaseOrderProduct->getQtyRecieved();
                }
                $process = round(($totalProductReceived / $totalProductOrder) * 100, 2);
                $purchaseOrder->setDeliveryProcess($process)->save();
                $totalProduct_Recieved = $purchaseOrder->getData('total_products_recieved') + $totalProductDelivery;
                $purchaseOrder->setTotalProductsRecieved($totalProduct_Recieved);
                $purchaseOrder->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('inventory')->__('Delivery was successfully saved!')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                $this->_redirect('inventoryadmin/adminhtml_purchaseorder/edit', array('id' => $this->getRequest()->getParam('purchaseorder_id'), 'active' => 'delivery'));
                return;
            }
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('inventory')->__('Unable to find delivery to save!')
            );
            $this->_redirect('inventoryadmin/adminhtml_purchaseorder/edit', array('id' => $this->getRequest()->getParam('purchaseorder_id'), 'active' => 'delivery'));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('inventory')->__('Unable to find delivery to save!')
            );
            $this->_redirect('inventoryadmin/adminhtml_purchaseorder/edit', array('id' => $this->getRequest()->getParam('purchaseorder_id'), 'active' => 'delivery'));
            return;
        }
    }

    public function saveReturnOrderAction() {
        $purchaseOrderId = $this->getRequest()->getParam('purchaseorder_id');
        if (!$purchaseOrderId) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('inventory')->__('Unable to find return order to save!')
            );
            $this->_redirect('*/*/');
        }
        try {
            if ($data = $this->getRequest()->getPost()) {
                $purchaseOrder = Mage::getModel('inventory/purchaseorder')->load($purchaseOrderId);
                if (isset($data['returnorder_products'])) {
                    $returnorderProducts = array();
                    parse_str(urldecode($data['returnorder_products']), $returnorderProducts);
                    if (count($returnorderProducts)) {
                        $productIds = '';
                        $supplierId = Mage::helper('inventory/purchaseorder')->getDataByPurchaseOrderId($purchaseOrderId, 'supplier_id');
                        $returnOrderModel = Mage::getModel('inventory/returnorder');
                        $returnOrderModel->setPurchaseOrderId($purchaseOrderId);
                        $returnOrderModel->setSupplierId($supplierId);
                        $returnOrderModel->setReturnedOn(date("Y-m-d"));
                        $returnOrderModel->save();
                        $newReturnOrderId = $returnOrderModel->getId();

                        $supplier = Mage::getModel('inventory/supplier')->load($supplierId);
                        $resource = Mage::getSingleton('core/resource');
                        $writeConnection = $resource->getConnection('core_write');
                        $readConnection = $resource->getConnection('core_read');
                        $installer = Mage::getModel('core/resource_setup');
                        $sqlReturnWarehouseNew = array();
                        $issuingData = array();
                        $totalProductReturns = 0;
                        $totalAmountReturned = 0;
                        $returnOrderIds = array();
                        $changeHistory = 0;
                        $returnOrderWarehouseIds = '';
                        $i = 0;
                        $returnWarehouseProducts = array();
                        $products = array();
                        foreach ($returnorderProducts as $pId => $enCoded) {
                            $codeArr = array();
                            parse_str(base64_decode($enCoded), $codeArr);
                            $purchaseorderProductItem = Mage::getModel('inventory/purchaseorderproduct')
                                ->getCollection()
                                ->addFieldToFilter('purchase_order_id', $purchaseOrderId)
                                ->addFieldToFilter('product_id', $pId)
                                ->getFirstItem();
                            if ($purchaseorderProductItem->getId()) {
                                $codeArr['qty_return'] = 0;
                                $totalForAProduct = 0;
                                foreach ($codeArr as $codeId => $code) {
                                    if ($codeId != 'qty_return') {
                                        $codeId = explode('_', $codeId);
                                        if ($codeId[1]) {
                                            $sql = 'SELECT qty_received,qty_returned from ' . $installer->getTable("erp_inventory_purchase_order_product_warehouse") . ' WHERE (purchase_order_id = ' . $purchaseOrderId . ') 
                                                                            AND (product_id = ' . $pId . ') AND (warehouse_id = ' . $codeId[1] . ')';
                                            $results = $readConnection->fetchAll($sql);
                                            $maxQtyReturn = 0;
                                            if (count($results)) {
                                                foreach ($results as $result) {
                                                    $maxQtyReturn = $result['qty_received'] - $result['qty_returned'];
                                                }
                                            }
                                            if (!$code)
                                                $code = 0;
                                            if ($code > $maxQtyReturn) {
                                                $code = $maxQtyReturn;
                                            }
                                            if (floatval($code) > 0) {
                                                $products[$pId] = $code . ',' . $purchaseorderProductItem->getProductSku() . ',' . $purchaseorderProductItem->getProductName();
                                                $returnWarehouseProducts[$codeId[1]] = $products;
                                                $changeHistory = 1;
                                                $totalProductReturns += $code;
                                                $totalForAProduct += $code;
                                                $issuingData[$codeId[1]][$pId] = $code;
                                                $admin = Mage::getModel('admin/session')->getUser()->getUsername();
                                                $returnNewData = array(
                                                    'returned_on' => now(),
                                                    'purchase_order_id' => $purchaseOrderId,
                                                    'product_id' => $pId,
                                                    'product_sku' => $purchaseorderProductItem->getProductSku(),
                                                    'product_name' => $purchaseorderProductItem->getProductName(),
                                                    'warehouse_id' => $codeId[1],
                                                    'warehouse_name' => Mage::getModel('inventory/warehouse')->load($codeId[1])->getName(),
                                                    'qty_return' => $code,
                                                    'create_by' => $admin,
                                                    'reason' => $data['reason']
                                                );
                                                $warehouseId = $codeId[1];
                                                $sqlWarehouseProductReturned = 'UPDATE ' . $installer->getTable("erp_inventory_purchase_order_product_warehouse") . ' SET qty_returned = qty_returned + ' . $code . ' 
                                                                            WHERE (product_id = ' . $pId . ') AND (purchase_order_id = ' . $purchaseOrderId . ') AND (warehouse_id = ' . $codeId[1] . ')';
                                                $writeConnection->query($sqlWarehouseProductReturned);
                                                $pName = $purchaseorderProductItem->getProductName();
                                                $pName = mysql_escape_string($pName);
                                                $pSku = $purchaseorderProductItem->getProductSku();
                                                $amountMoved = ($purchaseorderProductItem->getCost()) * (100 + $purchaseorderProductItem->getTax() - $purchaseorderProductItem->getDiscount()) / 100;
                                                $sqlReportProductMoved = 'INSERT INTO ' . $installer->getTable("erp_inventory_report_products_moved") . '
                                                    (product_id,product_name,product_sku,amount_moved,qty_moved,moved_at,moved_type)' . '
                                                    VALUES ("' . $pId . '","' . $pName . '","' . $pSku . '","' . $amountMoved . '","' . $code . '","' . $data['delivery_date'] . '","1")'; //1 for return order
                                                $writeConnection->query($sqlReportProductMoved);
                                                try {
                                                    $returnOrderWarehouse = Mage::getModel('inventory/returnwarehouse');
                                                    $returnOrderWarehouse->setData($returnNewData)->save();
                                                    if ($i > 0)
                                                        $returnOrderWarehouseIds .= ', ';
                                                    $returnOrderWarehouseIds .= $returnOrderWarehouse->getId();
                                                    $i++;
                                                } catch (Exception $e) {
                                                    
                                                }
                                            }
                                        }
                                    }
                                }

                                $purchaseorderProductItem->setQtyReturned($purchaseorderProductItem->getQtyReturned() + $totalForAProduct)
                                    ->save();
                                $returnOrderProduct = Mage::getModel('inventory/returnorderproduct');
                                $returnOrderProduct->setReturnedOrderId($newReturnOrderId)
                                    ->setQtyReturn($totalForAProduct)
                                    ->setProductId($pId)
                                    ->setProductName($purchaseorderProductItem->getProductName())
                                    ->setProductSku($purchaseorderProductItem->getProductSku())
                                    ->save();
                            }
                            $productPrice = $totalForAProduct * $purchaseorderProductItem->getCost() * (100 - $purchaseorderProductItem->getDiscount() + $purchaseorderProductItem->getTax()) / 100;
                            $totalAmountReturned += $productPrice;
                        }


                        //create transaction
                        if ($returnWarehouseProducts) {
                            foreach ($returnWarehouseProducts as $warehouseId => $products) {
                                $warehouse = Mage::getModel('inventory/warehouse')->load($warehouseId);
                                $transactionData['type'] = 4;
                                $transactionData['from_id'] = $warehouseId;
                                $transactionData['from_name'] = $warehouse->getName();
                                $transactionData['to_id'] = $supplierId;
                                $transactionData['to_name'] = $supplier->getName();
                                $transactionData['created_at'] = $returnOrderModel->getReturnedOn();
                                $transactionData['created_by'] = $purchaseOrder->getCreateBy();
                                $transactionDataModel = Mage::getModel('inventory/transaction')->setData($transactionData)
                                    ->save();
                                $totalProduct = 0;
                                foreach ($products as $productId => $information) {
                                    $pInfo = explode(',', $information);
                                    Mage::getModel('inventory/transactionproduct')
                                        ->setTransactionId($transactionDataModel->getId())
                                        ->setProductId($productId)
                                        ->setProductSku($pInfo[1])
                                        ->setProductName($pInfo[2])
                                        ->setQty($pInfo[0])
                                        ->save();
                                    $totalProduct += $pInfo[0];
                                }
                                $transactionDataModel->setTotalProducts($totalProduct)->save();
                            }
                        }
                        //history change
                        if ($totalProductReturns == 0) {
                            Mage::getSingleton('adminhtml/session')->addError(
                                Mage::helper('inventory')->__('Please select product and enter Qty greater than 0 to create return order!')
                            );
                            $returnOrderProduct->delete();
                            $returnOrderModel->delete();
                            $this->_redirect('*/*/newreturnorder', array(
                                'purchaseorder_id' => $purchaseOrderId,
                                'action' => 'newreturnorder',
                                'active' => 'return'
                            ));
                            return;
                        }

                        if ($changeHistory == '1') {
                            $admin = Mage::getModel('admin/session')->getUser()->getUsername();
                            $purchaseOrderHistory = Mage::getModel('inventory/purchaseorderhistory');
                            $purchaseOrderHistoryContent = Mage::getModel('inventory/purchaseorderhistorycontent');
                            $purchaseOrderHistory->setData('purchase_order_id', $purchaseOrderId)
                                ->setData('time_stamp', now())
                                ->setData('create_by', $admin)
                                ->save();
                            $purchaseOrderHistoryContent->setData('purchase_order_history_id', $purchaseOrderHistory->getId())
                                ->setData('field_name', $admin . ' created return order for this purchase order!')
                                ->setData('new_value', 'Return id(s): ' . $returnOrderWarehouseIds)
                                ->save();
                        }
                    } else {
                        Mage::getSingleton('adminhtml/session')->addError(
                            Mage::helper('inventory')->__('Please select product to create return order')
                        );

                        $this->_redirect('*/*/newreturnorder', array(
                            'purchaseorder_id' => $purchaseOrderId,
                            'action' => 'newreturnorder',
                            'active' => 'return'
                        ));
                        return;
                    }
                } else {
                    Mage::getSingleton('adminhtml/session')->addError(
                        Mage::helper('inventory')->__('Please select product to create return order')
                    );

                    $this->_redirect('*/*/newreturnorder', array(
                        'purchaseorder_id' => $purchaseOrderId,
                        'action' => 'newreturnorder',
                        'active' => 'return'
                    ));
                    return;
                }
                /* auto create issuing */
                if (count($issuingData)) {
                    foreach ($issuingData as $rData => $rCode) {
                        $stockIssuing = Mage::getModel('inventory/stockissuing');
                        $stockIssuing->setWarehouseIdFrom($rData)
                            ->setType('3')
                            ->setWarehouseNameFrom(Mage::getModel('inventory/warehouse')->load($rData)->getName())
                            ->setCreatedAt(date("Y-m-d"))
                            ->save();
                        $totalProductReturnWarehouse = 0;
                        $sqlStockIssuingNew = array();
                        foreach ($rCode as $rPId => $rPQty) {
                            $totalProductReturnWarehouse += $rPQty;
                            $sqlStockIssuingNew[] = array(
                                'stock_issuing_id' => $stockIssuing->getId(),
                                'product_id' => $rPId,
                                'qty' => $rPQty
                            );
                            if (count($sqlStockIssuingNew) == 1000) {
                                $writeConnection->insertMultiple($installer->getTable('inventory/stockissuingproduct'), $sqlStockIssuingNew);
                                $sqlStockIssuingNew = array();
                            }
                        }
                        if (!empty($sqlStockIssuingNew)) {
                            $writeConnection->insertMultiple($installer->getTable('inventory/stockissuingproduct'), $sqlStockIssuingNew);
                        }
                        $stockIssuing->setTotalProducts($totalProductReturnWarehouse)->save();
                    }
                }

                $returnOrderModel->setTotalProducts($totalProductReturns);
                $returnOrderModel->setTotalAmount($totalAmountReturned);
                $returnOrderModel->save();
                $totalProductRecieved = $purchaseOrder->getData('total_products_recieved') - $totalProductReturns;
                if ($totalProductRecieved >= 0) {
                    $purchaseOrder->setTotalProductsRecieved($totalProductRecieved);
                    $purchaseOrder->save();
                }
                if ($purchaseOrder->getTotalProductsRecieved() == 0) {
                    $purchaseOrder->setStatus(7);
                    $purchaseOrder->save();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('inventory')->__('Return order was successfully saved')
                );


                Mage::getSingleton('adminhtml/session')->setFormData(false);
                $this->_redirect('inventoryadmin/adminhtml_purchaseorder/edit', array('id' => $this->getRequest()->getParam('purchaseorder_id'), 'active' => 'return'));
                return;
            }
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            Mage::getSingleton('adminhtml/session')->setFormData($data);
            $this->_redirect('*/*/');
            return;
        }

        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('inventory')->__('Unable to find return order to save!')
        );
        $this->_redirect('*/*/');
    }

    public function returnOrderAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('inventory.purchaseorder.edit.tab.returnorder');
        $this->renderLayout();
    }

    public function returnOrderGridAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('inventory.purchaseorder.edit.tab.returnorder');
        $this->renderLayout();
    }

    public function newReturnOrderAction() {
        $purchaseOrderId = $this->getRequest()->getParam('purchaseorder_id');
        $model = Mage::getModel('inventory/purchaseorder')->load($purchaseOrderId);

        if ($model->getId() || $purchaseOrderId == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('purchaseorder_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('inventory/purchaseorder');

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('inventory/adminhtml_purchaseorder_returnorder'))
                ->_addLeft($this->getLayout()->createBlock('inventory/adminhtml_purchaseorder_returnorder_tabs'));

            $this->renderLayout();
            if (Mage::getModel('admin/session')->getData('returnorder_product_import')) {
                Mage::getModel('admin/session')->setData('returnorder_product_import', null);
            }
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('inventory')->__('Item does not exist')
            );
            $this->_redirect('*/*/');
        }
    }

    public function prepareNewReturnOrderAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('inventory.purchaseorder.edit.tab.preparenewreturnorder')
            ->setProducts($this->getRequest()->getPost('isproducts', null));
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $installer = Mage::getModel('core/resource_setup');
        $sql = 'SELECT distinct(`warehouse_id`) from ' . $installer->getTable("erp_inventory_purchase_order_product_warehouse") . ' WHERE (purchase_order_id = ' . $this->getRequest()->getParam("purchaseorder_id") . ')';
        $results = $readConnection->fetchAll($sql);
        foreach ($results as $result) {
            $this->getLayout()->getBlock('related_grid_serializer')->addColumnInputName('warehouse_' . $result['warehouse_id']);
        }
        $this->renderLayout();
        if (Mage::getModel('admin/session')->getData('returnorder_product_import')) {
            Mage::getModel('admin/session')->setData('returnorder_product_import', null);
        }
    }

    public function prepareNewReturnOrderGridAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('inventory.purchaseorder.edit.tab.preparenewreturnorder')
            ->setProducts($this->getRequest()->getPost('isproducts', null));
        $this->renderLayout();
    }

    public function cancelOrderAction() {
        $purchaseOrderId = $this->getRequest()->getParam('id');
        $deliveryModel = Mage::getModel('inventory/delivery')->getCollection()->addFieldToFilter('purchase_order_id', $purchaseOrderId);
        if (!$deliveryModel->getFirstItem()->getData()) {
            $purchaseOrderModel = Mage::getModel('inventory/purchaseorder')->load($purchaseOrderId);
            $purchaseOrderModel->setStatus(7);
            $purchaseOrderModel->save();
            //save history
            $admin = Mage::getModel('admin/session')->getUser()->getUsername();
            $purchaseOrderHistory = Mage::getModel('inventory/purchaseorderhistory');
            $purchaseOrderHistoryContent = Mage::getModel('inventory/purchaseorderhistorycontent');
            $purchaseOrderHistory->setData('purchase_order_id', $purchaseOrderId)
                ->setData('time_stamp', now())
                ->setData('create_by', $admin)
                ->save();
            $purchaseOrderHistoryContent->setData('purchase_order_history_id', $purchaseOrderHistory->getId())
                ->setData('field_name', $admin . ' canceled this purchase order!')
                ->save();
            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('inventory')->__('Purchase Order was successfully cancelled.')
            );
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('inventory')->__('Unable to cancel order because it has been on delivery!')
            );
        }
        $this->_redirect('*/*/');
    }

    public function viewDetailDeliveryAction() {
        
    }

    public function viewDetailReturnOrderAction() {
        
    }

    public function importproductAction() {
        if (isset($_FILES['fileToUpload']['name']) && $_FILES['fileToUpload']['name'] != '') {
            try {
                $fileName = $_FILES['fileToUpload']['tmp_name'];
                $Object = new Varien_File_Csv();
                $dataFile = $Object->getData($fileName);
                $purchaseorderProduct = array();
                $purchaseorderProducts = array();
                $fields = array();
                $count = 0;
                $purchaseorderHelper = Mage::helper('inventory/purchaseorder');
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
                                        $purchaseorderProduct[$fields[$index]] = $cell;
                                    }
                                }
                            $purchaseorderProducts[] = $purchaseorderProduct;
                        }
                    }
                $purchaseorderHelper->importProduct($purchaseorderProducts);
            } catch (Exception $e) {
                
            }
        }
    }

    public function importproductforcreatedeliveryAction() {
        $getParams = $this->getRequest()->getParams();
        $createAt = $getParams['create_at'];
        if ($createAt) {
            $deliveryDate = $createAt;
        } else {
            $deliveryDate = date("Y-m-d");
        }
        $purchaseOrderId = $getParams['purchaseorder_id'];
        $purchaseOrder = Mage::getModel('inventory/purchaseorder')->load($purchaseOrderId);
        if (isset($_FILES['fileToUpload']['name']) && $_FILES['fileToUpload']['name'] != '') {
            try {
                $fileName = $_FILES['fileToUpload']['tmp_name'];
                $Object = new Varien_File_Csv();
                $dataFile = $Object->getData($fileName);
                $newDeliveryProduct = array();
                $newDeliveryProducts = array();
                $fields = array();
                $count = 0;
                $purchaseorderHelper = Mage::helper('inventory/purchaseorder');
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
                                        $newDeliveryProduct[$fields[$index]] = $cell;
                                    }
                                }
                            $newDeliveryProducts[] = $newDeliveryProduct;
                        }
                    }//end foreach
                $purchaseorderHelper->importDeliveryProduct($newDeliveryProducts);
            } catch (Exception $e) {
                
            }
        }
    }

    public function importproductforreturnorderAction() {
        $getParams = $this->getRequest()->getParams();
        $createAt = $getParams['create_at'];
        if ($createAt) {
            $deliveryDate = $createAt;
        } else {
            $deliveryDate = date("Y-m-d");
        }
        $purchaseOrderId = $getParams['purchaseorder_id'];
        $purchaseOrder = Mage::getModel('inventory/purchaseorder')->load($purchaseOrderId);
        if (isset($_FILES['fileToUpload']['name']) && $_FILES['fileToUpload']['name'] != '') {
            try {
                $fileName = $_FILES['fileToUpload']['tmp_name'];
                $Object = new Varien_File_Csv();
                $dataFile = $Object->getData($fileName);
                $newReturnOrderProduct = array();
                $newReturnOrderProducts = array();
                $fields = array();
                $count = 0;
                $purchaseorderHelper = Mage::helper('inventory/purchaseorder');
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
                                        $newReturnOrderProduct[$fields[$index]] = $cell;
                                    }
                                }
                            $newReturnOrderProducts[] = $newReturnOrderProduct;
                        }
                    }//end foreach
                $purchaseorderHelper->importReturnOrderProduct($newReturnOrderProducts);
            } catch (Exception $e) {
                
            }
        }
    }

    public function exportPostAction($data) {
        $headers = new Varien_Object(array(
                'ID' => Mage::helper('inventory')->__('ID'),
                'Name' => Mage::helper('inventory')->__('Name'),
                'SKU' => Mage::helper('inventory')->__('SKU'),
                'Cost' => Mage::helper('inventory')->__('Cost'),
                'Price' => Mage::helper('inventory')->__('Price'),
                'Warehouse' => Mage::helper('inventory')->__('Warehouse'),
                'Supplyneeds' => Mage::helper('inventory')->__('Supplyneeds'),
                'Supplier' => Mage::helper('inventory')->__('Supplier')
            ));
        $template = '"{{ID}}","{{Name}}","{{SKU}}","{{Cost}}","{{Price}}","{{Supplyneeds}}","{{Warehouse}}","{{Supplier}}"';
        $content = $headers->toString($template);
        if (($data['product_list'])) {
            $info = array();
            $list = explode(';', $data['product_list']);
            $arr = Mage::helper('inventory/supplyneeds')->filterList($list);
            foreach ($arr as $productId => $qty) {
                $product = Mage::getModel('catalog/product')->getCollection()
                    ->addFieldToFilter('entity_id', $productId)
                    ->addAttributeToSelect('*')
                    ->getFirstItem();
                $warehouse = Mage::getModel('inventory/warehouse')
                    ->getCollection()
                    ->addFieldToFilter('warehouse_id', $data['warehouse_select'])
                    ->getFirstItem()
                    ->getName();
                $supplier = Mage::getModel('inventory/supplier')
                    ->getCollection()
                    ->addFieldToFilter('supplier_id', $data['supplier_select'])
                    ->getFirstItem()
                    ->getName();
                $cost = Mage::getModel('inventory/inventory')
                    ->getCollection()
                    ->addFieldToFilter('product_id', $productId)
                    ->getFirstItem()
                    ->getCostPrice();
                $info['ID'] = $productId;
                $info['Name'] = $product->getName();
                $info['SKU'] = $product->getSku();
                $info['Cost'] = $cost;
                $info['Price'] = $product->getPrice();
                $info['Supplyneeds'] = $qty;
                $info['Warehouse'] = $warehouse;
                $info['Supplier'] = $supplier;
                $csv_content = new Varien_Object($info);
                $content .= "\n";
                $content .= $csv_content->toString($template);
            }
        }
        $this->_prepareDownloadResponse('supplyneeds.csv', $content);
    }

    public function getFiledSaveHistory() {
        return array('purchase_on', 'bill_name', 'order_placed', 'start_date', 'cancel_date', 'expected_date', 'payment_date', 'ship_via', 'payment_term', 'comments', 'tax_rate', 'shipping_cost', 'delivery_process');
    }

    public function getTitleByField($field) {
        $fieldArray = array(
            'purchase_on' => Mage::helper('inventory')->__('Order Created On'),
            'bill_name' => Mage::helper('inventory')->__('Bill Name'),
            'order_placed' => Mage::helper('inventory')->__('Order placed via'),
            'start_date' => Mage::helper('inventory')->__('Start ship'),
            'cancel_date' => Mage::helper('inventory')->__('Cancel'),
            'expected_date' => Mage::helper('inventory')->__('Expected date'),
            'payment_date' => Mage::helper('inventory')->__('Payment date'),
            'ship_via' => Mage::helper('inventory')->__('Shipping via'),
            'payment_term' => Mage::helper('inventory')->__('Payment terms'),
            'comments' => Mage::helper('inventory')->__('Comment'),
            'tax_rate' => Mage::helper('inventory')->__('Tax Rate'),
            'shipping_cost' => Mage::helper('inventory')->__('Shipping Cost'),
            'delivery_process' => Mage::helper('inventory')->__('Delivery Process')
        );
        if (!$fieldArray[$field])
            return $field;
        return $fieldArray[$field];
    }

    public function historyAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('inventory.purchaseorder.edit.tab.history');
        $this->renderLayout();
    }

    public function historyGridAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('inventory.purchaseorder.edit.tab.history');
        $this->renderLayout();
    }

    public function showhistoryAction() {
        $form_html = $this->getLayout()
            ->createBlock('inventory/adminhtml_purchaseorder')
            ->setTemplate('inventory/purchaseorder/showhistory.phtml')
            ->toHtml();
        $this->getResponse()->setBody($form_html);
    }

    public function checkproductAction() {
        $purchaseorder_products = $this->getRequest()->getPost('products');
        $checkProduct = 0;
        $next = false;
        if (isset($purchaseorder_products)) {
            $purchaseorderProducts = array();
            $purchaseorderProductsExplodes = explode('&', urldecode($purchaseorder_products));
            if (count($purchaseorderProductsExplodes) <= 900) {
                parse_str(urldecode($purchaseorder_products), $purchaseorderProducts);
            } else {
                foreach ($purchaseorderProductsExplodes as $purchaseorderProductsExplode) {
                    $purchaseorderProduct = '';
                    parse_str($purchaseorderProductsExplode, $purchaseorderProduct);
                    $purchaseorderProducts = $purchaseorderProducts + $purchaseorderProduct;
                }
            }
            if (count($purchaseorderProducts)) {
                foreach ($purchaseorderProducts as $pId => $enCoded) {
                    $codeArr = array();
                    parse_str(base64_decode($enCoded), $codeArr);
                    foreach ($codeArr as $codeId => $code) {
                        if (!in_array($codeId, array('qty_order', 'cost_product', 'tax', 'discount'))) {
                            if ($codeId[1]) {
                                if (is_numeric($code) && $code > 0) {
                                    $checkProduct = 1;
                                    $next = true;
                                    break;
                                }
                            }
                        }
                    }
                    if ($next)
                        break;
                }
            }
        }
        echo $checkProduct;
    }

    public function sendEmail($supplierId, $sqlNews, $purchaseOrderId) {
        $store = Mage::app()->getStore();
        $templateId = Mage::getStoreConfig('inventory/email_supplier/template', $store->getId());
        if ($supplierId) {
            $supplierInfo = Mage::helper('inventory/supplier')->getBillingAddressBySupplierId($supplierId);
        }
        if (!$supplierId) {
            $supplierInfo = Mage::helper('inventory/purchaseorder')->getBilingAddressByPurchaseOrderId($purchaseOrderId);
        }
        $supplierCollection = Mage::getResourceModel('inventory/supplier_collection')
            ->addFieldToFilter('supplier_id', $supplierId);
        $supplierdata = $supplierCollection->getFirstItem()->getData();
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
        $transaction = Mage::getSingleton('core/email_template');
//            $transaction->setDesignConfig(array(
//                            'area' => 'backend',
//                            'store' => Mage::app()->getStore()->getId()
//                    ));
        $emailSubject = Mage::getStoreConfig('inventory/email_supplier/email_subject', $store->getId());
        $senderEmail = Mage::getStoreConfig('inventory/email_supplier/email_sender', $store->getId());
        $senderName = Mage::getStoreConfig('inventory/email_supplier/name_sender', $store->getId());
        $top_email = Mage::getStoreConfig('inventory/email_supplier/top_email', $store->getId());
        $domainName = $_SERVER['HTTP_HOST'];
        if (empty($senderEmail))
            $senderEmail = Mage::getStoreConfig('trans_email/ident_general/email', $store->getId());
        if (empty($senderName))
            $senderName = Mage::getStoreConfig('trans_email/ident_general/name', $store->getId());
        if (empty($emailSubject))
            $emailSubject = 'Purchase Order #' . $purchaseOrderId;
        if (empty($top_email))
            $top_email =
                '<p style="font-size:12px; line-height:16px; margin:0;">' .
                'We are from ' . $domainName . '<br/>' . '
                    We want to purchase order some product from you. And below are our information and list product that we want to
                    purchase.' . '</p>';
        $sender = array(
            'name' => $senderName,
            'email' => $senderEmail,
        );
        $items = '';
        $count = 0;
        foreach ($sqlNews as $item) {
            if ($count % 2 == 0)
                $items = $items . '<tbody bgcolor="#F6F6F6">';
            else
                $items = $items . '<tbody>';
            $items = $items . '<tr>
                                                                    <td align="left" valign="top" style="font-size:11px; padding:3px 9px; border-bottom:1px dotted #CCCCCC;">
                                                                            <strong style="font-size:11px;">' . $item["product_name"] . '</strong>
                                                                    </td>
                                                                    <td align="left" valign="top" style="font-size:11px; padding:3px 9px; border-bottom:1px dotted #CCCCCC;">' . $item["product_sku"] . '</td>
                                                                    <td align="left" valign="top" style="font-size:11px; padding:3px 9px; border-bottom:1px dotted #CCCCCC;">' . Mage::helper('core')->currency($item["cost"]) . '</td>
                                                                    <td align="left" valign="top" style="font-size:11px; padding:3px 9px; border-bottom:1px dotted #CCCCCC;">' . $item["tax"] . '</td>
                                                                    <td align="center" valign="top" style="font-size:11px; padding:3px 9px; border-bottom:1px dotted #CCCCCC;">' . $item["discount"] . '</td>
                                                                    <td align="right" valign="top" style="font-size:11px; padding:3px 9px; border-bottom:1px dotted #CCCCCC;">
                                                                                                                                                            <span class="price">' . $item["qty"] . '</span>            
                                                                    </td>
                                                            </tr>
                                                    </tbody>';
            $count++;
        }
        $purchaseOrder = 'Our Purchase Order #' . $purchaseOrderId;
        $transaction->sendTransactional(
            $templateId, //Template config
            $sender, $supplierdata['email'], $supplierdata['name'], array(//Infomation - variable in email template (we'll use then send email successfullly)
            'store' => $store,
            'top_email' => $top_email,
            'order_id' => $purchaseOrder,
            'billing' => $supplierInfo,
            'email_subject' => $emailSubject,
            'items' => $items,
            'purchaseorderid' => $purchaseOrderId,
            'sqlnews' => $sqlNews
            )
        );
        //new send email - Michael
//            $mailTemplate = Mage::getModel('core/email_template');
//            $translate = Mage::getSingleton('core/translate');            
//            $sender = array('email' => $from_email, 'name' => $from_name);        
//            $receipientEmail = $supplier->getEmail(); 
//            $receipientName = $supplier->getContactName().'('.$supplier->getName().')';
//            $mailTemplate
//                ->setTemplateSubject('Approve Drop Ship')
//                ->sendTransactional(
//                    $templateId, $sender, $receipientEmail, $receipientName, array(
//                        'dropship' => $dropship,
//                        'receipient_name' => $receipientName,
//                        'supplier_need_to_confirm_provide' => $supplierNeedToConfirmProvide,
//                    )
//            );
//            $translate->setTranslateInline(true);      
        // save history
        $admin = Mage::getModel('admin/session')->getUser()->getUsername();
        $purchaseOrderHistory = Mage::getModel('inventory/purchaseorderhistory');
        $purchaseOrderHistoryContent = Mage::getModel('inventory/purchaseorderhistorycontent');
        $purchaseOrderHistory->setData('purchase_order_id', $purchaseOrderId)
            ->setData('time_stamp', now())
            ->setData('create_by', $admin)
            ->save();
        $purchaseOrderHistoryContent->setData('purchase_order_history_id', $purchaseOrderHistory->getId())
            ->setData('field_name', $admin . ' sent to ' . $supplierdata['name'] . '!')
            ->save();
        Mage::getSingleton('adminhtml/session')->addSuccess(
            Mage::helper('inventory')->__('Email was successfully sent!')
        );
    }

    public function resendemailtosupplierAction() {
        $purchaseOrderId = $this->getRequest()->getParam('id');
        $purchaseOrder = Mage::getModel('inventory/purchaseorder')->load($purchaseOrderId);
        $purchaseOrderProducts = Mage::getModel('inventory/purchaseorderproduct')->getCollection()
            ->addFieldToFilter('purchase_order_id', $purchaseOrderId);

        $sqlNews = $purchaseOrderProducts->getData();
        $this->sendEmail($purchaseOrder->getSupplierId(), $sqlNews, $purchaseOrderId);
        $purchaseOrder->setSendMail(1)->save();
        $this->_redirect('*/*/edit', array('id' => $purchaseOrderId));
    }

}
