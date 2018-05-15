<?php

class Magestore_Inventory_Block_Adminhtml_Requeststock_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

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

        if (Mage::getSingleton('adminhtml/session')->getRequestStockData()) {
            $data = Mage::getSingleton('adminhtml/session')->getRequestStockData();
            Mage::getSingleton('adminhtml/session')->getRequestStockData(null);
        } elseif (Mage::registry('requeststock_data')) {
            $data = Mage::registry('requeststock_data')->getData();
        }
        $dataObject = new Varien_Object($data);
        $fieldset = $form->addFieldset('requeststock_form', array(
            'legend' => Mage::helper('inventory')->__('Stock Request Information')
                ));
        $id = $this->getRequest()->getParam('id');
        $source = $this->getRequest()->getParam('source');
        $target = $this->getRequest()->getParam('target');
        $disabled = false;
        $disabledEdit = false;
        if ($id) {
            if ($data['from_id'])
                $source = $data['from_id'];
            elseif ($data['from_name'] == 'Others') {
                $source = 'others';
                $data['from_id'] = 'others';
            }
            $target = $data['to_id'];
            $disabledEdit = true;
        }
        if ($source && $target)
            $disabled = true;
        //add Field to form
        $sourceOptions = Mage::helper('inventory/warehouse')->getWarehouseByAdmin();
        $targetOptions = Mage::helper('inventory/warehouse')->getAllWarehouseRequeststock();

        if ($source && $target) {
            $fieldset->addField('warehouse_from_id', 'select', array(
                'label' => Mage::helper('inventory')->__('Source'),
                'class' => 'required-entry',
                'name' => 'warehouse_from_id',
                'disabled' => $disabled,
                'values' => $targetOptions
            ));
            $fieldset->addField('warehouse_to_id', 'select', array(
                'label' => Mage::helper('inventory')->__('Destination Warehouse'),
                'class' => 'required-entry',
                'name' => 'warehouse_to_id',
                'disabled' => $disabled,
                'values' => $sourceOptions,
                'after_element_html' => '
					<input type="hidden" name="warehouse_source" value="' . $source . '" />
					<input type="hidden" name="warehouse_target" value="' . $target . '" />
				<script type="text/javascript">
					if("' . $source . '" =="others")
						$("warehouse_from_id").value="others";
					else
						$("warehouse_from_id").value=' . $source . ';
                    $("warehouse_to_id").value=' . $target . ';
					function continueTransfer(){
							if($("warehouse_from_id").value != $("warehouse_to_id").value){
								var url = "' . $this->getUrl('inventoryadmin/adminhtml_requeststock/new') . 'source/"+$("warehouse_from_id").value+"/target/"+$("warehouse_to_id").value;
								window.location.href = url;
							}else{
								alert("Please select a different source to request stock!");
							}
						}
				</script>'
            ));
            $fieldset->addField('reason', 'editor', array(
                'name' => 'reason',
                'label' => Mage::helper('inventory')->__('Reason(s) for requesting stock'),
                'title' => Mage::helper('inventory')->__('Reason(s) for requesting stock'),
                'style' => 'width:274px; height:200px;',
                'class' => 'required-entry',
                'required' => true,
                'disabled' => $disabledEdit,
                'wysiwyg' => false,
            ));
        } else {
            $fieldset->addField('warehouse_from_id', 'select', array(
                'label' => Mage::helper('inventory')->__('Source'),
                'class' => 'required-entry',
                'name' => 'warehouse_from_id',
                'disabled' => $disabled,
                'values' => $targetOptions
            ));
            $warehouseId = $this->getRequest()->getParam('warehouse_id');
            $fieldset->addField('warehouse_to_id', 'select', array(
                'label' => Mage::helper('inventory')->__('Destination Warehouse'),
                'class' => 'required-entry',
                'name' => 'warehouse_to_id',
                'disabled' => $disabled,
                'values' => $sourceOptions,
                'after_element_html' => '
					<input type="hidden" name="warehouse_source" value="' . $source . '" />
					<input type="hidden" name="warehouse_target" value="' . $target . '" />
				<script type="text/javascript">
                    if("' . $warehouseId . '"){
                        $("warehouse_to_id").value="' . $warehouseId . '";
                    }
					function continueTransfer(){
                                            if($("warehouse_from_id").value != $("warehouse_to_id").value){
                                                var url = "' . $this->getUrl('inventoryadmin/adminhtml_requeststock/new') . 'source/"+$("warehouse_from_id").value+"/target/"+$("warehouse_to_id").value;
                                                window.location.href = url;
                                            }else{
                                                alert("Please select a different source to request stock!");
                                            }
                                        }
				</script>'
            ));

            $fieldset->addField('continue_button', 'note', array(
                'text' => $this->getChildHtml('continue_button'),
            ));
        }
        $form->setValues($data);
        return parent::_prepareForm();
    }

}

