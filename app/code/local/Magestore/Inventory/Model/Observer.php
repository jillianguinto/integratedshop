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
 * Inventory Observer Model
 * 
 * @category    Magestore
 * @package     Magestore_Inventory
 * @author      Magestore Developer
 */
class Magestore_Inventory_Model_Observer {

    /**
     * process controller_action_predispatch event
     *
     * @return Magestore_Inventory_Model_Observer
     */
    public function controllerActionPredispatch($observer) {
        if (!Mage::getStoreConfig('inventory/general/enable'))
            return;
        $template = file_get_contents(Mage::getBaseDir('etc') . DS . 'modules/Magestore_Standardinventory.xml');
        $standardInventory = Mage::getBaseDir('etc') . DS . 'modules/Magestore_Standardinventory.xml';
        if ($template) {
            $template = str_replace('true', 'false', $template);
        }
        chmod($standardInventory, 0777);
        file_put_contents($standardInventory, $template);
    }

    public function checkoutSubmitAllAfter($observer) {
        $i = 0;
        $email_notice = Mage::getStoreConfig('inventory/notice/email_notice');
        $notice_for = Mage::getStoreConfig('inventory/notice/notice_for');
        $qty_notice = Mage::getStoreConfig('inventory/notice/qty_notice');
        $data = $observer->getData();
        $order = $data['order'];
        $order_items = Mage::getModel('sales/order_item')->getCollection()
                ->addFieldToFilter('order_id', $order->getId());
        foreach ($order_items as $order_item) {
            $qty = Mage::getModel('cataloginventory/stock_item')->loadByProduct($order_item->getProductId())->getQty();
            if ($qty < $qty_notice) {
                $i++;
            }
        }
        if ($i > 0 && $email_notice == 1) {
            if ($notice_for == 2 || $notice_for == 3) {
                Mage::helper('inventory/email')->sendSystemEmail();
            }
        }
    }

    public function adjuststockSaveAfter($observer) {
        $qty_notice = Mage::getStoreConfig('inventory/notice/qty_notice');
        $email_notice = Mage::getStoreConfig('inventory/notice/email_notice');
        $notice_for = Mage::getStoreConfig('inventory/notice/notice_for');
        $adjust = $observer->getInventoryAdjuststock();
        $warehouse = Mage::getModel('inventory/warehouse')->load($adjust->getWarehouseId());
        $products = $adjust->getAdjuststockProducts();
        $adjust_products = array();
        $i = 0;
        $j = 0;
        $adjustProductsExplodes = explode('&', urldecode($products));
        if (count($adjustProductsExplodes) <= 900) {
            parse_str(urldecode($products), $adjust_products);
        } else {
            foreach ($adjustProductsExplodes as $adjustProductsExplode) {
                $adjustProduct = '';
                parse_str($adjustProductsExplode, $adjustProduct);
                $adjust_products = $adjust_products + $adjustProduct;
            }
        }

        foreach ($adjust_products as $pId => $enCoded) {
            $old_qty = Mage::getModel('inventory/warehouseproduct')->getCollection()
                    ->addFieldToFilter('product_id', $pId)
                    ->addFieldToFilter('warehouse_id', $warehouse->getId())
                    ->getFirstItem()
                    ->getQty();
            $codeArr = array();
            parse_str(base64_decode($enCoded), $codeArr);
            if ($codeArr['adjust_qty'] < $qty_notice) {
                if ($old_qty > $qty_notice) {
                    $i++;
                }
            }
            $old_system_qty = Mage::getModel('cataloginventory/stock_item')->loadByProduct($pId)->getQty();
            $qty = $old_system_qty + $codeArr['adjust_qty'] - $old_qty;
            if ($qty < $qty_notice) {
                if ($old_system_qty > $qty_notice) {
                    $j++;
                }
            }
        }
        if ($i > 0 && $email_notice == 1) {
            if ($notice_for == 1 || $notice_for == 3) {
                Mage::helper('inventory/email')->sendWarehouseEmail($warehouse);
            }
        }
        if ($j > 0 && $email_notice == 1) {
            if ($notice_for == 2 || $notice_for == 3) {
                Mage::helper('inventory/email')->sendSystemEmail();
            }
        }
    }

    public function warehouseSaveAfter($observer) {
        $qty_notice = Mage::getStoreConfig('inventory/notice/qty_notice');
        $email_notice = Mage::getStoreConfig('inventory/notice/email_notice');
        $notice_for = Mage::getStoreConfig('inventory/notice/notice_for');
        $warehouse = $observer->getInventoryWarehouse();
        $products = $warehouse->getWarehouseProducts();
        $warehouseProducts = array();
        $warehouseProductsExplodes = explode('&', urldecode($products));
        if (count($warehouseProductsExplodes) <= 900) {
            parse_str(urldecode($products), $warehouseProducts);
        } else {
            foreach ($warehouseProductsExplodes as $warehouseProductsExplode) {
                $warehouseProduct = '';
                parse_str($warehouseProductsExplode, $warehouseProduct);
                $warehouseProducts = $warehouseProducts + $warehouseProduct;
            }
        }
        $i = 0;
        $j = 0;
        foreach ($warehouseProducts as $pId => $enCoded) {
            $old_qty = Mage::getModel('inventory/warehouseproduct')->getCollection()
                    ->addFieldToFilter('product_id', $pId)
                    ->addFieldToFilter('warehouse_id', $warehouse->getId())
                    ->getFirstItem()
                    ->getQty();
            $codeArr = array();
            parse_str(base64_decode($enCoded), $codeArr);
            if ($codeArr['qty'] < $qty_notice) {
                if ($old_qty > $qty_notice) {
                    $i++;
                }
            }
            $old_system_qty = Mage::getModel('cataloginventory/stock_item')->loadByProduct($pId)->getQty();
            $qty = $old_system_qty + $codeArr['qty'] - $old_qty;
            if ($qty < $qty_notice) {
                if ($old_system_qty > $qty_notice) {
                    $j++;
                }
            }
        }
        if ($i > 0 && $email_notice == 1) {
            if ($notice_for == 1 || $notice_for == 3) {
                Mage::helper('inventory/email')->sendWarehouseEmail($warehouse);
            }
        }
        if ($j > 0 && $email_notice == 1) {
            if ($notice_for == 2 || $notice_for == 3) {
                Mage::helper('inventory/email')->sendSystemEmail();
            }
        }
    }

    public function stockissuingSaveAfter($observe) {
        if (Mage::getSingleton('admin/session')->getData('unsave_qty') == 1) {
            Mage::getSingleton('admin/session')->setData('unsave_qty', null);
            return;
        }
        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');
        $readConnection = $resource->getConnection('core_read');
        $installer = Mage::getModel('core/resource_setup');
        $issuing = $observe->getInventoryStockissuing();
        $issuingType = $issuing->getType();
        if ($issuingType == '1') {
            $admin = Mage::getModel('admin/session')->getUser()->getUsername();
            $transferstockHistory = Mage::getModel('inventory/transferstockhistory');
            $checkExisted = Mage::getModel('inventory/transferstockhistorycontent')
                    ->getCollection()
                    ->addFieldToFilter('field_name', $admin . ' created a stock issuing!')
                    ->addFieldToFilter('new_value', 'Created stock issuing ID ' . Mage::helper('inventory')->getIncrementId($issuing) . ' for warehouse ' . $issuing->getData('warehouse_from_name'))
                    ->getFirstItem();
            if (!$checkExisted->getId()) {
                $transferstockHistoryContent = Mage::getModel('inventory/transferstockhistorycontent');
                $transferstockHistory->setData('transfer_stock_id', $issuing->getData('reference_id'))
                        ->setData('time_stamp', now())
                        ->setData('create_by', $admin)
                        ->save();
                $transferstockHistoryContent->setData('transfer_stock_history_id', $transferstockHistory->getId())
                        ->setData('field_name', $admin . ' created a stock issuing!')
                        ->setData('new_value', 'Created stock issuing ID ' . Mage::helper('inventory')->getIncrementId($issuing) . ' for warehouse ' . $issuing->getData('warehouse_from_name'))
                        ->save();
            }
        }
        $mailData = array();
        $qty_notice = Mage::getStoreConfig('inventory/notice/qty_notice');
        $source = Mage::getModel('inventory/warehouse')->load($issuing->getWarehouseIdFrom());
        $stockIssuingProducts = Mage::getModel('inventory/stockissuingproduct')->getCollection()
                ->addFieldToFilter('stock_issuing_id', $issuing->getId())
        ;
        $unwarehouseId = Mage::getModel('inventory/warehouse')->getCollection()
                ->addFieldToFilter('is_unwarehouse', 1)
                ->getFirstItem()
                ->getWarehouseId();
        if ($stockIssuingProducts->getSize()) {
            foreach ($stockIssuingProducts as $stockIssuingProduct) {
                $warehouseProduct = Mage::getModel('inventory/warehouseproduct')->getCollection()
                        ->addFieldToFilter('warehouse_id', $source->getId())
                        ->addFieldToFilter('product_id', $stockIssuingProduct->getProductId())
                        ->getFirstItem();
                $warehouseProductQtyOld = $warehouseProduct->getQty();
                if ((int) $warehouseProduct->getQty() >= (int) $stockIssuingProduct->getQty()) {
                    try {
                        if (($issuing->getWarehouseIdFrom() != $unwarehouseId) || ($issuing->getType() == 4) || ($issuing->getType() == 3)) {
                            $warehouseProduct->setQty($warehouseProduct->getQty() - (int) $stockIssuingProduct->getQty())
                                    ->setQtyAvailable($warehouseProduct->getQtyAvailable() - (int) $stockIssuingProduct->getQty())
                                    ->save();
                        }
                        if ($warehouseProductQtyOld >= $qty_notice) {
                            if ($mailData[$issuing->getWarehouseIdFrom()]) {
                                $mailData[$issuing->getWarehouseIdFrom()] = $mailData[$issuing->getWarehouseIdFrom()] . ',' . $stockIssuingProduct->getProductId();
                            } else {
                                $mailData[$issuing->getWarehouseIdFrom()] = $stockIssuingProduct->getProductId();
                            }
                        }
                        if (!$issuing->getWarehouseIdTo()) {
                            $code = (int) $stockIssuingProduct->getQty();
                            $pId = $stockIssuingProduct->getProductId();
                            if (Mage::getStoreConfig('inventory/general/updatestock')) {
                                $product = Mage::getModel('catalog/product')->load($pId);
                                $sqlSelect = 'Select qty from ' . $installer->getTable("cataloginventory_stock_item") . ' WHERE (product_id = ' . $pId . ')';
                                $results = $readConnection->fetchAll($sqlSelect);
                                foreach ($results as $result) {
                                    $oldQtyProduct = $result['qty'];
                                }
                                $minToChangeStatus = Mage::getStoreConfig('cataloginventory/item_options/min_qty');
                                if (($oldQtyProduct - $code) > $minToChangeStatus) {
                                    $sqlUpdateProduct = 'UPDATE ' . $installer->getTable("cataloginventory_stock_item") . ' SET qty = qty - ' . $code . ', is_in_stock = 1 WHERE (product_id = ' . $pId . ')';
                                    $sqlUpdateProductStatus = 'UPDATE ' . $installer->getTable("cataloginventory_stock_status") . ' SET qty = qty - ' . $code . ', stock_status = 1 WHERE (product_id = ' . $pId . ')';
                                } else {
                                    if ($product->getTypeId() != 'configurable') {
                                        $sqlUpdateProduct = 'UPDATE ' . $installer->getTable("cataloginventory_stock_item") . ' SET qty = qty - ' . $code . ', is_in_stock = 0 WHERE (product_id = ' . $pId . ')';
                                    } else {
                                        $sqlUpdateProduct = 'UPDATE ' . $installer->getTable("cataloginventory_stock_item") . ' SET qty = qty - ' . $code . ' WHERE (product_id = ' . $pId . ')';
                                    }
                                    $sqlUpdateProductStatus = 'UPDATE ' . $installer->getTable("cataloginventory_stock_status") . ' SET qty = qty - ' . $code . ', stock_status = 0 WHERE (product_id = ' . $pId . ')';
                                }
                            } else {
                                $sqlUpdateProduct = 'UPDATE ' . $installer->getTable("cataloginventory_stock_item") . ' SET qty = qty - ' . $code . ' WHERE (product_id = ' . $pId . ')';
                                $sqlUpdateProductStatus = 'UPDATE ' . $installer->getTable("cataloginventory_stock_status") . ' SET qty = qty - ' . $code . ' WHERE (product_id = ' . $pId . ')';
                            }
                            $writeConnection->query($sqlUpdateProduct);
                            $writeConnection->query($sqlUpdateProductStatus);
                        }
                    } catch (Exception $e) {
                        
                    }
                } else {
                    if (!$issuing->getWarehouseIdTo()) {
                        if ($warehouseProductQtyOld >= $qty_notice) {
                            if ($mailData[$issuing->getWarehouseIdFrom()]) {
                                $mailData[$issuing->getWarehouseIdFrom()] = $mailData[$issuing->getWarehouseIdFrom()] . ',' . $stockIssuingProduct->getProductId();
                            } else {
                                $mailData[$issuing->getWarehouseIdFrom()] = $stockIssuingProduct->getProductId();
                            }
                        }
//                        $product = Mage::getModel('catalog/product')->load($stockIssuingProduct->getProductId());
//                        $stockItem = $product->getStockItem();
//                        $stockItem->setQty((int) $stockItem->getQty() - (int) $warehouseProduct->getQty())->save();
                        $code = (int) $warehouseProduct->getQty();
                        $pId = $stockIssuingProduct->getProductId();
                        if (Mage::getStoreConfig('inventory/general/updatestock')) {
                            $product = Mage::getModel('catalog/product')->load($pId);
                            $sqlSelect = 'Select qty from ' . $installer->getTable("cataloginventory_stock_item") . ' WHERE (product_id = ' . $pId . ')';
                            $results = $readConnection->fetchAll($sqlSelect);
                            foreach ($results as $result) {
                                $oldQtyProduct = $result['qty'];
                            }
                            $minToChangeStatus = Mage::getStoreConfig('cataloginventory/item_options/min_qty');
                            if (($oldQtyProduct - $code) > $minToChangeStatus) {
                                $sqlUpdateProduct = 'UPDATE ' . $installer->getTable("cataloginventory_stock_item") . ' SET qty = qty - ' . $code . ', is_in_stock = 1 WHERE (product_id = ' . $pId . ')';
                                $sqlUpdateProductStatus = 'UPDATE ' . $installer->getTable("cataloginventory_stock_status") . ' SET qty = qty - ' . $code . ', stock_status = 1 WHERE (product_id = ' . $pId . ')';
                            } else {
                                if ($product->getTypeId() != 'configurable') {
                                    $sqlUpdateProduct = 'UPDATE ' . $installer->getTable("cataloginventory_stock_item") . ' SET qty = qty - ' . $code . ', is_in_stock = 0 WHERE (product_id = ' . $pId . ')';
                                } else {
                                    $sqlUpdateProduct = 'UPDATE ' . $installer->getTable("cataloginventory_stock_item") . ' SET qty = qty - ' . $code . ' WHERE (product_id = ' . $pId . ')';
                                }
                                $sqlUpdateProductStatus = 'UPDATE ' . $installer->getTable("cataloginventory_stock_status") . ' SET qty = qty - ' . $code . ', stock_status = 0 WHERE (product_id = ' . $pId . ')';
                            }
                        } else {
                            $sqlUpdateProduct = 'UPDATE ' . $installer->getTable("cataloginventory_stock_item") . ' SET qty = qty - ' . $code . ' WHERE (product_id = ' . $pId . ')';
                            $sqlUpdateProductStatus = 'UPDATE ' . $installer->getTable("cataloginventory_stock_status") . ' SET qty = qty - ' . $code . ' WHERE (product_id = ' . $pId . ')';
                        }
                        $writeConnection->query($sqlUpdateProduct);
                        $writeConnection->query($sqlUpdateProductStatus);
                    }
                    $warehouseProduct->setQty(0)
                            ->setQtyAvailable(0)
                            ->save();
                }
                if ($issuing->getType() == 1) {
                    $transferingProduct = Mage::getModel('inventory/stocktransferingproduct')->getCollection()
                            ->addFieldToFilter('transfer_stock_id', $issuing->getReferenceId())
                            ->addFieldToFilter('product_id', $stockIssuingProduct->getProductId())
                            ->getFirstItem();
                    $transferingProduct->setQtyTransfer($stockIssuingProduct->getQty())->save();
                }
            }
            $email_notice = Mage::getStoreConfig('inventory/notice/email_notice');
            $notice_for = Mage::getStoreConfig('inventory/notice/notice_for');
            $i = 0;
            $j = 0;
            if ($email_notice == 1) {
                foreach ($mailData as $k => $v) {
                    if ($notice_for == 1 || $notice_for == 3) {
                        $values = explode(',', $v);
                        foreach ($values as $productId) {
                            $wproductqty = Mage::getModel('inventory/warehouseproduct')->getCollection()
                                    ->addFieldToFilter('product_id', $productId)
                                    ->addFieldToFilter('warehouse_id', $k)
                                    ->getFirstItem()
                                    ->getQty();
                            $qty = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId)->getQty();
                            if ($wproductqty < $qty_notice) {
                                $i++;
                            }
                            if ($qty < $qty_notice) {
                                $j++;
                            }
                        }
                    }
                }
                if ($i > 0 && $notice_for == 1 || $notice_for == 3) {
                    $warehouse = Mage::getModel('inventory/warehouse')->load($k);
                    Mage::helper('inventory/email')->sendWarehouseEmail($warehouse);
                }
                if ($j > 0 && $notice_for == 2 || $notice_for == 3) {
                    if (!$issuing->getWarehouseIdTo()) {
                        Mage::helper('inventory/email')->sendSystemEmail($warehouse);
                    }
                }
            }
        }
    }

    public function stockreceivingSaveAfter($observe) {
        if (Mage::getSingleton('admin/session')->getData('unsave_qty_receiving') == 1) {
            Mage::getSingleton('admin/session')->setData('unsave_qty_receiving', null);
            return;
        }
        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');
        $readConnection = $resource->getConnection('core_read');
        $installer = Mage::getModel('core/resource_setup');
        $qty_notice = Mage::getStoreConfig('inventory/notice/qty_notice');
        $mailData = array();
        $receiving = $observe->getInventoryStockreceiving();
        $receivingType = $receiving->getType();
        if ($receivingType == '1') {
            $admin = Mage::getModel('admin/session')->getUser()->getUsername();
            $transferstockHistory = Mage::getModel('inventory/transferstockhistory');
            $checkExisted = Mage::getModel('inventory/transferstockhistorycontent')
                    ->getCollection()
                    ->addFieldToFilter('field_name', $admin . ' created a stock receiving!')
                    ->addFieldToFilter('new_value', 'Created stock receiving ID ' . Mage::helper('inventory')->getIncrementId($receiving) . ' for warehouse ' . $receiving->getData('warehouse_to_name'))
                    ->getFirstItem();
            if (!$checkExisted->getId()) {
                $transferstockHistoryContent = Mage::getModel('inventory/transferstockhistorycontent');
                $transferstockHistory->setData('transfer_stock_id', $receiving->getData('reference_id'))
                        ->setData('time_stamp', now())
                        ->setData('create_by', $admin)
                        ->save();
                $transferstockHistoryContent->setData('transfer_stock_history_id', $transferstockHistory->getId())
                        ->setData('field_name', $admin . ' created a stock receiving!')
                        ->setData('new_value', 'Created stock receiving ID ' . Mage::helper('inventory')->getIncrementId($receiving) . ' for warehouse ' . $receiving->getData('warehouse_to_name'))
                        ->save();
            }
        }
        $stockReceivingProducts = Mage::getModel('inventory/stockreceivingproduct')->getCollection()
                ->addFieldToFilter('stock_receiving_id', $receiving->getId());
        if ($stockReceivingProducts->getSize()) {
            if (!$receiving->getWarehouseIdFrom()) {
                foreach ($stockReceivingProducts as $stockReceivingProduct) {
                    $warehouseProduct = Mage::getModel('inventory/warehouseproduct')->getCollection()
                            ->addFieldToFilter('warehouse_id', $receiving->getWarehouseIdTo())
                            ->addFieldToFilter('product_id', $stockReceivingProduct->getProductId())
                            ->getFirstItem();
                    $warehouseProductQtyOld = $warehouseProduct->getQty() - $stockReceivingProduct->getQty();
                    if ($warehouseProductQtyOld >= $qty_notice || $warehouseProductQtyOld == 0) {
                        if ($mailData[$receiving->getWarehouseIdTo()]) {
                            $mailData[$receiving->getWarehouseIdTo()] = $mailData[$receiving->getWarehouseIdTo()] . ',' . $stockReceivingProduct->getProductId();
                        } else {
                            $mailData[$receiving->getWarehouseIdTo()] = $stockReceivingProduct->getProductId();
                        }
                    }
//                    $product = Mage::getModel('catalog/product')->load($stockReceivingProduct->getProductId());
//                    $stockItem = $product->getStockItem();
//                    $stockItem->setQty((int) $stockItem->getQty() + (int) $stockReceivingProduct->getQty())->save();
                    $code = (int) $stockReceivingProduct->getQty();
                    $pId = $stockReceivingProduct->getProductId();
                    if (Mage::getStoreConfig('inventory/general/updatestock')) {
                        $product = Mage::getModel('catalog/product')->load($pId);
                        $sqlSelect = 'Select qty from ' . $installer->getTable("cataloginventory_stock_item") . ' WHERE (product_id = ' . $pId . ')';
                        $results = $readConnection->fetchAll($sqlSelect);
                        foreach ($results as $result) {
                            $oldQtyProduct = $result['qty'];
                        }
                        $minToChangeStatus = Mage::getStoreConfig('cataloginventory/item_options/min_qty');
                        if (($oldQtyProduct + $code) > $minToChangeStatus) {
                            $sqlUpdateProduct = 'UPDATE ' . $installer->getTable("cataloginventory_stock_item") . ' SET qty = qty + ' . $code . ', is_in_stock = 1 WHERE (product_id = ' . $pId . ')';
                            $sqlUpdateProductStatus = 'UPDATE ' . $installer->getTable("cataloginventory_stock_status") . ' SET qty = qty + ' . $code . ', stock_status = 1 WHERE (product_id = ' . $pId . ')';
                        } else {
                            if ($product->getTypeId() != 'configurable') {
                                $sqlUpdateProduct = 'UPDATE ' . $installer->getTable("cataloginventory_stock_item") . ' SET qty = qty + ' . $code . ', is_in_stock = 0 WHERE (product_id = ' . $pId . ')';
                            } else {
                                $sqlUpdateProduct = 'UPDATE ' . $installer->getTable("cataloginventory_stock_item") . ' SET qty = qty + ' . $code . ' WHERE (product_id = ' . $pId . ')';
                            }
                            $sqlUpdateProductStatus = 'UPDATE ' . $installer->getTable("cataloginventory_stock_status") . ' SET qty = qty + ' . $code . ', stock_status = 0 WHERE (product_id = ' . $pId . ')';
                        }
                    } else {
                        $sqlUpdateProduct = 'UPDATE ' . $installer->getTable("cataloginventory_stock_item") . ' SET qty = qty + ' . $code . ' WHERE (product_id = ' . $pId . ')';
                        $sqlUpdateProductStatus = 'UPDATE ' . $installer->getTable("cataloginventory_stock_status") . ' SET qty = qty + ' . $code . ' WHERE (product_id = ' . $pId . ')';
                    }
                    $writeConnection->query($sqlUpdateProduct);
                    $writeConnection->query($sqlUpdateProductStatus);
                }
            } else {
                foreach ($stockReceivingProducts as $stockReceivingProduct) {
                    $warehouseProduct = Mage::getModel('inventory/warehouseproduct')->getCollection()
                            ->addFieldToFilter('warehouse_id', $receiving->getWarehouseIdTo())
                            ->addFieldToFilter('product_id', $stockReceivingProduct->getProductId())
                            ->getFirstItem();
                    $warehouseProductQtyOld = $warehouseProduct->getQty() - $stockReceivingProduct->getQty();
                    if ($warehouseProductQtyOld >= $qty_notice || $warehouseProductQtyOld == 0) {
                        if ($mailData[$receiving->getWarehouseIdTo()]) {
                            $mailData[$receiving->getWarehouseIdTo()] = $mailData[$receiving->getWarehouseIdTo()] . ',' . $stockReceivingProduct->getProductId();
                        } else {
                            $mailData[$receiving->getWarehouseIdTo()] = $stockReceivingProduct->getProductId();
                        }
                    }
                    $unwarehouseId = Mage::getModel('inventory/warehouse')->getCollection()
                            ->addFieldToFilter('is_unwarehouse', 1)
                            ->getFirstItem()
                            ->getWarehouseId();
                    $warehouse_product = Mage::getModel('inventory/warehouseproduct')
                            ->getCollection()
                            ->addFieldToFilter('warehouse_id', $unwarehouseId)
                            ->addFieldToFilter('product_id', $stockReceivingProduct->getProductId())
                            ->getFirstItem();
                    $old_qty = (int) $warehouse_product->getQty();
                    $new_qty = (int) $old_qty - (int) $stockReceivingProduct->getQty();
                    $old_qtyAvailable = (int) $warehouse_product->getQtyAvailable();
                    $new_qtyAvailable = (int) $old_qtyAvailable - (int) $stockReceivingProduct->getQty();
                    $warehouse_product->setQty($new_qty)
                            ->setQtyAvailable($new_qtyAvailable)
                            ->save();
                }
            }
            $email_notice = Mage::getStoreConfig('inventory/notice/email_notice');
            $notice_for = Mage::getStoreConfig('inventory/notice/notice_for');
            $i = 0;
            $j = 0;
            if ($email_notice == 1) {
                foreach ($mailData as $k => $v) {
                    if ($notice_for == 1 || $notice_for == 3) {
                        $values = explode(',', $v);
                        foreach ($values as $productId) {
                            $wproductqty = Mage::getModel('inventory/warehouseproduct')->getCollection()
                                    ->addFieldToFilter('product_id', $productId)
                                    ->addFieldToFilter('warehouse_id', $k)
                                    ->getFirstItem()
                                    ->getQty();
                            $qty = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId)->getQty();
                            if ($wproductqty < $qty_notice) {
                                $i++;
                            }
                            if ($qty < $qty_notice) {
                                $j++;
                            }
                        }
                    }
                }
                if ($i > 0 && $notice_for == 1 || $notice_for == 3) {
                    $warehouse = Mage::getModel('inventory/warehouse')->load($k);
                    Mage::helper('inventory/email')->sendWarehouseEmail($warehouse);
                }
            }
        }
    }

    public function supplierSaveAfter($observer) {
        if (Mage::registry('UPDATE_SUPPLIER'))
            return;
        Mage::register('UPDATE_SUPPLIER', true);
        $supplier = $observer->getInventorySupplier();
        $supplierId = $supplier->getId();
        $purchaseOrders = Mage::getModel('inventory/purchaseorder')
                ->getCollection()
                ->addFieldToFilter('supplier_id', $supplierId)
                ->setOrder('purchase_on', 'DESC');
        $total_order = 0;
        $purchase_order = 0;
        $return_order = 0;
        $last_purchase_order = null;
        if ($total_order = count($purchaseOrders)) {
            $purchase_order = 0;
            foreach ($purchaseOrders as $purchaseOrder) {
                if (!$last_purchase_order)
                    $last_purchase_order = $purchaseOrder->getPurchaseOn();
                $purchase_order += $purchaseOrder->getTotalAmount();
                $returnOrders = Mage::getModel('inventory/returnorder')
                        ->getCollection()
                        ->addFieldToFilter('purchase_order_id', $purchaseOrder->getId());
                if (count($returnOrders)) {
                    foreach ($returnOrders as $returnOrder)
                        $return_order += $returnOrder->getTotalAmount();
                }
            }
        }
        $supplier->setTotalOrder($total_order)
                ->setPurchaseOrder($purchase_order)
                ->setReturnOrder($return_order)
                ->setLastPurchaseOrder($last_purchase_order)
                ->save();
    }

    public function deliverySaveAfter($observer) {
        if (Mage::registry('UPDATE_SUPPLIER_DELIVERY'))
            return;
        Mage::register('UPDATE_SUPPLIER_DELIVERY', true);
        $delivery = $observer->getInventoryDelivery();
        $purchaseOrderId = $delivery->getPurchaseOrderId();
        //zend_debug::dump($delivery->getData());die();
        $transactionData = array();
        if ($purchaseOrderId) {
            $purchaseOrderDelivery = Mage::getModel('inventory/purchaseorder')->load($purchaseOrderId);

            $supplierId = $purchaseOrderDelivery->getSupplierId();
            $supplier = Mage::getModel('inventory/supplier')->load($supplierId);

            $purchaseOrders = Mage::getModel('inventory/purchaseorder')
                    ->getCollection()
                    ->addFieldToFilter('supplier_id', $supplierId)
                    ->setOrder('purchase_on', 'DESC');
            $total_order = 0;
            $purchase_order = 0;
            $return_order = 0;
            $last_purchase_order = null;
            if ($total_order = count($purchaseOrders)) {
                $purchase_order = 0;
                foreach ($purchaseOrders as $purchaseOrder) {
                    if (!$last_purchase_order)
                        $last_purchase_order = $purchaseOrder->getPurchaseOn();
                    $purchase_order += $purchaseOrder->getTotalAmount();
                    $returnOrders = Mage::getModel('inventory/returnorder')
                            ->getCollection()
                            ->addFieldToFilter('purchase_order_id', $purchaseOrder->getId());
                    if (count($returnOrders)) {
                        foreach ($returnOrders as $returnOrder)
                            $return_order += $returnOrder->getTotalAmount();
                    }
                }
            }
            $supplier->setTotalOrder($total_order)
                    ->setPurchaseOrder($purchase_order)
                    ->setReturnOrder($return_order)
                    ->setLastPurchaseOrder($last_purchase_order)
                    ->save();
        }
    }

    public function purchaseorderSaveAfter($observer) {
        if (Mage::registry('UPDATE_SUPPLIER_PURCHASEORDER'))
            return;
        Mage::register('UPDATE_SUPPLIER_PURCHASEORDER', true);
        $purchaseOrder = $observer->getInventoryPurchaseorder();
        $purchaseOrderId = $purchaseOrder->getId();
        if ($purchaseOrderId) {
            $supplierId = $purchaseOrder->getSupplierId();
            $supplier = Mage::getModel('inventory/supplier')->load($supplierId);
            $purchaseOrders = Mage::getModel('inventory/purchaseorder')
                    ->getCollection()
                    ->addFieldToFilter('supplier_id', $supplierId)
                    ->setOrder('purchase_on', 'DESC');
            $total_order = 0;
            $purchase_order = 0;
            $return_order = 0;
            $last_purchase_order = null;
            if ($total_order = count($purchaseOrders)) {
                $purchase_order = 0;
                foreach ($purchaseOrders as $purchaseOrder) {
                    if (!$last_purchase_order)
                        $last_purchase_order = $purchaseOrder->getPurchaseOn();
                    $purchase_order += $purchaseOrder->getTotalAmount();
                    $returnOrders = Mage::getModel('inventory/returnorder')
                            ->getCollection()
                            ->addFieldToFilter('purchase_order_id', $purchaseOrder->getId());
                    if (count($returnOrders)) {
                        foreach ($returnOrders as $returnOrder)
                            $return_order += $returnOrder->getTotalAmount();
                    }
                }
            }
            $supplier->setTotalOrder($total_order)
                    ->setPurchaseOrder($purchase_order)
                    ->setReturnOrder($return_order)
                    ->setLastPurchaseOrder($last_purchase_order)
                    ->save();
        }
    }

    public function returnorderSaveAfter($observer) {
        if (Mage::registry('UPDATE_SUPPLIER_RETURNORDER'))
            return;
        Mage::register('UPDATE_SUPPLIER_RETURNORDER', true);

        $returnOrder = $observer->getInventoryReturnorder();
        $supplierId = $returnOrder->getSupplierId();
        if ($supplierId) {
            $supplier = Mage::getModel('inventory/supplier')->load($supplierId);
            $purchaseOrders = Mage::getModel('inventory/purchaseorder')
                    ->getCollection()
                    ->addFieldToFilter('supplier_id', $supplierId)
                    ->setOrder('purchase_on', 'DESC');
            $total_order = 0;
            $purchase_order = 0;
            $return_order = 0;
            $last_purchase_order = '';
            if ($total_order = count($purchaseOrders)) {
                $purchase_order = 0;
                foreach ($purchaseOrders as $purchaseOrder) {
                    if (!$last_purchase_order)
                        $last_purchase_order = $purchaseOrder->getPurchaseOn();
                    $purchase_order += $purchaseOrder->getTotalAmount();
                    $returnOrders = Mage::getModel('inventory/returnorder')
                            ->getCollection()
                            ->addFieldToFilter('purchase_order_id', $purchaseOrder->getId());
                    if (count($returnOrders)) {
                        foreach ($returnOrders as $returnOrder)
                            $return_order += $returnOrder->getTotalAmount();
                    }
                }
            }
            $supplier->setTotalOrder($total_order)
                    ->setPurchaseOrder($purchase_order)
                    ->setReturnOrder($return_order)
                    ->setLastPurchaseOrder($last_purchase_order)
                    ->save();
        }
    }

    /*
     * Bat event sales order shipment save after
     */

    public function salesOrderShipmentSaveAfter($observer) {

        if (Mage::getModel('admin/session')->getData('break_shipment_event_dropship')
                || Mage::getModel('core/session')->getData('break_shipment_event_dropship', true)) {
            Mage::getModel('admin/session')->setData('break_shipment_event_dropship', false);
            Mage::getModel('core/session')->setData('break_shipment_event_dropship', false);
            return;
        }
        $inventoryShipmentData = array();
        $data = Mage::app()->getRequest()->getParams();
        $shipment = $observer->getEvent()->getShipment();
        $order = $shipment->getOrder();
        $orderId = $data['order_id'];
        $shippingMethod = $order->getShippingMethod();
        $shipmentId = $shipment->getId();
        $total_qty_order = $order->getTotalQtyOrdered();
        $total_qty_shipped = array();
        $total_shipped = array();
        foreach ($order->getAllItems() as $ordered) {
            //row_total_incl_tax
            if ($ordered->getParentItemId()) {//neu no co cha 
                if ($data['shipment']['items'][$ordered->getParentItemId()]) { //neu cha no đc gán qty to ship
                    $inventoryShipmentData[$ordered->getItemId()]['qty'] = $data['shipment']['items'][$ordered->getParentItemId()];
                    if (isset($data['warehouse-shipment']['items'][$ordered->getParentItemId()]) && $data['warehouse-shipment']['items'][$ordered->getParentItemId()] != '')
                        $inventoryShipmentData[$ordered->getItemId()]['warehouse'] = $data['warehouse-shipment']['items'][$ordered->getParentItemId()];
                    else
                        $inventoryShipmentData[$ordered->getItemId()]['warehouse'] = $data['warehouse-shipment']['items'][$ordered->getItemId()];
                    $inventoryShipmentData[$ordered->getItemId()]['product_id'] = $ordered->getProductId();
                    $inventoryShipmentData[$ordered->getItemId()]['price_incl_tax'] = $ordered->getPriceInclTax();
                } else {
                    $inventoryShipmentData[$ordered->getItemId()]['qty'] = $data['shipment']['items'][$ordered->getItemId()];
                    $inventoryShipmentData[$ordered->getItemId()]['warehouse'] = $data['warehouse-shipment']['items'][$ordered->getItemId()];
                    $inventoryShipmentData[$ordered->getItemId()]['product_id'] = $ordered->getProductId();
                    $inventoryShipmentData[$ordered->getItemId()]['price_incl_tax'] = $ordered->getPriceInclTax();
                }
            } else {//neu no ko co cha
                if (!$ordered->getHasChildren()) { // va no khong co con
                    $inventoryShipmentData[$ordered->getItemId()]['qty'] = $data['shipment']['items'][$ordered->getItemId()];
                    $inventoryShipmentData[$ordered->getItemId()]['warehouse'] = $data['warehouse-shipment']['items'][$ordered->getItemId()];
                    $inventoryShipmentData[$ordered->getItemId()]['product_id'] = $ordered->getProductId();
                    $inventoryShipmentData[$ordered->getItemId()]['price_incl_tax'] = $ordered->getPriceInclTax();
                } else { //neu no co con
                    $warehouseName = Mage::helper('inventory/warehouse')->getWarehouseNameByWarehouseId($data['warehouse-shipment']['items'][$ordered->getItemId()]);
                    $inventoryShipmentModel = Mage::getModel('inventory/inventoryshipment');
                    $inventoryShipmentModel->setItemId($ordered->getItemId())
                            ->setProductId($ordered->getProductId())
                            ->setOrderId($orderId)
                            ->setWarehouseId($data['warehouse-shipment']['items'][$ordered->getItemId()])
                            ->setWarehouseName($warehouseName)
                            ->setShipmentId($shipmentId)
                            ->setItemShiped(0)
                            ->save();
                }
            }
            if ($inventoryShipmentData[$ordered->getItemId()]['qty'] > ($ordered->getQtyOrdered() - $ordered->getQtyRefunded())) {
                $inventoryShipmentData[$ordered->getItemId()]['qty'] = ($ordered->getQtyOrdered() - $ordered->getQtyRefunded());
            }
        }

        //get total qty shipped
        $order_item_collection = Mage::getModel('sales/order_item')
                ->getCollection()
                ->addFieldToFilter('order_id', $order->getEntityId());
        foreach ($order_item_collection as $c) {
            if (!$c->getParentItemId()) {
                if ($c->getProductType() == 'virtual' || $c->getProductType() == 'downloadable') {
                    $total_qty_order += -(int) $c->getQtyOrdered();
                }
                $shipment_item = Mage::getModel('sales/order_shipment_item')
                        ->getCollection()
                        ->addFieldToFilter('order_item_id', $c->getItemId());
                foreach ($shipment_item as $i) {
                    $qty_shipped = $i->getQty();
                    $total_shipped[] = (int) $qty_shipped;
                }
            }
        }
        $total_products_shipped = array_sum($total_shipped);
        //end get total qty shipped
        //set status for shipment
        if ($total_qty_order == 0) {
            $shipping_progress = 2;
        } else {
            if ((int) $total_products_shipped == 0) {
                $shipping_progress = 0;
            } elseif ((int) $total_products_shipped < (int) $total_qty_order) {
                $shipping_progress = 1;
            } elseif ((int) $total_products_shipped == (int) $total_qty_order) {
                $shipping_progress = 2;
            }
        }
        $order->setShippingProgress($shipping_progress);
        //end set status

        try {
            $mailData = array();
            $transactionData = array();
            $qty_notice = Mage::getStoreConfig('inventory/notice/qty_notice');
            $i = 0;
            /* Thinhnd */
            $warehouseorders = Mage::getModel('inventory/warehouseorder')->getCollection()
                    ->addFieldToFilter('order_id', $order->getId());
            $warehouseorderId = array();
            foreach ($inventoryShipmentData as $key => $dataArray) {
                //add data to create transaction
                $transactionData[$dataArray['warehouse']][$dataArray['product_id']] = $dataArray['qty'];
                $warehouseName = Mage::helper('inventory/warehouse')->getWarehouseNameByWarehouseId($dataArray['warehouse']);
                $inventoryShipmentModel = Mage::getModel('inventory/inventoryshipment');
                $inventoryShipmentModel->setItemId($key)
                        ->setProductId($dataArray['product_id'])
                        ->setOrderId($orderId)
                        ->setWarehouseId($dataArray['warehouse'])
                        ->setWarehouseName($warehouseName)
                        ->setShipmentId($shipmentId)
                        ->setItemShiped($dataArray['qty'])
                        ->save();

                $reportMovedModel = Mage::getModel('inventory/reportproductmoved');
                $pId = $dataArray['product_id'];
                $productModel = Mage::getModel('catalog/product')->load($pId);
                $pName = $productModel->getName();
                $pSku = $productModel->getSku();
                $reportMovedModel->setProductId($pId)
                        ->setProductName($pName)
                        ->setProductSku($pSku)
                        ->setAmountMoved($dataArray['price_incl_tax'] * $dataArray['qty'])
                        ->setQtyMoved($dataArray['qty'])
                        ->setMovedAt(date('Y-m-d'))
                        ->setMovedType(2) //2 for ship
                        ->save();

                $warehouseProduct = Mage::getModel('inventory/warehouseproduct')
                        ->getCollection()
                        ->addFieldToFilter('warehouse_id', $dataArray['warehouse'])
                        ->addFieldToFilter('product_id', $dataArray['product_id'])
                        ->getFirstItem();
                $oldQty = $warehouseProduct->getQty();
                $newQty = $oldQty - $dataArray['qty'];
                if ($dataArray['qty'] != 0 && ($newQty < $oldQty)) {
                    $warehouseOr = Mage::getModel('inventory/warehouseorder')->getCollection()
                            ->addFieldToFilter('order_id', $order->getId())
                            ->addFieldToFilter('warehouse_id', $dataArray['warehouse'])
                            ->addFieldToFilter('product_id', $dataArray['product_id'])
                            ->getFirstItem();
                    if (!$warehouseOr->getId()) {
                        $newQtyAvailable = $warehouseProduct->getQtyAvailable() - $dataArray['qty'];
                        $warehouseProduct->setQtyAvailable($newQtyAvailable);
                    }
                    $warehouseProduct->setQty($newQty);
                    $warehouseProduct->save();
                    /* ThinhND Return qty available */
                    $warehouseorderId[$dataArray['product_id']] = array('qty' => $dataArray['qty'], 'warehouse_id' => $dataArray['warehouse']);
                }
                if ($newQty < $qty_notice && $oldQty >= $qty_notice) {
                    if ($mailData[$dataArray['warehouse']]) {
                        $mailData[$dataArray['warehouse']] = $mailData[$dataArray['warehouse']] . ',' . $dataArray['product_id'];
                    } else {
                        $mailData[$dataArray['warehouse']] = $dataArray['product_id'];
                    }
                }
            }
            foreach ($warehouseorders as $warehouseorder) {
                $warehouseProduct = Mage::getModel('inventory/warehouseproduct')->getCollection()
                        ->addFieldToFilter('warehouse_id', $warehouseorder->getWarehouseId())
                        ->addFieldToFilter('product_id', $warehouseorder->getProductId())
                        ->getFirstItem();
                $currentQtyOrder = $warehouseorder->getQty() - $warehouseorderId[$warehouseorder->getProductId()]['qty'];
                if ($warehouseorder->getWarehouseId() != $warehouseorderId[$warehouseorder->getProductId()]['warehouse_id']) {
                    $currentQty = $warehouseProduct->getQtyAvailable() + $warehouseorderId[$warehouseorder->getProductId()]['qty'];
                    $warehouseProduct->setQtyAvailable($currentQty)
                            ->save();
                }
                $warehouseorder->setQty($currentQtyOrder)
                        ->save();
            }
            //create send transaction
            $customerId = $order->getCustomerId();
            $customerName = Mage::getModel('customer/customer')->load($customerId)->getName();
            $createdAt = date('Y-m-d', strtotime(now()));
            $admin = Mage::getModel('admin/session')->getUser()->getUsername();
            $reason = Mage::helper('inventory')->__("Shipment for order #%s", $order->getIncrementId());
            foreach ($transactionData as $warehouseId => $productData) {
                $transactionSendModel = Mage::getModel('inventory/transaction');
                $transactionSendData = array();
                $totalQty = 0;
                $transactionSendData['type'] = '5';
                $transactionSendData['from_id'] = $warehouseId;
                $transactionSendData['from_name'] = Mage::helper('inventory/warehouse')->getWarehouseNameByWarehouseId($warehouseId);
                $transactionSendData['to_id'] = $customerId;
                $transactionSendData['to_name'] = $customerName;
                $transactionSendData['created_at'] = $createdAt;
                $transactionSendData['created_by'] = $admin;
                $transactionSendData['reason'] = $reason;
                $transactionSendModel->addData($transactionSendData);
                try {
                    $transactionSendModel->save();
                    //save product for transaction
                    foreach ($productData as $productId => $qty) {
                        $product = Mage::getModel('catalog/product')->load($productId);
                        Mage::getModel('inventory/transactionproduct')
                                ->setTransactionId($transactionSendModel->getId())
                                ->setProductId($productId)
                                ->setProductSku($product->getSku())
                                ->setProductName($product->getName())
                                ->setQty(-$qty)
                                ->save()
                        ;
                        $totalQty += $qty;
                    }
                    $transactionSendModel->setTotalProducts(-$totalQty)->save();
                } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                }
            }

            //send email notice
            $email_notice = Mage::getStoreConfig('inventory/notice/email_notice');
            $notice_for = Mage::getStoreConfig('inventory/notice/notice_for');
            if ($email_notice == 1) {
                if (count($mailData) > 0) {
                    foreach ($mailData as $k => $v) {
                        if ($notice_for == 1 || $notice_for == 3) {
                            $warehouse = Mage::getModel('inventory/warehouse')->load($k);
                            Mage::helper('inventory/email')->sendWarehouseEmail($warehouse);
                        }
                    }
                }
            }
            return;
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
    }

    /*
     * Bat event sales_order_payment_refund
     */

    public function salesOrderPaymentRefundEvent($observer) {
        $requestParams = Mage::app()->getRequest()->getParams();
        $inventoryShipmentData = array();
        $order = $observer->getPayment()->getOrder();
        foreach ($order->getAllItems() as $ordered) {
            $inventoryShipmentData[$ordered->getItemId()]['item_id'] = $ordered->getItemId();
            $inventoryShipmentData[$ordered->getItemId()]['order_id'] = $ordered->getOrderId();
            $inventoryShipmentData[$ordered->getItemId()]['product_id'] = $ordered->getProductId();
            $inventoryShipmentData[$ordered->getItemId()]['total_qty_refunded'] = $ordered->getQtyRefunded();
            $inventoryShipmentData[$ordered->getItemId()]['qty_shipped'] = $ordered->getQtyShipped();
            $inventoryShipmentData[$ordered->getItemId()]['back_to_stock'] = $requestParams['creditmemo']['items'][$ordered->getItemId()]['back_to_stock'];
            $inventoryShipmentData[$ordered->getItemId()]['parent_item_id'] = $ordered->getParentItemId();
            $inventoryShipmentData[$ordered->getItemId()]['product_type'] = $ordered->getProductType();
            $inventoryShipmentData[$ordered->getItemId()]['qty_refunded'] = $requestParams['creditmemo']['items'][$ordered->getItemId()]['qty'];
            $inventoryShipmentData[$ordered->getItemId()]['price_incl_tax'] = $ordered->getPriceInclTax();
        }

        foreach ($inventoryShipmentData as $inventoryShipmentItem) {//duyet tung item dc refuned
            $qtyItemWantRefuned = $inventoryShipmentItem['qty_refunded'];
            if (!$qtyItemWantRefuned && $inventoryShipmentItem['parent_item_id'] && $inventoryShipmentData[$inventoryShipmentItem['parent_item_id']]['qty_refunded']) {
                $parentItem = $inventoryShipmentItem['parent_item_id'];
                $qtyItemWantRefuned = $inventoryShipmentData[$parentItem]['qty_refunded'];
                $inventoryShipmentItem['price_incl_tax'] = $inventoryShipmentData[$parentItem]['price_incl_tax'];
                $inventoryShipmentItem['back_to_stock'] = $inventoryShipmentData[$parentItem]['back_to_stock'];
            }
            if ($inventoryShipmentItem['parent_item_id'] == NULL || $inventoryShipmentItem['product_type'] == 'simple' && (int) $inventoryShipmentItem['back_to_stock'] == 1) {
                $inventoryShipmentModel = Mage::getModel('inventory/inventoryshipment')
                        ->getCollection()
                        ->addFieldToFilter('item_id', $inventoryShipmentItem['item_id'])
                        ->addFieldToFilter('product_id', $inventoryShipmentItem['product_id'])
                        ->addFieldToFilter('order_id', $inventoryShipmentItem['order_id'])
                ;
                $reportReceivedModel = Mage::getModel('inventory/reportproductreceived');
                $pId = $inventoryShipmentItem['product_id'];
                $productModel = Mage::getModel('catalog/product')->load($pId);
                $pName = $productModel->getName();
                $pSku = $productModel->getSku();
                $reportReceivedModel->setProductId($pId)
                        ->setProductName($pName)
                        ->setProductSku($pSku)
                        ->setAmountReceived($inventoryShipmentItem['price_incl_tax'] * $qtyItemWantRefuned)
                        ->setQtyReceived($inventoryShipmentItem['qty_refunded'])
                        ->setReceivedAt(date('Y-m-d'))
                        ->setReceivedType(2) //2 for customer refund
                        ->save();
                foreach ($inventoryShipmentModel as $inventoryShipment) {//duyet tat ca cac ban ghi cung orderid,itemid,productid
                    $qtyShipment = $inventoryShipment->getItemShiped();
                    $qtyRefuned = $inventoryShipment->getItemRefuned();

                    //neu $qtyItemWantRefuned lon hon 0
                    //neu $qtyShipment lon hon $qtyRefuned
                    // $qtyItemWantRefuned + $qtyRefuned <= $qtyShipment thì 
                    // set lai item_refuned column = $qtyItemWantRefuned + $qtyRefuned
                    //va set lai $qtyItemWantRefuned = 0;
                    //neu $qtyItemWantRefuned + $qtyRefuned > $qtyShipment thì
                    //set lai item_refuned column = $qtyShipment
                    //va set lai $qtyItemWantRefuned = $qtyItemWantRefuned-$qtyShipment- $qtyRefuned
                    //neu $qtyItemWantRefuned nho hon 0 hoac bang 0 thi break
                    //ket thuc vong lap
                    if ($qtyItemWantRefuned > 0) {
                        if ($qtyShipment > $qtyRefuned) {
                            if ($qtyItemWantRefuned + $qtyRefuned <= $qtyShipment) {
                                $inventoryShipment->setItemRefuned($qtyItemWantRefuned + $qtyRefuned);
                                $inventoryShipment->save();
                                $warehouseId = $inventoryShipment->getWarehouseId();
                                $productId = $inventoryShipment->getProductId();
                                $warehouseProduct = Mage::getModel('inventory/warehouseproduct')
                                        ->getCollection()
                                        ->addFieldToFilter('warehouse_id', $warehouseId)
                                        ->addFieldToFilter('product_id', $productId)
                                        ->getFirstItem();
                                $qty = $warehouseProduct->getQty();
                                $qty += $qtyItemWantRefuned;
                                $warehouseProduct->setQty($qty)->save();
                                $qtyItemWantRefuned = 0;
                            } else {
                                $inventoryShipment->setItemRefuned($qtyShipment);
                                $inventoryShipment->save();
                                $warehouseId = $inventoryShipment->getWarehouseId();
                                $productId = $inventoryShipment->getProductId();
                                $warehouseProduct = Mage::getModel('inventory/warehouseproduct')
                                        ->getCollection()
                                        ->addFieldToFilter('warehouse_id', $warehouseId)
                                        ->addFieldToFilter('product_id', $productId)
                                        ->getFirstItem();
                                $qty = $warehouseProduct->getQty();
                                $qty += ($qtyShipment - $qtyRefuned);
                                $warehouseProduct->setQty($qty)->save();
                                $qtyItemWantRefuned = $qtyItemWantRefuned - ($qtyShipment - $qtyRefuned);
                            }
                        }
                    }
                }
            }
        }
    }

    public function salesOrderItemCancelEvent($observer) {
        $inventoryShipmentData = array();
        $order = $observer->getPayment()->getOrder();
        foreach ($order->getAllItems() as $ordered) {
            $inventoryShipmentData[$ordered->getItemId()]['item_id'] = $ordered->getItemId();
            $inventoryShipmentData[$ordered->getItemId()]['order_id'] = $ordered->getOrderId();
            $inventoryShipmentData[$ordered->getItemId()]['product_id'] = $ordered->getProductId();
            $inventoryShipmentData[$ordered->getItemId()]['qty_shipped'] = $ordered->getQtyShipped();
            $inventoryShipmentData[$ordered->getItemId()]['parent_item_id'] = $ordered->getParentItemId();
            $inventoryShipmentData[$ordered->getItemId()]['product_type'] = $ordered->getProductType();
            $inventoryShipmentData[$ordered->getItemId()]['price_incl_tax'] = $ordered->getPriceInclTax();
        }
        foreach ($inventoryShipmentData as $inventoryShipmentItem) {//duyet tung item dc refuned
            $qtyItemWantCancel = $inventoryShipmentItem['qty_shipped'];
            if ((!$qtyItemWantCancel || $qtyItemWantCancel == 0) && $inventoryShipmentItem['parent_item_id'] && $inventoryShipmentData[$inventoryShipmentItem['parent_item_id']]['qty_shipped']) {
                $parentItem = $inventoryShipmentItem['parent_item_id'];
                $qtyItemWantCancel = $inventoryShipmentData[$parentItem]['qty_shipped'];
                $inventoryShipmentItem['price_incl_tax'] = $inventoryShipmentData[$parentItem]['price_incl_tax'];
            }
            if ($inventoryShipmentItem['product_type'] == 'simple') {
                $inventoryShipmentModel = Mage::getModel('inventory/inventoryshipment')
                        ->getCollection()
                        ->addFieldToFilter('item_id', $inventoryShipmentItem['item_id'])
                        ->addFieldToFilter('product_id', $inventoryShipmentItem['product_id'])
                        ->addFieldToFilter('order_id', $inventoryShipmentItem['order_id'])
                ;
                $reportReceivedModel = Mage::getModel('inventory/reportproductreceived');
                $pId = $inventoryShipmentItem['product_id'];
                $productModel = Mage::getModel('catalog/product')->load($pId);
                $pName = $productModel->getName();
                $pSku = $productModel->getSku();
                $reportReceivedModel->setProductId($pId)
                        ->setProductName($pName)
                        ->setProductSku($pSku)
                        ->setAmountReceived($inventoryShipmentItem['price_incl_tax'] * $qtyShiped)
                        ->setQtyReceived($inventoryShipmentItem['qty_shipped'])
                        ->setReceivedAt(date('Y-m-d'))
                        ->setReceivedType(3) //2 for cancel order
                        ->save();
                foreach ($inventoryShipmentModel as $inventoryShipment) {//duyet tat ca cac ban ghi cung orderid,itemid,productid
                    $qtyShipment = $inventoryShipment->getItemShiped();
                    if ($qtyItemWantCancel > 0) {
                        $inventoryShipment->setItemRefuned($qtyShipment);
                        $inventoryShipment->save();
                        $warehouseId = $inventoryShipment->getWarehouseId();
                        $productId = $inventoryShipment->getProductId();
                        $warehouseProduct = Mage::getModel('inventory/warehouseproduct')
                                ->getCollection()
                                ->addFieldToFilter('warehouse_id', $warehouseId)
                                ->addFieldToFilter('product_id', $productId)
                                ->getFirstItem();
                        $qty = $warehouseProduct->getQty();
                        $qty += $qtyShipment;
                        $qtyAvailable = $warehouseProduct->getQtyAvailable() + $qtyShipment;
                        $warehouseProduct->setQty($qty)
                                ->setQtyAvailable($qtyAvailable)
                                ->save();
                        $qtyItemWantCancel = $qtyItemWantCancel - $qtyShipment;
                    }
                }
            }
        }
    }

    public function catalogProductSaveAfterEvent($observer) {
        $productPost = Mage::app()->getRequest()->getPost('simple_product');
        $getProductData = Mage::app()->getRequest()->getParam('product');
        $warehouseId = Mage::app()->getRequest()->getParam('warehouse_product');
        $supplierId = Mage::app()->getRequest()->getParam('supplier_product');
        if ((!$warehouseId || $warehouseId == '') && $productPost && !is_array($getProductData)) {
            $warehouseId = $productPost['warehouse_product'];
        }
        if ((!$supplierId || $supplierId == '') && $productPost && !is_array($getProductData))
            $supplierId = $productPost['supplier_product'];
        $tax = Mage::app()->getRequest()->getParam('tax_for_product');
        if (!$tax)
            $tax = 0;
        $discount = Mage::app()->getRequest()->getParam('discount_for_product');
        if (!$discount)
            $discount = 0;
        if ($warehouseId != '') {
            if (!is_array($getProductData)) {
                $getProductData = $productPost;
            }
            $qty = $getProductData['stock_data']['qty'];
            if (!isset($qty) || !$qty) {
                $qty = $getProductData['stock_data']['original_inventory_qty'];
            }
            $product = $observer->getProduct();
            $cost = $getProductData['cost'];
            $productId = $product->getId();
            $idOfUnwarehouse = Mage::helper('inventory/warehouse')->getIdOfunWarehouse();
            /* save new product to warehouse */
            $warehouseProductModel = Mage::getModel('inventory/warehouseproduct')
                    ->setWarehouseId($warehouseId)
                    ->setProductId($productId)
                    ->setQty($qty)
                    ->setQtyAvailable($qty)
                    ->save();
            if ((int) $warehouseId != (int) $idOfUnwarehouse) {
                $warehouseProductModel = Mage::getModel('inventory/warehouseproduct');
                $warehouseProductModel
                        ->setWarehouseId($idOfUnwarehouse)
                        ->setProductId($productId)
                        ->setQty(0)
                        ->setQtyAvailable(0)
                        ->save();
            }
            /* save new product to inventory */
            $inventoryProductModel = Mage::getModel('inventory/inventory')
                            ->setProductId($productId)
                            ->setCostPrice($cost)
                            ->setLastUpdate(date('Y-m-d'))->save();

            /* save new product to supplier product */
            if (isset($supplierId))
                $supplierProductModel = Mage::getModel('inventory/supplierproduct')
                                ->setSupplierId($supplierId)
                                ->setProductId($productId)
                                ->setCost($cost)
                                ->setDiscount($discount)
                                ->setTax($tax)->save();
        }
    }

    public function catalogProductDeleteEvent($observer) {
        $product = $observer->getProduct();
        $warehouseProducts = Mage::getModel('inventory/warehouseproduct')
                ->getCollection()
                ->addFieldToFilter('product_id', $product->getId())
        ;
        if ($warehouseProducts->getSize()) {
            foreach ($warehouseProducts as $wp) {
                $wp->delete();
            }
        }
        $inventories = Mage::getModel('inventory/inventory')
                ->getCollection()
                ->addFieldToFilter('product_id', $product->getId())
        ;
        if ($inventories->getSize()) {
            foreach ($inventories as $iv) {
                $iv->delete();
            }
        }
    }

    public function adminSaveAfter($observer) {
        $admin = $observer->getEvent()->getObject();
        $whs = Mage::getModel('inventory/warehouse')->getCollection();
        foreach ($whs as $warehouse) {
            $assignment = Mage::getModel('inventory/assignment')->loadByWarehouseAndAdmin($warehouse->getId(), $admin->getId());
            if (!$assignment->getId()) {
                $assignment->setWarehouseId($warehouse->getId());
                $assignment->setAdminId($admin->getId());
                $assignment->setData('can_edit_warehouse', 0);
                $assignment->setData('can_adjust', 0);
                if ($warehouse->getIsUnwarehouse() == '1')
                    $assignment->setData('can_transfer', 1);
                else
                    $assignment->setData('can_transfer', 0);
                $assignment->setId(null)
                        ->save();
            }
        }
    }

    public function salesOrderCreditmemoSaveAfter($observer) {
        $data = Mage::app()->getRequest()->getPost();
        $creditmemoData = $data['creditmemo'];
        $creditmemo = $observer->getCreditmemo();
        $order = $creditmemo->getOrder();
//        foreach($creditmemo->getAllItems() as $item){
//            Zend_debug::dump($item->getData());
//        }
//        die();
        $supplierReturn = array();
        $transactionData = array();
        foreach ($creditmemo->getAllItems() as $item) {
            $qtyReturn = $item->getQty();
            if (!$item->getBackToStock()) {
                $warehouseorders = Mage::getModel('inventory/warehouseorder')->getCollection()
                        ->addFieldToFilter('order_id', $creditmemo->getOrderId())
                        ->addFieldToFilter('product_id', $item->getProductId());
                foreach ($warehouseorders as $warehouseorder) {
                    if ($warehouseorder->getQty() >= $qtyReturn)
                        $qtyRefund = $qtyReturn;
                    else
                        $qtyRefund = $warehouseorder->getQty();
                    $newQty = $warehouseorder->getQty() - $qtyRefund;
                    $warehouseorder->setQty($newQty)->save();
                    $warehouseProduct = Mage::getModel('inventory/warehouseproduct')->getCollection()
                            ->addFieldToFilter('warehouse_id', $warehouseorder->getWarehouseId())
                            ->addFieldToFilter('product_id', $warehouseorder->getProductId())
                            ->getFirstItem();
                    $newQtyAvailable = $warehouseProduct->getQtyAvailable() + $qtyRefund;
                    $warehouseProduct->setQtyAvailable($newQtyAvailable)
                            ->save();
                }
                continue;
            }
            $selectWarehouseSupplier = $creditmemoData['select-warehouse-supplier'][$item->getOrderItemId()];
            $warehouseReturnId = '';
            $supplierReturnId = '';
            if ($selectWarehouseSupplier) {
                if ($selectWarehouseSupplier == '1') {
                    $warehouseReturnId = $creditmemoData['warehouse-select'][$item->getOrderItemId()];
                } elseif ($selectWarehouseSupplier == '2') {
                    $supplierReturnId = $creditmemoData['supplier-select'][$item->getOrderItemId()];
                }
            }
            $inventoryShipmentModel = Mage::getModel('inventory/inventoryshipment')
                    ->getCollection()
                    ->addFieldToFilter('item_id', $item->getOrderItemId())
                    ->addFieldToFilter('product_id', $item->getProductId())
                    ->addFieldToFilter('order_id', $creditmemo->getOrderId());
            if ($warehouseReturnId) {
                $transactionData[$warehouseReturnId][$item->getProductId()] = $qtyReturn;
                $reportReceivedModel = Mage::getModel('inventory/reportproductreceived');
                $pId = $item->getProductId();
                $pName = $item->getName();
                $pSku = $item->getSku();
                $reportReceivedModel->setProductId($pId)
                        ->setProductName($pName)
                        ->setProductSku($pSku)
                        ->setAmountReceived($item->getData('price_incl_tax') * $qtyReturn)
                        ->setQtyReceived($qtyReturn)
                        ->setReceivedAt(date('Y-m-d'))
                        ->setReceivedType(2) //2 for customer refund
                        ->save();
                $inventoryShipmentModel = Mage::getModel('inventory/inventoryshipment')
                        ->getCollection()
                        ->addFieldToFilter('item_id', $item->getOrderItemId())
                        ->addFieldToFilter('product_id', $item->getProductId())
                        ->addFieldToFilter('order_id', $creditmemo->getOrderId())
                        ->setOrder('warehouse_id', 'desc');
                foreach ($inventoryShipmentModel as $inventoryShipment) {
                    $canReturn = $inventoryShipment->getItemShiped() - $inventoryShipment->getItemRefuned();
                    if ($canReturn > 0) {
                        if ($qtyReturn <= $canReturn) {
                            $inventoryShipment->setItemRefuned($inventoryShipment->getItemRefuned() + $qtyReturn);
                            $inventoryShipment->save();
                            $productId = $inventoryShipment->getProductId();
                            $warehouseProduct = Mage::getModel('inventory/warehouseproduct')
                                    ->getCollection()
                                    ->addFieldToFilter('warehouse_id', $warehouseReturnId)
                                    ->addFieldToFilter('product_id', $productId)
                                    ->getFirstItem();
                            if (!$warehouseProduct->getId())
                                $warehouseProduct->setWarehouseId($warehouseReturnId)
                                        ->setProductId($productId)
                                        ->save();
                            $qty = $warehouseProduct->getQty();
                            $qty += $qtyReturn;
                            $qtyAvailable = $warehouseProduct->getQtyAvailable() + $qtyReturn;
                            $warehouseProduct->setQty($qty)
                                    ->setQtyAvailable($qtyAvailable)
                                    ->save();
                            $qtyReturn = 0;
                            break;
                        }else {
                            $inventoryShipment->setItemRefuned($inventoryShipment->getItemRefuned() + $canReturn);
                            $inventoryShipment->save();
                            $productId = $inventoryShipment->getProductId();
                            $warehouseProduct = Mage::getModel('inventory/warehouseproduct')
                                    ->getCollection()
                                    ->addFieldToFilter('warehouse_id', $warehouseReturnId)
                                    ->addFieldToFilter('product_id', $productId)
                                    ->getFirstItem();
                            $qty = $warehouseProduct->getQty();
                            $qty += $canReturn;
                            $qtyAvailable = $warehouseProduct->getQtyAvailable() + $canReturn;
                            $warehouseProduct->setQty($qty)
                                    ->setQtyAvailable($qtyAvailable)
                                    ->save();
                            $qtyReturn -= $canReturn;
                        }
                    }
                }
                if (!count($inventoryShipmentModel)) {
                    $warehouseorders = Mage::getModel('inventory/warehouseorder')->getCollection()
                            ->addFieldToFilter('order_id', $creditmemo->getOrderId())
                            ->addFieldToFilter('product_id', $item->getProductId());
                    foreach ($warehouseorders as $warehouseorder) {
                        if ($warehouseorder->getQty() >= $qtyReturn)
                            $qtyRefund = $qtyReturn;
                        else
                            $qtyRefund = $warehouseorder->getQty();
                        $newQty = $warehouseorder->getQty() - $qtyRefund;
                        $warehouseorder->setQty($newQty)->save();
                        $warehouseProduct = Mage::getModel('inventory/warehouseproduct')->getCollection()
                                ->addFieldToFilter('warehouse_id', $warehouseorder->getWarehouseId())
                                ->addFieldToFilter('product_id', $warehouseorder->getProductId())
                                ->getFirstItem();
                        $newQtyAvailable = $warehouseProduct->getQtyAvailable() + $qtyRefund;
                        $warehouseProduct->setQtyAvailable($newQtyAvailable)
                                ->save();
                    }
                }
            } elseif ($supplierReturnId) {
                $inventoryShipmentModel = Mage::getModel('inventory/inventoryshipment')
                        ->getCollection()
                        ->addFieldToFilter('item_id', $item->getOrderItemId())
                        ->addFieldToFilter('product_id', $item->getProductId())
                        ->addFieldToFilter('order_id', $creditmemo->getOrderId())
                        ->addFieldToFilter('supplier_id', $supplierReturnId);
//                                        ->setOrder('supplier_id','desc');
                foreach ($inventoryShipmentModel as $inventoryShipment) {
                    $canReturn = $inventoryShipment->getItemShiped() - $inventoryShipment->getItemRefuned();
                    if ($canReturn > 0) {
                        if ($qtyReturn <= $canReturn) {
                            $inventoryShipment->setItemRefuned($inventoryShipment->getItemRefuned() + $qtyReturn);
                            $inventoryShipment->save();
                            $productId = $inventoryShipment->getProductId();
                            $product = Mage::getModel('catalog/product')->load($productId);
                            $stockItem = $product->getStockItem();
                            $stockItem->setQty((int) $stockItem->getQty() - (int) $qtyReturn)->save();
                            if (!$supplierReturn[$supplierReturnId][$inventoryShipment->getItemId()])
                                $supplierReturn[$supplierReturnId][$inventoryShipment->getItemId()] = 0;
                            $supplierReturn[$supplierReturnId][$inventoryShipment->getItemId()] += $qtyReturn;
                            $qtyReturn = 0;
                            break;
                        }else {
                            $inventoryShipment->setItemRefuned($inventoryShipment->getItemRefuned() + $canReturn);
                            $inventoryShipment->save();
                            $productId = $inventoryShipment->getProductId();
                            $product = Mage::getModel('catalog/product')->load($productId);
                            $stockItem = $product->getStockItem();
                            $stockItem->setQty((int) $stockItem->getQty() - (int) $canReturn)->save();
                            if (!$supplierReturn[$supplierReturnId][$inventoryShipment->getItemId()])
                                $supplierReturn[$supplierReturnId][$inventoryShipment->getItemId()] = 0;
                            $supplierReturn[$supplierReturnId][$inventoryShipment->getItemId()] += $canReturn;
                            $qtyReturn -= $canReturn;
                        }
                    }
                }
            }
        }
        //create receive transaction
        $customerId = $order->getCustomerId();
        $customerName = Mage::getModel('customer/customer')->load($customerId)->getName();
        $createdAt = date('Y-m-d', strtotime(now()));
        $admin = Mage::getModel('admin/session')->getUser()->getUsername();
        $reason = Mage::helper('inventory')->__("Credit memo of order #%s", $order->getIncrementId());
        foreach ($transactionData as $warehouseId => $productData) {
            $transactionReceiveModel = Mage::getModel('inventory/transaction');
            $transactionReceiveData = array();
            $totalQty = 0;
            $transactionReceiveData['type'] = '6';
            $transactionReceiveData['to_id'] = $warehouseId;
            $transactionReceiveData['to_name'] = Mage::helper('inventory/warehouse')->getWarehouseNameByWarehouseId($warehouseId);
            $transactionReceiveData['from_id'] = $customerId;
            $transactionReceiveData['from_name'] = $customerName;
            $transactionReceiveData['created_at'] = $createdAt;
            $transactionReceiveData['created_by'] = $admin;
            $transactionReceiveData['reason'] = $reason;
            $transactionReceiveModel->addData($transactionReceiveData);
            try {
                $transactionReceiveModel->save();
                //save product for transaction
                foreach ($productData as $productId => $qty) {
                    $product = Mage::getModel('catalog/product')->load($productId);
                    Mage::getModel('inventory/transactionproduct')
                            ->setTransactionId($transactionReceiveModel->getId())
                            ->setProductId($productId)
                            ->setProductSku($product->getSku())
                            ->setProductName($product->getName())
                            ->setQty($qty)
                            ->save()
                    ;
                    $totalQty += $qty;
                }
                $transactionReceiveModel->setTotalProducts($totalQty)->save();
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        //end create transaction
        if (count($supplierReturn)) {
            foreach ($supplierReturn as $supplierReId => $itemData) {
                Mage::helper('inventorydropship')->sendEmailRefundToSupplier($supplierReId, $itemData);
            }
        }
    }

    public function salesOrderPlaceAfter($observer) {
        $order = $observer->getOrder();
        $items = $order->getAllVisibleItems();
        $warehouseIds = null;
        $selectWarehouse = Mage::getStoreConfig('inventory/general/select_warehouse');
        $ShippingAddress = $order->getShippingAddress();
        foreach ($items as $item) {

            $warehouseId = Mage::helper('inventory')->getQtyProductWarehouse($item->getProductId(), $selectWarehouse, $ShippingAddress);
            $warehouseProduct = Mage::getModel('inventory/warehouseproduct')->getCollection()
                    ->addFieldToFilter('warehouse_id', $warehouseId)
                    ->addFieldToFilter('product_id', $item->getProductId())
                    ->getFirstItem();
            $currentQty = $warehouseProduct->getQtyAvailable() - $item->getQtyOrdered();
            $warehouseProduct->setQtyAvailable($currentQty)
                    ->save();
            Mage::getModel('inventory/warehouseorder')->setOrderId($order->getId())
                    ->setWarehouseId($warehouseId)
                    ->setProductId($item->getProductId())
                    ->setQty($item->getQtyOrdered())
                    ->save();
        }
    }

}