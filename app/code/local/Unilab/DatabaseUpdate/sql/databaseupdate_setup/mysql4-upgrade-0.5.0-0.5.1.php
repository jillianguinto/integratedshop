<?php 

$installer = $this;

$installer->startSetup(); 

$this->getConnection()
    ->addColumn($this->getTable('sales_flat_quote'), 'unilab_insurance', "decimal(12,4)   AFTER `unilab_waybill_number`");
	
$this->getConnection()
    ->addColumn($this->getTable('sales_flat_order'), 'unilab_insurance', "decimal(12,4)  AFTER `unilab_waybill_number`");
	 
$installer->endSetup(); 