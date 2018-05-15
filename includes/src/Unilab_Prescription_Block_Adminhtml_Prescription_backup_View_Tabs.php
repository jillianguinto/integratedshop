<?php

class Unilab_Prescription_Block_Adminhtml_Prescription_View_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('prescription_tabs');
      $this->setDestElementId('edit_order_item_prescription_form');
      $this->setTitle(Mage::helper('prescription')->__('Prescription Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('prescription')->__('General Information'),
          'title'     => Mage::helper('prescription')->__('General Information'),
          'content'   => $this->getLayout()->createBlock('prescription/adminhtml_prescription_view_tab_general')->toHtml(),
      ));
	  
	  $this->addTab('approval_section', array(
          'label'     => Mage::helper('prescription')->__('Approvals'),
          'title'     => Mage::helper('prescription')->__('Approvals'),
          'content'   => $this->getLayout()->createBlock('prescription/adminhtml_prescription_view_tab_approval')->toHtml(),
      )); 
	  
      return parent::_beforeToHtml();
  }
}