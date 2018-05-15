<?php
 
class  RRA_Oneshop_Block_Adminhtml_Websiteurl_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
	
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('websiteurl_form', array('legend'=>Mage::helper('oneshop')->__('Website URL Details')));
	   

        $fieldset->addField('websiteurl', 'text', array(
            'label'     => Mage::helper('oneshop')->__('Website URL'),
            'required'  => true,
            'name'      => 'websiteurl',
        ));	   
		
		
       $fieldset->addField('categoryid', 'select', array(
            'label'     => Mage::helper('oneshop')->__('Store Name'),
            'required'  => true,
            'name'      => 'categoryid',
			'values'    =>	Mage::getModel('oneshop/values_category')->toOptionArray(),
        ));
		
		$fieldset->addField('token', 'textarea', array(
            'label'     => Mage::helper('oneshop')->__('Token'),    
            'name'      => 'token',
        ));	
		
		// $fieldset->addField('banner', 'text', array(
            // 'label'     => Mage::helper('oneshop')->__('Banner'),   
            // 'class'     => 'required-entry',
            // 'required'  => true,
            // 'name'      => 'banner',
        // ));	
		
	
        $fieldset->addField('date_created', 'hidden', array(
            'label'     => Mage::helper('oneshop')->__('Created Date'),
            'name'      => 'date_created',
			
        ));	   


		
        if ( Mage::getSingleton('adminhtml/session')->getwebsiteurlData() )
        {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getwebsiteurlData());
            Mage::getSingleton('adminhtml/session')->setwebsiteurlData(null);
        } elseif ( Mage::registry('websiteurl_data') ) {
            $form->setValues(Mage::registry('websiteurl_data')->getData());
        }
		
        return parent::_prepareForm();
    }

	
}