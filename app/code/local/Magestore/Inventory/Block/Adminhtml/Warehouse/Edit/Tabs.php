<?php

/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Inventory
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Warehouse Edit Tabs Block
 * 
 * @category    Magestore
 * @package     Magestore_Inventory
 * @author      Magestore Developer
 */
class Magestore_Inventory_Block_Adminhtml_Warehouse_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('warehouse_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('inventory')->__('Warehouse Information'));
    }

    /**
     * prepare before render block to html
     *
     * @return Magestore_Inventory_Block_Adminhtml_Inventory_Edit_Tabs
     */
    protected function _beforeToHtml() {
        $admin = Mage::getSingleton('admin/session')->getUser();
        $warehouse = Mage::registry('warehouse_data');
        $this->addTab('form_section', array(
            'label' => Mage::helper('inventory')->__('Warehouse Information'),
            'title' => Mage::helper('inventory')->__('Warehouse Information'),
            'content' => $this->getLayout()
                    ->createBlock('inventory/adminhtml_warehouse_edit_tab_form')
                    ->toHtml(),
        ));
        if ($this->getRequest()->getParam('id')) {
            $this->addTab('product_section', array(
                'label' => Mage::helper('inventory')->__('Products'),
                'title' => Mage::helper('inventory')->__('Products'),
                'url' => $this->getUrl('*/*/products', array('_current' => true, 'id' => $this->getRequest()->getParam('id'))),
                'class' => 'ajax',
            ));
        }

        if ($this->getRequest()->getParam('id')) {
            $this->addTab('customer_order', array(
                'label' => Mage::helper('inventory')->__('Customer Orders'),
                'title' => Mage::helper('inventory')->__('Customer Orders'),
                'content' => $this->getLayout()
                        ->createBlock('inventory/adminhtml_warehouse_edit_tab_customerorders')
                        ->toHtml(),
            ));

            $this->addTab('transaction_section', array(
                'label' => Mage::helper('inventory')->__('Transaction'),
                'title' => Mage::helper('inventory')->__('Transaction'),
                'url' => $this->getUrl('*/*/transaction', array('_current' => true, 'id' => $this->getRequest()->getParam('id'))),
                'class' => 'ajax',
            ));

            $this->addTab('adjuststock_section', array(
                'label' => Mage::helper('inventory')->__('Stock Adjustments'),
                'title' => Mage::helper('inventory')->__('Stock Adjustments'),
                'content' => $this->getLayout()
                        ->createBlock('inventory/adminhtml_warehouse_edit_tab_adjuststock')
                        ->toHtml(),
            ));
        }
        if ($this->getRequest()->getParam('id') == null || Mage::helper('inventory/warehouse')->canEdit($admin->getId(), $this->getRequest()->getParam('id'))) {
            $this->addTab('assignment_section', array(
                'label' => Mage::helper('inventory')->__('Permission'),
                'title' => Mage::helper('inventory')->__('Permission'),
                'content' => $this->getLayout()
                        ->createBlock('inventory/adminhtml_warehouse_edit_tab_assignment')
                        ->toHtml(),
            ));
        }
        if ($this->getRequest()->getParam('id')) {
            $this->addTab('history_section', array(
                'label' => Mage::helper('inventory')->__('Change History'),
                'title' => Mage::helper('inventory')->__('Change History'),
                'content' => $this->getLayout()
                        ->createBlock('inventory/adminhtml_warehouse_edit_tab_history')
                        ->toHtml(),
            ));
        }
        return parent::_beforeToHtml();
    }

}