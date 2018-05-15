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

if($breakSql == 0):

        $installer->run("

	DROP TABLE IF EXISTS {$this->getTable('erp_inventory_shipping_method')};
	CREATE TABLE {$this->getTable('erp_inventory_shipping_method')} (
            `shipping_method_id` int(11) unsigned NOT NULL auto_increment,
            `name` varchar(255) NOT NULL,
            `description` text,
            `status` tinyint(1) NOT NULL,
            `create_by` varchar(255) NOT NULL,
            PRIMARY KEY  (`shipping_method_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	
        DROP TABLE IF EXISTS {$this->getTable('erp_inventory_shipping_method_history')};
	CREATE TABLE {$this->getTable('erp_inventory_shipping_method_history')} (
            `shipping_method_history_id` int(11) unsigned NOT NULL auto_increment,
            `shipping_method_id` int(11) unsigned NOT NULL,
            `time_stamp` datetime,
            `create_by` varchar(255) NOT NULL,
            INDEX (`shipping_method_id`),
            FOREIGN KEY (`shipping_method_id`) REFERENCES {$this->getTable('erp_inventory_shipping_method')}(`shipping_method_id`) ON DELETE CASCADE ON UPDATE CASCADE,
            PRIMARY KEY  (`shipping_method_history_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        
        DROP TABLE IF EXISTS {$this->getTable('erp_inventory_shipping_method_history_content')};
	CREATE TABLE {$this->getTable('erp_inventory_shipping_method_history_content')} (
            `shipping_method_history_content_id` int(11) unsigned NOT NULL auto_increment,
            `shipping_method_history_id` int(11) unsigned NOT NULL,
            `field_name` varchar(255) NOT NULL,
            `old_value` text,
            `new_value` text,
            INDEX (`shipping_method_history_id`),
            FOREIGN KEY (`shipping_method_history_id`) REFERENCES {$this->getTable('erp_inventory_shipping_method_history')}(`shipping_method_history_id`) ON DELETE CASCADE ON UPDATE CASCADE,
            PRIMARY KEY  (`shipping_method_history_content_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        

	DROP TABLE IF EXISTS {$this->getTable('erp_inventory_payment_term')};
	CREATE TABLE {$this->getTable('erp_inventory_payment_term')} (
            `payment_term_id` int(11) unsigned NOT NULL auto_increment,
            `name` varchar(255) NOT NULL,
            `description` text,
            `payment_days` int(11) NOT NULL default 0,
            `status` tinyint(1) NOT NULL,
            `create_by` varchar(255) NOT NULL,
            PRIMARY KEY  (`payment_term_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	
        DROP TABLE IF EXISTS {$this->getTable('erp_inventory_payment_term_history')};
	CREATE TABLE {$this->getTable('erp_inventory_payment_term_history')} (
            `payment_term_history_id` int(11) unsigned NOT NULL auto_increment,
            `payment_term_id` int(11) unsigned NOT NULL,
            `time_stamp` datetime,
            `create_by` varchar(255) NOT NULL,
            INDEX (`payment_term_id`),
            FOREIGN KEY (`payment_term_id`) REFERENCES {$this->getTable('erp_inventory_payment_term')}(`payment_term_id`) ON DELETE CASCADE ON UPDATE CASCADE,
            PRIMARY KEY  (`payment_term_history_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        
        DROP TABLE IF EXISTS {$this->getTable('erp_inventory_payment_term_history_content')};
	CREATE TABLE {$this->getTable('erp_inventory_payment_term_history_content')} (
            `payment_term_history_content_id` int(11) unsigned NOT NULL auto_increment,
            `payment_term_history_id` int(11) unsigned NOT NULL,
            `field_name` varchar(255) NOT NULL,
            `old_value` text,
            `new_value` text,
            INDEX (`payment_term_history_id`),
            FOREIGN KEY (`payment_term_history_id`) REFERENCES {$this->getTable('erp_inventory_payment_term_history')}(`payment_term_history_id`) ON DELETE CASCADE ON UPDATE CASCADE,
            PRIMARY KEY  (`payment_term_history_content_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        
        ALTER TABLE {$this->getTable('erp_inventory_purchase_order')} 
            ADD `create_by` varchar(255) default '',
            ADD `order_placed` int(11),
            ADD `start_date` date,
            ADD `cancel_date` date,
            ADD `expected_date` date,
            ADD `payment_date` date,
            ADD `ship_via` int(11),
            ADD `payment_term` int(11);
        
        DROP TABLE IF EXISTS {$this->getTable('erp_inventory_purchase_order_history')};
	CREATE TABLE {$this->getTable('erp_inventory_purchase_order_history')} (
            `purchase_order_history_id` int(11) unsigned NOT NULL auto_increment,
            `purchase_order_id` int(11) unsigned NOT NULL,
            `time_stamp` datetime,
            `create_by` varchar(255) NOT NULL,
            INDEX (`purchase_order_id`),
            FOREIGN KEY (`purchase_order_id`) REFERENCES {$this->getTable('erp_inventory_purchase_order')}(`purchase_order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
            PRIMARY KEY  (`purchase_order_history_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        
        DROP TABLE IF EXISTS {$this->getTable('erp_inventory_purchase_order_history_content')};
	CREATE TABLE {$this->getTable('erp_inventory_purchase_order_history_content')} (
            `purchase_order_history_content_id` int(11) unsigned NOT NULL auto_increment,
            `purchase_order_history_id` int(11) unsigned NOT NULL,
            `field_name` varchar(255) NOT NULL,
            `old_value` text,
            `new_value` text,
            INDEX (`purchase_order_history_id`),
            FOREIGN KEY (`purchase_order_history_id`) REFERENCES {$this->getTable('erp_inventory_purchase_order_history')}(`purchase_order_history_id`) ON DELETE CASCADE ON UPDATE CASCADE,
            PRIMARY KEY  (`purchase_order_history_content_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        
        ALTER TABLE {$this->getTable('erp_inventory_delivery')} 
            ADD `create_by` varchar(255) default '';
         
        ALTER TABLE {$this->getTable('erp_inventory_return_product_warehouse')} 
            ADD `create_by` varchar(255) default '',
            ADD `reason` text default '';
            
        ALTER TABLE {$this->getTable('erp_inventory_adjuststock')} 
            ADD `create_by` varchar(255) default '',
            ADD `reason` text;
            
        DROP TABLE IF EXISTS {$this->getTable('erp_inventory_warehouse_history')};
	CREATE TABLE {$this->getTable('erp_inventory_warehouse_history')} (
            `warehouse_history_id` int(11) unsigned NOT NULL auto_increment,
            `warehouse_id` int(11) unsigned NOT NULL,
            `time_stamp` datetime,
            `create_by` varchar(255) NOT NULL,
            INDEX (`warehouse_id`),
            FOREIGN KEY (`warehouse_id`) REFERENCES {$this->getTable('erp_inventory_warehouse')}(`warehouse_id`) ON DELETE CASCADE ON UPDATE CASCADE,
            PRIMARY KEY  (`warehouse_history_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        
        DROP TABLE IF EXISTS {$this->getTable('erp_inventory_warehouse_history_content')};
	CREATE TABLE {$this->getTable('erp_inventory_warehouse_history_content')} (
            `warehouse_history_content_id` int(11) unsigned NOT NULL auto_increment,
            `warehouse_history_id` int(11) unsigned NOT NULL,
            `field_name` varchar(255) NOT NULL,
            `old_value` text,
            `new_value` text,
            INDEX (`warehouse_history_id`),
            FOREIGN KEY (`warehouse_history_id`) REFERENCES {$this->getTable('erp_inventory_warehouse_history')}(`warehouse_history_id`) ON DELETE CASCADE ON UPDATE CASCADE,
            PRIMARY KEY  (`warehouse_history_content_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        
        ALTER TABLE {$this->getTable('erp_inventory_warehouse')} 
            CHANGE `created_by` `created_by` varchar(255) default '';
            
        ALTER TABLE {$this->getTable('erp_inventory_supplier')} 
            ADD `create_by` varchar(255) default '';
        
        DROP TABLE IF EXISTS {$this->getTable('erp_inventory_supplier_history')};
	CREATE TABLE {$this->getTable('erp_inventory_supplier_history')} (
            `supplier_history_id` int(11) unsigned NOT NULL auto_increment,
            `supplier_id` int(11) unsigned NOT NULL,
            `time_stamp` datetime,
            `create_by` varchar(255) NOT NULL,
            INDEX (`supplier_id`),
            FOREIGN KEY (`supplier_id`) REFERENCES {$this->getTable('erp_inventory_supplier')}(`supplier_id`) ON DELETE CASCADE ON UPDATE CASCADE,
            PRIMARY KEY  (`supplier_history_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        
        DROP TABLE IF EXISTS {$this->getTable('erp_inventory_supplier_history_content')};
	CREATE TABLE {$this->getTable('erp_inventory_supplier_history_content')} (
            `supplier_history_content_id` int(11) unsigned NOT NULL auto_increment,
            `supplier_history_id` int(11) unsigned NOT NULL,
            `field_name` varchar(255) NOT NULL,
            `old_value` text,
            `new_value` text,
            INDEX (`supplier_history_id`),
            FOREIGN KEY (`supplier_history_id`) REFERENCES {$this->getTable('erp_inventory_supplier_history')}(`supplier_history_id`) ON DELETE CASCADE ON UPDATE CASCADE,
            PRIMARY KEY  (`supplier_history_content_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        
        ALTER TABLE {$this->getTable('erp_inventory_transfer_stock')} 
            ADD `create_by` varchar(255) default '',
            ADD `reason` varchar(255) default '';
        
        DROP TABLE IF EXISTS {$this->getTable('erp_inventory_transfer_stock_history')};
	CREATE TABLE {$this->getTable('erp_inventory_transfer_stock_history')} (
            `transfer_stock_history_id` int(11) unsigned NOT NULL auto_increment,
            `transfer_stock_id` int(11) unsigned NOT NULL,
            `time_stamp` datetime,
            `create_by` varchar(255) NOT NULL,
            INDEX (`transfer_stock_id`),
            FOREIGN KEY (`transfer_stock_id`) REFERENCES {$this->getTable('erp_inventory_transfer_stock')}(`transfer_stock_id`) ON DELETE CASCADE ON UPDATE CASCADE,
            PRIMARY KEY  (`transfer_stock_history_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        
        DROP TABLE IF EXISTS {$this->getTable('erp_inventory_transfer_stock_history_content')};
	CREATE TABLE {$this->getTable('erp_inventory_transfer_stock_history_content')} (
            `transfer_stock_history_content_id` int(11) unsigned NOT NULL auto_increment,
            `transfer_stock_history_id` int(11) unsigned NOT NULL,
            `field_name` varchar(255) NOT NULL,
            `old_value` text,
            `new_value` text,
            INDEX (`transfer_stock_history_id`),
            FOREIGN KEY (`transfer_stock_history_id`) REFERENCES {$this->getTable('erp_inventory_transfer_stock_history')}(`transfer_stock_history_id`) ON DELETE CASCADE ON UPDATE CASCADE,
            PRIMARY KEY  (`transfer_stock_history_content_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            
    "); 
            
    /* update create by for unWarehouse */
    $unWarehouse = Mage::getModel('inventory/warehouse')->getCollection()
                                    ->addFieldToFilter('is_unwarehouse',1)
                                    ->getFirstItem();
    if($unWarehouse->getId()){
        $unWarehouse->setData('created_by','Automatically created')
                    ->save();
    }
    
endif;
$installer->endSetup();