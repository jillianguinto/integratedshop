<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run(" 

UPDATE `{$installer->getTable('eav_attribute')}` 
SET backend_model='eav/entity_attribute_backend_array',
    backend_type='varchar',
	frontend_input='multiselect'
WHERE attribute_code IN('unilab_segment','unilab_benefit'); 
 
");


$installer->endSetup();
