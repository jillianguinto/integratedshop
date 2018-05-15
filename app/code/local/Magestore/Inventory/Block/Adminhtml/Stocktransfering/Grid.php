<?php

class Magestore_Inventory_Block_Adminhtml_Stocktransfering_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('warehousestocktransferingGrid');
        $this->setDefaultSort('transfer_stock_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('inventory/stocktransfering')->getCollection();
        
        $filter   = $this->getParam($this->getVarNameFilter(), null);
        $condorder = '';
        if($filter){
            $data = $this->helper('adminhtml')->prepareFilterString($filter);
            foreach($data as $value=>$key){
                if($value == 'create_at'){
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
                $collection->addFieldToFilter('create_at',array('gteq'=>$from));
            }
            if($to){
                $to = date('Y-m-d',strtotime($to));
                $to .= ' 23:59:59';
                $collection->addFieldToFilter('create_at',array('lteq'=>$to));
            }
        }   
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns(){
        $this->addColumn('transfer_stock_id', array(
            'header' => Mage::helper('inventory')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'transfer_stock_id',
            'renderer' => 'inventory/adminhtml_warehouse_edit_tab_renderer_increment'
        ));
        
        $this->addColumn('create_at',array(
            'header' => Mage::helper('inventory')->__('Created On'),
            'align'  => 'right',
            'width'  => '50px',
            'type'  => 'date',
            'index'  => 'create_at',
            'filter_condition_callback' => array($this, 'filterCreatedOn')
        ));
        
        $this->addColumn('create_by', array(
            'header' => Mage::helper('inventory')->__('Create by'),
            'width'     => '80px',
			'align' => 'left',
            'index' => 'create_by'
        ));
        
        $this->addColumn('warehouse_from_name',array(
            'header' => Mage::helper('inventory')->__('From Warehouse'),
            'align'  => 'left',
            'type'   => 'options', 
            'width'  => '350px',
            'options' => Mage::helper('inventory/warehouse')->getWarehouseNames(),
            'index'  => 'warehouse_from_name',
        ));
        
        $this->addColumn('warehouse_to_name',array(
            'header' => Mage::helper('inventory')->__('To Warehouse'),
            'align'  => 'left',
            'index'  => 'warehouse_to_name',
            'type'   => 'options',
            'width'  => '350px',
            'options' => Mage::helper('inventory/warehouse')->getWarehouseNames(),
        ));
        
        $this->addColumn('total_products',array(
            'header' => Mage::helper('inventory')->__('Total Qty'),
            'align'  => 'right',
            'width'  => '100px',
            'index'  => 'total_products',
            'type'   => 'number'
        ));
        
        $this->addColumn('type',array(
            'header' => Mage::helper('inventory')->__('Type'),
            'align' => 'left',
            'index'  => 'type',
            'type'   => 'options',
            'options' => array(
                1 => 'Issuing',
                2 => 'Receiving'
            )
        ));
        
        $this->addColumn('transfer_status',array(
            'header' => Mage::helper('inventory')->__('Status'),
            'align' => 'left',
            'width'  => '100px',
            'index'  => 'status',
            'type'      => 'options',
            'options'   => array(
              1 => 'Pending',
              2 => 'Processing',
              3 => 'Complete',
              4 => 'Cancel'
          ),
        ));
        
        $this->addColumn('action', array(
            'header' => Mage::helper('inventory')->__('Action'),
            'align'  => 'left',
            'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('inventory')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit/'),
                        'field'     => 'id',
                    ),
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stocktransfering',
                'is_system' => true,
        ));
        
		$this->addExportType('*/*/exportCsv', Mage::helper('inventory')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('inventory')->__('XML'));
		
        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
    
    public function getGridUrl(){
        return $this->getUrl('*/*/grid');
    }
    
    public function filterCreatedOn($collection, $column)
    {
        return $this;
    }
}
?>
