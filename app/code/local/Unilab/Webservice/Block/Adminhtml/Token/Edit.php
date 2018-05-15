<?php
 
class Unilab_Webservice_Block_Adminhtml_Token_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
               
        $this->_objectId = 'id';
        $this->_blockGroup = 'webservice';
        $this->_controller = 'adminhtml_token';
 
		$this->_updateButton('save', 'label', Mage::helper('webservice')->__('Save API Token'));
    }
 
 
    public function getHeaderText()
    {

        if( Mage::registry('token_data') && Mage::registry('token_data')->getId() ) {
            return Mage::helper('webservice')->__("Edit API Token Information", $this->htmlEscape(Mage::registry('token_data')->getTitle()));
        } else {
            return Mage::helper('webservice')->__('Add New API Token');
        }
    }
}