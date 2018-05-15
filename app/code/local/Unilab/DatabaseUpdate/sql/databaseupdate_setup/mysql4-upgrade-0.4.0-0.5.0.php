<?php 

$installer = $this;

$installer->startSetup(); 

$this->getConnection()
    ->addColumn($this->getTable('sales_flat_quote'), 'unilab_waybill_number', "varchar(150)   AFTER `is_persistent`");
	
$this->getConnection()
    ->addColumn($this->getTable('sales_flat_order'), 'unilab_waybill_number', "varchar(150)  AFTER `gift_message_id`");
	
$this->getConnection()
    ->addColumn($this->getTable('sales_flat_order_grid'), 'unilab_waybill_number', "varchar(150)  AFTER `billing_name`");
	
$installer->endSetup(); 