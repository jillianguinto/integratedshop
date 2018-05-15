<?php

class Magestore_Inventory_Block_Adminhtml_Warehouse_Edit_Tab_Stockreceiving extends Mage_Adminhtml_Block_Widget_Grid {
    public function __construct() {
        parent::__construct();
        $this->setId('stockreceiving_grid');
        $this->setDefaultSort('stock_receiving_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
    }
    
    public function _addColumnFilterToCollection($column){
       if ($column->getId() == 'in_stockreceivings') {
            $stockreceivingIds = $this->_getSelectedStockreceiving();
            if (empty($stockreceivingIds)) {
                $stockreceivingIds = array(0);
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('stockissuing_id', array('in'=>$stockreceivingIds));
            } else {
                if($stockreceivingIds) {
                    $this->getCollection()->addFieldToFilter('stockreceiving_id', array('nin'=>$stockreceivingIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;  
        
    }
    
    public function _getSelectedStockreceiving(){
        $stockreceivingIds = $this->getStockreceivings();
        if(!is_array($stockreceivingIds)) {
            $stockreceivingIds = array_keys($this->getSelectedStockreceiving());
        }
        return $stockreceivingIds;    
    }
    
    public function getSelectedStockreceiving(){
        $stockreceivings = array();
        $stockreceivingIds = $this->getWarehouse()->getStockreceivingIds();
        if(count($stockreceivingIds)){
            foreach($stockreceivingIds as $stockreceivingId) {
                $stockreceivings[$stockreceivingId] = array('position'=>0);
            }
        }
        return $stockreceivings;
    }
    
    public function getWarehouse(){
        return Mage::getModel('inventory/warehouse')
                        ->load($this->getRequest()->getParam('id'));
    }
    
    public function _prepareCollection(){
        $warehouse = $this->getWarehouse();
        $collection = Mage::getModel('inventory/stockreceiving')->getCollection()
                ->addFieldToFilter('warehouse_id_to',$warehouse->getId());
        
        $filter   = $this->getParam($this->getVarNameFilter(), null);
        $condorder = '';
        if($filter){
            $data = $this->helper('adminhtml')->prepareFilterString($filter);
            foreach($data as $value=>$key){
                if($value == 'created_at'){
                    $condorder = $key;
                }
            }
        }
        if($condorder){
            $condorder = Mage::helper('inventory')->filterDates($condorder,array('from','to'));
            $from = $condorder['from'];
            $to = $condorder['to'];
            if($from){
                $from = date('Y-m-d',strtotime($from));
                $collection->addFieldToFilter('created_at',array('gteq'=>$from));
            }
            if($to){
                $to = date('Y-m-d',strtotime($to));
                $to .= ' 23:59:59';
                $collection->addFieldToFilter('created_at',array('lteq'=>$to));
            }
        } 
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns(){
		$this->addColumn('stock_receiving_id', array(
            'header'    => Mage::helper('inventory')->__('ID'),
            'sortable'  => true,
            'width'     => '60px',
            'index'     => 'stock_receiving_id',
            'renderer'  => 'inventory/adminhtml_warehouse_edit_tab_renderer_increment'
        ));
        
        $this->addColumn('stockissuing_type', array(
            'header'    => Mage::helper('inventory')->__('Type'),
            'sortable'  => true,
            'index'     => 'type',
            'type'      => 'options',
            'options'   => array(
              1 => 'Stock Transferring',
              2 => 'Purchase Order',
              3 => 'Custom'
          ),
        ));
        
        $this->addColumn('warehouse_from_name', array(
            'header'    => Mage::helper('inventory')->__('From Warehouse'),
            'sortable'  => true,
            'index'     => 'warehouse_from_name',
            'type'   => 'options',
            'options' => Mage::helper('inventory/warehouse')->getWarehouseNames(),
        ));
        
        
        
        $this->addColumn('total_products', array(
            'header'    => Mage::helper('inventory')->__('Total Qty'),
            'width'     => '80px',
            'index'     => 'total_products',
            'type'      => 'number',
            'default'   => 0
        ));
		
        $this->addColumn('created_at', array(
            'header'    => Mage::helper('inventory')->__('Created On'),
            'sortable'  => true,
            'type'      =>'date',
            'index'     => 'created_at',
            'filter_condition_callback' => array($this, 'filterCreatedOn')
        ));
        
        $this->addColumn('comment', array(
            'header'    => Mage::helper('inventory')->__('Comment'),
            'sortable'  => true,
            'index'     => 'comment'
        ));

		return parent::_prepareColumns();
		
    }
    
    public function getGridUrl(){
        return $this->getData('grid_url')
            ? $this->getData('grid_url')
            : $this->getUrl('*/*/stockreceivingGrid', array('_current'=>true,'id'=>$this->getRequest()->getParam('id')));
    }
    
    public function getRowUrl($row){
        return $this->getUrl('*/adminhtml_stockreceiving/view', array('id' => $row->getStockReceivingId(),'warehouse_id'=>$this->getRequest()->getParam('id')));
    }
    
    public function filterCreatedOn($collection, $column)
    {
        return $this;
    }
}

?>
