<?php

class Magestore_Inventory_Block_Adminhtml_Report_Purchases_Caseoneall extends Mage_Adminhtml_Block_Widget_Grid {

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
        $periodSelect = $this->getRequest()->getParam('period');
        $datefrom = $this->getRequest()->getParam('date_from');
        $dateto = $this->getRequest()->getParam('date_to');
        $supplierSelect = $this->getRequest()->getParam('supplier_select');

        if (!isset($supplierSelect) || $supplierSelect == '') {
            $supplierCollection = Mage::getModel('inventory/supplier')->getCollection();
            $supplierFirst = $supplierCollection->getFirstItem();
            $supplierSelect = $supplierFirst->getId();
        }
        $currencyCode = Mage::app()->getStore()->getBaseCurrency()->getCode();
//        $purchaseOrders = Mage::getModel('inventory/purchaseorder')->getCollection()
//                            ->addFieldToFilter('supplier_id',$supplierSelect);
//        $pOrderIds = array();
//        foreach($purchaseOrders as $pOrder)
//            $pOrderIds[] = $pOrder->getId();
        $collection = Mage::getModel('inventory/purchaseorderproduct')->getCollection();
//                            ->addFieldToFilter('supplier_id',$supplierSelect);    
//                            ->addFieldToFilter('purchase_order_id',array('in'=>$pOrderIds));            
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
        $resource = Mage::getSingleton('core/resource');
        $collection
                ->getSelect()
                ->join(array('purchase' => $resource->getTableName("erp_inventory_purchase_order")), "main_table.purchase_order_id = purchase.purchase_order_id 
                            and purchase.supplier_id = $supplierSelect", array('*'))
                ->group('main_table.product_name')
                ->group('purchase.warehouse_id');
        ;


        $collection->getSelect()->columns(array(
            'warehouse_name' => 'purchase.warehouse_name',
            'purchase_amount' => 'SUM(main_table.cost*main_table.qty*(100+main_table.tax-main_table.discount)/100)',
            'qty_request' => 'SUM(main_table.qty)',
            'qty_received' => 'SUM(main_table.qty_recieved - main_table.qty_returned)',
            'qty_returned' => 'SUM(main_table.qty_returned)'
        ));
//        echo $collection->getSelect();die();
        $collection->setIsGroupCountSql(true);
        if ($collection->getSize())
            $this->setCountTotals(true);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $currencyCode = Mage::app()->getStore()->getBaseCurrency()->getCode();
        $this->addColumn('product_id', array(
            'header' => Mage::helper('inventory')->__('ID #'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'product_id',
            'totals_label' => Mage::helper('inventory')->__('Total'),
        ));

        $this->addColumn('product_name', array(
            'header' => Mage::helper('inventory')->__('Product Name'),
            'width' => '150px',
            'align' => 'left',
            'index' => 'product_name',
        ));

        $this->addColumn('product_sku', array(
            'header' => Mage::helper('inventory')->__('SKU'),
            'width' => '150px',
            'align' => 'left',
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

        $this->addColumn('warehouse_name', array(
            'header' => Mage::helper('inventory')->__('Warehouse'),
            'width' => '150px',
            'align' => 'left',
            'index' => 'warehouse_name',
        ));

        $this->addColumn('qty_request', array(
            'header' => Mage::helper('inventory')->__('Total Qty Ordered'),
            'type' => 'number',
            'width' => '80px',
            'align' => 'right',
            'index' => 'qty_request',
            'filter_index' => 'SUM(main_table.qty)',
            'filter_condition_callback' => array($this, '_filterQtyRequestCallbackAll'),
            'total' => 'sum',
        ));

        $this->addColumn('qty_received', array(
            'header' => Mage::helper('inventory')->__('Total Qty Received'),
            'type' => 'number',
            'align' => 'right',
            'width' => '80px',
            'index' => 'qty_received',
            'filter_index' => 'SUM(main_table.qty_recieved)',
            'filter_condition_callback' => array($this, '_filterQtyReceivedCallbackAll'),
            'total' => 'sum'
        ));

        $this->addColumn('qty_returned', array(
            'header' => Mage::helper('inventory')->__('Total Qty Returned'),
            'type' => 'number',
            'align' => 'right',
            'width' => '80px',
            'index' => 'qty_returned',
            'filter_index' => 'SUM(main_table.qty_returned)',
            'filter_condition_callback' => array($this, '_filterQtyReturnedCallbackAll'),
            'total' => 'sum'
        ));
        $this->addColumn('purchase_amount', array(
            'header' => Mage::helper('inventory')->__('Purchase Amount'),
            'currency_code' => $currencyCode,
            'type' => 'price',
            'align' => 'right',
            'width' => '150px',
            'index' => 'purchase_amount',
            'filter_index' => 'main_table.cost*main_table.qty*(100+main_table.tax)*(100-main_table.discount)/100',
            'total' => 'sum'
        ));


        $this->addExportType('*/*/exportCsvCaseOneAll', Mage::helper('inventory')->__('CSV'));
        $this->addExportType('*/*/exportXmlCaseOneAll', Mage::helper('inventory')->__('XML'));

        return parent::_prepareColumns();
    }

    protected function _filterQtyRequestCallbackAll($collection, $column) {
        $filter = $column->getFilter()->getValue();
        if (isset($filter['from']) && $filter['from']) {
            $collection->getSelect()->having('SUM(main_table.qty) >= ?', $filter['from']);
        }
        if (isset($filter['to']) && $filter['to']) {
            $collection->getSelect()->having('SUM(main_table.qty) <= ?', $filter['to']);
        }
    }

    protected function _filterQtyReceivedCallbackAll($collection, $column) {
        $filter = $column->getFilter()->getValue();
        if (isset($filter['from']) && $filter['from']) {
            $collection->getSelect()->having('SUM(main_table.qty_recieved) >= ?', $filter['from']);
        }
        if (isset($filter['to']) && $filter['to']) {
            $collection->getSelect()->having('SUM(main_table.qty_recieved) <= ?', $filter['to']);
        }
    }

    protected function _filterQtyReturnedCallbackAll($collection, $column) {
        $filter = $column->getFilter()->getValue();
        if (isset($filter['from']) && $filter['from']) {
            $collection->getSelect()->having('SUM(main_table.qty_returned) >= ?', $filter['from']);
        }
        if (isset($filter['to']) && $filter['to']) {
            $collection->getSelect()->having('SUM(main_table.qty_returned) <= ?', $filter['to']);
        }
    }

}