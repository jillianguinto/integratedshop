<?php 
class Magestore_Inventory_Block_Adminhtml_Report_Sales_Renderer_Case2
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row) 
	{
            $qty = 0;
            $total = 0;
            $warehouseId = $row->getId();
            $dateForm = $this->getRequest()->getParam('date_from');
            $dateTo = $this->getRequest()->getParam('date_to');
            $shipmentCollection = Mage::getModel('inventory/inventoryshipment')->getCollection()
                    ->addFieldToFilter('warehouse_id',$warehouseId);
            if($shipmentCollection->getSize()){
                $shipmentIds = array();
                foreach($shipmentCollection as $inventoryShipment){
                    $shipmentId= $inventoryShipment->getShipmentId();
                    $shipmentIds[] = $shipmentId;
                }
                $shipments = Mage::getModel('sales/order_shipment')->getCollection()
                        ->addFieldToFilter('entity_id', array('in'=>$shipmentIds))
                        ;
                if($dateForm)
                    $shipments->addFieldToFilter('date(created_at)', array('from'=>$dateForm));
                if($dateTo)
                    $shipments->addFieldToFilter('date(created_at)', array('to'=>$dateTo));
                $items = Mage::getModel('sales/order_shipment_item')->getCollection()
                        ->addFieldToFilter('parent_id', array('in'=>$shipments->getAllIds()))
                        ;
                
                if($items->getSize()){

                    foreach($items as $it){
                        $qty += $it->getQty();
                        $total += floatval($it->getQty()) * floatval($it->getPrice());
                    }

                }
            }
            $from = strtotime($dateForm);
            $to = strtotime($dateTo);
            $dateDiff = $to - $from;
            $fullDays = floor($dateDiff/(60*60*24));
            if($this->getColumn()->getId() == 'qty_sold'){
                if($qty == 0)
                    return '0';
                else return $qty;
            }elseif($this->getColumn()->getId() == 'sales_total')
                return Mage::helper('core')->currency($total);
            elseif($this->getColumn()->getId() == 'sales_rate'){
                if($fullDays){
                    return number_format($qty/$fullDays, 2);
                }
            }
            return '';
	}
}