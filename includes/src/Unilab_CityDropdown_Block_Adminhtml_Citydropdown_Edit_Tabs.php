<?php

class Unilab_CityDropdown_Block_Adminhtml_Citydropdown_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('citydropdown_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('citydropdown')->__('City Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('citydropdown')->__('City Dropdown Information'),
          'title'     => Mage::helper('citydropdown')->__('City Dropdown Information'),
          'content'   => $this->getLayout()->createBlock('citydropdown/adminhtml_citydropdown_edit_tab_form')->toHtml(),
      ));

      return parent::_beforeToHtml();
  }
}
