<?php
class Magestore_Inventory_Block_Adminhtml_Report_Sales_Renderer_Case5supplier
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row) 
	{
        $product_id = $row->getProductId();
        $supplier_product_collection = Mage::getModel('inventory/supplierproduct')->getCollection()
            ->addFieldToFilter('product_id',$product_id);
        $supplier_name = array();
        $supplier_model = Mage::getModel('inventory/supplier');
        foreach($supplier_product_collection as $collection){
            $supplier = $supplier_model->getCollection()
                ->addFieldToFilter('supplier_id',$collection->getSupplierId());
            foreach($supplier as $supp){
                array_push($supplier_name,$supp->getName());
            }
        }
        return implode(",", $supplier_name);
    }
}
?>
