<?php

class Magestore_Inventory_Block_Adminhtml_Stockissuing_Edit_Tab_Products extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('productGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        if(Mage::getModel('admin/session')->getData('stockissuing_product_import'))
            $this->setDefaultFilter(array('in_products' => 1));
    }
    
    protected function _addColumnFilterToCollection($column) {
        if ($column->getId() == 'in_products') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds))
                $productIds = 0;
            if ($column->getFilter()->getValue())
                $this->getCollection()->addFieldToFilter('entity_id', array('in' => $productIds));
            elseif ($productIds)
                $this->getCollection()->addFieldToFilter('entity_id', array('nin' => $productIds));
            return $this;
        }
        return parent::_addColumnFilterToCollection($column);
    }

    protected function _prepareCollection() {
        $warehouse = $this->getRequest()->getParam('warehouse_id');
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('*');
        $collection->joinField('warehouse_qty',
                'inventory/warehouseproduct',
                'qty',
                'product_id=entity_id',
                "{{table}}.warehouse_id=$warehouse",
                'inner');
        $collection->addFieldToFilter('warehouse_qty',array('gt'=>0));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('in_products', array(
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'name' => 'in_products',
            'values' => $this->_getSelectedProducts(),
            'align' => 'center',
            'index' => 'entity_id',
            'use_index' => true,
        ));


        $this->addColumn('entity_id', array(
            'header' => Mage::helper('catalog')->__('ID'),
            'sortable' => true,
            'width' => '60',
            'index' => 'entity_id'
        ));

        $this->addColumn('product_name', array(
            'header' => Mage::helper('catalog')->__('Name'),
            'align' => 'left',
            'index' => 'name',
        ));
        
                $this->addColumn('product_sku', array(
            'header' => Mage::helper('catalog')->__('SKU'),
            'width' => '80px',
            'index' => 'sku'
        ));
        
        $this->addColumn('product_image', array(
                'header' => Mage::helper('catalog')->__('Image'),
                'width' => '90px',
                'index' => 'product_image',
                'filter' => false,
                'renderer' => 'inventory/adminhtml_renderer_productimage'   
        ));        
        
        $this->addColumn('product_status', array(
            'header' => Mage::helper('catalog')->__('Status'),
            'width' => '90px',
            'index' => 'status',
            'type' => 'options',
            'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));

        $this->addColumn('product_price', array(
            'header' => Mage::helper('catalog')->__('Price'),
            'type' => 'currency',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'index' => 'price'
        ));
        
        $adminId = Mage::getSingleton('admin/session')->getUser()->getId();
        $warehouse = $this->getRequest()->getParam('id');
        $this->addColumn('warehouse_qty', array(
            'header' => Mage::helper('catalog')->__('Qty In Warehouse'),
            'width' => '80px',
            'index' => 'warehouse_qty',
            'type' => 'number',
        ));
        $this->addColumn('qty_issuing', array(
            'header' => Mage::helper('catalog')->__('Qty Issuing'),
            'width' => '80px',
            'index' => 'qty_issuing',
            'type' => 'input',
            'filter' => false,
            'default' => 0
        ));
        return parent::_prepareColumns();
    }

    public function _getSelectedProducts() {
        $products = $this->getProducts();
        if (!is_array($products)) {
            $products = array_keys($this->getSelectedProducts());
        }
        return $products;
    }

    public function getSelectedProducts() {
        $stockIssuing = $this->getStockissuing();
        $products = array();
        $productCollection = Mage::getResourceModel('inventory/stockissuingproduct_collection')
            ->addFieldToFilter('stock_issuing_id', $stockIssuing->getId());
        if (count($productCollection)) {
            foreach ($productCollection as $product) {
                $products[$product->getProductId()] = array('qty' => $product->getQty());
            }
        }
        if ($stockissuingProductImports = Mage::getModel('admin/session')->getData('stockissuing_product_import')) {
            $productModel = Mage::getModel('catalog/product');
            foreach ($stockissuingProductImports as $productImport) {              
                $productId = $productModel->getIdBySku($productImport['SKU']);
                if ($productId)
                    $products[$productId] = array('qty_issuing' => $productImport['QTY_ISSUE']
                    );
            }
        }
        return $products;
    }
    
    public function getWarehouse(){
        return Mage::getModel('inventory/warehouse')
                ->load($this->getRequest()->getParam('warehouse_id'));
    }

    public function getStockissuing() {
        return Mage::getModel('inventory/stockissuing')
                ->load($this->getRequest()->getParam('id'));
    }

    public function getGridUrl() {
        return $this->getUrl('*/*/productGrid', array(
                '_current' => true,
                'id' => $this->getRequest()->getParam('id'),
                'store' => $this->getRequest()->getParam('store')
            ));
    }

    public function getStore() {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }
}

