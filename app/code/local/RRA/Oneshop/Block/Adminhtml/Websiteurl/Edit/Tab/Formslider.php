<?php
 
class  RRA_Oneshop_Block_Adminhtml_Websiteurl_Edit_Tab_Formslider extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
	
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('websiteurl_form1', array('legend'=>Mage::helper('oneshop')->__('Website URL Details')));
	   

        // $fieldset->addField('websiteurl', 'text', array(
            // 'label'     => Mage::helper('oneshop')->__('Website URLasdasdasdasdasd'),
            
            // 'required'  => true,
			
            // 'name'      => 'websiteurl1',
        // ));	   
		
		
        $fieldset->addField('categoryid', 'select', array(
            'label'     => Mage::helper('oneshop')->__('Store Name'),
            'title'     => Mage::helper('oneshop')->__('Store Name'),
            'name'      => 'storeCategoryid',
            'required' => true,
            'options'    => array(
				'0' => Mage::helper('oneshop')->__('Please Select Store'), 
                '1' => Mage::helper('oneshop')->__('Active Health'), 
                '2' => Mage::helper('oneshop')->__('Athena'),
            ),
        ));
		
		// $fieldset->addField('banner', 'text', array(
            // 'label'     => Mage::helper('oneshop')->__('Banner'),   
            // 'class'     => 'required-entry',
            // 'required'  => true,
            // 'name'      => 'banner',
        // ));	
		
		
		$fieldset->addField('slider', 'file', array(
			'label' => Mage::helper('oneshop')->__('Add Slider Image'),
			//'class' => 'required-entry',
			'name' => 'slider',
			)
		); 
		
        // $fieldset->addField('token', 'text', array(
            // 'label'     => Mage::helper('oneshop')->__('Token'),
            // 'required'  => false,
			// 'readonly'	=> true,
			// 'note'		=> 'Token is auto generated',
            // 'name'      => 'token',
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