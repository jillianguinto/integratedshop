<?php
class Magestore_Inventory_Block_Adminhtml_Stockissuing_Customnew extends Mage_Core_Block_Template
{
    public function __construct()
    {
       
    }
    public function getWarehouseByAdmin(){
        $adminId = Mage::getSingleton('admin/session')->getUser()->getId();
        $warehouseIds = array();
        $collection = Mage::getModel('inventory/assignment')->getCollection()
                            ->addFieldToFilter('admin_id',$adminId)
                            ->addFieldToFilter('can_adjust',1);
        foreach($collection as $assignment){
            $warehouseIds[] = $assignment->getWarehouseId();
        }
        $warehouseCollection = Mage::getModel('inventory/warehouse')->getCollection()
                                    ->addFieldToFilter('warehouse_id',array('in'=>$warehouseIds));
        return $warehouseCollection;
    }
}