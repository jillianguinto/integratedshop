<?php

class Magestore_Inventory_Block_Adminhtml_Adjuststock_Listadjuststock_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('listadjuststockGrid');
        $this->setDefaultSort('adjuststock_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
    }

    /**
     * prepare collection for block to display
     *
     * @return Magestore_Inventory_Block_Adminhtml_Adjuststock_Grid
     */
    protected function _prepareCollection() {
        $collection = Mage::getModel('inventory/adjuststock')->getCollection();
        $resource = Mage::getSingleton('core/resource');
        $collection
                ->getSelect()
                ->join(array('warehouse' => $resource->getTableName("erp_inventory_warehouse")), "main_table.warehouse_id = warehouse.warehouse_id", array('warehouse.*'));

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

    /**
     * prepare columns for this grid
     *
     * @return Magestore_Inventory_Block_Adminhtml_Adjuststock_Grid
     */
    protected function _prepareColumns() {
        $this->addColumn('adjuststock_id', array(
            'header' => Mage::helper('inventory')->__('ID'),
            'sortable' => true,
            'width' => '60',
            'align' => 'right',
            'type' => 'number',
            'index' => 'adjuststock_id'
        ));

        $this->addColumn('created_at', array(
            'header' => Mage::helper('inventory')->__('Created on'),
            'type' => 'date',
            'width' => '150px',
            'align' => 'right',
            'index' => 'created_at',
            'filter_condition_callback' => array($this, 'filterCreatedOn')
        ));

        $this->addColumn('create_by', array(
            'header' => Mage::helper('inventory')->__('Created by'),
            'width' => '80px',
            'align' => 'left',
            'index' => 'create_by'
        ));

        $this->addColumn('warehouse_name', array(
            'header' => Mage::helper('inventory')->__('Adjusted Warehouse'),
            'width' => '150px',
            'align' => 'left',
            'index' => 'warehouse_name'
        ));


        $this->addColumn('warehouse_contact', array(
            'header' => Mage::helper('inventory')->__('Warehouse\'s Contact'),
            'width' => '150px',
            'align' => 'left',
            'index' => 'manager_name',
        ));

        $this->addColumn('warehouse_email', array(
            'header' => Mage::helper('inventory')->__('Warehouse\'s Email'),
            'width' => '150px',
            'align' => 'left',
            'index' => 'manager_email',
        ));

        $this->addColumn('warehouse_phone', array(
            'header' => Mage::helper('inventory')->__('Warehouse\'s Phone'),
            'width' => '150px',
            'align' => 'right',
            'index' => 'telephone',
        ));

        $this->addColumn('warehouse_country', array(
            'header' => Mage::helper('inventory')->__('Warehouse\'s Country'),
            'width' => '150px',
            'align' => 'left',
            'index' => 'country_id',
            'type' => 'options',
            'options' => Mage::helper('inventory')->getCountryList()
        ));


        $this->addColumn('action', array(
            'header' => Mage::helper('inventory')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getAdjuststockId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('inventory')->__('View'),
                    'url' => array('base' => '*/*/edit'),
                    'field' => 'id'
            )),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('inventory')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('inventory')->__('XML'));

        return parent::_prepareColumns();
    }

    /**
     * get url for each row in grid
     *
     * @return string
     */
    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getAdjuststockId()));
    }

    public function getGridUrl() {
        return $this->getUrl('*/*/grid');
    }

    public function filterCreatedOn($collection, $column) {
        return $this;
    }

}