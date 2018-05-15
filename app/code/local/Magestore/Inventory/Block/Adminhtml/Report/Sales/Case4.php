<?php

//Show report with warehouse and period
class Magestore_Inventory_Block_Adminhtml_Report_Sales_Case4 extends Mage_Adminhtml_Block_Widget_Grid {

    public static function cmpAscRate($a, $b) {
        return $a->getSalesRate() > $b->getSalesRate();
    }

    public static function cmpDescRate($a, $b) {
        return $a->getSalesRate() < $b->getSalesRate();
    }

    public function __construct() {
        parent::__construct();
        $this->setId('report_nonperiod_warehouse');
        $this->setDefaultSort('shipment.created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setCountTotals(true);
        if (!$this->getFilterData())
            $this->setFilterData(new Varien_Object());
    }

    protected function _getStore() {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _prepareCollection() {
        $filterData = $this->getFilterData();
        $period = $filterData->getData('period_type');
        $dateFrom = $filterData->getData('date_from');
        $dateTo = $filterData->getData('date_to');
        if ($filterData->getData('warehouse_select')) {
            $warehouseId = $filterData->getData('warehouse_select');
        } else {
            $warehouseId = 0;
        }
        $collection = Mage::getModel('inventory/inventoryshipment')->getCollection();
        $collection->getSelect()
                ->join(
                        array('order' => Mage::getModel('sales/order')
                    ->getCollection()->getTable('sales/order')), "main_table.order_id = order.entity_id" .
                        " and main_table.warehouse_id = $warehouseId", array())
                ->join(
                        array('shipmentitem' => Mage::getModel('sales/order_shipment_item')
                    ->getCollection()->getTable('sales/shipment_item')), "main_table.shipment_id = shipmentitem.parent_id" .
                        " and main_table.product_id = shipmentitem.product_id" .
                        " and shipmentitem.order_item_id = main_table.item_id", array('*')
                )
                ->join(
                        array('shipment' => Mage::getModel('sales/order_shipment')
                    ->getCollection()->getTable('sales/shipment')), "main_table.shipment_id = shipment.entity_id" .
                        " and shipment.created_at between '$dateFrom' and '$dateTo'", array('*'))
        ;
        $collection->getSelect()->columns(array('period' => 'date(shipment.created_at)'));
        if ($period == 2) {
            $collection->getSelect()->group(array('DATE_FORMAT(shipment.created_at, "%Y-%m")', 'main_table.product_id'));
        } elseif ($period == 3) {
            $collection->getSelect()->group(array('DATE_FORMAT(shipment.created_at, "%Y")', 'main_table.product_id'));
        } else {
            $collection->getSelect()->group(array('date(shipment.created_at)', 'main_table.product_id'));
        }
        $collection->getSelect()->columns(array('total_shipment' => 'SUM(qty)'));
        $collection->getSelect()->columns(array('total_amount' => 'SUM(shipmentitem.qty * shipmentitem.price)'));
        $collection->setIsGroupCountSql(true);
        if ($collection->getSize())
            $this->setCountTotals(true);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _afterLoadCollection() {
        $totalObj = new Magestore_Inventory_Model_Warehousesales_Totals();
        $this->setTotals($totalObj->countTotals($this));
    }

    protected function _prepareColumns() {
        $this->addColumn('period', array(
            'header' => Mage::helper('inventory')->__('Period'),
            'type' => 'date',
            'index' => 'period',
            'align' => 'right',
            'width' => '150px',
            'sortable' => false,
            'filter_index' => 'date(shipment.created_at)',
            'period_type' => $this->getPeriodType(),
            'renderer' => 'inventory/adminhtml_report_sales_grid_column_renderer_date',
            'totals_label' => Mage::helper('adminhtml')->__('Total'),
            'html_decorators' => array('nobr'),
        ));
        $this->addColumn('product_name', array(
            'header' => Mage::helper('inventory')->__('Product Name'),
            'index' => 'name',
            'align' => 'left',
            'width' => '650px',
        ));
        $this->addColumn('product_sku', array(
            'header' => Mage::helper('inventory')->__('SKU'),
            'index' => 'sku',
            'align' => 'left',
            'width' => '150px',
        ));

        if (!$this->_isExport) {
            $this->addColumn('product_image', array(
                'header' => Mage::helper('catalog')->__('Image'),
                'width' => '90px',
                'index' => 'product_image',
                'filter' => false,
                'renderer' => 'inventory/adminhtml_renderer_productimage'
            ));
        }

        $this->addColumn('product_price', array(
            'header' => Mage::helper('inventory')->__('Price'),
            'index' => 'price',
            'align' => 'right',
            'width' => '150px',
            'type' => 'currency',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
        ));
        $this->addColumn('sold_qty', array(
            'header' => Mage::helper('inventory')->__('Qty Sold'),
            'index' => 'total_shipment',
            'align' => 'right',
            'type' => 'number',
            'filter_index' => 'SUM(qty)',
            'filter_condition_callback' => array($this, '_filterSoldQtyCallback'),
            'total' => 'sum'
        ));
        $this->addColumn('total_amount', array(
            'header' => Mage::helper('inventory')->__('Sales Amount'),
            'index' => 'total_amount',
            'align' => 'right',
            'type' => 'currency',
            'total' => 'sum',
            'filter_index' => 'SUM(shipmentitem.qty * shipmentitem.price)',
            'filter_condition_callback' => array($this, '_filterTotalAmountCallback'),
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
        ));
        $this->addColumn('sales_rate', array(
            'header' => Mage::helper('inventory')->__('Avg. Sales (item/day)'),
            'index' => 'sales_rate',
            'type' => 'number',
            'align' => 'right',
            'width' => '100px',
            'renderer' => 'inventory/adminhtml_report_sales_renderer_case5salerate',
            'filter_condition_callback' => array($this, '_filterSalesRateCallback'),
        ));
        $this->addExportType('*/*/exportCase4Csv', Mage::helper('adminhtml')->__('CSV'));
        $this->addExportType('*/*/exportCase4Xml', Mage::helper('adminhtml')->__('Excel XML'));
        return parent::_prepareColumns();
    }

    public function prepareExport() {
        $this->_prepareColumns();
        $this->_prepareCollection();
        return $this;
    }

    protected function _filterTotalAmountCallback($collection, $column) {
        $filter = $column->getFilter()->getValue();
        if (isset($filter['from']) && $filter['from']) {
            $collection->getSelect()->having('SUM(shipmentitem.qty * shipmentitem.price) >= ?', $filter['from']);
        }
        if (isset($filter['to']) && $filter['to']) {
            $collection->getSelect()->having('SUM(shipmentitem.qty * shipmentitem.price) <= ?', $filter['to']);
        }
    }

    protected function _filterSoldQtyCallback($collection, $column) {
        $filter = $column->getFilter()->getValue();
        if (isset($filter['from']) && $filter['from']) {
            $collection->getSelect()->having('SUM(qty) >= ?', $filter['from']);
        }
        if (isset($filter['to']) && $filter['to']) {
            $collection->getSelect()->having('SUM(qty) <= ?', $filter['to']);
        }
    }

    protected function _filterSalesRateCallback($collection, $column) {
        $filter = $column->getFilter()->getValue();
        $filterData = $this->getFilterData();
        foreach ($collection as $item) {
            $salesRate = Mage::helper('inventory/report')->getSalesRateValue($item, $filterData);
            $pass = FALSE;
            if (isset($filter['from']) && $filter['from'] >= 0) {
                if (floatval($salesRate) >= floatval($filter['from'])) {
                    $pass = TRUE;
                }
            }
            if ($pass) {
                if (isset($filter['to']) && $filter['to']) {
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

?>
