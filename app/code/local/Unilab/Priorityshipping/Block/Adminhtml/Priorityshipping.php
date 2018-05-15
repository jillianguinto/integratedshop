<?php

class Unilab_Priorityshipping_Block_Adminhtml_Priorityshipping extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'minimumordervalue';
        $this->_controller = 'adminhtml_minimumordervalue';
        $this->_headerText = Mage::helper('minimumordervalue')->__('MOV Shipping');
		$this->_addButtonLabel = Mage::helper('minimumordervalue')->__('Add Group');
 
        parent::__construct();
    }
}