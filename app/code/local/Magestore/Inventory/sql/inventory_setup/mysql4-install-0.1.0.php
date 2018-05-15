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

$resource = Mage::getSingleton('core/resource');
$readConnection = $resource->getConnection('core_read');
$sql = "SELECT * FROM `".$resource->getTableName('core_resource')."` WHERE (`code` = 'standardinventory_setup')";
$results = $readConnection->fetchAll($sql);
$breakSql = 0;
foreach($results as $result){
    $version = $result['version'];
    $dataVersion = $result['data_version'];
    if($dataVersion >= '0.1.0'){
        $breakSql = 1;
    }
}
    
$installer->startSetup();

if($breakSql == 0):    

    $installer->run("

            DROP TABLE IF EXISTS {$this->getTable('erp_inventory_warehouse')};
            CREATE TABLE {$this->getTable('erp_inventory_warehouse')} (
                `warehouse_id` int(11) unsigned NOT NULL auto_increment,
                `created_by` int(11) unsigned NOT NULL default '0',
                `name` varchar(255) NOT NULL,
                `manager_name` varchar(255) NOT NULL,
                `manager_email` varchar(255) default NULL,
                `telephone` varchar(50) default NULL,
                `street` text,
                `city` varchar(255) default NULL,
                `country_id` char(3) default '',
                `state` varchar(255) default NULL,
                `state_id` int(11) NULL,
                `postcode` varchar(255) default NULL,
                `latitude` varchar(255) default NULL,
                `longtitude` varchar(255) default NULL,
                `total_purchase` int(11) default '0',
                `status` tinyint(1) NOT NULL,
                `is_unwarehouse` tinyint(1) NOT NULL default '0',
                PRIMARY KEY  (`warehouse_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


            DROP TABLE IF EXISTS {$this->getTable('erp_inventory_warehouse_product')};
            CREATE TABLE {$this->getTable('erp_inventory_warehouse_product')} (
                `warehouse_product_id` int(11) unsigned NOT NULL auto_increment,
                `warehouse_id` int(11) unsigned NOT NULL,
                `product_id` int(11) unsigned NOT NULL,
                `qty` decimal(10,0) default '0',
                PRIMARY KEY  (`warehouse_product_id`),
                UNIQUE KEY `warehouse_id` (`warehouse_id`,`product_id`),
                INDEX (`warehouse_id`),
                INDEX (`product_id`),
                FOREIGN KEY (`warehouse_id`) REFERENCES {$this->getTable('erp_inventory_warehouse')}(`warehouse_id`) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (`product_id`) REFERENCES {$this->getTable('catalog_product_entity')}(`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

            DROP TABLE IF EXISTS {$this->getTable('erp_inventory_warehouse_assignment')};		
            CREATE TABLE {$this->getTable('erp_inventory_warehouse_assignment')}(
                `assignment_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `warehouse_id` int(11) unsigned NOT NULL,
                `admin_id` int(11) unsigned NOT NULL,
                `can_edit_warehouse` tinyint(1) NOT NULL,
                `can_transfer` tinyint(1) NOT NULL,
                `can_adjust` tinyint(1) NOT NULL,
                `can_edit_qty` tinyint(1) NOT NULL,
                INDEX (`warehouse_id`),
                INDEX (`admin_id`),
                PRIMARY KEY(`assignment_id`),
                FOREIGN KEY (`warehouse_id`) REFERENCES {$this->getTable('erp_inventory_warehouse')}(`warehouse_id`) ON DELETE CASCADE ON UPDATE CASCADE
            )ENGINE=InnoDB DEFAULT CHARSET=utf8;


            DROP TABLE IF EXISTS {$this->getTable('erp_inventory_supplier')};
            CREATE TABLE {$this->getTable('erp_inventory_supplier')} (
                `supplier_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL default '',
                `contact_name` varchar(255),
                `email` varchar(255) NOT NULL default '',
                `telephone` varchar(50) NOT NULL default '',
                `fax` varchar(50) default '',
                `street` text NOT NULL default '',
                `city` varchar(255) NOT NULL default '',
                `country_id` char(3) NOT NULL default '',
                `state` varchar(255) NOT NULL default '',
                `postcode` varchar(255) NOT NULL default '',
                `description` text default '',
                `website` varchar(255) default '',
                `status` tinyint(1) NOT NULL default '1',
                `total_order` decimal(10,0) NOT NULL default '0',
                `purchase_order` decimal(12,4) NOT NULL default '0',
                `return_order` decimal(12,4) NOT NULL default '0',
                `last_purchase_order` date default NULL,
                PRIMARY KEY(`supplier_id`)
            )ENGINE=InnoDB DEFAULT CHARSET=utf8;

            DROP TABLE IF EXISTS {$this->getTable('erp_inventory_supplier_product')};		
            CREATE TABLE {$this->getTable('erp_inventory_supplier_product')}(
                `supplier_product_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `supplier_id` int(11) unsigned NOT NULL,
                `product_id` int(11) unsigned NOT NULL,
                `cost` decimal(12,4) unsigned NOT NULL default '0.0000',
                `discount` float unsigned NOT NULL default '0.0000',
                `tax` float unsigned NOT NULL default '0.0000',
                INDEX (`supplier_id`),
                INDEX (`product_id`),
                PRIMARY KEY(`supplier_product_id`),
                FOREIGN KEY (`supplier_id`) REFERENCES {$this->getTable('erp_inventory_supplier')}(`supplier_id`) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (`product_id`) REFERENCES {$this->getTable('catalog_product_entity')}(`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
            )ENGINE=InnoDB DEFAULT CHARSET=utf8;


            DROP TABLE IF EXISTS {$this->getTable('erp_inventory_purchase_order')};
            CREATE TABLE {$this->getTable('erp_inventory_purchase_order')} (
                `purchase_order_id` int(11) unsigned NOT NULL auto_increment,
                `purchase_on` DATETIME default NULL,
                `bill_name` varchar(255) default NULL,
                `warehouse_id` varchar(255) DEFAULT '0',
                `warehouse_name` varchar(255) default '',
                `supplier_id` int(11) unsigned NOT NULL,
                `supplier_name` varchar(255) default '',
                `total_products` decimal(10,0) default '0',
                `total_amount` decimal(12,4) default '0',
                `comments` text,
                `currency` varchar(255) default NULL,
                `tax_rate` float default '0',
                `shipping_cost` float default '0',
                `delivery_process` float default '0',
                `status` tinyint(1) NOT NULL default '1',
                `paid` decimal(12,4) default '0',
                `total_products_recieved` decimal(10,0) default '0',
                PRIMARY KEY  (`purchase_order_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


            DROP TABLE IF EXISTS {$this->getTable('erp_inventory_purchase_order_product')};
            CREATE TABLE {$this->getTable('erp_inventory_purchase_order_product')} (
                `purchase_order_product_id` int(11) unsigned NOT NULL auto_increment,
                `product_id` int(11) unsigned default NULL,
                `product_name` varchar(255) default '',
                `product_sku` varchar(255) default '',
                `purchase_order_id` int(11) unsigned NOT NULL,
                `qty` decimal(10,0) default '0',
                `qty_recieved` decimal(10,0) default '0',
                `cost` decimal(12,4) unsigned NOT NULL default '0.0000',
                `discount` float unsigned NOT NULL default '0.0000',
                `tax` float unsigned NOT NULL default '0.0000',
                `qty_returned` decimal(10,0) default '0',
                PRIMARY KEY(`purchase_order_product_id`),
                INDEX(`purchase_order_id`),
                FOREIGN KEY (`purchase_order_id`) REFERENCES {$this->getTable('erp_inventory_purchase_order')}(`purchase_order_id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


            DROP TABLE IF EXISTS {$this->getTable('erp_inventory_delivery')};
            CREATE TABLE {$this->getTable('erp_inventory_delivery')} (
                `delivery_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `delivery_date` datetime,
                `qty_delivery` decimal(10,0) unsigned NOT NULL default '0',
                `purchase_order_id` int(11) unsigned NOT NULL,
                `product_id` int(11) unsigned NOT NULL,
                `product_name` varchar(255) NOT NULL,
                `product_sku` varchar(255) NOT NULL,
                `sametime` varchar(255) default '',
                PRIMARY KEY(`delivery_id`),
                FOREIGN KEY (`purchase_order_id`) REFERENCES {$this->getTable('erp_inventory_purchase_order')}(`purchase_order_id`) ON DELETE CASCADE ON UPDATE CASCADE
            )ENGINE=InnoDB DEFAULT CHARSET=utf8;


            DROP TABLE IF EXISTS {$this->getTable('erp_inventory_returned_order')};
            CREATE TABLE {$this->getTable('erp_inventory_returned_order')} (
                `returned_order_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `purchase_order_id` int(11) unsigned NOT NULL,
                `total_products` decimal(10,0) unsigned NOT NULL default '0',
                `total_amount` decimal(12,4) unsigned NOT NULL default '0.0000',
                `returned_on` datetime,
                `status` tinyint(1) NOT NULL default '1',
                `paid` decimal(12,4) default '0',
                `supplier_id` int(11) unsigned NOT NULL default '0',
                PRIMARY KEY(`returned_order_id`),
                INDEX(`purchase_order_id`),
                FOREIGN KEY (`purchase_order_id`) REFERENCES {$this->getTable('erp_inventory_purchase_order')}(`purchase_order_id`) ON DELETE CASCADE ON UPDATE CASCADE
            )ENGINE=InnoDB DEFAULT CHARSET=utf8;


            DROP TABLE IF EXISTS {$this->getTable('erp_inventory_returned_order_product')};
            CREATE TABLE {$this->getTable('erp_inventory_returned_order_product')} (
                `returned_order_product_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `returned_order_id` int(11) unsigned NOT NULL,
                `qty_return` decimal(10,0) unsigned NOT NULL default '0',
                `product_id` int(11) unsigned NOT NULL,
                `product_name` varchar(255) default '',
                `product_sku` varchar(255) default '',
                PRIMARY KEY(`returned_order_product_id`),
                INDEX (`returned_order_id`),
                FOREIGN KEY (`returned_order_id`) REFERENCES {$this->getTable('erp_inventory_returned_order')}(`returned_order_id`) ON DELETE CASCADE ON UPDATE CASCADE
            )ENGINE=InnoDB DEFAULT CHARSET=utf8;


            DROP TABLE IF EXISTS {$this->getTable('erp_inventory_stock_issuing')};
            CREATE TABLE {$this->getTable('erp_inventory_stock_issuing')} (
                `stock_issuing_id` int(11) unsigned NOT NULL auto_increment,
                `type` smallint(2) default NULL,
                `reference_id` int(11) NOT NULL,
                `warehouse_id_from` int(11) default NULL,
                `warehouse_id_to` int(11) default NULL,
                `warehouse_from_name` varchar(255) default '',
                `warehouse_to_name` varchar(255) default '',
                `total_products` decimal(10,0) default NULL,
                `created_at` DATE default NULL,
                `status` tinyint(2) NOT NULL default '1',
                `reference_invoice_number` varchar(255) NOT NULL default '',
                `comment` varchar(255) default '',
                PRIMARY KEY  (`stock_issuing_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


            DROP TABLE IF EXISTS {$this->getTable('erp_inventory_stock_issuing_product')};
            CREATE TABLE {$this->getTable('erp_inventory_stock_issuing_product')} (
                `stock_issuing_product_id` int(11) unsigned NOT NULL auto_increment,
                `stock_issuing_id` int(11) unsigned NOT NULL,
                `product_id` int(11) unsigned NOT NULL,
                `product_name` varchar(255) NOT NULL,
                `product_sku` varchar(255) NOT NULL,
                `qty` decimal(10,0) default NULL,
                PRIMARY KEY  (`stock_issuing_product_id`),
                INDEX(`stock_issuing_id`),
                FOREIGN KEY (`stock_issuing_id`) REFERENCES {$this->getTable('erp_inventory_stock_issuing')}(`stock_issuing_id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


            DROP TABLE IF EXISTS {$this->getTable('erp_inventory_stock_receiving')};
            CREATE TABLE {$this->getTable('erp_inventory_stock_receiving')} (
                `stock_receiving_id` int(11) unsigned NOT NULL auto_increment,
                `type` smallint(2) default NULL,
                `reference_id` int(11) NOT NULL,
                `warehouse_id_from` int(11) default NULL,
                `warehouse_id_to` int(11) default NULL,
                `warehouse_from_name` varchar(255) default '',
                `warehouse_to_name` varchar(255) default '',
                `total_products` decimal(10,0) default NULL,
                `created_at` DATE default NULL,
                `status` tinyint(2) NOT NULL default '1',
                `comment` varchar(255) default '',
                `reference_invoice_number` varchar(255) NOT NULL default '',
                PRIMARY KEY  (`stock_receiving_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


            DROP TABLE IF EXISTS {$this->getTable('erp_inventory_stock_receiving_product')};
            CREATE TABLE {$this->getTable('erp_inventory_stock_receiving_product')} (
                `stock_receiving_product_id` int(11) unsigned NOT NULL auto_increment,
                `stock_receiving_id` int(11) unsigned NOT NULL,
                `product_id` int(11) unsigned NOT NULL,
                `product_name` varchar(255) NOT NULL,
                `product_sku` varchar(255) NOT NULL,
                `qty` decimal(10,0) default NULL,
                PRIMARY KEY  (`stock_receiving_product_id`),
                INDEX(`stock_receiving_id`),
                FOREIGN KEY (`stock_receiving_id`) REFERENCES {$this->getTable('erp_inventory_stock_receiving')}(`stock_receiving_id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


            DROP TABLE IF EXISTS {$this->getTable('erp_inventory_transfer_stock')};
            CREATE TABLE {$this->getTable('erp_inventory_transfer_stock')} (
                `transfer_stock_id` int(11) unsigned NOT NULL auto_increment,
                `warehouse_from_id` int(11) unsigned default NULL,
                `warehouse_from_name` varchar(255) default '',
                `warehouse_to_id` int(11) unsigned default NULL,
                `warehouse_to_name` varchar(255) default '',
                `total_products` decimal(10,0) default '0',
                `create_at` date default NULL,
                `status` tinyint(2) NOT NULL default '1',
                `type` tinyint(1) NOT NULL default '1',
                PRIMARY KEY  (`transfer_stock_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


            DROP TABLE IF EXISTS {$this->getTable('erp_inventory_transfer_stock_product')};
            CREATE TABLE {$this->getTable('erp_inventory_transfer_stock_product')} (
                `tranfer_stock_product_id` int(11) unsigned NOT NULL auto_increment,
                `product_id` int(11) unsigned default NULL,
                `product_name` varchar(255) default '',
                `product_sku` varchar(255) default '',
                `transfer_stock_id` int(11) unsigned default NULL,
                `qty_transfer` decimal(10,0) default '0',
                `qty_request` decimal(10,0) default '0',
                `qty_receive` int(11) NOT NULL,
                PRIMARY KEY  (`tranfer_stock_product_id`),
                INDEX(`transfer_stock_id`),
                FOREIGN KEY (`transfer_stock_id`) REFERENCES {$this->getTable('erp_inventory_transfer_stock')}(`transfer_stock_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


            DROP TABLE IF EXISTS {$this->getTable('erp_inventory_products')};		
            CREATE TABLE {$this->getTable('erp_inventory_products')} (
                `inventory_product_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `product_id` int(11) unsigned NOT NULL,
                `cost_price` decimal(12,4) unsigned NOT NULL default '0.0000',
                `last_update` datetime default NULL,
                INDEX (`product_id`),
                UNIQUE (`product_id`),
                PRIMARY KEY(`inventory_product_id`),
                FOREIGN KEY (`product_id`) REFERENCES {$this->getTable('catalog_product_entity')}(`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
            )ENGINE=InnoDB DEFAULT CHARSET=utf8;

            DROP TABLE IF EXISTS {$this->getTable('erp_inventory_shipment')};
            CREATE TABLE {$this->getTable('erp_inventory_shipment')} (
                `inventory_shipment_id` int(11) unsigned NOT NULL auto_increment,
                `item_id` int(11) unsigned  NOT NULL,
                `product_id` int(11) unsigned  NOT NULL,
                `order_id` int(11) unsigned  NOT NULL,
                `warehouse_id` int(11) unsigned  NOT NULL,
                `warehouse_name` varchar(255) NOT NULL,
                `shipment_id` int(11) unsigned  NOT NULL,
                `item_refuned` int(11) NOT NULL default '0',
                `item_shiped` int(11) NOT NULL default '0',
                PRIMARY KEY  (`inventory_shipment_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

            DROP TABLE IF EXISTS {$this->getTable('erp_inventory_shipment_transfer')};
            CREATE TABLE {$this->getTable('erp_inventory_shipment_transfer')} (
                `shipment_transfer_id` int(11) unsigned NOT NULL auto_increment,
                `item_id` int(11) unsigned  NOT NULL,
                `product_id` int(11) unsigned  NOT NULL,
                `order_id` int(11) unsigned  NOT NULL,
                `warehouse_id` int(11) unsigned  NOT NULL,
                `warehouse_name` varchar(255) NOT NULL,
                `qty_need_transfer` int(11) default '0',
                `transfer_stock_id` int(11) unsigned  NOT NULL,
                `status` tinyint(1) NOT NULL,
                PRIMARY KEY  (`shipment_transfer_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

            DROP TABLE IF EXISTS {$this->getTable('erp_inventory_adjuststock')};
            CREATE TABLE {$this->getTable('erp_inventory_adjuststock')} (
                `adjuststock_id` int(11) unsigned NOT NULL auto_increment,
                `warehouse_id` int(11) unsigned NOT NULL,
                `warehouse_name` varchar(255) NOT NULL,
                `file_path` varchar(255) NOT NULL,
                `created_at` date,
                PRIMARY KEY  (`adjuststock_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

            DROP TABLE IF EXISTS {$this->getTable('erp_inventory_adjuststock_product')};
            CREATE TABLE {$this->getTable('erp_inventory_adjuststock_product')} (
                `adjuststockproduct_id` int(11) unsigned NOT NULL auto_increment,
                `adjuststock_id` int(11) unsigned  NOT NULL,
                `product_id` int(11) unsigned  NOT NULL,
                `old_qty` decimal(10,0) default '0',
                `adjust_qty` decimal(10,0) default '0',
                `updated_qty` decimal(10,0) default '0',
                PRIMARY KEY  (`adjuststockproduct_id`),
                INDEX(`adjuststock_id`),
                FOREIGN KEY (`adjuststock_id`) REFERENCES {$this->getTable('erp_inventory_adjuststock')}(`adjuststock_id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

            DROP TABLE IF EXISTS {$this->getTable('erp_inventory_purchase_order_product_warehouse')};
            CREATE TABLE {$this->getTable('erp_inventory_purchase_order_product_warehouse')} (
                `purchase_order_product_warehouse_id` int(11) unsigned NOT NULL auto_increment,
                `purchase_order_id` int(11) unsigned NOT NULL,
                `product_id` int(11) unsigned NOT NULL,
                `product_name` varchar(255) default '',
                `product_sku` varchar(255) default '',
                `warehouse_id` int(11) unsigned NOT NULL,
                `warehouse_name` varchar(255) default '',
                `qty_order` decimal(10,0) default '0',
                `qty_received` decimal(10,0) default '0',
                `qty_returned` decimal(10,0) default '0',
                PRIMARY KEY  (`purchase_order_product_warehouse_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

            DROP TABLE IF EXISTS {$this->getTable('erp_inventory_delivery_warehouse')};
            CREATE TABLE {$this->getTable('erp_inventory_delivery_warehouse')} (
                `delivery_warehouse_id` int(11) unsigned NOT NULL auto_increment,
                `delivery_date` datetime,
                `purchase_order_id` int(11) unsigned NOT NULL,
                `product_id` int(11) unsigned NOT NULL,
                `product_name` varchar(255) default '',
                `product_sku` varchar(255) default '',
                `warehouse_id` int(11) unsigned NOT NULL,
                `warehouse_name` varchar(255) default '',
                `qty_delivery` decimal(10,0) default '0',
                `sametime` varchar(255) default '',
                PRIMARY KEY  (`delivery_warehouse_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

            DROP TABLE IF EXISTS {$this->getTable('erp_inventory_return_product_warehouse')};
            CREATE TABLE {$this->getTable('erp_inventory_return_product_warehouse')} (
                `return_product_warehouse_id` int(11) unsigned NOT NULL auto_increment,
                `returned_on` datetime,
                `purchase_order_id` int(11) unsigned NOT NULL,
                `product_id` int(11) unsigned NOT NULL,
                `product_name` varchar(255) default '',
                `product_sku` varchar(255) default '',
                `warehouse_id` int(11) unsigned NOT NULL,
                `warehouse_name` varchar(255) default '',
                `qty_return` decimal(10,0) default '0',
                PRIMARY KEY  (`return_product_warehouse_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

            DROP TABLE IF EXISTS {$this->getTable('erp_inventory_notice')};
            CREATE TABLE {$this->getTable('erp_inventory_notice')} (
                `notice_id` int(11) unsigned NOT NULL auto_increment,
                `notice_date` date,
                `description` TEXT default '',
                `comment` TEXT default '',
                `status` tinyint(1) NOT NULL default '0',
                PRIMARY KEY  (`notice_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

            DROP TABLE IF EXISTS {$this->getTable('erp_inventory_report_products_received')};		
            CREATE TABLE {$this->getTable('erp_inventory_report_products_received')} (
                `inventory_report_pr_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `product_id` int(11) unsigned NOT NULL,
                `product_name` varchar(255) NOT NULL,
                `product_sku` varchar(255) default '',
                `amount_received` decimal(12,4) unsigned NOT NULL default '0.0000',
                `qty_received` decimal(10,0) default '0',
                `received_at` datetime default NULL,
                `received_type` smallint(2) default NULL,
                PRIMARY KEY(`inventory_report_pr_id`)
            )ENGINE=InnoDB DEFAULT CHARSET=utf8;

            DROP TABLE IF EXISTS {$this->getTable('erp_inventory_report_products_moved')};		
            CREATE TABLE {$this->getTable('erp_inventory_report_products_moved')} (
                `inventory_report_pm_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `product_id` int(11) unsigned NOT NULL,
                `product_name` varchar(255) NOT NULL,
                `product_sku` varchar(255) default '',
                `amount_moved` decimal(12,4) unsigned NOT NULL default '0.0000',
                `qty_moved` decimal(10,0) default '0',
                `moved_at` datetime default NULL,
                `moved_type` smallint(2) default NULL,
                PRIMARY KEY(`inventory_report_pm_id`)
            )ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");
endif;
/* create unWarehouse, add all products to unWarehouse
 * add product to inventory product
 */
$resource = Mage::getSingleton('core/resource');
$writeConnection = $resource->getConnection('core_write');

if($breakSql == 0){
    $readConnection = $resource->getConnection('core_read');
    $warehouse = Mage::getModel('inventory/warehouse');
    $warehouse->setData(
        array(
            'name' => 'unWarehouse',
            'is_unwarehouse' => 1
        )
    );
    $warehouse->setId(null)
        ->setStatus(1)
        ->save();
}

$warehouse = Mage::getModel('inventory/warehouse')->getCollection()
                        ->addFieldToFilter('is_unwarehouse',1)
                        ->getFirstItem();

$admins = Mage::getModel('admin/user')->getCollection()->getAllIds();
foreach ($admins as $adminId) {
    $assignment = Mage::getModel('inventory/assignment')->loadByWarehouseAndAdmin($warehouse->getId(), $adminId);
    $assignment->setWarehouseId($warehouse->getId());
    $assignment->setAdminId($adminId);
    $assignment->setData('can_edit_warehouse', 0);
    $assignment->setData('can_adjust', 0);
    $assignment->setData('can_transfer', 1);
    $assignment->setId(null)
        ->save();
}

if($breakSql == 0){
    $collection = Mage::getModel('catalog/product')->getCollection()
        ->addAttributeToSelect('*');
    $collection->joinField('qty', 'cataloginventory/stock_item', 'qty', 'product_id=entity_id', '{{table}}.stock_id=1', 'left');
    for ($i = 1; $i <= ceil($collection->getSize() / 500); $i++) {

        $col = clone $collection;
        $col->setPageSize(500)->setCurPage($i);
        $data = array();
        $data1 = array();
        foreach ($col as $product) {
            $qty_not_shipped = array();
            $product_id = $product->getId();
            $orderItems = $readConnection->fetchAll("SELECT `product_type`,`qty_ordered`,`qty_canceled`,`qty_shipped`, `parent_item_id` FROM `" . $resource->getTableName('sales/order_item') . "` WHERE (product_id = $product_id)");
            foreach ($orderItems as $orderItem) {
                if($orderItem['product_type'] == 'configurable') continue;
                $qty_ordered = $orderItem['qty_ordered'];
                $qty_canceled = $orderItem['qty_canceled'];
                $qty_shipped = $orderItem['qty_shipped'];
                if($orderItem['parent_item_id']){
                    if($qty_shipped == 0){
                        $parent_item_id = $orderItem['parent_item_id'];
                        $order_parent_items = $readConnection->fetchAll("SELECT `qty_shipped`,`product_type` FROM `" . $installer->getTable('sales/order_item') . "` WHERE (item_id = $parent_item_id)");                    
                        foreach($order_parent_items as $p){
                            if($p['product_type'] == 'configurable'){
                                $qty_shipped = $p['qty_shipped'];
                            }
                        }
                    }
                }
                $qty_not_ship = $qty_ordered - $qty_canceled - $qty_shipped;
                $qty_not_shipped[] = $qty_not_ship;
            }
            $qty_for_unwarehouse = (int) array_sum($qty_not_shipped) + (int) $product->getQty();
            $data[] = array(
                'product_id' => $product->getId(),
                'warehouse_id' => $warehouse->getId(),
                'qty' => $qty_for_unwarehouse
            );
            $data1[] = array(
                'product_id' => $product->getId(),
                'last_update' => now(),
                'cost_price' => $product->getCost()
            );
        }
        $writeConnection->insertMultiple($installer->getTable('inventory/warehouseproduct'), $data);
        $writeConnection->insertMultiple($installer->getTable('inventory/inventory'), $data1);
    }
}
/* update shipping progesss
 * 
 */

//Check exists column shipping_progress
$result = $readConnection->fetchAll("SHOW COLUMNS FROM " . $resource->getTableName('sales/order') . " LIKE 'shipping_progress'");
if (count($result) == 0) {
    $writeConnection->query("ALTER TABLE " . $resource->getTableName('sales/order') . " ADD COLUMN `shipping_progress` TINYINT(2) NULL DEFAULT '0';");
}

$orders = $readConnection->fetchAll("SELECT `total_qty_ordered`,`entity_id`,`status` FROM `" . $installer->getTable('sales/order') . "`");
foreach ($orders as $order) {
    $orderId = $order['entity_id'];
    if ($order['status'] == 'complete') {
        $shipping_progress = 2;
    } else {
        $total_qty_order = $order['total_qty_ordered'];
        $total_qty_shipped = array();       
        $order_items = $readConnection->fetchAll("SELECT `qty_shipped`,`qty_ordered`,`product_type`,`parent_item_id` FROM `" . $installer->getTable('sales/order_item') . "` WHERE (order_id = $orderId)");
        foreach ($order_items as $c) {
            if ($c['parent_item_id'] == null) {
                if ($c['product_type'] == 'virtual' || $c['product_type'] == 'downloadable') {
                    $total_qty_order += -(int) $c['qty_ordered'];
                }
                $total_qty_shipped[] = $c['qty_shipped'];
            }
        }
        $total_products_shipped = array_sum($total_qty_shipped);
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
    }
    $update_sql = 'UPDATE ' . $installer->getTable('sales/order') . ' 
                            SET `shipping_progress` = \'' . $shipping_progress . '\'
                                 WHERE `entity_id` =' . $orderId . ';';
    $writeConnection->query($update_sql);
}

$installer->endSetup();