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
class Magestore_Inventory_Block_Adminhtml_Report_Sales_Case3 extends Mage_Adminhtml_Block_Widget_Grid {

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
    }

    public function prepareExport() {
        $this->_prepareColumns();
        $this->_prepareCollection();
        return $this;
    }

    protected function _prepareCollection() {
        $resource = Mage::getSingleton('core/resource');
        $filterData = $this->getFilterData();
        $period = $filterData->getData('period_type');
        $dateFrom = $filterData->getData('date_from');
        $dateTo = $filterData->getData('date_to');

        $collection = Mage::getModel('inventory/inventoryshipment')->getCollection();
        $collection->getSelect()
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
        );
        if ($period == 2) {
            $collection->getSelect()->group(array('DATE_FORMAT(shipment.created_at, "%Y-%m")', 'warehouse.warehouse_id'));
        } elseif ($period == 3) {
            $collection->getSelect()->group(array('DATE_FORMAT(shipment.created_at, "%Y")', 'warehouse.warehouse_id'));
        } elseif ($period == 1) {
            $collection->getSelect()->group(array('date(shipment.created_at)', 'warehouse.warehouse_id'));
        } else {
            $collection->getSelect()->group(array('warehouse.warehouse_id'));
        }

        $collection->getSelect()->columns(array(
            'period' => 'date(shipment.created_at)',
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

    protected function _prepareCollectionBak() {
        $resource = Mage::getSingleton('core/resource');
        $filterData = $this->getFilterData();
        $period = $filterData->getData('period_type');
        $collection = Mage::getModel('inventory/shipment')->getCollection();
        $dateFrom = $filterData->getData('date_from');
        $dateTo = $filterData->getData('date_to');
        if ($dateFrom)
            $collection->addFieldToFilter('created_at', array('from' => $dateFrom));
        if ($dateTo)
            $collection->addFieldToFilter('created_at', array('to' => $dateTo));

        $collection->getSelect()
                ->join(
                        array('inventory_shipment' => $collection->getTable('inventory/inventoryshipment')), 'main_table.entity_id = inventory_shipment.shipment_id', array('*')
                )
                ->join(
                        array('warehouse' => $collection->getTable('inventory/warehouse')), 'inventory_shipment.warehouse_id = warehouse.warehouse_id', array('*')
                )
        ;

        $collection->getSelect()->columns(array('period' => 'date(main_table.created_at)'));

        $collection->getSelect()
                ->join(
                        array('shipment_item' => $resource->getTableName('sales_flat_shipment_item')), 'main_table.entity_id = shipment_item.parent_id', array('*')
                )
        ;
        if ($period == 2) {
            $collection->getSelect()->group(array('DATE_FORMAT(main_table.created_at, "%Y-%m")', 'warehouse.warehouse_id'));
        } elseif ($period == 3) {
            $collection->getSelect()->group(array('DATE_FORMAT(main_table.created_at, "%Y")', 'warehouse.warehouse_id'));
        } else {
            $collection->getSelect()->group(array('date(main_table.created_at)', 'warehouse.warehouse_id'));
        }
        $collection->getSelect()->columns(array(
            'warehouse_name' => 'warehouse.name',
            'total_shipment' => 'SUM(shipment_item.qty)',
        ));
        $collection->getSelect()->columns(array('sales_total' => 'SUM(shipment_item.qty * shipment_item.price)'));
        $collection->setIsGroupCountSql(true);
        if ($collection->getSize())
            $this->setCountTotals(true);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $currencyCode = Mage::app()->getStore()->getBaseCurrency()->getCode();
        $this->addColumn('period', array(
            'header' => Mage::helper('sales')->__('Period'),
            'index' => 'period',
            'width' => 100,
            'type' => 'date',
            'sortable' => false,
            'filter_index' => 'date(shipment.created_at)',
            'period_type' => $this->getPeriodType(),
            'renderer' => 'inventory/adminhtml_report_sales_grid_column_renderer_date',
            'totals_label' => Mage::helper('inventory')->__('Total'),
            'html_decorators' => array('nobr'),
        ));

        $this->addColumn('warehouse_name', array(
            'header' => Mage::helper('inventory')->__('Name'),
            'align' => 'left',
            'index' => 'warehouse_name',
            'filter_index' => 'warehouse.name'
        ));

        $this->addColumn('manager_email', array(
            'header' => Mage::helper('inventory')->__("Manager's Email"),
            'align' => 'left',
            'index' => 'manager_email',
        ));

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

        $this->addExportType('*/*/exportCase3Csv', Mage::helper('adminhtml')->__('CSV'));
        $this->addExportType('*/*/exportCase3Xml', Mage::helper('adminhtml')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    protected function _afterLoadCollection() {
        $totalObj = new Magestore_Inventory_Model_Warehousesales_Totals();
        $this->setTotals($totalObj->countTotals($this));
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

}