<?php 

$installer = $this;

$installer->startSetup(); 

$this->getConnection()
    ->addColumn($this->getTable('sales_flat_quote_shipping_rate'), 'insurance_fee', "decimal(12,4)   AFTER `method_title`");
 
$installer->endSetup(); 