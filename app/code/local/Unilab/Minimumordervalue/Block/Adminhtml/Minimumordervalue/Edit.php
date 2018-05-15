<?php
 
class Unilab_Minimumordervalue_Block_Adminhtml_Minimumordervalue_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
               
        $this->_objectId   = 'id';
        $this->_blockGroup = 'minimumordervalue';
        $this->_controller = 'adminhtml_minimumordervalue';
 
		$this->_updateButton('save', 'label', Mage::helper('minimumordervalue')->__('Save Shipping Group'));
    }
 
    public function getHeaderText()
    {

        if( Mage::registry('movshipping_data') && Mage::registry('movshipping_data')->getId() ) {
            return Mage::helper('minimumordervalue')->__("Edit Shipping Group", $this->htmlEscape(Mage::registry('movshipping_data')->getTitle()));
        } else {
            return Mage::helper('minimumordervalue')->__('Add Shipping Group');
        }
    }
}