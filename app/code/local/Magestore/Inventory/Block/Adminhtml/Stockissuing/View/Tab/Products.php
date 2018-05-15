<?php

class Magestore_Inventory_Block_Adminhtml_Stockissuing_View_Tab_Products extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('productGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        if ($this->getWarehouse() && $this->getWarehouse()->getId()) {
            $this->setDefaultFilter(array('in_products' => 1));
        }
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
        $stockissuing = $this->getStockissuing();
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addFieldToFilter('status', 1)
            ->addAttributeToSelect('*');
        $id = $this->getRequest()->getParam('id');
        $collection->joinField('qty',
                'inventory/stockissuingproduct',
                'qty',
                'product_id=entity_id',
                "{{table}}.stock_issuing_id=$id",
                'inner');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
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
        $this->addColumn('qty', array(
            'header' => Mage::helper('catalog')->__('Qty Transfer'),
            'width' => '80px',
            'index' => 'qty',
            'type' => 'number',
            'editable' => false,
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
        return $products;
    }

    public function getStockissuing() {
        return Mage::getModel('inventory/stockissuing')
                ->load($this->getRequest()->getParam('id'));
    }

    public function getGridUrl() {
        return $this->getUrl('*/*/productGridView', array(
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

