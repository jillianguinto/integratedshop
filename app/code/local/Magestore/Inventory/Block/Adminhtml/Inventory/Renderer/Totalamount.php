<?php

class Magestore_Inventory_Block_Adminhtml_Inventory_Renderer_Totalamount extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $cost = $row->getCostPrice();
        $qty = $row->getQty();
        $total = $cost * $qty;
		if(in_array(Mage::app()->getRequest()->getActionName(),array('exportCsv','exportXml')))
		return '$'.$total;
			else
        return Mage::helper('core')->currency($total);
    }

}

?>
