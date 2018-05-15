<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run(" 
UPDATE `{$installer->getTable('eav_attribute')}` SET note='Will override price by unit price multiplied by MOQ ' WHERE attribute_code='unilab_unit_price';
");
$installer->endSetup();
