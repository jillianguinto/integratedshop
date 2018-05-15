<?php 
$installer = $this;
/* @var $installer Unilab_Inquiry_Model_Mysql4_Setup */

$now = now();

$installer->run("
INSERT INTO {$this->getTable('cms/block')} (`title`, `identifier`, `content`, `creation_time`, `update_time`, `is_active`) VALUES ('Extended Contacts Text', 'inquiry-form', '', '{$now}', '{$now}', 1);
INSERT INTO {$this->getTable('cms/block_store')} (`block_id`, `store_id`) SELECT `block_id`, 0 FROM {$this->getTable('cms/block')} WHERE `identifier` = 'inquiry-form';
");