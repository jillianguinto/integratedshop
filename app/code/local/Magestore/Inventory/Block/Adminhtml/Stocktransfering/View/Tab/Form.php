<?php

class Magestore_Inventory_Block_Adminhtml_Stocktransfering_View_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {
    
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
        
        $fieldset = $form->addFieldset('stocktransfering_form', array(
            'legend'=>Mage::helper('inventory')->__('Stock transfer information')
        ));
        $id = $this->getRequest()->getParam('id');
        $disabled = false;
        if($id) $disabled = true;
        //add Field to form
        $fieldset->addField('type','select',array(
            'label'        => Mage::helper('inventory')->__('Type'),
            'class'        => 'required-entry',
            'name'         => 'type',
            'disabled'     => $disabled,
            'values'       => array(
                1 => 'Transfer from my warehouses',
                2 => 'Transfer to my warehouses'
            ),
            'after_element_html'    => '<script type="text/javascript">
                    Event.observe("type","change",function(){
                        if($("type").value == 2){
                            $("stockissuing_form").hide();
                            $("stockissuing_form").previous().hide();
                        }else{
                            $("stockissuing_form").show();
                            $("stockissuing_form").previous().show();
                        }
                        var url = "'.$this->getUrl('inventory/adminhtml_stocktransfering/loadWarehouse').'type/"+$("type").value;
                        var request = new Ajax.Request(url,{
                            onSuccess: function(transport){
                                
                                if($("type").value == 2){
                                    $("warehouse_to_id").innerHTML = transport.responseText;
                                    $("warehouse_from_id").innerHTML = \''.Mage::helper('inventory/warehouse')->getHtmlWarehouses().'\';
                                }else{
                                    $("warehouse_from_id").innerHTML = transport.responseText;
                                    $("warehouse_to_id").innerHTML = \''.Mage::helper('inventory/warehouse')->getHtmlWarehouses().'\';
                                }
                            }
                        });
                    });
                    function continueTransfer(){
                        if($("warehouse_from_id").value != $("warehouse_to_id").value){
                            var url = "'.$this->getUrl('inventoryadmin/adminhtml_stocktransfering/new').'type/"+$("type").value+"/source/"+$("warehouse_from_id").value+"/target/"+$("warehouse_to_id").value;
                            window.location.href = url;
                        }else{
                            alert("Plese select other target warehouse!");
                        }
                    }
                </script>'
        ));
        
        $fieldset->addField('warehouse_from_id','select',array(
            'label'        => Mage::helper('inventory')->__('From Warehouse'),
            'class'        => 'required-entry',
            'name'         => 'warehouse_from_id',
            'disabled'     => $disabled,
            'values'       => Mage::helper('inventory/warehouse')->getWarehouseByAdmin()
        ));
        $source = $this->getRequest()->getParam('source');
        $target = $this->getRequest()->getParam('target');
        
        if($source && $target){
            $fieldset->addField('warehouse_to_id','select',array(
                'label'        => Mage::helper('inventory')->__('To Warehouse'),
                'class'        => 'required-entry',
                'name'         => 'warehouse_to_id',
                'disabled'        => $disabled,
                'values'       => Mage::helper('inventory/warehouse')->getAllWarehouseName(),
                'after_element_html'    => '<script type="text/javascript">
                    $("warehouse_from_id").value='.$source.';
                    $("warehouse_to_id").value='.$target.';
                </script>'
            ));
        }else{
            $fieldset->addField('warehouse_to_id','select',array(
                'label'        => Mage::helper('inventory')->__('To Warehouse'),
                'class'        => 'required-entry',
                'name'         => 'warehouse_to_id',
                'disabled'        => $disabled,
                'values'       => Mage::helper('inventory/warehouse')->getAllWarehouseName()
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
        }
        
        $form->setValues($data);
        return parent::_prepareForm();
    }

}

?>
