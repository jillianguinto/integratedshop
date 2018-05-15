<?php

class Unilab_Inquiry_Block_Adminhtml_Inquiry_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'inquiry';
        $this->_controller = 'adminhtml_inquiry';

		/*
        $this->_updateButton('save', 'label', Mage::helper('inquiry')->__('Save Inquiry'));
        $this->_updateButton('delete', 'label', Mage::helper('inquiry')->__('Delete Inquiry'));

        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);
		*/
		$this->_removeButton('save');
		$this->_removeButton('saveandcontinue');
		//$this->_removeButton('delete');
		$this->_removeButton('reset');
		
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('inquiry_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'inquiry_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'inquiry_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('inquiry_data') && Mage::registry('inquiry_data')->getId() ) {
            return Mage::helper('inquiry')->__("View Inquiry From [<b>'%s'</b>]", uc_words(strtolower($this->htmlEscape(Mage::registry('inquiry_data')->getName()))));
        } else {
            return Mage::helper('inquiry')->__('Add Inquiry');
        }
    }
}
