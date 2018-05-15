<?php

class Unilab_CityDropdown_Block_Adminhtml_Citydropdown_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'citydropdown';
        $this->_controller = 'adminhtml_citydropdown';

	 
        $this->_updateButton('save', 'label', Mage::helper('citydropdown')->__('Save City'));
        $this->_updateButton('delete', 'label', Mage::helper('citydropdown')->__('Delete City'));

        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);		  
		
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('citydropdown_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'citydropdown_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'citydropdown_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('citydropdown_data') && Mage::registry('citydropdown_data')->getId() ) {
            return Mage::helper('citydropdown')->__("View City [<b>'%s'</b>]", uc_words(strtolower($this->htmlEscape(Mage::registry('citydropdown_data')->getName()))));
        } else {
            return Mage::helper('citydropdown')->__('Add City Dropdown');
        }
    }
}
