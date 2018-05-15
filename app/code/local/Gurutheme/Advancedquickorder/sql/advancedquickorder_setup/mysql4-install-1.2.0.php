<?php

$installer = $this;
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();
$attribute_code = 'product_suggestion';

$m = Mage::getModel('catalog/resource_eav_attribute')
    ->loadByCode('catalog_product',$attribute_code);

if(!$m->getId())
{
    $setup->addAttribute('catalog_product', $attribute_code, array(
        'group' => 'General',
        'label' => 'Suggestion',
        'type' => 'int',
        'input' => 'boolean',
        'backend' => '',
        'frontend' => '',
        'default' => 0,
        'source' => 'eav/entity_attribute_source_table',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible' => true,
        'required' => false,
        'user_defined' => true,
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'is_visible_on_front' => true,
        'used_in_product_listing' => true,
        'visible_in_advanced_search' => false,
        'unique' => false
    ));
    $installer->endSetup();
}

?>
