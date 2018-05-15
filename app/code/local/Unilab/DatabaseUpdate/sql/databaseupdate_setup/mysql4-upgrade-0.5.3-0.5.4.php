<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("
UPDATE `{$installer->getTable('eav_attribute')}` SET is_required=0 WHERE attribute_code='price' AND entity_type_id = 4;
UPDATE `{$installer->getTable('eav_attribute')}` SET note='Will override price by base price multiplied by MOQ ' WHERE attribute_code='unilab_baseprice';
");
$installer->endSetup();
