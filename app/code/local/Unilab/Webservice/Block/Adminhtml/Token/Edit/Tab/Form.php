<?php
 
class Unilab_Webservice_Block_Adminhtml_Token_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
	
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('token_form', array('legend'=>Mage::helper('webservice')->__('API Token Details')));
	   
	   
	   $fieldset->addField('store', 'select', array(
            'label'     => Mage::helper('webservice')->__('Store Name'),
            'title'     => Mage::helper('webservice')->__('Store Name'),
            'name'      => 'store',
			'values'    =>	Mage::getModel('oneshop/values_category')->toOptionArray(), //Mage::getModel('webservice/values_storename')->toOptionArray(),
        ));

		
        $fieldset->addField('hostname', 'text', array(
            'label'     => Mage::helper('webservice')->__('Host Name'),
            'class'     => 'required-entry',
            'required'  => true,
			'note'		=> 'Ex. unilab.com.ph Note: Do not include http:// and www.',
            'name'      => 'hostname',
        ));	   
		

        $fieldset->addField('returnurl', 'text', array(
            'label'     => Mage::helper('webservice')->__('Return URL'),
            'class'     => 'required-entry',
            'required'  => true,
			'note'		=> 'Ex. http://www.unilab.com.ph',
            'name'      => 'returnurl',
        ));	   

		
        $fieldset->addField('isactive', 'select', array(
            'label'     => Mage::helper('webservice')->__('Status'),
            'title'     => Mage::helper('webservice')->__('Status'),
            'name'      => 'isactive',
            'required' => true,
            'options'    => array(
                '1' => Mage::helper('salesrule')->__('Active'),
                '0' => Mage::helper('salesrule')->__('Inactive'),
            ),
        ));
		
		
        $fieldset->addField('token', 'textarea', array(
            'label'     => Mage::helper('webservice')->__('Token'),
            'required'  => false,
			'readonly'	=> true,
			'note'		=> 'Token is auto generated',
            'name'      => 'token',
        ));	   
				
		
        $fieldset->addField('createddate', 'hidden', array(
            'label'     => Mage::helper('webservice')->__('Created Date'),
            'name'      => 'createddate',
			
        ));	   


		
        if ( Mage::getSingleton('adminhtml/session')->gettokenData() )
        {
            $form->setValues(Mage::getSingleton('adminhtml/session')->gettokenData());
            Mage::getSingleton('adminhtml/session')->settokenData(null);
        } elseif ( Mage::registry('token_data') ) {
            $form->setValues(Mage::registry('token_data')->getData());
        }
		
        return parent::_prepareForm();
    }

	
}