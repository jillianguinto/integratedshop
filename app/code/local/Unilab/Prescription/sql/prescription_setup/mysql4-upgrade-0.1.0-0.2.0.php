<?php 

/*** ADDED PRESCRIPTION REFERENCE FOR QUOTE/ORDER/INVOICE ITEMS **/

$installer = $this;

$installer->getConnection()->addColumn($this->getTable('sales_flat_quote_item'), 'prescription_id', "INT(11)");	   
$installer->getConnection()->addColumn($this->getTable('sales_flat_order_item'), 'prescription_id', "INT(11)");	
$installer->getConnection()->addColumn($this->getTable('sales_flat_invoice_item'), 'prescription_id', "INT(11)"); 	