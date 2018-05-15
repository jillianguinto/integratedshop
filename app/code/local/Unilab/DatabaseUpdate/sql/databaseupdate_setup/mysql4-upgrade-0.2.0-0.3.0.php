<?php
$installer = $this;

$installer->startSetup(); 
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

//START ADDING CIVIL STATUS TO CUSTOMER
$setup->removeAttribute( 'customer', 'civil_status' );

$setup->addAttribute(
    'customer', 'civil_status', array(
    'backend'       => '',
    'frontend'      => '', 
    'label'         => 'Civil Status', 
    'input'         => 'text',
    'required'      => false,
    'user_defined'  => true,
    'default'       => ''
));

// add new attribute to customer forms
$forms       = array('adminhtml_customer','checkout_register','customer_account_create','customer_account_edit');
$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'civil_status');
$usedInForms = $attribute->getUsedInForms();
foreach($forms as $form) {
    if (!in_array($form, $usedInForms)) {
        $usedInForms[] = $form;
        $attribute->setData('used_in_forms', $usedInForms);
        $attribute->save();
    }
}
//END ADDING OF CIVIL STATUS TO CUSTOMER  
 
 
 
//START ADDING MOBILE TO CUSTOMER
$installer = $this;
$installer->startSetup();
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

 
$setup->removeAttribute('customer', 'mobile' );
$setup->addAttribute('customer', 'mobile', array('backend'       => '',
												 'frontend'      => '',
												 'type'          => 'varchar',
												 'label'         => 'Mobile',
												 'note'          => '',
												 'input'         => 'text',
												 'required'      => false,
												 'user_defined'  => true,
												 'default'       => '0'
));

$addressHelper = Mage::helper('customer/address');
$store         = Mage::app()->getStore(Mage_Core_Model_App::ADMIN_STORE_ID);  
 
$eavConfig 	   = Mage::getSingleton('eav/config');
  
$attributes = array('mobile' => array('type'          => 'varchar',
									  'backend_type'  => 'varchar',
									  'label'         => 'Mobile',
									  'frontend_label'=> 'Mobile',
									  'note'          => '',
									  'input'         => 'text',
									  'frontend_input'=> 'text',
									  'required'      => false,
									  'user_defined'  => true,
									  'sort_order'    => 140,
									  'position'      => 140,
									  'default'       => '0')
);
 
foreach ($attributes as $attributeCode => $data) {
    $attribute = $eavConfig->getAttribute('customer_address', $attributeCode);
  //  $attribute->setWebsite($store->getWebsite());
    $attribute->addData($data);
        $usedInForms = array(
            'adminhtml_customer_address',
            'customer_address_edit',
            'customer_register_address'
        );
        $attribute->setData('used_in_forms', $usedInForms);
    $attribute->save();
}
 
$installer->run("
    ALTER TABLE {$this->getTable('sales_flat_quote_address')} ADD COLUMN `mobile` VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL AFTER `fax`;
    ALTER TABLE {$this->getTable('sales_flat_order_address')} ADD COLUMN `mobile` VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL AFTER `fax`;
    "); 

$installer->endSetup();