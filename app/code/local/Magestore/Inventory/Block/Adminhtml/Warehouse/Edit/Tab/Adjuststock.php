<?php

class Magestore_Inventory_Block_Adminhtml_Warehouse_Edit_Tab_Adjuststock extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('adjuststock_grid');
        $this->setDefaultSort('adjuststock_id');
        $this->setUseAjax(true);
    }

    public function getWarehouse() {
        return Mage::getModel('inventory/warehouse')->load($this->getRequest()->getParam('id'));
    }

    public function _prepareCollection() {
        $warehouse = $this->getWarehouse();
        $collection = Mage::getModel('inventory/adjuststock')->getCollection()
                ->addFieldToFilter('warehouse_id', $warehouse->getId());

        $filter = $this->getParam($this->getVarNameFilter(), null);
        $condorder = '';
        if ($filter) {
            $data = $this->helper('adminhtml')->prepareFilterString($filter);
            foreach ($data as $value => $key) {
                if ($value == 'created_at') {
                    $condorder = $key;
                }
            }
        }
        if ($condorder) {
            $condorder = Mage::helper('inventory')->filterDates($condorder, array('from', 'to'));
            $from = $condorder['from'];
            $to = $condorder['to'];
            if ($from) {
                $from = date('Y-m-d', strtotime($from));
                $collection->addFieldToFilter('created_at', array('gteq' => $from));
            }
            if ($to) {
                $to = date('Y-m-d', strtotime($to));
                $to .= ' 23:59:59';
                $collection->addFieldToFilter('created_at', array('lteq' => $to));
            }
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $warehouseId = $this->getRequest()->getParam('id');
        $this->addColumn('adjuststock_id', array(
            'header' => Mage::helper('inventory')->__('ID'),
            'sortable' => true,
            'width' => '40',
            'index' => 'adjuststock_id'
        ));

        $this->addColumn('create_by', array(
            'header' => Mage::helper('inventory')->__('Created By'),
            'width' => '150px',
            'index' => 'create_by'
        ));

        $this->addColumn('created_at', array(
            'header' => Mage::helper('inventory')->__('Created On'),
            'type' => 'date',
            'width' => '150px',
            'index' => 'created_at',
            'filter_condition_callback' => array($this, 'filterCreatedOn')
        ));

        $this->addColumn('warehouse_name', array(
            'header' => Mage::helper('inventory')->__('Warehouse Adjusted'),
            'width' => '150px',
            'index' => 'warehouse_name'
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('inventory')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('inventory')->__('View'),
                    'url' => array('base' => 'inventoryadmin/adminhtml_adjuststock/edit/warehouseback_id/' . $warehouseId),
                    'field' => 'id'
            )),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        return $this->getUrl('inventoryadmin/adminhtml_adjuststock/edit', array('id' => $row->getId(), 'warehouseback_id' => $this->getRequest()->getParam('id')));
    }

    public function getGridUrl() {
        return $this->getUrl('inventoryadmin/adminhtml_warehouse/adjuststockgrid', array('id' => $this->getRequest()->getParam('id')));
    }

    public function filterCreatedOn($collection, $column) {
        return $this;
    }

}

?>
