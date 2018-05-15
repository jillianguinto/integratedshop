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
 * Inventory Adjust Stock Edit Block
 * 
 * @category     Magestore
 * @package     Magestore_Inventory
 * @author      Magestore Developer
 */
class Magestore_Inventory_Block_Adminhtml_Adjuststock_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'inventory';
        $this->_controller = 'adminhtml_adjuststock';
        $this->_updateButton('save', 'label', Mage::helper('inventory')->__('Save Stock Adjustment'));
        $this->_updateButton('save', 'onclick', 'saveAction()');
        $this->_updateButton('reset', 'onclick', 'setLocation(\'' . $this->getUrl('inventoryadmin/adminhtml_adjuststock/new') . '\')');
        if ($warehouseBack = $this->getRequest()->getParam('warehouseback_id'))
            $this->_updateButton('back', 'onclick', 'setLocation(\'' . $this->getUrl('inventoryadmin/adminhtml_warehouse/edit/id/' . $warehouseBack) . '\')');
        $this->removeButton('delete');
        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('adminhtml')->__('Save And View'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
                ), -100);
        if ($this->getRequest()->getParam('id')) {
            $this->removeButton('save');
            $this->removeButton('saveandcontinue');
            $this->removeButton('reset');
        }
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('inventory_content') == null)
                    tinyMCE.execCommand('mceAddControl', false, 'inventory_content');
                else
                    tinyMCE.execCommand('mceRemoveControl', false, 'inventory_content');
            }

             function saveAndContinueEdit(){
                var r=confirm('Are you sure you want to save this stock adjustment?');
                if (r==true){
                    editForm.submit($('edit_form').action+'back/edit/');
                }
            }
            
            function saveAction(){
                var r=confirm('Are you sure?');
                if (r==true){
                    editForm.submit();
                }
            }
            
            function fileSelected() {
                var file = document.getElementById('fileToUpload').files[0];
                if (file) {
                    var fileSize = 0;
                    if (file.size > 1024 * 1024)
                            fileSize = (Math.round(file.size * 100 / (1024 * 1024)) / 100).toString() + 'MB';
                    else
                            fileSize = (Math.round(file.size * 100 / 1024) / 100).toString() + 'KB';

                    document.getElementById('fileName').innerHTML = 'Name: ' + file.name;
                    document.getElementById('fileSize').innerHTML = 'Size: ' + fileSize;
                    document.getElementById('fileType').innerHTML = 'Type: ' + file.type;
                }
            }

            function uploadFile() {
                if(!$('fileToUpload') || !$('fileToUpload').value){
                    alert('Please choose CSV file to import!');return false;
                }
                var fd = new FormData();
                fd.append('fileToUpload', document.getElementById('fileToUpload').files[0]);
                fd.append('form_key', document.getElementById('form_key').value);
                var xhr = new XMLHttpRequest();
                xhr.upload.addEventListener('progress', uploadProgress, false);
                xhr.addEventListener('load', uploadComplete, false);
                xhr.addEventListener('error', uploadFailed, false);
                xhr.addEventListener('abort', uploadCanceled, false);
                xhr.open('POST', '" . $this->getUrl('inventoryadmin/adminhtml_adjuststock/importproduct') . "');
                xhr.send(fd);
            }

            function uploadProgress(evt) {

            }

            function uploadComplete(evt) {
                $('adjuststock_tabs_form_section').addClassName('notloaded');
                adjuststock_tabsJsTabs.showTabContent($('adjuststock_tabs_form_section'));
                //varienGlobalEvents.attachEventHandler('showTab',function(){ productGridJsObject.doFilter(); });
            }

            function uploadFailed(evt) {
                alert('There was an error attempting to upload the file.');
            }

            function uploadCanceled(evt) {
                alert('The upload has been canceled by the user or the browser dropped the connection.');
            }

        ";
    }

    /**
     * get text to show in header when edit an item
     *
     * @return string
     */
    public function getHeaderText() {
        if (Mage::registry('adjuststock_data')
                && Mage::registry('adjuststock_data')->getId()
        ) {
            return Mage::helper('inventory')->__("View Stock Adjustment No. '%s'", $this->htmlEscape(Mage::registry('adjuststock_data')->getId())
            );
        }
        return Mage::helper('inventory')->__('Add Stock Adjustment');
    }

}