<?php

class Magestore_Inventory_Block_Adminhtml_Sendstock_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareLayout() {
        $this->setChild('continue_button', $this->getLayout()->createBlock('adminhtml/widget_button')
                        ->setData(array(
                            'label' => Mage::helper('catalog')->__('Continue'),
                            'onclick' => 'continueTransfer()',
                            'class' => 'save'
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

        if (Mage::getSingleton('adminhtml/session')->getSendStockData()) {
            $data = Mage::getSingleton('adminhtml/session')->getSendStockData();
            Mage::getSingleton('adminhtml/session')->setSendStockData(null);
        } elseif (Mage::registry('sendstock_data')) {
            $data = Mage::registry('sendstock_data')->getData();
        }
        $dataObject = new Varien_Object($data);
        $fieldset = $form->addFieldset('sendstock_form', array(
            'legend' => Mage::helper('inventory')->__('Stock Sending Information')
                ));
        $id = $this->getRequest()->getParam('id');
        $source = $this->getRequest()->getParam('source');
        $target = $this->getRequest()->getParam('target');
        $disabled = false;
        if ($id || $source && $target)
            $disabled = true;
        //add Field to form
        $sourceOptions = Mage::helper('inventory/warehouse')->getWarehouseByAdmin();
        $targetOptions = Mage::helper('inventory/warehouse')->getAllWarehouseSendstockWithId();
        if (!$this->getRequest()->getParam('id')) {
            $warehouseId = $this->getRequest()->getParam('warehouse_id');
            if ($this->getRequest()->getParam('warehouse_id')) {
                $fieldset->addField('from_id', 'select', array(
                    'label' => Mage::helper('inventory')->__('Source Warehouse'),
                    'class' => 'required-entry',
                    'name' => 'from_id',
                    'disabled' => $disabled,
                    'values' => $sourceOptions,
                    'after_element_html' => '<script type="text/javascript">
                        $("from_id").value="' . $warehouseId . '";
                    </script>',
                    'note' => Mage::helper('inventory')->__('Stock will be sent from this warehouse to another warehouse or other destination.'),
                ));
            } else {
                $fieldset->addField('from_id', 'select', array(
                    'label' => Mage::helper('inventory')->__('Source Warehouse'),
                    'class' => 'required-entry',
                    'name' => 'from_id',
                    'disabled' => $disabled,
                    'values' => $sourceOptions,
                    'note' => Mage::helper('inventory')->__('Stock will be sent from this warehouse to another warehouse or other destination.'),
                ));
            }
            if ($source && $target) {
                $fieldset->addField('to_id', 'select', array(
                    'label' => Mage::helper('inventory')->__('Destination'),
                    'class' => 'required-entry',
                    'name' => 'to_id',
                    'disabled' => $disabled,
                    'values' => $targetOptions,
                    'after_element_html' => '
            <input type="hidden" name="warehouse_source" value="' . $source . '" />
            <input type="hidden" name="warehouse_target" value="' . $target . '" />    
            <script type="text/javascript">
                $("from_id").value="' . $source . '";
                $("to_id").value="' . $target . '";
                function continueTransfer(){
                        if($("from_id").value != $("to_id").value){
                            var url = "' . $this->getUrl('inventoryadmin/adminhtml_sendstock/new') . 'source/"+$("from_id").value+"/target/"+$("to_id").value;
                            window.location.href = url;
                        }else{
                            alert("Please select a different destination to send stock!");
                        }
                    }
                </script>'
                ));
                $fieldset->addField('reason', 'editor', array(
                    'name' => 'reason',
                    'label' => Mage::helper('inventory')->__('Reason(s) for sending stock'),
                    'title' => Mage::helper('inventory')->__('Reason(s) for sending stock'),
                    'style' => 'width:274px; height:200px;',
                    'class' => 'required-entry',
                    'required' => true,
                    'wysiwyg' => false,
                ));
            } else {
                $fieldset->addField('to_id', 'select', array(
                    'label' => Mage::helper('inventory')->__('Destination'),
                    'class' => 'required-entry',
                    'name' => 'to_id',
                    'disabled' => $disabled,
                    'values' => $targetOptions,
                    'after_element_html' => '<script type="text/javascript">
                function continueTransfer(){
                        if($("from_id").value != $("to_id").value){
                            var url = "' . $this->getUrl('inventoryadmin/adminhtml_sendstock/new') . 'source/"+$("from_id").value+"/target/"+$("to_id").value;
                            window.location.href = url;
                        }else{
                            alert("Please select a different destination to send stock!");
                        }
                    }
                </script>'
                ));
                $fieldset->addField('continue_button', 'note', array(
                    'text' => $this->getChildHtml('continue_button'),
                ));
            }
        } else {
            $fieldset->addField('from_name', 'label', array(
                'label' => Mage::helper('inventory')->__('From Warehouse'),
                'class' => 'required-entry',
                'name' => 'from_name',
            ));
            $fieldset->addField('to_name', 'label', array(
                'label' => Mage::helper('inventory')->__('To Warehouse'),
                'class' => 'required-entry',
                'name' => 'to_name',
            ));
            $fieldset->addField('reason', 'label', array(
                'name' => 'reason',
                'label' => Mage::helper('inventory')->__('Reason(s) for sending stock'),
                'title' => Mage::helper('inventory')->__('Reason(s) for sending stock'),
            ));
        }
        $form->setValues($data);
        return parent::_prepareForm();
    }

}

