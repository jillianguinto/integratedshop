<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('prescription/prescription')};
CREATE TABLE IF NOT EXISTS {$this->getTable('prescription/prescription')} (
  `prescription_id` int(11) unsigned NOT NULL auto_increment,
  `customer_id` int(11),
  `date_prescribed` DATE,
  `patient_name` varchar(60),
  `ptr_no`  varchar(150),
  `doctor` varchar(150),
  `clinic` varchar(150),
  `clinic_address` varchar(200),
  `contact_number` varchar(60),
  `expiry_date` date,
  `consumed` smallint(6),
  `status` enum('PENDING_APPROVAL','VALID','INVALID') NOT NULL default 'PENDING_APPROVAL',
  `remarks` text,
  `scanned_rx` text,
  `created_at` datetime NULL,
  `updated_at` datetime NULL,
  PRIMARY KEY (`prescription_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup();
