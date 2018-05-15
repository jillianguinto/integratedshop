<?php
    class Magestore_Inventory_Block_Adminhtml_Sendstock_Renderer_Action
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row) 
    {
        $sendstockId = $row->getId();
        $canEdit = 0;
        $sendstock = Mage::getModel('inventory/sendstock')->getCollection()
                                    ->addFieldToFilter('send_stock_id',$sendstockId)
                                    ->getFirstItem();
        if($sendstock->getStatus() == 1)
            $canEdit = 1;
        if($canEdit == 1){
            $html = '<a href="'.$this->getUrl('inventoryadmin/adminhtml_sendstock/edit',array('id'=>$sendstockId)).'">'.Mage::helper('inventory')->__('Edit').'</a>';
        }else{
            $html = '<a href="'.$this->getUrl('inventoryadmin/adminhtml_sendstock/edit',array('id'=>$sendstockId)).'">'.Mage::helper('inventory')->__('View').'</a>';
        }
        return $html;
    }
}
?>
