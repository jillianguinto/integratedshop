<?php
 
class Unilab_Promovouchers_Block_Adminhtml_Promovouchers_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
 
    public function __construct()
    {
        parent::__construct();
        $this->setId('promovouchers_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('promovouchers')->__('Add Promo Vouchers'));
		
    }
 
    protected function _beforeToHtml()
    {	
        $this->addTab('promovouchers_form', array(
            'label'     => Mage::helper('promovouchers')->__('Promo Vouchers'),
            'title'     => Mage::helper('promovouchers')->__('Promo Vouchers'),
            'content'   => $this->getLayout()->createBlock('promovouchers/adminhtml_promovouchers_edit_tab_form')->toHtml(),
        ));
		

        return parent::_beforeToHtml();
    }
	
	
	
}