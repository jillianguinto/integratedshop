<?php

class Magestore_Inventory_Block_Adminhtml_Warehouse_Edit_Tab_Transaction extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('transaction_grid');
        $this->setDefaultSort('transaction_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    public function getWarehouse() {
        return Mage::getModel('inventory/warehouse')
                        ->load($this->getRequest()->getParam('id'));
    }

    public function _prepareCollection() {
        $arr = array();
        $warehouseId = $this->getRequest()->getParam('id');
        $warehouse = $this->getWarehouse();
        $warehouseName = $warehouse->getName();
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core/read');
        $installer = Mage::getModel('core/resource_setup');
        $sql = 'SELECT `type`,`transaction_id`,`from_name`,`to_name`,`from_id`,`to_id` FROM ' . $installer->getTable('inventory/transaction');
        $datas = $readConnection->fetchAll($sql);
        foreach ($datas as $data) {
            $type = $data['type'];
            if ($type == '1') {
                if ($data['from_id'] == $warehouseId) {
                    $arr[] = $data['transaction_id'];
                }
            } elseif ($type == '2') {
                if ($data['to_id'] == $warehouseId) {
                    $arr[] = $data['transaction_id'];
                }
            } elseif ($type == '3') {
                if ($data['to_id'] == $warehouseId) {
                    $arr[] = $data['transaction_id'];
                }
            } elseif ($type == '4') {
                if ($data['from_id'] == $warehouseId) {
                    $arr[] = $data['transaction_id'];
                }
            } elseif ($type == '5') {
                if ($data['from_id'] == $warehouseId) {
                    $arr[] = $data['transaction_id'];
                }
            } elseif ($type == '6') {
                if ($data['to_id'] == $warehouseId) {
                    $arr[] = $data['transaction_id'];
                }
            }
        }

        $collection = Mage::getResourceModel('inventory/transaction_collection')
                ->addFieldToFilter('transaction_id', array('in' => $arr));

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {

        $this->addColumn('transaction_id', array(
            'header' => Mage::helper('inventory')->__('ID'),
            'sortable' => true,
            'width' => '60px',
            'index' => 'transaction_id',
            'renderer' => 'inventory/adminhtml_warehouse_edit_tab_renderer_increment',
            'type' => 'number'
        ));

        $this->addColumn('transaction_type', array(
            'header' => Mage::helper('inventory')->__('Type'),
            'sortable' => true,
            'index' => 'type',
            'type' => 'options',
            'options' => Mage::helper('inventory')->getTransactionType()
        ));

        $this->addColumn('transaction_object', array(
            'header' => Mage::helper('inventory')->__('Sender/Receipient'),
            'sortable' => true,
            'renderer' => 'inventory/adminhtml_warehouse_edit_tab_renderer_transactionobject',
            'filter_condition_callback' => array($this, 'filterTransactionObject')
        ));

        $this->addColumn('total_products', array(
            'header' => Mage::helper('inventory')->__('Product Qty'),
            'width' => '80px',
            'index' => 'total_products',
            'type' => 'number',
            'default' => 0,
        ));

        $this->addColumn('created_at', array(
            'header' => Mage::helper('inventory')->__('Created At'),
            'width' => '80px',
            'index' => 'created_at',
            'type' => 'date',
            'filter_condition_callback' => array($this, 'filterCreatedAt')
        ));

        $this->addColumn('created_by', array(
            'header' => Mage::helper('inventory')->__('Created By'),
            'sortable' => true,
            'index' => 'created_by',
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('inventory')->__('Action'),
            'align' => 'left',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('inventory')->__('View'),
                    'url' => array('base' => '*/adminhtml_warehouse/viewTransaction',
                        'params' => array('warehouse_id' => $this->getRequest()->getParam('id'))
                    ),
                    'field' => 'transaction_id',
                ),
            ),
            'filter' => false,
            'sortable' => false,
            'is_system' => true,
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl() {
        return $this->getData('grid_url') ? $this->getData('grid_url') : $this->getUrl('*/*/transactionGrid', array('_current' => true, 'id' => $this->getRequest()->getParam('id')));
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/adminhtml_warehouse/viewTransaction', array('transaction_id' => $row->getTransactionId(), 'warehouse_id' => $this->getRequest()->getParam('id')));
    }

    public function filterTransactionObject($collection, $column) {
        $value = $column->getFilter()->getValue();
        $arr = array();

        foreach ($collection as $c) {
            $type = $c->getType();
            if ($type == '1') {
                if (strpos($c->getToName(), $value) !== false) {
                    $arr[] = $c->getId();
                }
            } elseif ($type == '2') {
                if (strpos($c->getFromName(), $value) !== false) {
                    $arr[] = $c->getId();
                }
            } elseif ($type == '3') {
                if (strpos($c->getToName(), $value) !== false) {
                    $arr[] = $c->getId();
                }
            } elseif ($type == '4') {
                if (strpos($c->getFromName(), $value) !== false) {
                    $arr[] = $c->getId();
                }
            } elseif ($type == '5') {
                if (strpos($c->getToName(), $value) !== false) {
                    $arr[] = $c->getId();
                }
            } elseif ($type == '6') {
                if (strpos($c->getFromName(), $value) !== false) {
                    $arr[] = $c->getId();
                }
            }
        }
        $tempCollection = Mage::getModel('inventory/transaction')->getCollection()
                ->addFieldToFilter('transaction_id', array('in' => $arr));
        $this->setCollection($tempCollection);
    }

    public function filterCreatedAt($collection, $column) {
        $value = $column->getFilter()->getValue();
        $from = $value['orig_from'];
        $to = $value['orig_to'];
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

}

?>
