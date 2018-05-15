<?php

class Magestore_Inventory_Block_Adminhtml_Stocktransfering extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_stocktransfering';
        $this->_blockGroup = 'inventory';
        $this->_headerText = Mage::helper('inventory')->__('Stock Transfer Manager');
        $this->_addButtonLabel = Mage::helper('inventory')->__('Add New Stock Transfer');
        parent::__construct();
    }
    
    public function getTransferstockHistory($id)
    {
        return Mage::getModel('inventory/transferstockhistory')->load($id);
    }
    
    public function getTransferstockContentByHistoryId($id)
    {
        $collection = Mage::getModel('inventory/transferstockhistorycontent')
                                    ->getCollection()
                                    ->addFieldToFilter('transfer_stock_history_id',$id);
        return $collection;
    }
}
?>
