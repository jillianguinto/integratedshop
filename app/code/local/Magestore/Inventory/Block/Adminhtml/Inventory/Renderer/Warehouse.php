<?php

class Magestore_Inventory_Block_Adminhtml_Inventory_Renderer_Warehouse extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $product_id = $row->getId();
        $content = '';
        $totalWarehouse = 0;
        $warehouse_products = Mage::getModel('inventory/warehouseproduct')
                ->getCollection()
                ->addFieldToFilter('product_id', $product_id);
        $check = 0;
        $warehouseId = $this->getRequest()->getParam('warehouse');
        foreach ($warehouse_products as $warehouse_product) {
            $warehouse_id = $warehouse_product->getWarehouseId();
            if ($warehouseId && $warehouseId != $warehouse_id)
                continue;
            $totalWarehouse++;
            $url = Mage::helper('adminhtml')->getUrl('inventoryadmin/adminhtml_warehouse/edit', array('id' => $warehouse_id, 'inventory' => true));
            $warehouse = Mage::getModel('inventory/warehouse')
                    ->getCollection()
                    ->addFieldToFilter('warehouse_id', $warehouse_id)
                    ->getFirstItem();
            $name = $warehouse->getName();
            if (in_array(Mage::app()->getRequest()->getActionName(), array('exportCsv', 'exportXml'))) {
                if ($check)
                    $content.=', ' . $name .'(' . $warehouse_product->getQty() . '/' . $warehouse_product->getQtyAvailable() . ')';
                else
                    $content.=$name.'(' . $warehouse_product->getQty() . '/' . $warehouse_product->getQtyAvailable() . ')';
            }
            else
                $content .= "<a href=" . $url . ">$name</a>" . "<br/>" . '(' . $warehouse_product->getQty() . '/' . $warehouse_product->getQtyAvailable() . ')' . "<br/>";
            $check++;
        }
        if ($totalWarehouse > 5) {
            $contentScroll = '<div style="overflow-y:scroll; height: 110px;">' . $content . '</div>';
            return $contentScroll;
        }
        return $content;
    }

}

?>
