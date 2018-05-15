<?php

/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Inventory
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Inventory Grid Block
 * 
 * @category    Magestore
 * @package     Magestore_Inventory
 * @author      Magestore Developer
 */
class Magestore_Inventory_Block_Adminhtml_Report_Customerorders_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('sales_order_grid');
        //$this->setUseAjax(true);
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass() {
        return 'sales/order_grid_collection';
    }

    public function getWarehouseShipmentCollection() {
        $resource = Mage::getSingleton('core/resource');
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $collection->addAttributeToSelect('*');
        $collection->getSelect()
                ->joinLeft(array('order' => $resource->getTableName('sales/order')), 'main_table.entity_id=order.entity_id', array('shipping_progress' => 'shipping_progress'))
                ->joinLeft(
                        array('inventory_shipment' => $resource->getTableName('inventory/inventoryshipment')), 'main_table.entity_id=inventory_shipment.order_id', array('GROUP_CONCAT(DISTINCT inventory_shipment.warehouse_name) AS names')
                )
                ->group('main_table.entity_id')
        ;
        return $collection;
    }

    protected function _prepareCollection() {
        $collection = $this->getWarehouseShipmentCollection();
        $this->setCollection($collection);
        try {
            parent::_prepareCollection();
        } catch (Exception $e) {
            
        }
        return $this;
    }

    protected function _prepareColumns() {

        $this->addColumn('real_order_id', array(
            'header' => Mage::helper('sales')->__('Order #'),
            'width' => '80px',
            'align' => 'right',
            'type' => 'text',
            'index' => 'increment_id',
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header' => Mage::helper('sales')->__('Purchased From (Store)'),
                'index' => 'store_id',
                'type' => 'store',
                'align' => 'left',
                'store_view' => true,
                'display_deleted' => true,
            ));
        }

        $this->addColumn('created_at', array(
            'header' => Mage::helper('sales')->__('Purchased On'),
            'index' => 'created_at',
            'type' => 'datetime',
            'align' => 'right',
            'width' => '100px',
        ));

        $this->addColumn('billing_name', array(
            'header' => Mage::helper('sales')->__('Bill to Name'),
            'align' => 'left',
            'index' => 'billing_name',
        ));

        $this->addColumn('shipping_name', array(
            'header' => Mage::helper('sales')->__('Ship to Name'),
            'align' => 'left',
            'index' => 'shipping_name',
        ));

        $this->addColumn('base_grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Base)'),
            'index' => 'base_grand_total',
            'type' => 'currency',
            'align' => 'right',
            'currency' => 'base_currency_code',
        ));

        $this->addColumn('grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Purchased)'),
            'index' => 'grand_total',
            'type' => 'currency',
            'align' => 'right',
            'currency' => 'order_currency_code',
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('sales')->__('Status'),
            'index' => 'status',
            'type' => 'options',
            'align' => 'left',
            'width' => '70px',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
        ));

        $this->addColumn('shipping_progress', array(
            'header' => Mage::helper('sales')->__('Shipping Progress'),
            'width' => '70px',
            'type' => 'options',
            'align' => 'left',
            'options' => array(
                0 => 'Not ship',
                1 => 'Partial',
                2 => 'Complete',
            ),
            'sortable' => false,
            'index' => 'shipping_progress',
            'filter_index' => 'order.shipping_progress',
        ));

        $this->addColumn('names', array(
            'header' => Mage::helper('sales')->__('Warehouses Shipped'),
            'index' => 'names',
            'align' => 'left',
            'filter_index' => 'inventory_shipment.warehouse_name',
            'type' => 'options',
            'options' => Mage::helper('inventory/warehouse')->getAllWarehouseName(),
            'filter_condition_callback' => array($this, 'filterCallback'),
            'sortable' => false,
            'renderer' => 'inventory/adminhtml_report_customerorders_renderer_warehouse'
        ));

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            $this->addColumn('action', array(
                'header' => Mage::helper('sales')->__('Action'),
                'width' => '80px',
                'filter' => false,
                'align' => 'right',
                'sortable' => false,
                'index' => 'stores',
                'is_system' => true,
                'renderer' => 'inventory/adminhtml_report_customerorders_renderer_action'
            ));
        }
        $this->addRssList('rss/order/new', Mage::helper('sales')->__('New Order RSS'));

        $this->addExportType('*/*/exportCustomerOrdersCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('*/*/exportCustomerOrdersExcel', Mage::helper('sales')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            return $this->getUrl('adminhtml/sales_order/view', array('order_id' => $row->getId()));
        }
        return false;
    }

    public function getGridUrl() {
        return $this->getUrl('inventoryadmin/adminhtml_report/customerorders', array('_current' => true));
    }

    /**
     * Callback filter for Warehouse
     * 
     * @param type $collection
     * @param type $column
     * @return type
     */
    public function filterCallback($collection, $column) {
        $value = $column->getFilter()->getValue();
        if (!is_null(@$value)) {
            $collection->getSelect()->where('inventory_shipment.warehouse_id = ?', $value);
        }
        return $this;
    }

    public function getAllWarehouses() {
        $warehouses = array();
        $model = Mage::getModel('inventory/warehouse');
        $collection = $model->getCollection();
        foreach ($collection as $warehouse) {
            $warehouses[$warehouse->getName()] = $warehouse->getName();
        }
        return $warehouses;
    }

}