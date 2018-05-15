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
class Magestore_Inventory_Block_Adminhtml_Report_Sales_Case2 extends Mage_Adminhtml_Block_Widget_Grid {

    public static function cmpAscRate($a, $b) {
        return $a->getSalesRate() > $b->getSalesRate();
    }

    public static function cmpDescRate($a, $b) {
        return $a->getSalesRate() < $b->getSalesRate();
    }

    public function __construct() {
        parent::__construct();
        $this->setId('warehouseGrid');
        $this->setDefaultSort('warehouse_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        if (!$this->getFilterData())
            $this->setFilterData(new Varien_Object());
        $this->setCountTotals(true);
    }

    protected function _prepareCollection() {
        $resource = Mage::getSingleton('core/resource');
        $filterData = $this->getFilterData();
        $dateFrom = $filterData->getData('date_from');
        $dateTo = $filterData->getData('date_to');

        $collection = Mage::getModel('inventory/inventoryshipment')->getCollection();
        $collection->getSelect()
                /* ->join(
                  array('order'=>$resource->getTableName('sales/order')),
                  "main_table.order_id = order.entity_id".
                  " and order.status='complete'",
                  array()) */
                ->join(
                        array('shipment_item' => $resource->getTableName('sales/shipment_item')), "main_table.shipment_id = shipment_item.parent_id" .
                        " and main_table.product_id = shipment_item.product_id" .
                        " and shipment_item.order_item_id = main_table.item_id", array('*')
                )
                ->join(
                        array('shipment' => $resource->getTableName('sales/shipment')), "main_table.shipment_id = shipment.entity_id" .
                        " and shipment.created_at between '$dateFrom' and '$dateTo'", array('*'))
                ->join(
                        array('warehouse' => $resource->getTableName('inventory/warehouse')), "main_table.warehouse_id = warehouse.warehouse_id", array('*')
                )
        ;
        $collection->getSelect()->group(array('warehouse.warehouse_id'));
        $collection->getSelect()->columns(array(
            'warehouse_name' => 'warehouse.name',
            'total_shipment' => 'SUM(shipment_item.qty)',
            'sales_total' => 'SUM(shipment_item.qty * shipment_item.price)'
        ));
        $collection->setIsGroupCountSql(true);
        if ($collection->getSize())
            $this->setCountTotals(true);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    public function prepareExport() {
        $this->_prepareColumns();
        $this->_prepareCollection();
        return $this;
    }

    protected function _afterLoadCollection() {
        $totalObj = new Magestore_Inventory_Model_Warehousesales_Totals();
        $this->setTotals($totalObj->countTotals($this));
    }

    protected function _prepareColumns() {

        $this->addColumn('warehouse_id', array(
            'header' => Mage::helper('inventory')->__('ID'),
            'align' => 'left',
            'type' => 'number',
            'index' => 'warehouse_id',
            'filter_index' => 'warehouse.warehouse_id',
            'totals_label' => Mage::helper('inventory')->__('Total'),
        ));

        $this->addColumn('warehouse_name', array(
            'header' => Mage::helper('inventory')->__('Warehouse'),
            'align' => 'left',
            'index' => 'warehouse_name',
            'filter_index' => 'warehouse.name'
        ));

        $this->addColumn('manager_email', array(
            'header' => Mage::helper('inventory')->__("Manager Email Address"),
            'align' => 'left',
            'index' => 'manager_email',
        ));

        $currencyCode = Mage::app()->getStore()->getBaseCurrency()->getCode();
        $this->addColumn('total_products', array(
            'header' => Mage::helper('inventory')->__('Total Qty Sold'),
            'align' => 'left',
            'index' => 'total_shipment',
            'filter_index' => 'SUM(shipment_item.qty)',
            'type' => 'number',
            'filter_condition_callback' => array($this, '_filterTotalProductsCallback'),
            'total' => 'sum'
        ));

        $this->addColumn('sales_total', array(
            'header' => Mage::helper('inventory')->__('Total Sales Amount'),
            'index' => 'sales_total',
            'align' => 'right',
            'type' => 'currency',
            'total' => 'sum',
            'filter_index' => 'SUM(shipment_item.qty * shipment_item.price)',
            'filter_condition_callback' => array($this, '_filterSalesTotalCallback'),
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
        ));

        $this->addColumn('sales_rate', array(
            'header' => Mage::helper('inventory')->__('Avg. Sales (item/day)'),
            'align' => 'right',
            'index' => 'sales_rate',
            'type' => 'number',
            'renderer' => 'inventory/adminhtml_report_sales_renderer_case3',
            'filter_condition_callback' => array($this, '_filterSalesRateCallback'),
        ));

        /* $this->addColumn('status', array(
          'header' => Mage::helper('inventory')->__('Status'),
          'align' => 'left',
          'width' => '80px',
          'index' => 'status',
          'type' => 'options',
          'options' => array(
          1 => 'Enabled',
          2 => 'Disabled',
          ),
          'filter' => false,
          'sortable' => false,
          )); */



        $this->addExportType('*/*/exportCase2Csv', Mage::helper('adminhtml')->__('CSV'));
        $this->addExportType('*/*/exportCase2Xml', Mage::helper('adminhtml')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    protected function _filterSalesTotalCallback($collection, $column) {
        $filter = $column->getFilter()->getValue();
        if (isset($filter['from']) && $filter['from']) {
            $collection->getSelect()->having('SUM(shipment_item.qty * shipment_item.price) >= ?', $filter['from']);
        }
        if (isset($filter['to']) && $filter['to']) {
            $collection->getSelect()->having('SUM(shipment_item.qty * shipment_item.price) <= ?', $filter['to']);
        }
    }

    protected function _filterTotalProductsCallback($collection, $column) {
        $filter = $column->getFilter()->getValue();
        if (isset($filter['from']) && $filter['from']) {
            $collection->getSelect()->having('SUM(shipment_item.qty) >= ?', $filter['from']);
        }
        if (isset($filter['to']) && $filter['to']) {
            $collection->getSelect()->having('SUM(shipment_item.qty) <= ?', $filter['to']);
        }
    }

    protected function _filterSalesRateCallback($collection, $column) {
        $filter = $column->getFilter()->getValue();
        $filterData = $this->getFilterData();
        foreach ($collection as $item) {
            $salesRate = Mage::helper('inventory/report')->getSalesRateValue($item, $filterData);
            $pass = TRUE;
            if (isset($filter['from']) && $filter['from'] >= 0) {
                if (floatval($salesRate) < floatval($filter['from'])) {
                    $pass = FALSE;
                }
            }
            if ($pass) {
                if (isset($filter['to']) && $filter['to'] >= 0) {
                    if (floatval($salesRate) > floatval($filter['to'])) {
                        $pass = FALSE;
                    }
                }
            }
            if ($pass) {
                $item->setSalesRate($salesRate);
                $arr[] = $item;
            }
        }
        $temp = Mage::helper('inventory/report')->_tempCollection(); // A blank collection 
        for ($i = 0; $i < count($arr); $i++) {
            $temp->addItem($arr[$i]);
        }
        $this->setCollection($temp);
    }

    protected function _setCollectionOrder($column) {
        $filterData = $this->getFilterData();
        $collection = $this->getCollection();
        if ($collection) {
            switch ($column->getId()) {
                case 'sales_rate':
                    $arr = array();
                    foreach ($collection as $item) {
                        $rate = Mage::helper('inventory/report')->getSalesRateValue($item, $filterData);
                        $item->setSalesRate($rate);
                        $arr[] = $item;
                    }
                    if ($column->getDir() == 'asc') {
                        $sorted = usort($arr, array('Magestore_Inventory_Block_Adminhtml_Report_Sales_Case3', 'cmpAscRate'));
                    } else {
                        $sorted = usort($arr, array('Magestore_Inventory_Block_Adminhtml_Report_Sales_Case3', 'cmpDescRate'));
                    }
                    $temp = Mage::helper('inventory/report')->_tempCollection(); // A blank collection 
                    for ($i = 0; $i < count($arr); $i++) {
                        $temp->addItem($arr[$i]);
                    }
                    $this->setCollection($temp);
                    break;
                default:
                    $filter = $column->getIndex();
                    if ($column->getFilterIndex())
                        $filter = $column->getFilterIndex();
                    if ($column->getDir() == 'asc') {
                        $collection->setOrder($filter, 'ASC');
                    } else {
                        $collection->setOrder($filter, 'DESC');
                    }
                    break;
            }
        }
    }

    public function getChildItems($item) {
        $collection = Mage::getModel('inventory/warehouse')->getCollection()
                ->addFieldToFilter('main_table.warehouse_id', array('neq' => $item->getId()))
                ->joinAdminUser()
                ->joinShipment();
        $filterData = $this->getFilterData();
        $period = $filterData->getData('period_type');
        $collection->getSelect()->columns(array('period' => 'date(shipment.created_at)'));


        $collection->getSelect()
                ->join(
                        array(
                    'warehouse_product' => $collection->getTable('inventory/warehouseproduct')
                        ), 'main_table.warehouse_id=warehouse_product.warehouse_id', array('total_products' => 'SUM(warehouse_product.qty)')
                )
        ;
        if ($period == 2) {
            $collection->addFieldToFilter('DATE_FORMAT(shipment.created_at, "%Y-%m")', array('eq' => $item->getPeriod()));
        } elseif ($period == 3) {
            $collection->addFieldToFilter('DATE_FORMAT(shipment.created_at, "%Y")', array('eq' => $item->getPeriod()));
        } else {
            $collection->addFieldToFilter('date(shipment.created_at)', array('eq' => $item->getPeriod()));
        }
        $collection->getSelect()->group('main_table.warehouse_id');
        return $collection;
    }

}