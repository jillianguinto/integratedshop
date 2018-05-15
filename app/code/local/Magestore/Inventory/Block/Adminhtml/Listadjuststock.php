<?php

class Magestore_Inventory_Block_Adminhtml_Listadjuststock extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_adjuststock_listadjuststock';
        $this->_blockGroup = 'inventory';
        $this->_headerText = Mage::helper('inventory')->__('Stock Adjustment Manager');
        $this->_addButtonLabel = Mage::helper('inventory')->__('Add Stock Adjustment');
        parent::__construct();
    }
}