<?php
    class Magestore_Inventory_Block_Adminhtml_Warehouse_Renderer_Action
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row) 
    {
        $warehouseId = $row->getId();
        $adminId = Mage::getModel('admin/session')->getUser()->getId();
        $canEdit = 0;
        $assignment = Mage::getModel('inventory/assignment')->getCollection()
                                    ->addFieldToFilter('warehouse_id',$warehouseId)
                                    ->addFieldToFilter('admin_id',$adminId)
                                    ->addFieldToFilter('can_edit_warehouse',1)
                                    ->getFirstItem();
        if($assignment->getId())
            $canEdit = 1;
        if($canEdit == 1){
            $html = '<a href="'.$this->getUrl('inventoryadmin/adminhtml_warehouse/edit',array('id'=>$warehouseId)).'">'.Mage::helper('inventory')->__('Edit').'</a>';
        }else{
            $html = '<a href="'.$this->getUrl('inventoryadmin/adminhtml_warehouse/edit',array('id'=>$warehouseId)).'">'.Mage::helper('inventory')->__('View').'</a>';
        }
        return $html;
    }
}
?>
