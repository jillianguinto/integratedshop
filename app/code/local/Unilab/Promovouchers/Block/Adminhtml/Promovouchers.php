<?php
class Unilab_Promovouchers_Block_Adminhtml_Promovouchers extends Mage_Adminhtml_Block_Widget_Grid_Container

{

    public function __construct()

    {

        $this->_controller = 'adminhtml_promovouchers';

        $this->_blockGroup = 'promovouchers';

        $this->_headerText = Mage::helper('promovouchers')->__('Manage Promo Vouchers');

        $this->_addButtonLabel = Mage::helper('promovouchers')->__('Add Promo Voucher');
		
		$this->_addButton('importvoucher', array(
			'label'   => Mage::helper('promovouchers')->__('Import Promo Vouchers'),
			'onclick' => "setLocation('".$this->getUrl('promovouchers/adminhtml_importvoucher/index/')."')"
		));

        parent::__construct();

    }



}