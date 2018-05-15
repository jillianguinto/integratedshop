<?php

$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('prescription/prescription')} 	  
	ADD COLUMN `original_filename` varchar(150) AFTER `scanned_rx`	 
");

$installer->endSetup();
