<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("
UPDATE `{$installer->getTable('eav_attribute')}` SET is_required=0 WHERE attribute_code='telephone';
");
$installer->endSetup();
