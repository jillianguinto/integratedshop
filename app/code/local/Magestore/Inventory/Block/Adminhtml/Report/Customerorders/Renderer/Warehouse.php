<?php

class Magestore_Inventory_Block_Adminhtml_Report_Customerorders_Renderer_Warehouse extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $inventoryShipments = Mage::getModel('inventory/inventoryshipment')
            ->getCollection()
            ->addFieldToFilter('order_id', $row->getId())
        ;
        $inventoryShipments->getSelect()->group('warehouse_id');
        $html = '';
        $htmlExport = '';
        $whs = Mage::helper('inventory/warehouse')->getAllWarehouseName();
        if ($inventoryShipments->getSize() > 0) {
            foreach ($inventoryShipments as $inventoryShipment) {
                if($inventoryShipment->getWarehouseId() != 0){
                $html .= '<a href="' . $this->getUrl('inventoryadmin/adminhtml_warehouse/edit', array('id' => $inventoryShipment->getWarehouseId())) . '" >' . $whs[$inventoryShipment->getWarehouseId()] . '</a><br/>';
                $htmlExport .= $whs[$inventoryShipment->getWarehouseId()].',';
                }
            }
        } else {
            $html .= 'None';
        }
        $htmlExport = rtrim($htmlExport, ',');
        if(in_array(Mage::app()->getRequest()->getActionName(),array('exportCustomerOrdersCsv','exportCustomerOrdersExcel')))
            return $htmlExport;
        return $html;
    }

}