<?php
class Unilab_CityDropdown_Block_Adminhtml_Citydropdown extends Mage_Adminhtml_Block_Widget_Grid_Container {
	public function __construct() {
		$this -> _controller = 'adminhtml_citydropdown';
		$this -> _blockGroup = 'citydropdown';
		$this -> _headerText = Mage::helper('citydropdown') -> __('Manage Philippine City Lists');
		$this->_addButtonLabel = Mage::helper('citydropdown')->__('Add New City');
		parent::__construct(); 
	}
}
