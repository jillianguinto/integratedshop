<?php
 
class Unilab_Webservice_Block_Adminhtml_Token_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
 
    public function __construct()
    {
        parent::__construct();
        $this->setId('token_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('webservice')->__('API Token Information'));
		
    }
 
    protected function _beforeToHtml()
    {	
        $this->addTab('ulahtoken_form', array(
            'label'     => Mage::helper('webservice')->__('API Token'),
            'title'     => Mage::helper('webservice')->__('API Token'),
            'content'   => $this->getLayout()->createBlock('webservice/adminhtml_token_edit_tab_form')->toHtml(),
        ));
        return parent::_beforeToHtml();
    }
	
	
	
}