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
 * Inventory Supplier Edit Block
 * 
 * @category     Magestore
 * @package     Magestore_Inventory
 * @author      Magestore Developer
 */
class Magestore_Inventory_Block_Adminhtml_Purchaseorder_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'inventory';
        $this->_controller = 'adminhtml_purchaseorder';

        $this->_updateButton('save', 'label', Mage::helper('inventory')->__('Save Order'));
//        $this->_updateButton('save', 'onclick', saveAndContinueEdit());
        $this->removeButton('delete');

        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEditCheck()',
            'class' => 'save',
                ), -100);

        if ($this->getRequest()->getParam('id')) {
            $this->_updateButton('saveandcontinue', 'onclick', 'saveAndContinueEdit()');

            $deliveryModel = Mage::getModel('inventory/delivery')->getCollection()->addFieldToFilter('purchase_order_id', $this->getRequest()->getParam('id'));
            $purchaseOrder = Mage::getModel('inventory/purchaseorder')->load($this->getRequest()->getParam('id'));
            $cancelDate = $purchaseOrder->getCancelDate();
            $canCancel = 0;
            if (strtotime($cancelDate) >= strtotime(now())) {
                $canCancel = 1;
            }
            if (!$deliveryModel->getFirstItem()->getData() && ($canCancel == '1')) {
                $this->_addButton('cancel_order_button', array(
                    'label' => Mage::helper('adminhtml')->__('Cancel Order'),
                    'onclick' => 'setLocation(\'' . $this->getUrl('*/*/cancelorder', array('_current' => true)) . '\')',
                    'class' => 'delete',
                        ), -100);
            }
            $this->_addButton('export_csv', array(
                'label' => Mage::helper('adminhtml')->__('Print'),
                'onclick' => 'setLocation(\'' . $this->getUrl('*/*/exportcsvpurchaseorder', array('_current' => true)) . '\')',
                    ), -100);
            $id = $this->getRequest()->getParam('id');
            $purchaseOrder = Mage::getModel('inventory/purchaseorder')->load($id);
            $isResendEmail = $purchaseOrder->getStatus();
            if ($isResendEmail == 1 || $isResendEmail == 3 || $isResendEmail == 4 || $isResendEmail == 5) {
                $this->_addButton('resend_email', array(
                    'label' => Mage::helper('adminhtml')->__('Resend email to supplier'),
                    'onclick' => 'setLocation(\'' . $this->getUrl('*/*/resendemailtosupplier', array('_current' => true)) . '\')',
                        ), -100);
            }
        } else {
            $this->_updateButton('save', 'onclick', 'saveCheck()');
        }

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('inventory_content') == null)
                    tinyMCE.execCommand('mceAddControl', false, 'inventory_content');
                else
                    tinyMCE.execCommand('mceRemoveControl', false, 'inventory_content');
            }
            function saveAndContinueEdit()
            {
                editForm.submit($('edit_form').action+'back/edit/');
            }
            
            function saveAndContinueEditCheck(){
                var checkProduct = checkProductQty();
                if((!checkProduct) || (checkProduct=='')){
                    alert('Please fill qty for product(s) and qty greater than 0 to purchase order!');
                    return false;
                }else{
                    var parameters = {products: checkProduct};
                    var check_product_url = '" . $this->getUrl('inventoryadmin/adminhtml_purchaseorder/checkproduct') . "';
                    var request = new Ajax.Request(check_product_url, {	
                        parameters: parameters,
                        onSuccess: function(transport) {
                            if(transport.status == 200)	{                                                                
                                var response = transport.responseText;  
                                if(response=='1'){
                                    editForm.submit($('edit_form').action+'back/edit/');
                                }else{
                                    alert('Please select product(s) and enter Qty greater than 0 to create purchase order!');
                                    return false;
                                }
                            }
                        },
                        onFailure: ''
                    });
                    return false;
                }
//                editForm.submit($('edit_form').action+'back/edit/');
            }
            
            function saveCheck(){
                var checkProduct = checkProductQty();
                if((!checkProduct) || (checkProduct=='')){
                    alert('Please fill qty for product(s) and qty greater than 0 to purchase order!');
                    return false;
                }else{
                    var parameters = {products: checkProduct};
                    var check_product_url = '" . $this->getUrl('inventoryadmin/adminhtml_purchaseorder/checkproduct') . "';
                    var request = new Ajax.Request(check_product_url, {	
                        parameters: parameters,
                        onSuccess: function(transport) {
                            if(transport.status == 200)	{                                                                
                                var response = transport.responseText;  
                                if(response=='1'){
                                    editForm.submit($('edit_form').action);
                                }else{
                                    alert('Please select product(s) and enter Qty greater than 0 to create purchase order!');
                                    return false;
                                }
                            }
                        },
                        onFailure: ''
                    });
                    return false;
                }
            }
            
            function checkProductQty()
            {
                var purchaseorder_products = document.getElementsByName('purchaseorder_products');
                if(purchaseorder_products && purchaseorder_products != '' && purchaseorder_products[0]){                
                    return purchaseorder_products[0].value;
                }else{                    
                    return false;                    
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
                xhr.open('POST', '" . $this->getUrl('inventoryadmin/adminhtml_purchaseorder/importproduct') . "');
                xhr.send(fd);
            }

            function uploadProgress(evt) {

            }

            function uploadComplete(evt) {
                $('purchaseorder_tabs_products_section').addClassName('notloaded');
                purchaseorder_tabsJsTabs.showTabContent($('purchaseorder_tabs_products_section'));
                //varienGlobalEvents.attachEventHandler('showTab',function(){ productGridJsObject.doFilter(); });
            }

            function uploadFailed(evt) {
                    alert('There was an error attempting to upload the file.');
            }

            function uploadCanceled(evt) {
                    alert('The upload has been canceled by the user or the browser dropped the connection.');
            }

            function createNewDeliveryOrder(){
                var url = '" . $this->getUrl('inventoryadmin/adminhtml_purchaseorder/newdelivery', array('purchaseorder_id' => $this->getRequest()->getParam('id'))) . "';
                window.location.href = url;
           }
        ";
    }

    /**
     * get text to show in header when edit an item
     *
     * @return string
     */
    public function getHeaderText() {
        if (Mage::registry('purchaseorder_data')
                && Mage::registry('purchaseorder_data')->getId()
        ) {
            return Mage::helper('inventory')->__("Edit Order No. '%s'", $this->htmlEscape(Mage::registry('purchaseorder_data')->getId())
            );
        }
        return Mage::helper('inventory')->__('Add New Purchase Order');
    }

}