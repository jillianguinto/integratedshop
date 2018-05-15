<?php

class Magestore_Inventory_Block_Adminhtml_Stocktransfering_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {
    
    protected function _prepareLayout()
    {
        $url = $this->getUrl('inventory/adminhtml_stocktransfering/new');
        $this->setChild('continue_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Continue'),
                    'onclick'   => 'continueTransfer()',
                    'class'     => 'save'
                    ))
                );
        return parent::_prepareLayout();
    }
    /**
     * prepare tab form's information
     *
     * @return Magestore_Inventory_Block_Adminhtml_Stocktransfering_Edit_Tab_Form
     */
    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        if (Mage::getSingleton('adminhtml/session')->getStockTransferingData()) {
            $data = Mage::getSingleton('adminhtml/session')->getStockTransferingData();
            Mage::getSingleton('adminhtml/session')->setStockTransferingData(null);
        } elseif (Mage::registry('stocktransfering_data')) {
            $data = Mage::registry('stocktransfering_data')->getData();
        }
        $dataObject = new Varien_Object($data);
        $fieldset = $form->addFieldset('stocktransfering_form', array(
            'legend'=>Mage::helper('inventory')->__('Stock transfer information')
        ));
        $id = $this->getRequest()->getParam('id');
        $source = $this->getRequest()->getParam('source');
        $target = $this->getRequest()->getParam('target');
        $type = $this->getRequest()->getParam('type',1);
        $disabled = false;
        
        if($this->getRequest()->getParam('id'))
            $fieldset->addField('create_by', 'label', array(
                'label'        => Mage::helper('inventory')->__('Create by'),
            ));
        
        if($id || ($type && $source && $target)) $disabled = true;
        //add Field to form
        $fieldset->addField('type','select',array(
            'label'        => Mage::helper('inventory')->__('Type'),
            'class'        => 'required-entry',
            'name'         => 'type',
            'disabled'     => $disabled,
            'values'       => Mage::helper('inventory/stocktransfering')->getTypeOptions(),
            'after_element_html'    => '<script type="text/javascript">
                    Event.observe("type","change",function(){
                        if($("type").value == 2){
                            if($("stockissuing_form")){
                                $("stockissuing_form").hide();
                                $("stockissuing_form").previous().hide();
                            }
                        }else{
                            if($("stockissuing_form")){
                                $("stockissuing_form").show();
                                $("stockissuing_form").previous().show();
                            }
                        }
                        var url = "'.$this->getUrl('inventory/adminhtml_stocktransfering/loadWarehouse').'type/"+$("type").value;
                        var request = new Ajax.Request(url,{
                            onSuccess: function(transport){
                                
                                if($("type").value == 2){
                                    $("warehouse_to_id").innerHTML = transport.responseText;
                                    $("warehouse_from_id").innerHTML = \''.Mage::helper('inventory/warehouse')->getHtmlWarehouses().'\';
                                }else{
                                    $("warehouse_from_id").innerHTML = transport.responseText;
                                    $("warehouse_to_id").innerHTML = \''.Mage::helper('inventory/warehouse')->getHtmlWarehousesTarget().'\';
                                }
                            }
                        });
                    });
                    function continueTransfer(){
                        if($("warehouse_from_id").value != $("warehouse_to_id").value){
                            var url = "'.$this->getUrl('inventoryadmin/adminhtml_stocktransfering/new').'type/"+$("type").value+"/source/"+$("warehouse_from_id").value+"/target/"+$("warehouse_to_id").value;
                            window.location.href = url;
                        }else{
                            alert("Please select other target warehouse!");
                        }
                    }
                </script>'
        ));
        
        
        if($source && $target && $type){
            $sourceOptions = Mage::helper('inventory/warehouse')->getWarehouseByAdmin();
            $targetOptions = Mage::helper('inventory/warehouse')->getAllWarehouseTarget();
            if($type == 2){
            }
            if(!$this->getRequest()->getParam('id')){
                $fieldset->addField('warehouse_from_id','select',array(
                    'label'        => Mage::helper('inventory')->__('From Warehouse'),
                    'class'        => 'required-entry',
                    'name'         => 'warehouse_from_id',
                    'disabled'     => $disabled,
                    'values'       => $sourceOptions
                ));
                $fieldset->addField('warehouse_to_id','select',array(
                    'label'        => Mage::helper('inventory')->__('To Warehouse'),
                    'class'        => 'required-entry',
                    'name'         => 'warehouse_to_id',
                    'disabled'        => $disabled,
                    'values'       => $targetOptions,
                    'after_element_html'    => '
                        <input type="hidden" name="transfer_type" value="'.$type.'" />
                        <input type="hidden" name="warehouse_source" value="'.$source.'" />
                        <input type="hidden" name="warehouse_target" value="'.$target.'" />
                        <script type="text/javascript">
                        $("type").value = '.$type.';
                        $("warehouse_from_id").value='.$source.';
                        $("warehouse_to_id").value='.$target.';
                    </script>'
                ));
            }else{
                $fieldset->addField('warehouse_from_name','label',array(
                    'label'        => Mage::helper('inventory')->__('From Warehouse'),
                    'class'        => 'required-entry',
                    'name'         => 'warehouse_from_name',
                    'disabled'     => $disabled,
//                    'values'       => $sourceOptions
                ));
                $fieldset->addField('warehouse_to_name','label',array(
                    'label'        => Mage::helper('inventory')->__('To Warehouse'),
                    'class'        => 'required-entry',
                    'name'         => 'warehouse_to_name',
                    'disabled'        => $disabled,
//                    'values'       => $targetOptions,
                    'after_element_html'    => '
                        <input type="hidden" name="transfer_type" value="'.$type.'" />
                        <input type="hidden" name="warehouse_source" value="'.$source.'" />
                        <input type="hidden" name="warehouse_target" value="'.$target.'" />
                        <script type="text/javascript">
                        $("type").value = '.$type.';
                        $("warehouse_from_id").value='.$source.';
                        $("warehouse_to_id").value='.$target.';
                    </script>'
                ));
            }
            if(!$this->getRequest()->getParam('id'))
                $fieldset->addField('reason', 'editor', array(
                    'name'        => 'reason',
                    'label'        => Mage::helper('inventory')->__('Reason(s) for transfering stock'),
                    'title'        => Mage::helper('inventory')->__('Reason(s) for transfering stock'),
                    'style'        => 'width:274px; height:200px;',
                    'class'     => 'required-entry',
                    'required'  => true,
                    'wysiwyg'    => false,
                ));            
        }else{
            $adminOptions = Mage::helper('inventory/warehouse')->getWarehouseByAdmin();
            $allOptions = Mage::helper('inventory/warehouse')->getAllWarehouseTarget();
            if($this->getRequest()->getParam('id')){
                $sourceOptions = $adminOptions;
                $targetOptions = $allOptions;
            }else{
                $sourceOptions = $adminOptions;
                $targetOptions = $allOptions;
            }
            if($warehouse_id = $this->getRequest()->getParam('warehouse_id')){
                $fieldset->addField('warehouse_from_id','select',array(
                    'label'        => Mage::helper('inventory')->__('From Warehouse'),
                    'class'        => 'required-entry',
                    'name'         => 'warehouse_from_id',
                    'disabled'     => $disabled,
                    'values'       => $sourceOptions,
                    'after_element_html'    => '<script>$("warehouse_from_id").value='.$warehouse_id.';</script>'
                ));
            }else{
                if(!$this->getRequest()->getParam('id')){
                    $fieldset->addField('warehouse_from_id','select',array(
                        'label'        => Mage::helper('inventory')->__('From Warehouse'),
                        'class'        => 'required-entry',
                        'name'         => 'warehouse_from_id',
                        'disabled'     => $disabled,
                        'values'       => $sourceOptions,
                    ));
                }else{
                    $fieldset->addField('warehouse_from_name','label',array(
                        'label'        => Mage::helper('inventory')->__('From Warehouse'),
                        'class'        => 'required-entry',
                        'name'         => 'warehouse_from_name',
                        'disabled'     => $disabled,
//                        'values'       => $sourceOptions,
                    ));
                }
            }
            if(!$this->getRequest()->getParam('id')){
                $fieldset->addField('warehouse_to_id','select',array(
                    'label'        => Mage::helper('inventory')->__('To Warehouse'),
                    'class'        => 'required-entry',
                    'name'         => 'warehouse_to_id',
                    'disabled'        => $disabled,
                    'values'       => $targetOptions, 
                ));
            }else{
                $fieldset->addField('warehouse_to_name','label',array(
                    'label'        => Mage::helper('inventory')->__('To Warehouse'),
                    'class'        => 'required-entry',
                    'name'         => 'warehouse_to_name',
                    'disabled'        => $disabled,
//                    'values'       => $targetOptions, 
                ));
            }
            if(!$this->getRequest()->getParam('id')){
                $fieldset->addField('continue_button', 'note', array(
                    'text' => $this->getChildHtml('continue_button'),
                ));
            }else{
                $fieldset->addField('status','select',array(
                    'label'        => Mage::helper('inventory')->__('Status'),
                    'class'        => 'required-entry',
                    'name'         => 'warehouse_to_id',
                    'disabled'        => $disabled,
                    'values'       => Mage::helper('inventory/stocktransfering')->getStatusOptions()
                ));
                $fieldset->addField('create_at', 'note', array(
                    'label'        => Mage::helper('inventory')->__('Transfer Created On'),
                    'text' =>  Mage::helper('core')->formatDate($dataObject->getCreateAt(),'medium'),
                ));
             
                $fieldset->addField('reason', 'label', array(
                    'label'        => Mage::helper('inventory')->__('Reason(s) for transfering stock'),
                ));
             
            }
        }
        
        
        $transferstockId = $this->getRequest()->getParam('id');
        $warehouseSource = Mage::getModel('inventory/warehouse')->load($source);
        $can_transfer = Mage::helper('inventory/stocktransfering')->checkEditStocktransfering($transferstockId);
        if(($dataObject->getStatus() == 1 && $can_transfer==1) || (!$this->getRequest()->getParam('id') && $type && $source && $type==1 && $warehouseSource->getIsUnwarehouse()=='0')){
            
            $stockIssuingFieldset = $form->addFieldset('stockissuing_form', array(
                'legend'=>Mage::helper('inventory')->__('Stock issuing')
            ));


            $stockIssuingFieldset->addField('stockissuing_created_at', 'date', array(
                    'label'        => Mage::helper('inventory')->__('Create Stock Issuing On'),
                    'class'        => 'required-entry',
                    'name'          => 'stockissuing_created_at',
                    'format'        => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
                    'image' => $this->getSkinUrl('images/grid-cal.gif'),
                    'value' => 'stockissuing_created_at', 
                    'time' => false,
                    'required' => true
                ));

            $stockIssuingFieldset->addField('reference_invoice_number_issuing','text',array(
                'label'        => Mage::helper('inventory')->__('Reference Invoice Number'),
                'name'         => 'reference_invoice_number_issuing',
                'disabled'     => false,
            ));
            
            $stockIssuingFieldset->addField('comment','textarea',array(
                'label'        => Mage::helper('inventory')->__('Comment'),
                'name'         => 'comment',
                'disabled'     => false,
            ));
        }
        $canSaveAndApply = Mage::helper('inventory/stocktransfering')->canSaveAndApply($source, $target);
        if(($dataObject->getStatus()==2 && $can_transfer==1)){

            $stockIssuingFieldset = $form->addFieldset('stockreceiving_form', array(
                'legend'=>Mage::helper('inventory')->__('Stock Receiving')
            ));


            $stockIssuingFieldset->addField('stockreceiving_created_at', 'date', array(
                'label'        => Mage::helper('inventory')->__('Create Stock Receiving On'),
                'class'        => 'required-entry',
                'name'          => 'stockreceiving_created_at',
                'format'        => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
                'image' => $this->getSkinUrl('images/grid-cal.gif'),
                'value' => 'stockreceiving_created_at', 
                'time' => false,
                'required'    => true
            ));                       
            
            $stockIssuingFieldset->addField('reference_invoice_number_receiving','text',array(
                'label'        => Mage::helper('inventory')->__('Reference Invoice Number'),
                'name'         => 'reference_invoice_number_receiving',
                'disabled'     => false,
            ));
            
            $stockIssuingFieldset->addField('comment','textarea',array(
                'label'        => Mage::helper('inventory')->__('Comment'),
                'name'         => 'comment',
                'disabled'     => false,
            ));
        }
        
        
        $form->setValues($data);
        return parent::_prepareForm();
    }

}

?>
