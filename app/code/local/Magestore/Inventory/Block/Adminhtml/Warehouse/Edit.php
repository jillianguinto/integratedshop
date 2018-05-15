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
 * Warehouse Edit Block
 * 
 * @category     Magestore
 * @package     Magestore_Inventory
 * @author      Magestore Developer
 */
class Magestore_Inventory_Block_Adminhtml_Warehouse_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'inventory';
        $this->_controller = 'adminhtml_warehouse';

        $this->_updateButton('save', 'label', Mage::helper('inventory')->__('Save Warehouse'));
        if ($this->getRequest()->getParam('inventory')) {
            $this->_updateButton('back', 'onclick', 'backInventoryCheck()');
            $Inventoryurl = $this->getUrl('inventoryadmin/adminhtml_inventory/index');
        }
        $this->_updateButton('delete', 'label', Mage::helper('inventory')->__('Delete Warehouse'));
        $warehouseId = $this->getRequest()->getParam('id');
        $admin = Mage::getSingleton('admin/session')->getUser();
        if ($warehouseId && Mage::helper('inventory/warehouse')->canTransfer($admin->getId(), $warehouseId)) {
            $this->_addButton('send_stock', array(
                'label' => 'Send Stock',
                'onclick' => 'createSendstock()',
                'class' => 'add',
                    ), 0);

            $this->_addButton('request_stock', array(
                'label' => 'Request Stock',
                'onclick' => 'createRequeststock()',
                'class' => 'add',
                    ), 0);

            $this->_removeButton('reset');
        }
        if ($warehouseId && !Mage::helper('inventory/warehouse')->canEdit($admin->getId(), $warehouseId)) {
            $this->_removeButton('save');
            $this->_removeButton('delete');
            $this->_removeButton('reset');
        } else {
            $this->_addButton('saveandcontinue', array(
                'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
                'onclick' => 'saveAndContinueEdit()',
                'class' => 'save',
                    ), -100);
        }

        $this->_formScripts[] = "
            function backInventoryCheck(){
                window.location.href = '" . $Inventoryurl . "';
            }
            function toggleEditor() {
                if (tinyMCE.getInstanceById('inventory_content') == null)
                    tinyMCE.execCommand('mceAddControl', false, 'inventory_content');
                else
                    tinyMCE.execCommand('mceRemoveControl', false, 'inventory_content');
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
            
            function createSendstock(){
                var url = '" . $this->getUrl('inventoryadmin/adminhtml_sendstock/new', array('warehouse_id' => $this->getRequest()->getParam('id'))) . "';
                window.location.href = url;
            }
            
            function createRequeststock(){
                var url = '" . $this->getUrl('inventoryadmin/adminhtml_requeststock/new') . 'warehouse_id/' . $this->getRequest()->getParam('id') . "';
                window.location.href = url;
            }
        ";

        if ($this->getRequest()->getParam('loadptab')) {
            $this->_formScripts[] = "
                warehouse_tabsJsTabs.showTabContent($('warehouse_tabs_product_section'));
            ";
        }
    }

    /**
     * get text to show in header when edit an item
     *
     * @return string
     */
    public function getHeaderText() {
        if (Mage::registry('warehouse_data')
                && Mage::registry('warehouse_data')->getId()
        ) {
            return Mage::helper('inventory')->__("Edit Warehouse '%s'", $this->htmlEscape(Mage::registry('warehouse_data')->getName())
            );
        }
        return Mage::helper('inventory')->__('Add Warehouse');
    }

}