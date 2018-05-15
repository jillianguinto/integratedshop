<?php

class Magestore_Inventory_Block_Adminhtml_Report_Inventoryproduct_Renderer_Causemoved extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $product_id = $row->getId();
        $returnorder_products = Mage::getModel('inventory/returnorderproduct')
            ->getCollection()
            ->addFieldToFilter('main_table.product_id',$product_id);
        $returnorder_products ->getSelect()
                    ->joinLeft(
                        array('returnorder' => $returnorder_products->getTable('inventory/returnorder')), 
                        'main_table.returned_order_id=returnorder.returned_order_id',
                        array('purchase_order_id')
                    )
                    ->joinLeft(
                        array('purchaseorderproduct' => $returnorder_products->getTable('inventory/purchaseorderproduct')), 
                        'returnorder.purchase_order_id=purchaseorderproduct.purchase_order_id AND main_table.product_id=purchaseorderproduct.product_id',
                        array('cost','tax','discount')
                    );
        $content = '';
        if($returnorder_products->getFirstItem()->getData()){
            $content .= "<b> Returned On </b> : <br/>";
            $content .= '<div style="max-height:70px;width:300px;border:1px solid #ccc;overflow:auto;margin-left:5px;">';
            foreach($returnorder_products as $returnorder_product){
                $qtyReturned = $returnorder_product->getQtyReturn();
                $returnOrderId = $returnorder_product->getReturnedOrderId();
                if(!$qtyReturned)$qtyReturned = 0;
                if($returnOrderId){
                    $cost = $returnorder_product->getCost();
                    $tax = $returnorder_product->getTax();
                    $discount = $returnorder_product->getDiscount();
                    $returnOrderModel = Mage::getModel('inventory/returnorder')
                        ->getCollection()
                        ->addFieldToFilter('returned_order_id',$returnOrderId)
                        ->getFirstItem();
                    $ReturnedOn = $returnOrderModel->getReturnedOn();
                    $ReturnedOn = Mage::helper('core')->formatDate($ReturnedOn, 'long', $showTime=false);
                    $amount = Mage::helper('core')->currency(($cost*(100+$tax-$discount)/100+$cost)*$qtyReturned);
                    $content .= " $ReturnedOn with $qtyReturned products ⇔ $amount<br/> ";  
                }
            }
            $content .='</div>';
        }
        
        $resource = Mage::getSingleton('core/resource');
        $order_item_collection =    Mage::getResourceModel('sales/order_shipment_item_collection')
                 -> addAttributeToFilter('product_id',$product_id);
        $order_item_collection ->getSelect()
                    ->joinLeft(
                        array('shipment' => $resource->getTableName('sales_flat_shipment')), 
                        'main_table.parent_id=shipment.entity_id',
                        array('updated_at')
                    );
            $text = '';
            
            if($order_item_collection->getFirstItem()){
                foreach($order_item_collection as $order_item_product){
                    $qtyShipped = $order_item_product->getQty()*1;
                    $shipeddAt = $order_item_product->getUpdatedAt();
                    $shipeddAt = Mage::helper('core')->formatDate($shipeddAt, 'long', $showTime=false);
                    if($qtyShipped != 0){
                        $text .= " $shipeddAt with  $qtyShipped products<br/> ";  
                    }
                }
            }
            if($text != ''){
                $content .= "<b> Shipped </b> : <br/>";
                $content .= '<div style="max-height:70px;width:300px;border:1px solid #ccc;overflow:auto;margin-left:5px;">';
                $content .= $text;
                $content .='</div>';
            }
        
        return ' <label>'.$content.'</label>';
    }

}

?>
