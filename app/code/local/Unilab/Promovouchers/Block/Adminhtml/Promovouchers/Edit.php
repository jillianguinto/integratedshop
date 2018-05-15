<?php
 
class Unilab_Promovouchers_Block_Adminhtml_Promovouchers_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
               
        $this->_objectId = 'id';
        $this->_blockGroup = 'promovouchers';
        $this->_controller = 'adminhtml_promovouchers';
        // $this->_addButton('sendemailblast', array(
            // 'label'   => Mage::helper('promovouchers')->__('Import Coupon Codes'),  
            //'onclick' => "setLocation('".$this->getUrl('eventreg/adminhtml_eventreg/sendemailblast/')."')"
            // 'onclick'   => "setLocation('".$this->getUrl("admincontroller/adminhtml_addcouponcode/index/id/$id")."')"
        // ));

		$this->_updateButton('save', 'label', Mage::helper('promovouchers')->__('Save Promo Voucher'));
    }
 
    public function getHeaderText()
    {

        if( Mage::registry('promovouchers_data') && Mage::registry('promovouchers_data')->getId() ) {
            return Mage::helper('promovouchers')->__("Edit Promo Voucher", $this->htmlEscape(Mage::registry('promovouchers_data')->getTitle()));
        } else {
            return Mage::helper('promovouchers')->__('Add New Promo Voucher');
        }
    }
}