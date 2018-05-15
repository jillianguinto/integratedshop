<?php

class Unilab_Prescription_Block_Adminhtml_Prescription_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('prescription_form', array('legend'=>Mage::helper('prescription')->__('Item information')));

      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('prescription')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('prescription')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));

      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('prescription')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('prescription')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('prescription')->__('Disabled'),
              ),
          ),
      ));

      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('prescription')->__('Content'),
          'title'     => Mage::helper('prescription')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));

      if ( Mage::getSingleton('adminhtml/session')->getPrescriptionData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getPrescriptionData());
          Mage::getSingleton('adminhtml/session')->setPrescriptionData(null);
      } elseif ( Mage::registry('prescription_data') ) {
          $form->setValues(Mage::registry('prescription_data')->getData());
      }
      return parent::_prepareForm();
  }
}