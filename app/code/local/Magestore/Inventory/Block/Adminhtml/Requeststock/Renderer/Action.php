<?php
    class Magestore_Inventory_Block_Adminhtml_Requeststock_Renderer_Action
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row) 
    {
        $requeststockId = $row->getId();
        if(Mage::helper('inventory/requeststock')->checkCancelRequeststock($requeststockId))
            $html = '<a href="'.$this->getUrl('inventoryadmin/adminhtml_requeststock/edit',array('id'=>$requeststockId)).'">'.Mage::helper('inventory')->__('Edit').'</a>';
        else
            $html = '<a href="'.$this->getUrl('inventoryadmin/adminhtml_requeststock/edit',array('id'=>$requeststockId)).'">'.Mage::helper('inventory')->__('View').'</a>';
        return $html;
    }
}
?>
