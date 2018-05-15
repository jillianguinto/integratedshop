<?php

class Magestore_Inventory_Block_Adminhtml_Warehouse_Edit_Tab_Stocktransfering extends
Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('stocktransferingGrid');
        $this->setDefaultSort('transfer_stock_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection() {
        $warehouseId = $this->getRequest()->getParam('id');
        $collection = Mage::getModel('inventory/stocktransfering')->getCollection()
            ->addFieldToFilter(
            array('warehouse_from_id', 'warehouse_to_id'), array(
            array('warehouse_from_id', 'eq' => $warehouseId),
            array('warehouse_to_id', 'eq' => $warehouseId)
            ));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('transfer_stock_id', array(
            'header' => Mage::helper('inventory')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'transfer_stock_id',
            'renderer' => 'inventory/adminhtml_warehouse_edit_tab_renderer_increment'
        ));

        $this->addColumn('warehouse_from_name', array(
            'header' => Mage::helper('inventory')->__('From Warehouse'),
            'align' => 'left',
            'type' => 'options',
            'width' => '350px',
            'options' => Mage::helper('inventory/warehouse')->getWarehouseNames(),
            'index' => 'warehouse_from_name',
        ));

        $this->addColumn('warehouse_to_name', array(
            'header' => Mage::helper('inventory')->__('To Warehouse'),
            'align' => 'left',
            'index' => 'warehouse_to_name',
            'type' => 'options',
            'width' => '350px',
            'options' => Mage::helper('inventory/warehouse')->getWarehouseNames(),
        ));

        $this->addColumn('total_products', array(
            'header' => Mage::helper('inventory')->__('Total Product'),
            'align' => 'right',
            'width' => '100px',
            'index' => 'total_products',
            'type' => 'number'
        ));

        $this->addColumn('transfer_status', array(
            'header' => Mage::helper('inventory')->__('Status'),
            'align' => 'right',
            'width' => '100px',
            'index' => 'status',
            'type' => 'options',
            'options' => array(
                1 => 'Pending',
                2 => 'Processing',
                3 => 'Complete'
            ),
        ));
        $warehouse_id = $this->getRequest()->getParam('id');
        $adminId = Mage::getSingleton('admin/session')->getUser()->getId();
        $adminCollection = Mage::getModel('inventory/assignment')->getCollection()
            ->addFieldToFilter('admin_id', $adminId)
            ->addFieldToFilter('warehouse_id', $warehouse_id);
        foreach ($adminCollection as $admin) {
            $can_transfer = $admin->getCanTransfer();
        }
        if ($can_transfer == 1) {
            $this->addColumn('action', array(
                'header' => Mage::helper('inventory')->__('Action'),
                'align' => 'left',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('inventory')->__('Edit'),
                        'url' => array('base' => '*/adminhtml_stocktransfering/edit/'),
                        'field' => 'id',
                    ),
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'stocktransfering',
                'is_system' => true,
            ));
        } else {
            $this->addColumn('action', array(
                'header' => Mage::helper('inventory')->__('Action'),
                'align' => 'left',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('inventory')->__('View'),
                        'url' => array('base' => '*/adminhtml_stocktransfering/view/'."warehouse_id/$warehouse_id"),
                        'field' => 'id',
                    ),
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'stocktransfering',
                'is_system' => true,
            ));
        }
        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        $adminId = Mage::getSingleton('admin/session')->getUser()->getId();
        $adminCollection = Mage::getModel('inventory/assignment')->getCollection()
            ->addFieldToFilter('admin_id', $adminId)
            ->addFieldToFilter('warehouse_id', $this->getRequest()->getParam('id'));
        foreach ($adminCollection as $admin) {
            $can_transfer = $admin->getCanTransfer();
        }
        if ($can_transfer == 1) {
            return $this->getUrl('*/adminhtml_stocktransfering/edit', array('id' => $row->getId()));
        } else {
            return $this->getUrl('*/adminhtml_stocktransfering/view', array('id' => $row->getId(),'warehouse_id'=>$this->getRequest()->getParam('id')));
        }
    }

    public function getGridUrl() {
        return $this->_getData('grid_url') ? $this->_getData('grid_url') : $this->getUrl('*/*/stocktransferingGrid', array('_current' => true, 'id' => $this->getRequest()->getParam('id')));
    }

    public function _getSelectedStockTransfering() {
        $stocktransferingIds = $this->getStocktransfering();
        if (!is_array($stocktransferingIds)) {
            $stocktransferingIds = array_keys($this->getSelectedStockTransfering());
        }
        return $stocktransferingIds;
    }

    public function getSelectedStockTransfering() {
        $stocktransfering = array();
        $stocktransferingIds = $this->getWarehouse()->getStockissuingIds();
        if (count($stocktransferingIds)) {
            foreach ($stocktransferingIds as $stocktransferingId) {
                $stocktransfering[$stocktransferingId] = array('position' => 0);
            }
        }
        return $stocktransfering;
    }

    public function getWarehouse() {
        return Mage::getModel('inventory/warehouse')
                ->load($this->getRequest()->getParam('id'));
    }

}

?>
