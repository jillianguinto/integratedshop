<?php
    class Magestore_Inventory_Block_Adminhtml_Sendstock extends Mage_Adminhtml_Block_Widget_Grid_Container{
        public function __construct(){
            $this->_controller = 'adminhtml_sendstock';
            $this->_blockGroup = 'inventory';
            $this->_headerText = Mage::helper('inventory')->__('Stock Sending Manager');
            $this->_addButtonLabel = Mage::helper('inventory')->__('Create Stock Sending');
            parent::__construct();
        }
    }
