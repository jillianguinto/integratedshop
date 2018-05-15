<?php 
class Magestore_Inventory_Block_Adminhtml_Report_Sales_Renderer_Rate
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row) 
	{
            $qty = 0;
            $total = 0;
            $warehouseId = $this->getRequest()->getParam('warehouse_select');
            $dateForm = $this->getRequest()->getParam('date_from');
            $dateTo = $this->getRequest()->getParam('date_to');
            $itemCollection = Mage::getModel('sales/order_item')->getCollection()
                    ->addFieldToFilter('main_table.product_id',$row->getId());
            $itemCollection->getSelect()
                ->join(
                    array(
                        'warehouse_product'=>$itemCollection->getTable('inventory/warehouseproduct')
                    ), 
                    'main_table.product_id=warehouse_product.product_id AND warehouse_product.warehouse_id = '.$warehouseId, 
                    array()
                );
            if($dateForm)
                $itemCollection->addFieldToFilter('date(created_at)', array('from'=>$dateForm));
            if($dateTo)
                $itemCollection->addFieldToFilter('date(created_at)', array('to'=>$dateTo));
            $from = strtotime($dateForm);
            $to = strtotime($dateTo);
            $dateDiff = $to - $from;
            $fullDays = floor($dateDiff/(60*60*24));
            $qty = 0;
            foreach($itemCollection as $item){
                $qty += $item->getQtyOrdered();
            }
            if($fullDays)
                return number_format($qty/$fullDays, 2);
            return '';
	}
}