<?php

class Magestore_Inventory_Block_Adminhtml_Sendstock_Edit_Tabs extends
Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('sendstock_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('inventory')->__('Stock Sending Information'));
    }

    protected function _beforeToHtml() {
        $source = $this->getRequest()->getParam('source');
        $target = $this->getRequest()->getParam('target');
        if (!$this->getRequest()->getParam('id')) {
            $this->addTab('form_section', array(
                'label' => Mage::helper('inventory')->__('General Information'),
                'title' => Mage::helper('inventory')->__('General Information'),
                'content' => $this->getLayout()
                    ->createBlock('inventory/adminhtml_sendstock_edit_tab_form')
                    ->toHtml(),
            ));
            if ($source && $target) {
                $this->addTab('products_section', array(
                    'label' => Mage::helper('inventory')->__('Products'),
                    'title' => Mage::helper('inventory')->__('Products'),
                    'url' => $this->getUrl('*/*/products', array('_current' => true)),
                    'class' => 'ajax',
                ));
            }
        } else {
            $this->addTab('form_section', array(
                'label' => Mage::helper('inventory')->__('General Information'),
                'title' => Mage::helper('inventory')->__('General Information'),
                'content' => $this->getLayout()
                    ->createBlock('inventory/adminhtml_sendstock_edit_tab_form')
                    ->toHtml(),
            ));
            $this->addTab('products_section', array(
                'label' => Mage::helper('inventory')->__('Products'),
                'title' => Mage::helper('inventory')->__('Products'),
                'content' => $this->getLayout()
                    ->createBlock('inventory/adminhtml_sendstock_edit_tab_products')
                    ->toHtml(),
            ));
        }
        return parent::_beforeToHtml();
    }

}

?>
