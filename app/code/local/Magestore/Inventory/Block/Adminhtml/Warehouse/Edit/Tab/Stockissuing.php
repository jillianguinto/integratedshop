<?php

class Magestore_Inventory_Block_Adminhtml_Warehouse_Edit_Tab_Stockissuing extends Mage_Adminhtml_Block_Widget_Grid {
    public function __construct() {
        parent::__construct();
        $this->setId('stockissuing_grid');
        $this->setDefaultSort('stock_issuing_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
    }
    
    public function _addColumnFilterToCollection($column){
       if ($column->getId() == 'in_stockissuings') {
            $stockissuingIds = $this->_getSelectedStockissuing();
            if (empty($stockissuingIds)) {
                $stockissuingIds = array(0);
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('stockissuing_id', array('in'=>$stockissuingIds));
            } else {
                if($stockissuingIds) {
                    $this->getCollection()->addFieldToFilter('stockissuing_id', array('nin'=>$stockissuingIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;  
        
    }
    
    public function _getSelectedStockissuing(){
        $stockissuingIds = $this->getStockissuings();
		if(!is_array($stockissuingId)) {
			$stockissuingIds = array_keys($this->getSelectedStockissuing());
		}
		return $stockissuingIds;    
    }
    
    public function getSelectedStockissuing(){
        $stockissuings = array();
		$stockissuingIds = $this->getWarehouse()->getStockissuingIds();
		if(count($stockissuingIds)){
			foreach($stockissuingIds as $stockissuingId) {
				$stockissuings[$stockissuingId] = array('position'=>0);
			}
		}
		return $stockissuings;
    }
    
    public function getWarehouse(){
        return Mage::getModel('inventory/warehouse')
						->load($this->getRequest()->getParam('id'));
    }
    
    public function _prepareCollection(){
        $warehouse = $this->getWarehouse();
        $collection = Mage::getModel('inventory/stockissuing')->getCollection()
                ->addFieldToFilter('warehouse_id_from',$warehouse->getId());
        
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
		
		$this->addColumn('stock_issuing_id', array(
            'header'    => Mage::helper('inventory')->__('ID'),
            'sortable'  => true,
            'width'     => '60px',
            'index'     => 'stock_issuing_id',
            'renderer'  => 'inventory/adminhtml_warehouse_edit_tab_renderer_increment'
        ));
        
        $this->addColumn('stockissuing_type', array(
            'header'    => Mage::helper('inventory')->__('Type'),
            'sortable'  => true,
            'index'     => 'type',
            'type'      => 'options',
            'options'   => Mage::helper('inventory')->getStockissuingType()
        ));

        $this->addColumn('warehouse_to_name', array(
            'header'    => Mage::helper('inventory')->__('To Warehouse'),
            'sortable'  => true,
            'index'     => 'warehouse_to_name',
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
        /*$this->addColumn('stockissuing_status',array(
            'header' => Mage::helper('inventory')->__('Status'),
            'align'  => 'right',
            'width'  => '80px',
            'index'  => 'status',
            'type'      => 'options',
            'options'   => array(
              0 => 'Pending',
              1 => 'Processing',
              2 => 'Complete'
          ),
        ));*/
        
		return parent::_prepareColumns();
		
    }
    
    public function getGridUrl(){
        return $this->getData('grid_url')
            ? $this->getData('grid_url')
            : $this->getUrl('*/*/stockissuingGrid', array('_current'=>true,'id'=>$this->getRequest()->getParam('id')));
    }
    
    public function getRowUrl($row){
        return $this->getUrl('*/adminhtml_stockissuing/view', array('id' => $row->getStockIssuingId(),'warehouse_id'=>$this->getRequest()->getParam('id')));
    }
    
    public function filterCreatedOn($collection, $column)
    {
        return $this;
    }
}

?>
