<?php

class Magestore_Inventory_Block_Adminhtml_Inventory_Renderer_AvailQty extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $product_id = $row->getEntityId();
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $installer = Mage::getModel('core/resource_setup');
        $sql = "SELECT `qty_available` FROM ".$installer->getTable('inventory/warehouseproduct').
            ' WHERE `product_id` = '.$product_id;
        $result = $readConnection->fetchAll($sql);
        $avail = 0;
        foreach($result as $r){
           $avail += $r['qty_available']; 
        }
        return $avail;
    }
}

?>
