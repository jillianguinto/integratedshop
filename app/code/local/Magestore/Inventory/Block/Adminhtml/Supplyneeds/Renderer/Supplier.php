<?php
class Magestore_Inventory_Block_Adminhtml_Supplyneeds_Renderer_Supplier
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row) 
    {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $installer = Mage::getModel('core/resource_setup');
        $product_id = $row->getProductId();
        $supplier_products = $readConnection->fetchAll("SELECT `supplier_id`,`cost` FROM `".$installer->getTable('inventory/supplierproduct')."` WHERE (product_id = $product_id)");
        $strings = array();
        foreach($supplier_products as $supplier_product){
            $supplier_id = $supplier_product['supplier_id'];
            $cost = (float) $supplier_product['cost'];
            $supplier_name = $readConnection->fetchAll("SELECT `name` FROM `".$installer->getTable('inventory/supplier')."` WHERE (supplier_id = $supplier_id)");
            $name = $supplier_name[0]['name'];
            $url = Mage::helper('adminhtml')->getUrl('inventoryadmin/adminhtml_supplier/edit',array('id'=>$supplier_id));
            $string = "<a href=".$url.">".$name."</a> - Cost price: $". round($cost,2);
            $strings[] = $string;
        }
        $supplier_string = implode('<br/>',$strings);
        return $supplier_string;
    }
}
?>
