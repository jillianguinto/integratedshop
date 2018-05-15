<?php
class Magestore_Inventory_Block_Adminhtml_Supplier_Renderertotalexcl extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $data = $row->getData();
        $total_amount =  $row->getData($this->getColumn()->getIndex());
        $purchaseOrderId = $data['purchase_order_id'];
        $tax_rate = Mage::helper('inventory/purchaseorder')->getDataByPurchaseOrderId($purchaseOrderId,'tax_rate');
        $totalexcl_curency = $total_amount/(1+($tax_rate/100));
        return Mage::helper('core')->currency($totalexcl_curency);
    }
}
?>