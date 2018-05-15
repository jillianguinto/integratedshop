<?php
 
class RRA_Oneshop_Block_Adminhtml_Slider extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
		
        $this->_controller = 'adminhtml_slider';
		
        $this->_blockGroup = 'oneshop';
		
        $this->_headerText = Mage::helper('oneshop')->__('Manage Slider');
		
        $this->_addButtonLabel = Mage::helper('oneshop')->__('Add Slider Image');
		
        parent::__construct();
    }

}