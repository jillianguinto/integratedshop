<?php 
class Magestore_Inventory_Block_Adminhtml_Report_Sales_Renderer_Case3
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	
     public function render(Varien_Object $row){
        $filterData = Mage::registry('report_filter_data');
        if($this->getColumn()->getId() == 'sales_rate'){
            $rate = Mage::helper('inventory/report')->getSalesRateValue($row, $filterData);
            return $rate;
        }
        return ;
     }
}