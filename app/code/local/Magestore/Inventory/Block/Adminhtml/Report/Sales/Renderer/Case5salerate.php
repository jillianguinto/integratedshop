<?php
class Magestore_Inventory_Block_Adminhtml_Report_Sales_Renderer_Case5salerate
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row) 
	{
        $filterData = Mage::registry('report_filter_data');
        $rate = Mage::helper('inventory/report')->getSalesRateValue($row, $filterData);
        return $rate;
    }
}
?>
