<?php
 
class RRA_Oneshop_Block_Adminhtml_Websiteurl_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
 
    public function __construct()
    {
        parent::__construct();
        $this->setId('athenatoken_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('oneshop')->__('Website URL'));
		
    }
 
    protected function _beforeToHtml()
    {	
        $this->addTab('athenatoken_form', array(
            'label'     => Mage::helper('oneshop')->__('Website URL'),
            'title'     => Mage::helper('oneshop')->__('Website URL'),
            'content'   => $this->getLayout()->createBlock('oneshop/adminhtml_websiteurl_edit_tab_form')->toHtml(),
        ));
		
		// $this->addTab('athenatoken1_form', array(
            // 'label'     => Mage::helper('oneshop')->__('Store Slider'),
            // 'title'     => Mage::helper('oneshop')->__('Store Slider'),
            // 'content'   => $this->getLayout()->createBlock('oneshop/adminhtml_websiteurl_edit_tab_formslider')->toHtml(),
        // ));
        return parent::_beforeToHtml();
    }
	
	
	
}