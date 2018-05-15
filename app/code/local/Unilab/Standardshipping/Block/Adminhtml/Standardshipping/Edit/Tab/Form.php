<?php
 
class Unilab_Standardshipping_Block_Adminhtml_Standardshipping_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
	
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('minimumordervalue_form', array('legend'=>Mage::helper('minimumordervalue')->__('Shipping Group Details')));
	   


		$fieldset->addField('group', 'text', array(
            'label'     => Mage::helper('minimumordervalue')->__('Group'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'group',
        ));	
		
		$fieldset->addField('listofcities', 'multiselect', array(
            'label'     => Mage::helper('minimumordervalue')->__('List of Cities'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'listofcities[]',
			'values' 	=> Mage::getModel('minimumordervalue/resource_cities')->toOptionArray(),
			'value' 	=> 'listofcities',
        ));	
		
		
		$fieldset->addField('greaterequal_mov', 'text', array(
            'label'     => Mage::helper('minimumordervalue')->__('Greater Equal MOV'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'greaterequal_mov',
        ));	
		
		$fieldset->addField('lessthan_mov', 'text', array(
            'label'     => Mage::helper('minimumordervalue')->__('Less than MOV'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'lessthan_mov',
        ));	

		
        if (Mage::getSingleton('adminhtml/session')->getmovshippingData())
        {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getmovshippingData());
            Mage::getSingleton('adminhtml/session')->setmovshippingData(null);
        } elseif ( Mage::registry('movshipping_data') ) {
            $form->setValues(Mage::registry('movshipping_data')->getData());
        }
		
        return parent::_prepareForm();
    }
	
	
	

	
	
}