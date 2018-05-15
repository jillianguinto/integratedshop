<?php
class Unilab_Inquiry_Block_Adminhtml_Inquiry extends Mage_Adminhtml_Block_Widget_Grid_Container {
	public function __construct() {
		$this -> _controller = 'adminhtml_inquiry';
		$this -> _blockGroup = 'inquiry';
		$this -> _headerText = Mage::helper('inquiry') -> __('Inquiry Manager');
		parent::__construct();
		$this->_removeButton('add');
	}

}
