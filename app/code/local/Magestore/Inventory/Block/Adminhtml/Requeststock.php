<?php

class Magestore_Inventory_Block_Adminhtml_Requeststock extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_controller = 'adminhtml_requeststock';
        $this->_blockGroup = 'inventory';
        $this->_headerText = Mage::helper('inventory')->__('Request Stock Manager');
        parent::__construct();
        // $this->_removeButton('add');
        $this->_updateButton('add', 'label', Mage::helper('inventory')->__('Create Stock Request'));
        $this->_updateButton('add', 'onclick', 'setLocation(\'' . $this->getUrl('inventoryadmin/adminhtml_requeststock/new') . '\')');
    }

}
