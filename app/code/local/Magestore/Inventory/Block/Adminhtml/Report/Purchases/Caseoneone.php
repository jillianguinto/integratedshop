<?php

class Magestore_Inventory_Block_Adminhtml_Report_Purchases_Caseoneone extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('purchasereportGrid');
        $this->setDefaultSort('purchase_on');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setCountTotals(true);
    }

    protected function _afterLoadCollection() {
        $totalObj = new Magestore_Inventory_Model_Warehousesales_Totals();
        $this->setTotals($totalObj->countTotals($this));
    }

    protected function _prepareCollection() {
        $supplierSelect = $this->getRequest()->getParam('supplier_select');
        $periodSelect = $this->getRequest()->getParam('period');
        $datefrom = $this->getRequest()->getParam('date_from');
        $dateto = $this->getRequest()->getParam('date_to');

        $collection = Mage::getModel('inventory/purchaseorderproduct')->getCollection();
//                            ->addFieldToFilter('supplier_id',$supplierSelect);    
        $resource = Mage::getSingleton('core/resource');
        if ($periodSelect == 'month') {
            $collection
                    ->getSelect()
                    ->join(array('purchase' => $resource->getTableName("erp_inventory_purchase_order")), "main_table.purchase_order_id = purchase.purchase_order_id and purchase.supplier_id = $supplierSelect", array('*',))
                    ->group('main_table.product_name')
                    ->group('month(purchase.purchase_on)')
            ;
        } else if ($periodSelect == 'year') {
            $collection
                    ->getSelect()
                    ->join(array('purchase' => $resource->getTableName("erp_inventory_purchase_order")), "main_table.purchase_order_id = purchase.purchase_order_id and purchase.supplier_id = $supplierSelect", array('*',))
                    ->group('main_table.product_name')
                    ->group('year(purchase.purchase_on)')
            ;
        } else {
            $collection
                    ->getSelect()
                    ->join(array('purchase' => $resource->getTableName("erp_inventory_purchase_order")), "main_table.purchase_order_id = purchase.purchase_order_id and purchase.supplier_id = $supplierSelect", array('*',))
                    ->group('main_table.product_name')
                    ->group('day(purchase.purchase_on)')
            ;
        }
        if ($datefrom != '') {
            $datefrom = date('Y-m-d H:i:s', strtotime($datefrom));
            $collection->getSelect()
                    ->where('purchase.purchase_on >= \'' . $datefrom . '\'');
        }
        if ($dateto != '') {
            $dateto = date('Y-m-d H:i:s', strtotime($dateto));
            $collection->getSelect()
                    ->where('purchase.purchase_on <= \'' . $dateto . '\'');
        }

        $collection->getSelect()->columns(array(
            'purchase_amount' => 'SUM(main_table.cost*main_table.qty*(100+main_table.tax-main_table.discount)/100)',
            'qty_request' => 'sum(main_table.qty)',
            'qty_received' => 'sum(main_table.qty_recieved - main_table.qty_returned)',
            'qty_returned' => 'sum(main_table.qty_returned)'
        ));
        $collection->setIsGroupCountSql(true);
        if ($collection->getSize())
            $this->setCountTotals(true);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare columns for this grid
     *
     * @return Magestore_Inventory_Block_Adminhtml_Purchaseorder_Grid
     */
    protected function _prepareColumns() {
        $currencyCode = Mage::app()->getStore()->getBaseCurrency()->getCode();
        $period = $this->getRequest()->getParam('period');

        if ($period == 'month') {
            $this->addColumn('purchase_on', array(
                'header' => Mage::helper('inventory')->__('Period'),
                'align' => 'left',
                'type' => 'date',
                'format' => 'M-Y',
                'index' => 'purchase_on',
                'column_css_class' => 'purchase-on',
                'totals_label' => Mage::helper('inventory')->__('Total')
            ));
        } else if ($period == 'year') {
            $this->addColumn('purchase_on', array(
                'header' => Mage::helper('inventory')->__('Period'),
                'align' => 'left',
                'type' => 'date',
                'format' => 'Y',
                'index' => 'purchase_on',
                'column_css_class' => 'purchase-on',
                'totals_label' => Mage::helper('inventory')->__('Total')
            ));
        } else {
            $this->addColumn('purchase_on', array(
                'header' => Mage::helper('inventory')->__('Period'),
                'align' => 'left',
                'type' => 'date',
                'index' => 'purchase_on',
                'column_css_class' => 'purchase-on',
                'totals_label' => Mage::helper('inventory')->__('Total'),
            ));
        }


        $this->addColumn('product_name', array(
            'header' => Mage::helper('inventory')->__('Product Name'),
            'width' => '150px',
            'index' => 'product_name',
        ));

        $this->addColumn('product_sku', array(
            'header' => Mage::helper('inventory')->__('SKU'),
            'width' => '150px',
            'index' => 'product_sku',
        ));

        if (!$this->_isExport) {
            $this->addColumn('product_image', array(
                'header' => Mage::helper('catalog')->__('Image'),
                'width' => '90px',
                'filter' => false,
                'renderer' => 'inventory/adminhtml_renderer_productimage'
            ));
        }

        $this->addColumn('qty_request', array(
            'header' => Mage::helper('inventory')->__('Total Qty Ordered'),
            'type' => 'number',
            'width' => '80px',
            'index' => 'qty_request',
            'filter_index' => 'SUM(main_table.qty)',
            'total' => 'sum',
            'filter_condition_callback' => array($this, '_filterQtyRequestCallbackOne'),
        ));

        $this->addColumn('qty_received', array(
            'header' => Mage::helper('inventory')->__('Total Qty Received'),
            'type' => 'number',
            'width' => '80px',
            'index' => 'qty_received',
            'filter_index' => 'SUM(main_table.qty_recieved)',
            'filter_condition_callback' => array($this, '_filterQtyReceivedCallbackOne'),
            'total' => 'sum'
        ));

        $this->addColumn('qty_returned', array(
            'header' => Mage::helper('inventory')->__('Total Qty Returned'),
            'type' => 'number',
            'width' => '80px',
            'index' => 'qty_returned',
            'filter_index' => 'SUM(main_table.qty_returned)',
            'filter_condition_callback' => array($this, '_filterQtyReturnedCallbackOne'),
            'total' => 'sum'
        ));

        $this->addColumn('purchase_amount', array(
            'header' => Mage::helper('inventory')->__('Purchase Amount'),
            'width' => '150px',
            'type' => 'price',
            'currency_code' => $currencyCode,
            'index' => 'purchase_amount',
            'total' => 'sum'
        ));


        $this->addExportType('*/*/exportCsvCaseOneOne', Mage::helper('inventory')->__('CSV'));
        $this->addExportType('*/*/exportXmlCaseOneOne', Mage::helper('inventory')->__('XML'));

        return parent::_prepareColumns();
    }

    protected function _filterQtyRequestCallbackOne($collection, $column) {
        $filter = $column->getFilter()->getValue();
        if (isset($filter['from']) && $filter['from']) {
            $collection->getSelect()->having('SUM(main_table.qty) >= ?', $filter['from']);
        }
        if (isset($filter['to']) && $filter['to']) {
            $collection->getSelect()->having('SUM(main_table.qty) <= ?', $filter['to']);
        }
    }

    protected function _filterQtyReceivedCallbackOne($collection, $column) {
        $filter = $column->getFilter()->getValue();
        if (isset($filter['from']) && $filter['from']) {
            $collection->getSelect()->having('SUM(main_table.qty_received) >= ?', $filter['from']);
        }
        if (isset($filter['to']) && $filter['to']) {
            $collection->getSelect()->having('SUM(main_table.qty_received) <= ?', $filter['to']);
        }
    }

    protected function _filterQtyReturnedCallbackOne($collection, $column) {
        $filter = $column->getFilter()->getValue();
        if (isset($filter['from']) && $filter['from']) {
            $collection->getSelect()->having('SUM(main_table.qty_returned) >= ?', $filter['from']);
        }
        if (isset($filter['to']) && $filter['to']) {
            $collection->getSelect()->having('SUM(main_table.qty_returned) <= ?', $filter['to']);
        }
    }

}