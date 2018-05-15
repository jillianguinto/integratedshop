<?php
 
class Unilab_Expeditedshipping_Block_Adminhtml_Expeditedshipping_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
 
    public function __construct()
    {
        parent::__construct();
        $this->setId('minimumordervalue_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('minimumordervalue')->__('Add Shipping Group'));
		
    }
 
    protected function _beforeToHtml()
    {	
        $this->addTab('minimumordervalue_form', array(
            'label'     => Mage::helper('minimumordervalue')->__('Shipping Group'),
            'title'     => Mage::helper('minimumordervalue')->__('Shipping Group'),
            'content'   => $this->getLayout()->createBlock('minimumordervalue/adminhtml_minimumordervalue_edit_tab_form')->toHtml(),
        ));
		

        return parent::_beforeToHtml();
    }
	
	
	
}