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
class Magestore_Inventory_Adminhtml_InventoryController extends Mage_Adminhtml_Controller_Action {

    /**
     * init layout and set active for current menu
     *
     * @return Magestore_Inventory_Adminhtml_InventoryController
     */
    protected function _initAction() {
        $this->loadLayout()
            ->_setActiveMenu('inventory/inventory')
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
     * Grid action
     */
    public function gridAction() {
        $this->_initAction()
            ->renderLayout();
    }

    /**
     * Update cost price action
     */
    public function updateAction() {
        $type = Mage::getStoreConfig('inventory/calculate/cost_type');
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $writeConnection = $resource->getConnection('core_write');
        $installer = Mage::getModel('core/resource_setup');
        try {
            $purchase_order_product_ids = array();
            $purchase_product = $readConnection->fetchAll("SELECT * FROM `" . $installer->getTable('inventory/purchaseorderproduct') . "`");
            foreach ($purchase_product as $p) {
                $purchase_order_product_ids[] = $p['product_id'];
            }
            $purchase_order_product_id = array_unique($purchase_order_product_ids);
            $product_ids = implode("','", $purchase_order_product_id);
            $inventory_product = $readConnection->fetchAll("SELECT * FROM `" . $installer->getTable('inventory/inventory') . "` WHERE product_id IN ('$product_ids')");
            foreach ($inventory_product as $product) {
                $product_id = $product['product_id'];
                $purchase_order_id = array();
                $total = 0;
                $qty_shipped = array();
                $purchase_amount = array();
                $qty_purchase = array();
                $order_items = $readConnection->fetchAll("SELECT * FROM `" . $installer->getTable('sales/order_item') . "` WHERE (product_id = $product_id)");
                foreach ($order_items as $oItem) {
                    array_push($qty_shipped, $oItem['qty_shipped']);
                }
                $total_shipped = array_sum($qty_shipped);
                $purchase_items = $readConnection->fetchAll("SELECT * FROM `" . $installer->getTable('inventory/purchaseorderproduct') . "` WHERE (product_id = $product_id)");
                if (count($purchase_items) > 0) {
                    //Type 1 => LIFO
                    if ($type == 1) {
                        foreach ($purchase_items as $pItem) {
                            array_push($purchase_order_id, $pItem['purchase_order_id']);
                        }
                        $purchase_order = Mage::getModel('inventory/purchaseorder')
                            ->getCollection()
                            ->addFieldToFilter('purchase_order_id', array('in' => $purchase_order_id))
                            ->addFieldToFilter('status', 6)
                            ->setOrder('purchase_on', 'desc');
                        if ($purchase_order->getSize()) {
                            foreach ($purchase_order as $pOrder) {
                                $pOrderId = $pOrder->getPurchaseOrderId();
                                $items = $readConnection->fetchAll("SELECT * FROM `" . $installer->getTable('inventory/purchaseorderproduct') . "` WHERE (product_id = $product_id) AND (purchase_order_id = $pOrderId)");
                                foreach ($items as $item) {
                                    $total+=((int) $item['qty_recieved'] - (int) $item['qty_returned']);
                                    if ($total > $total_shipped) {
                                        break;
                                    }
                                }
                                break;
                            }
                            $cost_price_collection = $readConnection->fetchAll("SELECT * FROM `" . $installer->getTable('inventory/purchaseorderproduct') . "` WHERE (product_id = $product_id) AND (purchase_order_id = $pOrderId)");
                            $cost_price = $cost_price_collection[0]['cost'];
                        } else {
                            $cost_price = Mage::getModel('catalog/product')
                                ->getCollection()
                                ->addAttributeToSelect('*')
                                ->addFieldToFilter('entity_id', $product_id)
                                ->getFirstItem()
                                ->getCost();
                        }
                    }
                    //Type 2 => FIFO
                    if ($type == 2) {
                        foreach ($purchase_items as $pItem) {
                            array_push($purchase_order_id, $pItem['purchase_order_id']);
                        }
                        $purchase_order = Mage::getModel('inventory/purchaseorder')
                            ->getCollection()
                            ->addFieldToFilter('purchase_order_id', array('in' => $purchase_order_id))
                            ->addFieldToFilter('status', 6)
                            ->setOrder('purchase_on', 'asc');
                        if ($purchase_order->getSize()) {
                            foreach ($purchase_order as $pOrder) {
                                $pOrderId = $pOrder->getPurchaseOrderId();
                                $items = $readConnection->fetchAll("SELECT * FROM `" . $installer->getTable('inventory/purchaseorderproduct') . "` WHERE (product_id = $product_id) AND (purchase_order_id = $pOrderId)");
                                foreach ($items as $item) {
                                    $total+=((int) $item['qty_recieved'] - (int) $item['qty_returned']);
                                    if ($total > $total_shipped) {
                                        break;
                                    }
                                }
                                break;
                            }
                            $cost_price_collection = $readConnection->fetchAll("SELECT * FROM `" . $installer->getTable('inventory/purchaseorderproduct') . "` WHERE (product_id = $product_id) AND (purchase_order_id = $pOrderId)");
                            $cost_price = $cost_price_collection[0]['cost'];
                        } else {
                            $cost_price = Mage::getModel('catalog/product')
                                ->getCollection()
                                ->addAttributeToSelect('*')
                                ->addFieldToFilter('entity_id', $product_id)
                                ->getFirstItem()
                                ->getCost();
                        }
                    }
                    //Type 3 =>AVG
                    if ($type == 3) {
                        foreach ($purchase_items as $pItem) {
                            $amount = (float) (((int) $pItem['qty_recieved'] - (int) $pItem['qty_return']) * $pItem['cost']);
                            array_push($purchase_amount, $amount);
                            array_push($qty_purchase, $pItem['qty_recieved']);
                        }
                        $total_purchase_amount = array_sum($purchase_amount);
                        $total_purchase = array_sum($qty_purchase);
                        $cost_price = $total_purchase_amount / $total_purchase;
                    }
                    $now = $this->_filterDates(now());
                    $update_cost_sql = 'UPDATE ' . $installer->getTable('inventory/inventory') . ' 
                                                                            SET `cost_price` = \'' . $cost_price . '\'
                                                                                    WHERE `product_id` =' . $product_id . ';';
                    $update_last_update = 'UPDATE ' . $installer->getTable('inventory/inventory') . ' 
                                                                            SET `last_update` = \'' . $now . '\'
                                                                                    WHERE `product_id` =' . $product_id . ';';
                    $writeConnection->query($update_cost_sql);
                    $writeConnection->query($update_last_update);
                } else {
                    $cost = Mage::getModel('catalog/product')
                        ->getCollection()
                        ->addAttributeToSelect('*')
                        ->addFieldToFilter('entity_id', $product_id)
                        ->getFirstItem()
                        ->getCost();
                    $update_cost = 'UPDATE ' . $installer->getTable('inventory/inventory') . ' 
                                                                SET `cost_price` = \'' . $cost . '\'
                                                                        WHERE `product_id` =' . $product_id . ';';
                    $update_last = 'UPDATE ' . $installer->getTable('inventory/inventory') . ' 
                                                                SET `last_update` = \'' . $now . '\'
                                                                        WHERE `product_id` =' . $product_id . ';';
                    $writeConnection->query($update_cost);
                    $writeConnection->query($update_last);
                }
            }
            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('inventory')->__('Stock was successfully updated.'));
            $this->_redirect('*/*/');
            return;
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_redirect('*/*/');
            return;
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('inventory')->__('Unable to update stock'));
        $this->_redirect('*/*/');
        return;
    }

    /**
     * view and edit item action
     */
    public function editAction() {
        $inventoryId = $this->getRequest()->getParam('id');
        $model = Mage::getModel('inventory/inventory')->load($inventoryId);

        if ($model->getId() || $inventoryId == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('inventory_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('inventory/inventory');

            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager')
            );
            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News')
            );

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('inventory/adminhtml_inventory_edit'))
                ->_addLeft($this->getLayout()->createBlock('inventory/adminhtml_inventory_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('inventory')->__('Item does not exist')
            );
            $this->_redirect('*/*/');
        }
    }

    public function viewAction() {
        if ($this->getRequest()->getParam('id')) {
            $this->loadLayout();
            $this->_setActiveMenu('inventory/inventory');
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('inventory/adminhtml_inventory_edit'))
                ->_addLeft($this->getLayout()->createBlock('inventory/adminhtml_inventory_edit_tabs'));
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('inventory')->__('Product does not exist')
            );
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {
        $this->_forward('edit');
    }

    /**
     * save item action
     */
    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
            if (isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
                try {
                    /* Starting upload */
                    $uploader = new Varien_File_Uploader('filename');

                    // Any extention would work
                    $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                    $uploader->setAllowRenameFiles(false);

                    // Set the file upload mode 
                    // false -> get the file directly in the specified folder
                    // true -> get the file in the product like folders 
                    //    (file.jpg will go in something like /media/f/i/file.jpg)
                    $uploader->setFilesDispersion(false);

                    // We set media as the upload dir
                    $path = Mage::getBaseDir('media') . DS;
                    $result = $uploader->save($path, $_FILES['filename']['name']);
                    $data['filename'] = $result['file'];
                } catch (Exception $e) {
                    $data['filename'] = $_FILES['filename']['name'];
                }
            }

            $model = Mage::getModel('inventory/inventory');
            $model->setData($data)
                ->setId($this->getRequest()->getParam('id'));

            try {
                if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
                    $model->setCreatedTime(now())
                        ->setUpdateTime(now());
                } else {
                    $model->setUpdateTime(now());
                }
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('inventory')->__('Item was successfully saved')
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
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('inventory')->__('Unable to find item to save')
        );
        $this->_redirect('*/*/');
    }

    /**
     * export grid item to CSV type
     */
    public function exportCsvAction() {
        $fileName = 'inventory.csv';
        $content = $this->getLayout()
            ->createBlock('inventory/adminhtml_inventory_grid')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction() {
        $fileName = 'inventory.xml';
        $content = $this->getLayout()
            ->createBlock('inventory/adminhtml_inventory_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('inventory');
    }

    /**
     * show grid purchase order for product
     */
    public function purchaseorderAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('inventory.inventory.edit.tab.purchaseorder');
        $this->renderLayout();
    }

    public function purchaseorderGridAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('inventory.inventory.edit.tab.purchaseorder');
        $this->renderLayout();
    }

    /**
     * show grid return order for product
     */
    public function returnorderAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('inventory.inventory.edit.tab.returnorder');
        $this->renderLayout();
    }

    public function returnorderGridAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('inventory.inventory.edit.tab.returnorder');
        $this->renderLayout();
    }

}