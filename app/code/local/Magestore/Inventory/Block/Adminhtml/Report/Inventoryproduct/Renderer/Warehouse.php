<?php

class Magestore_Inventory_Block_Adminhtml_Report_Inventoryproduct_Renderer_Warehouse extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $product_id = $row->getId();
        $costPrice = $row->getCostPrice();
        $warehouse_products = Mage::getModel('inventory/warehouseproduct')
            ->getCollection()
            ->addFieldToFilter('product_id',$product_id);
			$check=0;
        foreach($warehouse_products as $warehouse_product){
            $warehouse_id = $warehouse_product->getWarehouseId();
            $qtyInWarehouse = $warehouse_product->getQty()*1;
            if(!$qtyInWarehouse)$qtyInWarehouse = 0;
            $url = Mage::helper('adminhtml')->getUrl('inventory/adminhtml_warehouse/edit',array('id'=>$warehouse_id));
            $warehouse = Mage::getModel('inventory/warehouse')
                ->getCollection()
                ->addFieldToFilter('warehouse_id',$warehouse_id)
                ->getFirstItem();
            $name = $warehouse->getName();
			if(in_array(Mage::app()->getRequest()->getActionName(),array('exportCsvProductInfo','exportXmlProductInfo')))
			{
				$amount = '$'.$costPrice*$qtyInWarehouse;
				if($check)
				{
					$content .= ', '.$name.' : ( '.$qtyInWarehouse.' products ⇔ '.$amount.' )';
				}
				else
				{
					$content .= $name.' : ( '.$qtyInWarehouse.' products ⇔ '.$amount.' )';
				}
				$check++;
			}
			else
			{
				$amount = Mage::helper('core')->currency($costPrice*$qtyInWarehouse);
				$content .= "<b> $name : </b> ( $qtyInWarehouse products ⇔ $amount )<br/>";
			}
        }
		if(in_array(Mage::app()->getRequest()->getActionName(),array('exportCsvProductInfo','exportXmlProductInfo')))
		return $content;
		else
        return ' <label>'.$content.'</label>';
    }

}

?>
