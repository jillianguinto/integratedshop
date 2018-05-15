<?php
class Magestore_Inventory_Block_Adminhtml_Stockreceiving_Customnew extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_stockreceiving';
        $this->_blockGroup = 'inventory';
        $this->_headerText = Mage::helper('inventory')->__('Custom Stock Receiving');
        $this->_addButtonLabel = Mage::helper('inventory')->__('Create New Custom Stock Receiving');
        parent::__construct();
        $this->setTemplate('inventory/stockreceiving/customnew.phtml');
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