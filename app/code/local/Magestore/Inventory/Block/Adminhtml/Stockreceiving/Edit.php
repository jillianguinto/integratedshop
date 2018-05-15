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
 * Inventory Edit Block
 * 
 * @category     Magestore
 * @package     Magestore_Inventory
 * @author      Magestore Developer
 */
class Magestore_Inventory_Block_Adminhtml_Stockreceiving_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        
        $this->_objectId = 'id';
        $this->_blockGroup = 'inventory';
        $this->_controller = 'adminhtml_stockreceiving';
        
        $warehouse_id = $this->getRequest()->getParam('warehouse_id');
        $this->_updateButton('back', 'onclick', 'setLocation(\''.$this->getUrl("inventoryadmin/adminhtml_warehouse/edit",array("id"=>$warehouse_id)).'\')');
        $this->_updateButton('save', 'label', Mage::helper('inventory')->__('Save Stock Receiving'));
        $this->_updateButton('delete', 'label', Mage::helper('inventory')->__('Delete Stock Receiving'));
        $this->_addButton('saveandcontinue', array(
            'label'        => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'    => 'saveAndContinueEdit()',
            'class'        => 'save',
        ), -100);
        
        
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('inventory_content') == null)
                    tinyMCE.execCommand('mceAddControl', false, 'inventory_content');
                else
                    tinyMCE.execCommand('mceRemoveControl', false, 'inventory_content');
            }
            
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
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
                xhr.open('POST', '" . $this->getUrl('inventoryadmin/adminhtml_stockreceiving/getImportCsv') . "');
                xhr.send(fd);
            }

            function uploadProgress(evt) {
                if (evt.lengthComputable) {
                    var percentComplete = Math.round(evt.loaded * 100 / evt.total);
                    document.getElementById('progressNumber').innerHTML = percentComplete.toString() + '%';
                    document.getElementById('prog').value = percentComplete;
                }
                else {
                    document.getElementById('progressNumber').innerHTML = 'unable to compute';
                }
            }

            function uploadComplete(evt) {
                $('stockreceiving_tabs_products_section').addClassName('notloaded');
                stockreceiving_tabsJsTabs.showTabContent($('stockreceiving_tabs_products_section'));
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
    public function getHeaderText()
    {
        if (Mage::registry('inventory_data')
            && Mage::registry('inventory_data')->getId()
        ) {
            return Mage::helper('inventory')->__("Edit Stock Receiving '%s'",
                                                $this->htmlEscape(Mage::registry('inventory_data')->getTitle())
            );
        }
        return Mage::helper('inventory')->__('Add Stock Receiving');
    }
}