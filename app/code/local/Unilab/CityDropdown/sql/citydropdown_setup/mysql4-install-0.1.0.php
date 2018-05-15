<?php

$installer = $this;

$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS {$this->getTable('citydropdown/citydropdown')};
CREATE TABLE {$this->getTable('citydropdown/citydropdown')} (
  `city_id` int(11) unsigned NOT NULL auto_increment,
  `country_id` varchar(2) NOT NULL,  
  `region_id` smallint(6) NOT NULL,
  `region_code` varchar(2) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`city_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
$installer->endSetup(); 