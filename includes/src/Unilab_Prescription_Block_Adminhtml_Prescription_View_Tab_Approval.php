<?php

class Unilab_Prescription_Block_Adminhtml_Prescription_View_Tab_Approval extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  { 
	  $form = new Varien_Data_Form(); 
	  $form->setHtmlIdPrefix('general_');
	  $form->setFieldNameSuffix('general');
      $this->setForm($form); 
	  
	  $fieldset = $form->addFieldset('prescription_form', array('legend'=>Mage::helper('prescription')->__('Approval')));
	  
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('prescription')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 'PENDING_APPROVAL',
                  'label'     => Mage::helper('prescription')->__('Pending Approval'),
              ),
              array(
                  'value'     => 'VALID',
                  'label'     => Mage::helper('prescription')->__('Valid'),
              ),			  
			  array(
                  'value'     => 'INVALID',
                  'label'     => Mage::helper('prescription')->__('Invalid'),
              ),
          ),
      ));
	  
	  $fieldset->addField('remarks', 'editor', array(
          'label'     => Mage::helper('prescription')->__('Remarks'), 
          'required'  => true,
          'name'      => 'remarks',
      ));  
	  
	  $fieldset->addType('scanned_prescription', Mage::getConfig()->getBlockClassName('prescription/adminhtml_prescription_view_renderer_scannedprescription'));
    
      $fieldset->addField('scanned_rx', 'scanned_prescription', array(
          'label'     => Mage::helper('prescription')->__('Scanned Prescription'), 
          'required'  => false,
          'name'      => 'scanned_rx', 
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