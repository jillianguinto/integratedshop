<?php

class Magestore_Inventory_Block_Adminhtml_Requeststock_Edit_Tabs extends
Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('requeststock_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('inventory')->__('Stock Requesting Information'));
    }

    protected function _beforeToHtml() {
        $id = $this->getRequest()->getParam('id');
        $source = $this->getRequest()->getParam('source');
        $target = $this->getRequest()->getParam('target');
        $this->addTab('form_section', array(
            'label' => Mage::helper('inventory')->__('General Information'),
            'title' => Mage::helper('inventory')->__('General Information'),
            'content' => $this->getLayout()
                    ->createBlock('inventory/adminhtml_requeststock_edit_tab_form')
                    ->toHtml(),
        ));
        if ($id || ($source && $target)) {
            $this->addTab('products_section', array(
                'label' => Mage::helper('inventory')->__('Products'),
                'title' => Mage::helper('inventory')->__('Products'),
                'url' => $this->getUrl('*/*/products', array('_current' => true, 'id' => $this->getRequest()->getParam('id'))),
                'class' => 'ajax',
            ));
        }

        return parent::_beforeToHtml();
    }

}

?>
