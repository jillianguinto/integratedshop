<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('prescription/prescription_temp_rx')};
CREATE TABLE IF NOT EXISTS {$this->getTable('prescription/prescription_temp_rx')} (
  `prescription_temp_rx_id` int(11) unsigned NOT NULL auto_increment,
  `quote_id` int(11),
  `name` varchar(150),
  `size` int(150),
  `type` varchar(50),
  `error`  varchar(200), 
  `created_at` datetime NULL,
  `updated_at` datetime NULL,
  PRIMARY KEY (`prescription_temp_rx_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup();
