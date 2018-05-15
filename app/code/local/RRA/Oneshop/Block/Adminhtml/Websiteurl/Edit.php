<?php
 
class RRA_Oneshop_Block_Adminhtml_Websiteurl_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
	
		
        parent::__construct();
               
        $this->_objectId = 'id';
        $this->_blockGroup = 'oneshop';
        $this->_controller = 'adminhtml_websiteurl';
 
		$this->_updateButton('save', 'label', Mage::helper('oneshop')->__('Save Website URL'));


    }
 
    public function getHeaderText()
    {

        if( Mage::registry('websiteurl_data') && Mage::registry('websiteurl_data')->getId() ) {
            return Mage::helper('oneshop')->__("Edit Website URL", $this->htmlEscape(Mage::registry('websiteurl_data')->getTitle()));
        } else {
            return Mage::helper('oneshop')->__('Add Website URL');
        }
    }
}