<?php

class Magestore_Inventory_Block_Adminhtml_Stocktransfering_Edit_Tab_History extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('transfer_stock_history_id');
        $this->setDefaultSort('transfer_stock_history_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * Retrieve collection class
     *
     * @return string
     */
  
    protected function _prepareCollection() {
        $transferstockId = $this->getRequest()->getParam('id');
        $collection = Mage::getModel('inventory/transferstockhistory')->getCollection()
                                    ->addFieldToFilter('transfer_stock_id',$transferstockId);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('transfer_stock_history_id', array(
            'header' => Mage::helper('inventory')->__('ID'),
            'width' => '80px',
            'type' => 'text',
            'index' => 'transfer_stock_history_id',
        ));
        
        $this->addColumn('create_by', array(
            'header' => Mage::helper('inventory')->__('Action Owner'),
            'type' => 'text',
            'index' => 'create_by',
        ));
        
        $this->addColumn('field_change', array(
            'header' => Mage::helper('catalog')->__('Changed field(s)'),
            'renderer' => 'inventory/adminhtml_stocktransfering_edit_tab_renderer_fieldchanged',
            'filter_index' => 'field_change',
            'filter_condition_callback' => array($this, 'filterCallback'),
        ));
        
        $this->addColumn('time_stamp', array(
            'header' => Mage::helper('inventory')->__('Time Stamp'),
            'index' => 'time_stamp',
            'type' => 'datetime',
            'width' => '150px',
        ));
        
        $this->addColumn('show_history', array(
            'header' => Mage::helper('inventory')->__('Action'),
            'width' => '80px',
            'type' => 'text',
            'filter' => false,
            'sortable' => false,
            'renderer' => 'inventory/adminhtml_stocktransfering_edit_tab_renderer_history'
        ));
        
        return parent::_prepareColumns();
    }
    
    public function filterCallback($collection, $column)
    {
        $value = $column->getFilter()->getValue();
        if (!is_null(@$value)) {
            if($id = $this->getRequest()->getParam('id')){
                $resource = Mage::getSingleton('core/resource');
                $readConnection = $resource->getConnection('core_read');
                $sql = 'SELECT distinct(`transfer_stock_history_id`) from ' . $resource->getTableName("erp_inventory_transfer_stock_history_content") . ' WHERE (field_name like \'%'.$value.'%\')';
                $results = $readConnection->fetchAll($sql);
                $transferstockHistoryIds = array();
                foreach ($results as $result) {
                    $transferstockHistoryIds[] = $result['transfer_stock_history_id'];
                }
            }
            $collection->addFieldToFilter('transfer_stock_history_id',array('in'=>$transferstockHistoryIds));
        }
        return $this;
    }
    
    public function getGridUrl() {
         return $this->getData('grid_url')
            ? $this->getData('grid_url')
            : $this->getUrl('*/*/historyGrid', array('_current'=>true,'id'=>$this->getRequest()->getParam('id')));
    }
}

?>
