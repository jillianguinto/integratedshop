<?php

class Unilab_Prescription_Block_Adminhtml_Prescription_View_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form(array(
                                      'id' => 'edit_order_item_prescription_form',
                                      'action' => $this->getUrl('*/*/savePrescription', array('id' => Mage::registry('sales_order_item_prescription')->getId(),'item_id' => Mage::registry('sales_order_item')->getId())),
                                      'method' => 'post',
        							  'enctype' => 'multipart/form-data'
                                   )
      );

      $form->setUseContainer(true);
      $this->setForm($form);
	  
      return parent::_prepareForm();
  }
}