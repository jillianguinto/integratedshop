<?php

class Magestore_Inventory_Block_Adminhtml_Report_Supplierproduct extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('productGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->getSupplier();
    }

    protected function _prepareCollection() {
        $supplierSelect = $this->getRequest()->getParam('id');
        $supplier = Mage::helper('inventory/supplier')->getAllSupplierName();
        if (count($supplier) && !$supplierSelect) {
            $model = Mage::getModel('inventory/supplier');
            $firstItem = $model->getCollection()->getFirstItem();
            $supplierSelect = $firstItem->getSupplierId();
        }
        $collection = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToSelect('*');

        $collection->getSelect()->join(array('supplierproduct' => $collection->getTable('inventory/supplierproduct')), "e.entity_id=supplierproduct.product_id AND supplierproduct.supplier_id=$supplierSelect", array('discount', 'tax', 'costnew' => 'supplierproduct.cost'));
        if ($storeId = $this->getRequest()->getParam('store', 0))
            $collection->addStoreFilter($storeId);
        if ($this->getRequest()->getParam('dir')) {
            $sort = $this->getRequest()->getParam('sort');
            if ($sort == 'cost' || $sort == 'tax' || $sort == 'discount') {
                $dir = strtoupper($this->getRequest()->getParam('dir'));
                $collection->getSelect()->order("$sort $dir");
            }
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $currencyCode = Mage::app()->getStore()->getBaseCurrency()->getCode();

        $this->addColumn('entity_id', array(
            'header' => Mage::helper('catalog')->__('ID'),
            'sortable' => true,
            'width' => '60',
            'align' => 'right',
            'index' => 'entity_id'
        ));

        $this->addColumn('product_name', array(
            'header' => Mage::helper('catalog')->__('Name'),
            'align' => 'left',
            'align' => 'left',
            'index' => 'name',
        ));

        $this->addColumn('product_sku', array(
            'header' => Mage::helper('catalog')->__('SKU'),
            'width' => '80px',
            'align' => 'left',
            'index' => 'sku'
        ));

        if (!$this->_isExport) {
            $this->addColumn('product_image', array(
                'header' => Mage::helper('catalog')->__('Image'),
                'width' => '90px',
                'renderer' => 'inventory/adminhtml_renderer_productimage',
                'index' => 'product_image',
                'filter' => false,
            ));
        }

        $this->addColumn('product_price', array(
            'header' => Mage::helper('catalog')->__('Price'),
            'type' => 'currency',
            'align' => 'right',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'index' => 'price'
        ));

        $this->addColumn('product_status', array(
            'header' => Mage::helper('catalog')->__('Status'),
            'width' => '90px',
            'align' => 'left',
            'index' => 'status',
            'type' => 'options',
            'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));

        $this->addColumn('cost', array(
            'header' => Mage::helper('inventory')->__('Cost'),
            'name' => 'cost',
            'type' => 'number',
            'align' => 'right',
            'index' => 'costnew',
            'filter_index' => 'supplierproduct.cost',
            'filter_condition_callback' => array($this, '_filterCostCallback')
        ));

        $this->addColumn('tax', array(
            'header' => Mage::helper('inventory')->__('Tax(%)'),
            'name' => 'tax',
            'type' => 'number',
            'align' => 'right',
            'index' => 'tax',
            'filter_index' => 'supplierproduct.tax',
            'filter_condition_callback' => array($this, '_filterTaxCallback')
        ));

        $this->addColumn('discount', array(
            'header' => Mage::helper('inventory')->__('Discount(%)'),
            'name' => 'discount',
            'type' => 'number',
            'align' => 'right',
            'index' => 'discount',
            'filter_index' => 'supplierproduct.discount',
            'filter_condition_callback' => array($this, '_filterDiscountCallback')
        ));

        if (!$this->_isExport) {
            $this->addColumn('show_report', array(
                'header' => Mage::helper('inventory')->__('Report'),
                'sortable' => false,
                'filter' => false,
                'width' => '60',
                'renderer' => 'inventory/adminhtml_supplier_edit_tab_renderer_showreport'
            ));
        }

        $this->addExportType('*/*/exportSupplierProductCsv', Mage::helper('inventory')->__('CSV'));
        $this->addExportType('*/*/exportSupplierProductXml', Mage::helper('inventory')->__('XML'));
    }

    public function getGridUrl() {
        $supplierSelect = $this->getRequest()->getParam('id');
        $supplier = Mage::helper('inventory/supplier')->getAllSupplierName();
        if (count($supplier) && !$supplierSelect) {
            $model = Mage::getModel('inventory/supplier');
            $firstItem = $model->getCollection()->getFirstItem();
            $supplierSelect = $firstItem->getSupplierId();
        }
        return $this->getUrl('*/*/reportsupplierproductgrid', array(
                    '_current' => true,
                    'id' => $supplierSelect,
                    'store' => $this->getRequest()->getParam('store')
                ));
    }

    public function getRowUrl($row) {
        return false;
    }

    public function getStore() {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _filterDiscountCallback($collection, $column) {
        $filter = $column->getFilter()->getValue();
        if (isset($filter['from']) && $filter['from']) {
            $collection->getSelect()->where('supplierproduct.discount >= ?', $filter['from']);
        }
        if (isset($filter['to']) && $filter['to']) {
            $collection->getSelect()->where('supplierproduct.discount <= ?', $filter['to']);
        }
    }

    protected function _filterTaxCallback($collection, $column) {
        $filter = $column->getFilter()->getValue();
        if (isset($filter['from']) && $filter['from']) {
            $collection->getSelect()->where('supplierproduct.tax >= ?', $filter['from']);
        }
        if (isset($filter['to']) && $filter['to']) {
            $collection->getSelect()->where('supplierproduct.tax <= ?', $filter['to']);
        }
    }

    protected function _filterCostCallback($collection, $column) {
        $filter = $column->getFilter()->getValue();
        if (isset($filter['from']) && $filter['from']) {
            $collection->getSelect()->where('supplierproduct.cost >= ?', $filter['from']);
        }
        if (isset($filter['to']) && $filter['to']) {
            $collection->getSelect()->where('supplierproduct.cost <= ?', $filter['to']);
        }
    }

}