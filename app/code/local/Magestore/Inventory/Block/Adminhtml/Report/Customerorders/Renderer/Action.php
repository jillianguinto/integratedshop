<?php
    class Magestore_Inventory_Block_Adminhtml_Report_Customerorders_Renderer_Action
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row) 
	{
        $orderItem = Mage::getModel('sales/order')->getCollection()
            ->addFieldToFilter('increment_id',$row->getIncrementId())
            ->getFirstItem();
        $orderId = $orderItem->getEntityId();
        $orderStatus = $orderItem->getStatus();
        if($row->getShippingProgress() == 2){
            $html = '<p><a href="'.$this->getUrl('adminhtml/sales_order/view',array('order_id'=>$orderId)).'">View</a></p>';
        }else{
            if($orderStatus == 'canceled' || $orderStatus == 'closed'){
              $html = '<p><a href="'.$this->getUrl('adminhtml/sales_order/view',array('order_id'=>$orderId)).'">View</a></p>';  
            }else{
            $html = '<p>
                        <a href="'.$this->getUrl('adminhtml/sales_order/view',array('order_id'=>$orderId)).'">View</a>
                            &nbsp;
                            /
                            &nbsp;
                        <a href="'.$this->getUrl('adminhtml/sales_order_shipment/new',array('order_id'=>$orderId)).'">Ship</a>
                    </p>
                        
                    ';
            }
        }
        return $html;
    }
}
?>
