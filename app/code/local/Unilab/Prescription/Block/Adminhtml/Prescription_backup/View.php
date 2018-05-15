<?php

class Unilab_Prescription_Block_Adminhtml_Prescription_View extends Mage_Adminhtml_Block_Widget_Form_Container
{ 
	 public function __construct()
    { 
        parent::__construct();

		$this->setTemplate('prescription/widget/form/container.phtml');
		
        $this->_objectId   = 'id';
        $this->_blockGroup = 'prescription';
        $this->_controller = 'adminhtml_prescription';
        $this->_mode	   = 'view';

        $this->removeButton('back');
        $this->removeButton('delete');
		
        $this->_updateButton('save', 'label', Mage::helper('prescription')->__('Save Prescription'));
       // $this->_updateButton('save', 'area', 'footer');		 

        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('prescription_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'prescription_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'prescription_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_order_item_prescription_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('sales_order_item_prescription') && Mage::registry('sales_order_item_prescription')->getId() ) {
            return Mage::helper('prescription')->__("Prescription '%s'", $this->htmlEscape(Mage::registry('sales_order_item_prescription')->getPtrNo()));
        } else {
            return Mage::helper('prescription')->__('Add Prescription');
        }
    }
}