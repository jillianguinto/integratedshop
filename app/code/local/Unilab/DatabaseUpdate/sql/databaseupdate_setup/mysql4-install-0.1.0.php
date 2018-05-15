<?php

/* @package 		Unilab_Database_update
 * @author 			Movent, Inc. - Jerick Y. Duguran (jerick.duguran@gmail.com)
 * @date   			November 18, 2013
 * @description   - Creates two new category attributes: List Icon and List Title
 */

$installer = $this;
$installer->startSetup();

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$entityTypeId     = $setup->getEntityTypeId('catalog_category');
$attributeSetId   = $setup->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $setup->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$setup->addAttribute('catalog_category', 'list_title', array(
	'type'  			=> 'varchar',
	'input' 			=> 'text', 
    'group' 			=> 'Custom  Fields',
    'label'         	=> 'List Title',
    'visible'       	=> 1,
    'required'      	=> 0,
    'user_defined'  	=> 1,
    'frontend_input' 	=>'',
    'global'       	 	=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible_on_front'  => 1,
));


$setup->addAttribute('catalog_category', 'list_icon', array(
	'type'  			=> 'varchar',
	'input' 			=> 'image',
	'backend'			=> 'catalog/category_attribute_backend_image',
    'group' 			=> 'Custom  Fields',
    'label'         	=> 'List Icon',
    'visible'       	=> 1,
    'required'      	=> 0,
    'user_defined' 		=> 1,
    'frontend_input' 	=>'',
    'global'        	=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible_on_front'  => 1,
));

/* 
$setup->addAttributeToGroup(
 $entityTypeId,
 $attributeSetId,
 $attributeGroupId,
 'list_title',
 '990'  //sort_order
); 

$setup->addAttributeToGroup(
 $entityTypeId,
 $attributeSetId,
 $attributeGroupId,
 'list_icon',
 '999'  //sort_order
); */

$installer->endSetup();