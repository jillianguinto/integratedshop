<?php

class Magestore_Inventory_Block_Adminhtml_Report_Inventoryproduct_Renderer_Causerecieved extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $product_id = $row->getId();
        $purchaseorder_products = Mage::getModel('inventory/purchaseorderproduct')
            ->getCollection()
            ->addFieldToFilter('product_id',$product_id);
        $content = '';
        if($purchaseorder_products->getFirstItem()->getData()){
            $content .= "<b> Purchase On </b> : <br/>";
            $content .= '<div style="max-height:70px;width:300px;border:1px solid #ccc;overflow:auto;margin-left:5px;">';
            foreach($purchaseorder_products as $purchaseorder_product){
                $qtyRecieved = $purchaseorder_product->getQtyRecieved();
                $purchaseOrderId = $purchaseorder_product->getPurchaseOrderId();
                if(!$qtyRecieved)$qtyRecieved = 0;
                if($purchaseOrderId){
                    $cost = $purchaseorder_product->getCost();
                    $tax = $purchaseorder_product->getTax();
                    $discount = $purchaseorder_product->getDiscount();
                    $purchaseModel = Mage::getModel('inventory/purchaseorder')
                        ->getCollection()
                        ->addFieldToFilter('purchase_order_id',$purchaseOrderId)
                        ->getFirstItem();
                    $purchaseOn = $purchaseModel->getPurchaseOn();
                    $purchaseOn = Mage::helper('core')->formatDate($purchaseOn, 'long', $showTime=false);
                    $amount = Mage::helper('core')->currency(($cost*(100+$tax-$discount)/100)*$qtyRecieved);
                    $content .= " $purchaseOn with $qtyRecieved products ⇔ $amount<br/> ";  
                }
            }
            $content .='</div>';
        }
        
        $resource = Mage::getSingleton('core/resource');
        $order_item_collection =    Mage::getResourceModel('sales/order_creditmemo_item_collection')
                 -> addAttributeToFilter('product_id',$product_id);
        $order_item_collection ->getSelect()
                    ->joinLeft(
                        array('creaditmemo' => $resource->getTableName('sales_flat_creditmemo')), 
                        'main_table.parent_id=creaditmemo.entity_id',
                        array('updated_at')
                    );
            $text = '';
            
            if($order_item_collection->getFirstItem()){
                foreach($order_item_collection as $order_item_product){
                    $qtyRefunded = $order_item_product->getQty()*1;
                    $refundedAt = $order_item_product->getUpdatedAt();
                    $refundedAt = Mage::helper('core')->formatDate($refundedAt, 'long', $showTime=false);
                    if($qtyRefunded != 0){
                        $text .= " $refundedAt with $qtyRefunded products<br/> ";  
                    }
                }
            }
            if($text != ''){
                $content .= "<b> Refunded </b> : <br/>";
                $content .= '<div style="max-height:70px;width:300px;border:1px solid #ccc;overflow:auto;margin-left:5px;">';
                $content .= $text;
                $content .='</div>';
            }
        
        return ' <label>'.$content.'</label>';
    }

}

?>
