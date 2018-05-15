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
/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("
    DROP TABLE IF EXISTS {$this->getTable('erp_inventory_send_stock')};
    CREATE TABLE {$this->getTable('erp_inventory_send_stock')} (
        `send_stock_id` int(11) unsigned NOT NULL auto_increment,        
        `from_id` int(11) unsigned default NULL,
        `from_name` varchar(255) default '',
        `to_id` int(11) unsigned default NULL,
        `to_name` varchar(255) default '',
        `total_products` decimal(10,0) default '0',
        `created_at` date default NULL,
        `created_by` varchar(255) default '',
        `status` tinyint(2) NOT NULL default '1',
        `reason` text default '',
        PRIMARY KEY  (`send_stock_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    
    DROP TABLE IF EXISTS {$this->getTable('erp_inventory_send_stock_product')};
    CREATE TABLE {$this->getTable('erp_inventory_send_stock_product')} (
        `send_stock_product_id` int(11) unsigned NOT NULL auto_increment,
        `send_stock_id` int(11) unsigned default NULL,
        `product_id` int(11) unsigned default NULL,
        `product_sku` varchar(255) default '',
        `product_name` varchar(255) default '',
        `qty` decimal(10,0) default '0',        
        PRIMARY KEY  (`send_stock_product_id`),
        INDEX(`send_stock_id`),
        FOREIGN KEY (`send_stock_id`) REFERENCES {$this->getTable('erp_inventory_send_stock')}(`send_stock_id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    
    DROP TABLE IF EXISTS {$this->getTable('erp_inventory_request_stock')};
    CREATE TABLE {$this->getTable('erp_inventory_request_stock')} (
        `request_stock_id` int(11) unsigned NOT NULL auto_increment,        
        `from_id` int(11) unsigned default NULL,
        `from_name` varchar(255) default '',
        `to_id` int(11) unsigned default NULL,
        `to_name` varchar(255) default '',
        `total_products` decimal(10,0) default '0',
        `created_at` date default NULL,
        `created_by` varchar(255) default '',
        `status` tinyint(2) NOT NULL default '1',
        `reason` text default '',
        PRIMARY KEY  (`request_stock_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    
    DROP TABLE IF EXISTS {$this->getTable('erp_inventory_request_stock_product')};
    CREATE TABLE {$this->getTable('erp_inventory_request_stock_product')} (
        `request_stock_product_id` int(11) unsigned NOT NULL auto_increment,
        `request_stock_id` int(11) unsigned default NULL,
        `product_id` int(11) unsigned default NULL,
        `product_sku` varchar(255) default '',
        `product_name` varchar(255) default '',
        `qty` decimal(10,0) default '0',        
        PRIMARY KEY  (`request_stock_product_id`),
        INDEX(`request_stock_id`),
        FOREIGN KEY (`request_stock_id`) REFERENCES {$this->getTable('erp_inventory_request_stock   ')}(`request_stock_id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    
    DROP TABLE IF EXISTS {$this->getTable('erp_inventory_transaction')};
    CREATE TABLE {$this->getTable('erp_inventory_transaction')} (
        `transaction_id` int(11) unsigned NOT NULL auto_increment,
        `send_stock_id` int(11) unsigned default NULL,
        `request_stock_id` int(11) unsigned default NULL,
        `type` tinyint(1) NOT NULL default '1',
        `from_id` int(11) unsigned default NULL,
        `from_name` varchar(255) default '',
        `to_id` int(11) unsigned default NULL,
        `to_name` varchar(255) default '',
        `total_products` decimal(10,0) default '0',
        `created_at` date default NULL,
        `created_by` varchar(255) default '',
        `reason` text default '',
        PRIMARY KEY  (`transaction_id`),
        INDEX (`send_stock_id`),
        INDEX (`request_stock_id`),
        FOREIGN KEY (`send_stock_id`) REFERENCES {$this->getTable('erp_inventory_send_stock')}(`send_stock_id`) ON DELETE CASCADE ON UPDATE CASCADE,
        FOREIGN KEY (`request_stock_id`) REFERENCES {$this->getTable('erp_inventory_request_stock')}(`request_stock_id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    DROP TABLE IF EXISTS {$this->getTable('erp_inventory_transaction_product')};
    CREATE TABLE {$this->getTable('erp_inventory_transaction_product')} (
        `transaction_product_id` int(11) unsigned NOT NULL auto_increment,
        `transaction_id` int(11) unsigned default NULL,
        `product_id` int(11) unsigned default NULL,
        `product_sku` varchar(255) default '',
        `product_name` varchar(255) default '',
        `qty` decimal(10,0) default '0',        
        PRIMARY KEY  (`transaction_product_id`),
        INDEX(`transaction_id`),
        FOREIGN KEY (`transaction_id`) REFERENCES {$this->getTable('erp_inventory_transaction')}(`transaction_id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	
    ALTER TABLE {$this->getTable('erp_inventory_purchase_order')}	
	ADD COLUMN `change_rate` varchar(255) NOT NULL default '1' AFTER `currency`;
    
    ALTER TABLE {$this->getTable('erp_inventory_warehouse_product')}
        ADD COLUMN `qty_available` decimal(10,0) default '0',
        ADD INDEX (`qty_available`);
    
    DROP TABLE IF EXISTS {$this->getTable('erp_inventory_warehouse_order')};
    CREATE TABLE {$this->getTable('erp_inventory_warehouse_order')} (
        `warehouse_order_id` int(11) unsigned NOT NULL auto_increment,
        `order_id` int(11) unsigned NOT NULL,
        `warehouse_id` int(11) unsigned NOT NULL,
        `product_id` int(11) unsigned NOT NULL,
        `qty` decimal(10,0) default '0',
        PRIMARY KEY  (`warehouse_order_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    
    ALTER TABLE {$this->getTable('erp_inventory_transaction')} AUTO_INCREMENT = 1;
");

$installer->endSetup();

/* default currency */
$purchaseOrders = Mage::getModel('inventory/purchaseorder')
        ->getCollection();
$baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
foreach ($purchaseOrders as $purchaseOrder) {
    $purchaseOrder->setCurrency($baseCurrencyCode)
            ->setChangeRate('1')
            ->save();
}

/* add qty available */
$warehouseProducts = Mage::getModel('inventory/warehouseproduct')
        ->getCollection();
foreach ($warehouseProducts as $warehouseProduct) {
    $warehouseProduct->setQtyAvailable($warehouseProduct->getQty())
            ->save();
}


/* need code to update from old database to new database */
$admin = '';
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
    $transactionData = array();
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
    $transactionModel->addData($transactionData)->save();

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