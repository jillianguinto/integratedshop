<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('inquiry/inquiry')};
CREATE TABLE {$this->getTable('inquiry/inquiry')} (
  `inquiry_id` int(11) unsigned NOT NULL auto_increment,
  `store_id` smallint(6) NOT NULL,
  `customer_id` smallint(6) NOT NULL default '0',
  `department` varchar(255) NOT NULL,
  `department_email` varchar(255) NOT NULL,
  `concern` text NOT NULL,
  `email_address` varchar(255) NOT NULL default '',
  `name` varchar(500) NOT NULL default '',  
  `created_time` datetime NULL,
  PRIMARY KEY (`inquiry_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");

$installer->endSetup();
