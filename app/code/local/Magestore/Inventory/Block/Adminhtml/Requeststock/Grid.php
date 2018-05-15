<?php

class Magestore_Inventory_Block_Adminhtml_Requeststock_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('requeststockGrid');
        $this->setDefaultSort('request_stock_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('inventory/requeststock')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('request_stock_id', array(
            'header' => Mage::helper('inventory')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'request_stock_id',
            'renderer' => 'inventory/adminhtml_warehouse_edit_tab_renderer_increment'
        ));


        $this->addColumn('from_name', array(
            'header' => Mage::helper('inventory')->__('Source'),
            'align' => 'left',
            'type' => 'options',
            'width' => '350px',
            'options' => Mage::helper('inventory/warehouse')->getAllWarehouseRequeststockGird(),
            'index' => 'from_name',
            'filter_condition_callback' => array($this, 'filterWarehouseTo')
        ));

        $this->addColumn('to_name', array(
            'header' => Mage::helper('inventory')->__('Destination Warehouse'),
            'align' => 'left',
            'index' => 'to_name',
            'type' => 'options',
            'width' => '350px',
            'options' => Mage::helper('inventory/warehouse')->getWarehouseNames(),
        ));

        $this->addColumn('total_products', array(
            'header' => Mage::helper('inventory')->__('Qty requested'),
            'align' => 'right',
            'width' => '100px',
            'index' => 'total_products',
            'type' => 'number'
        ));

        $this->addColumn('created_at', array(
            'header' => Mage::helper('inventory')->__('Created On'),
            'align' => 'right',
            'width' => '50px',
            'type' => 'date',
            'index' => 'created_at',
            'filter_condition_callback' => array($this, 'filterCreatedOn')
        ));

        $this->addColumn('created_by', array(
            'header' => Mage::helper('inventory')->__('Created by'),
            'width' => '80px',
            'align' => 'left',
            'index' => 'created_by'
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('inventory')->__('Status'),
            'align' => 'left',
            'width' => '100px',
            'index' => 'status',
            'type' => 'options',
            'options' => array(
                1 => 'Complete',
                2 => 'Cancel'
            ),
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('sales')->__('Action'),
            'width' => '80px',
            'filter' => false,
            'align' => 'left',
            'sortable' => false,
            'index' => 'requeststock',
            'is_system' => true,
            'renderer' => 'inventory/adminhtml_requeststock_renderer_action'
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('inventory')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('inventory')->__('XML'));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    public function getGridUrl() {
        return $this->getUrl('*/*/grid');
    }

    public function filterCreatedOn($collection, $column) {
        return $this;
    }

    public function filterWarehouseTo($collection, $column) {
        $value = $column->getFilter()->getValue();

        if (!is_null(@$value)) {
            if ($value == 'Others') {
                $collection->getSelect()->where('main_table.from_name like ?', $value);
            } else {
                $collection->getSelect()->where('main_table.from_name like ?', $value);
            }
        }
        return $this;
    }

}