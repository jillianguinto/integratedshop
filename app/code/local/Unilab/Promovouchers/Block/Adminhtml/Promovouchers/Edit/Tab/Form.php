<?php
 
class Unilab_Promovouchers_Block_Adminhtml_Promovouchers_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
	
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('promovouchers_form', array('legend'=>Mage::helper('promovouchers')->__('Promo Vouchers')));
	   

        $fieldset->addField('salesrule_parent', 'select', array(
            'label'     => Mage::helper('promovouchers')->__('Shopping Cart Rule'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'salesrule_parent',
			'values' 	=> Mage::getModel('promovouchers/values_salesrule')->toOptionArray(),
        ));	

        $fieldset->addField('voucher_code', 'text', array(
            'label'     => Mage::helper('promovouchers')->__('Voucher Code'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'voucher_code',
        )); 
	

		$fieldset->addField('voucher_credits', 'text', array(
            'label'     => Mage::helper('promovouchers')->__('Voucher Credits'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'voucher_credits',
        ));	


        $fieldset->addField('voucher_customer', 'select', array(
            'label'     => Mage::helper('promovouchers')->__('Customer Assigned'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'voucher_customer',
			'values'    => Mage::getModel('promovouchers/values_customers')->toOptionArray(),
        )); 
		
		$fieldset->addField('voucher_id', 'hidden', array(
            'label'     => Mage::helper('promovouchers')->__('ID'),
            'class'     => 'required-entry',
            'required'  => false,
            'name'      => 'voucher_id',
        ));	
		
		
        if ( Mage::getSingleton('adminhtml/session')->getpromovouchersData() )
        {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getpromovouchersData());
            Mage::getSingleton('adminhtml/session')->setpromovouchersData(null);
        } elseif ( Mage::registry('promovouchers_data') ) {
            $form->setValues(Mage::registry('promovouchers_data')->getData());
		}
	}
}