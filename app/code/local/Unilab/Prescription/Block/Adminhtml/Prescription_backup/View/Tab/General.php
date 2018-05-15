<?php

class Unilab_Prescription_Block_Adminhtml_Prescription_View_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  { 
	   $form = new Varien_Data_Form(); 
	   $form->setHtmlIdPrefix('general_');
	  $form->setFieldNameSuffix('prescription');
      $this->setForm($form); 
	  
	  $fieldset = $form->addFieldset('prescription_form', array('legend'=>Mage::helper('prescription')->__('Prescription information')));

      $fieldset->addField('date_prescribed', 'date', array(
          'label'     		=> Mage::helper('prescription')->__('Date Prescribed'), 
          'required'  		=> false,
          'format'          => Varien_Date::DATE_INTERNAL_FORMAT,
          'image'           => $this->getSkinUrl('images/grid-cal.gif'),
          'input_format'    => Varien_Date::DATE_INTERNAL_FORMAT,
          'name'      		=> 'date_prescribed',
      )); 

      $fieldset->addField('patient_name', 'text', array(
          'name'      => 'patient_name',
          'label'     => Mage::helper('prescription')->__("Patient's Name"),
          'title'     => Mage::helper('prescription')->__("Patient's Name"), 
          'wysiwyg'   => false,
          'required'  => false,
      ));  
	  
	    $fieldset->addField('ptr_no', 'text', array(
          'name'      => 'ptr_no',
          'label'     => Mage::helper('prescription')->__("PTR No."),
          'title'     => Mage::helper('prescription')->__("PTR No."), 
          'wysiwyg'   => false,
          'required'  => false,
      )); 
	  
	   $fieldset->addField('doctor', 'text', array(
          'name'      => 'doctor',
          'label'     => Mage::helper('prescription')->__("Doctor's Name"),
          'title'     => Mage::helper('prescription')->__("Doctor's Name"), 
          'wysiwyg'   => false,
          'required'  => false,
      )); 
	  
	   $fieldset->addField('clinic', 'text', array(
          'name'      => 'clinic',
          'label'     => Mage::helper('prescription')->__("Clinic Name"),
          'title'     => Mage::helper('prescription')->__("Clinic Name"), 
          'wysiwyg'   => false,
          'required'  => false,
      )); 
	  
	   $fieldset->addField('clinic_address', 'editor', array(
          'name'      => 'clinic_address',
          'label'     => Mage::helper('prescription')->__("Clinic Address"),
          'title'     => Mage::helper('prescription')->__("Clinic Address"), 
          'wysiwyg'   => false,
          'required'  => false,
      )); 
	  
	   $fieldset->addField('contact_number', 'text', array(
          'name'      => 'contact_number',
          'label'     => Mage::helper('prescription')->__("Contact Number"),
          'title'     => Mage::helper('prescription')->__("Contact Number"), 
          'wysiwyg'   => false,
          'required'  => false,
      )); 
	  
	   $fieldset->addField('expiry_date', 'date', array(
          'name'      => 'expiry_date',
          'label'     => Mage::helper('prescription')->__("Expiry Date"),
          'title'     => Mage::helper('prescription')->__("Expiry Date"),  
          'required'  => false,
          'format'          => Varien_Date::DATE_INTERNAL_FORMAT,
          'image'           => $this->getSkinUrl('images/grid-cal.gif'),
          'input_format'    => Varien_Date::DATE_INTERNAL_FORMAT,
      )); 
	  
	   $fieldset->addField('consumed', 'checkbox', array(
          'name'      => 'consumed',
          'label'     => Mage::helper('prescription')->__("Consumed"),
          'title'     => Mage::helper('prescription')->__("Consumed"),  
          'required'  => false,
      )); 
	  
      if ( Mage::getSingleton('adminhtml/session')->getPrescriptionData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getPrescriptionData());
          Mage::getSingleton('adminhtml/session')->setPrescriptionData(null);
      } elseif ( Mage::registry('sales_order_item_prescription') ) {
          $form->setValues(Mage::registry('sales_order_item_prescription')->getData());
      } 
	  
      return parent::_prepareForm();
  }
}