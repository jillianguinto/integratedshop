<?php
$installer = $this;

$installer->startSetup(); 
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

//START ADDING TERMS AGREEMENT TO CUSTOMER
$setup->removeAttribute( 'customer', 'agree_on_terms' );

$setup->addAttribute(
    'customer', 'agree_on_terms', array(
    'backend'       => 'customer/attribute_backend_data_boolean',
    //'source_model'  => 'eav/entity_attribute_source_boolean',
    'frontend'      => '', 
    'label'         => 'Agree on Terms',
    'frontend_label'=> 'Agree on Terms',
    'input'         => 'boolean',
    'frontend_input'=> 'boolean', 
    'required'      => true,
    'user_defined'  => true,
    'default'       => 0,
	'sort_order'    => 150,
	'position'      => 150
));

// add new attribute to customer forms
$forms       = array('adminhtml_customer','checkout_register','customer_account_create','customer_account_edit');
$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'agree_on_terms');
$usedInForms = $attribute->getUsedInForms();
foreach($forms as $form) {
    if (!in_array($form, $usedInForms)) {
        $usedInForms[] = $form;
        $attribute->setData('used_in_forms', $usedInForms);
        $attribute->save();
    }
}
//END ADDING OF TERMS AGREEMENT TO CUSTOMER   