<?php

class Unilab_CityDropdown_Block_Adminhtml_Citydropdown_Grid_Column_Renderer_Region extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
		$productId = $row->getData($this->getColumn()->getIndex());
		if ($region_id = $row->getData('region_id')) {
			 $region = Mage::getModel('directory/region')->load($region_id) ;
			return $region->getName();
		}
		return "";
	}
}