<?php

class Magestore_Inventory_Block_Adminhtml_Report_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();
        $this->_objectId = 'notice_id';
        $this->_blockGroup = 'inventory';
        $this->_controller = 'adminhtml_report';

        //$this->_updateButton('delete', 'label', Mage::helper('inventory')->__('Delete Notice'));
        $this->_removeButton('delete');
        $this->_removeButton('reset');
        $this->_removeButton('back');
        //$id = Mage::app()->getRequest()->getParam('id')
        $url = Mage::helper("adminhtml")->getUrl('inventoryadmin/adminhtml_report/notice');
        $this->_addButton('back', array(
            'label' => Mage::helper('adminhtml')->__('Back'),
            'onclick' => "window.location = '$url'",
            'class' => 'back',
                ), 0, -110);
        $this->_updateButton('save', 'label', Mage::helper('inventory')->__('Save Request'));

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('inventory_content') == null)
                    tinyMCE.execCommand('mceAddControl', false, 'inventory_content');
                else
                    tinyMCE.execCommand('mceRemoveControl', false, 'inventory_content');
            }
        ";
    }

    /**
     * get text to show in header when edit an item
     *
     * @return string
     */
    public function getHeaderText() {
        if (Mage::registry('notice_data')
                && Mage::registry('notice_data')->getNoticeId()
        ) {
            return Mage::helper('inventory')->__("Edit Backorder Request '%s'", $this->htmlEscape(Mage::registry('notice_data')->getNoticeId())
            );
        }
    }

}