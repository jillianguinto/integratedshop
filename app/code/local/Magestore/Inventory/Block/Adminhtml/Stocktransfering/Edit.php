<?php

class Magestore_Inventory_Block_Adminhtml_Stocktransfering_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'inventory';
        $this->_controller = 'adminhtml_stocktransfering';
        $type = $this->getRequest()->getParam('type');
        $source = $this->getRequest()->getParam('source');
        $target = $this->getRequest()->getParam('target');
        $transfer = Mage::getModel('inventory/stocktransfering')
                ->load($this->getRequest()->getParam('id'));
        $transferStatus = $transfer->getStatus();
        $transferstockId = $this->getRequest()->getParam('id');
        $can_transfer = Mage::helper('inventory/stocktransfering')->checkEditStocktransfering($transferstockId);
        $warehouseId = $this->getRequest()->getParam('warehouse_id');
        if($warehouseId)
            $this->_updateButton('back', 'onclick', 'setLocation(\''.$this->getUrl("inventoryadmin/adminhtml_warehouse/edit",array("id"=>$warehouseId)).'\')');
        if ($this->getRequest()->getParam('id')) {
            if ($transferStatus == 1 && $can_transfer) {
                $this->_addButton('checkandsave', array(
                    'label' => Mage::helper('adminhtml')->__('Create Stock Issuing'),
                    'onclick' => 'checkandsubmit()',
                    'class' => 'save',
                ));
                $this->_addButton('cancel', array(
                    'label' => Mage::helper('adminhtml')->__('Cancel'),
                    'onclick' => 'cancelStockTransfering()',
                    'class' => 'delete',
                        ), -100);
                $this->removeButton('save');
                $this->removeButton('delete');
                $this->removeButton('reset');
            } elseif ($transferStatus == 2 && $can_transfer == 1) {
                $this->_addButton('checkandsave', array(
                    'label' => Mage::helper('adminhtml')->__('Apply Transfer'),
                    'onclick' => 'checkandsubmit()',
                    'class' => 'save',
                ));
                $this->removeButton('save');
                $this->removeButton('delete');
                $this->removeButton('reset');
            } else {
                $this->removeButton('save');
                $this->removeButton('delete');
                $this->removeButton('reset');
            }
        } else {
            if ($type && $source && $target) {
                $this->_updateButton('back', 'onclick', 'setLocation(\''.$this->getUrl("inventoryadmin/adminhtml_stocktransfering/new").'\')');
                if (Mage::helper('inventory/stocktransfering')->canSaveAndApply($source, $target)) {
                    $this->_addButton('saveandapply', array(
                        'label' => Mage::helper('adminhtml')->__('Save And Apply'),
                        'onclick' => 'checkandsubmit(1)',
                        'class' => 'save',
                    ));
                }
                $this->removeButton('save');
                $this->_addButton('checkandsave', array(
                    'label' => Mage::helper('adminhtml')->__('Save Stock Transfer'),
                    'onclick' => 'checkandsubmit()',
                    'class' => 'save',
                ));
                $this->_updateButton('delete', 'label', Mage::helper('inventory')->__('Delete Stock Transfer'));
                $this->_addButton('saveandcontinue', array(
                    'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
                    'onclick' => 'saveAndContinueEditCheck()',
                    'class' => 'save',
                        ), -100);
            } else {
                $this->removeButton('save');
                $this->removeButton('delete');
                $this->removeButton('reset');
            }
        }
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('inventory_content') == null)
                    tinyMCE.execCommand('mceAddControl', false, 'inventory_content');
                else
                    tinyMCE.execCommand('mceRemoveControl', false, 'inventory_content');
            }

            function saveAndContinueEditCheck(){
                var checkProduct = checkProductQty();
                if((!checkProduct) || (checkProduct=='')){
                    alert('Please select product(s) and enter Qty greater than 0 to transfer stock!');
                    return false;
                }else{
                    var parameters = {products: checkProduct};
                    var check_product_url = '".$this->getUrl('inventoryadmin/adminhtml_stocktransfering/checkproduct')."';
                    var request = new Ajax.Request(check_product_url, {	
                        parameters: parameters,
                        onSuccess: function(transport) {
                            if(transport.status == 200)	{                                                                
                                var response = transport.responseText;  
                                if(response=='1'){
                                    editForm.submit($('edit_form').action+'back/edit/');
                                }else{
                                    alert('Please select product(s) and enter Qty greater than 0 to transfer stock!');
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
            
            function checkProductQty()
            {
                var stocktransfering_products = document.getElementsByName('stocktransfering_products');
                if(stocktransfering_products && stocktransfering_products != '' && stocktransfering_products[0]){                
                    return stocktransfering_products[0].value;
                }else{                    
                    return false;                    
                }
            }

            function checkandsubmit(i){
                if(i){
                    var action = '".$this->getUrl('inventoryadmin/adminhtml_stocktransfering/save',array('custom'=>1))."';
                    $('edit_form').action = action;
                }
                var className = $('stocktransfering_tabs_product_section').className;
                if(className == 'tab-item-link ajax notloaded'){
                    alert('Please enter the stock quantity to transfer!');
                    stocktransfering_tabsJsTabs.showTabContent($('stocktransfering_tabs_product_section'));
                }else{
                    editForm.submit();
                }
            }
            function cancelStockTransfering(){
                var url = '" . $this->getUrl('inventory/adminhtml_stocktransfering/cancel', array('id' => $this->getRequest()->getParam('id'))) . "';
                window.location.href = url;
            }

            function saveAndContinueEdit(){
                var className = $('stocktransfering_tabs_product_section').className;
                if(className == 'tab-item-link ajax notloaded'){
                    alert('Please enter the stock quantity to transfer!');
                    stocktransfering_tabsJsTabs.showTabContent($('stocktransfering_tabs_product_section'));
                }else{
                   editForm.submit($('edit_form').action+'back/edit/');
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
                xhr.open('POST', '" . $this->getUrl('inventoryadmin/adminhtml_stocktransfering/getImportCsv',array('source' => $source,'target'=>$target)) . "');
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
                $('stocktransfering_tabs_product_section').addClassName('notloaded');
                stocktransfering_tabsJsTabs.showTabContent($('stocktransfering_tabs_product_section'));
                //varienGlobalEvents.attachEventHandler('showTab',function(){ stocktransferingproductGridJsObject.doFilter(); });
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
        if (Mage::registry('stocktransfering_data') && Mage::registry('stocktransfering_data')->getId()
        ) {
            return Mage::helper('inventory')->__("Edit Stock Transfering '%s'", $this->htmlEscape(Mage::helper('inventory')->getIncrementId(Mage::registry('stocktransfering_data')))
            );
        }
        return Mage::helper('inventory')->__('Add New Stock Transfer');
    }

}

?>
