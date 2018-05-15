<?php

class Unilab_CityDropdown_Block_Adminhtml_Citydropdown_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
  
	  $regions 	    = array(); 
	  $region_lists = array();
	  
	  $region_collection = Mage::getResourceModel('directory/region_collection')->addCountryFilter('PH')->load(); 
	  foreach($region_collection as $region){ 
		$region_lists[$region->getRegionId()] = array('title'	=> $region->getName(),
													  'value'	=> $region->getRegionId(),
													  'label'	=> $region->getName());
	  } 
	  
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('citydropdown_form', array('legend'=>Mage::helper("citydropdown")->__('City information')));
 
	   $fieldset->addField('country_id', 'select', array(
          'name'      => 'country_id',
          'label'     => Mage::helper("citydropdown")->__('Country'),
          'title'     => Mage::helper("citydropdown")->__('Country'), 
          'required'  => false, 
		  'values'    => Mage::getResourceModel('directory/country_collection')->addFieldToFilter("country_id",array('PH'))->load()->toOptionArray()
      ));
	  
	  $fieldset->addField('region_id', 'select', array(
          'name'      => 'region_id',
          'label'     => Mage::helper("citydropdown")->__('Region'),
          'title'     => Mage::helper("citydropdown")->__('Region'),
		  'values'    => $region_lists
      ));
	  
	  $fieldset->addField('name', 'text', array(
          'name'      => 'name',
          'label'     => Mage::helper("citydropdown")->__('City Name'),
          'title'     => Mage::helper("citydropdown")->__('City Name'), 
          'required'  => true, 
      )); 

      if ( Mage::getSingleton('adminhtml/session')->getCitydropdownData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getCitydropdownData());
          Mage::getSingleton('adminhtml/session')->setCitydropdownData(null);
      } elseif ( Mage::registry('citydropdown_data') ) { 
          $form->setValues(Mage::registry('citydropdown_data')->getData());
      }
      return parent::_prepareForm();
  } 
}
