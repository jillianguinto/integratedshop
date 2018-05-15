<?php 
class Magestore_Inventory_Block_Adminhtml_Warehouse_Edit_Tab_Renderer_Orderid
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row) 
	{
		$value =  $row->getData($this->getColumn()->getIndex());
        return '<a href="#">'.$value.'</a>';
	}
}