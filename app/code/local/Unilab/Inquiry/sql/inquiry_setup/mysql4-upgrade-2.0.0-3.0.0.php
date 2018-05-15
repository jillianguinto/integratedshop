<?php

$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('inquiry/inquiry')} 	  
	ADD COLUMN `is_read` boolean default 0 AFTER `name`	 
");

$installer->endSetup();
