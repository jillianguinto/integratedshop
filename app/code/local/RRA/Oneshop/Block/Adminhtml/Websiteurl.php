<?php
 
class RRA_Oneshop_Block_Adminhtml_Websiteurl extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
		
        $this->_controller = 'adminhtml_websiteurl';
		
        $this->_blockGroup = 'oneshop';
		
        $this->_headerText = Mage::helper('oneshop')->__('Manage Website URL');
		
        $this->_addButtonLabel = Mage::helper('oneshop')->__('Add Website URL');
		
        parent::__construct();
    }

}