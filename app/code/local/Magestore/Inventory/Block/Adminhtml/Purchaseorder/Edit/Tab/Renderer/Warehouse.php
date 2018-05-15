<?php 

class Magestore_Inventory_Block_Adminhtml_Purchaseorder_Edit_Tab_Renderer_Warehouse
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row) 
    {
        $columnName = $this->getColumn()->getName();
        $columnName = explode('_',$columnName);
        if($columnName[1]){
            $resource = Mage::getSingleton('core/resource');
            $readConnection = $resource->getConnection('core_read');
            $installer = Mage::getModel('core/resource_setup');
            $warehouseId = $columnName[1];
            $purchase_order_id = $this->getRequest()->getParam('id');
            if(!$this->getRequest()->getParam('id')){
                $producId = $row->getId();
            }else{
                $producId = $row->getProductId();
            }
            $sql = 'SELECT qty_order from '.$installer->getTable("erp_inventory_purchase_order_product_warehouse").' WHERE (purchase_order_id = '.$purchase_order_id.') AND (product_id = '.$producId.') AND (warehouse_id = '.$warehouseId.')';
            $results = $readConnection->fetchAll($sql);
            foreach($results as $result){
                echo $result['qty_order'];
            }
        }else{
            parent::render($row);
        }
    }
}