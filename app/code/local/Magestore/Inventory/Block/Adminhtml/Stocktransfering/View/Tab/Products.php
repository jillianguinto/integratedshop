<?php

/**
 * Product grid for stock transfer
 */
class Magestore_Inventory_Block_Adminhtml_Stocktransfering_View_Tab_Products extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('stocktransferingproductGrid');
        $this->setDefaultSort('sort_order');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        if ($this->getStocktransfering() && $this->getStocktransfering()->getId()) {
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
        $source = $this->getRequest()->getParam('source');
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('*');
        $warehouseproduct = Mage::getModel('inventory/warehouseproduct')->getCollection();
        $transferstockproduct = Mage::getModel('inventory/stocktransferingproduct')->getCollection();
        if ($source) {       
                $collection->joinField('qty',
                'inventory/warehouseproduct',
                'qty',
                'product_id=entity_id',
                "{{table}}.warehouse_id=$source",
                'inner');
        } else {
            $id = $this->getRequest()->getParam('id');
            $collection->joinField('qty_request',
                'inventory/stocktransferingproduct',
                'qty_request',
                'product_id=entity_id',
                "{{table}}.transfer_stock_id=$id",
                'inner');
            $collection->joinField('qty_transfer',
                'inventory/stocktransferingproduct',
                'qty_transfer',
                'product_id=entity_id',
                "{{table}}.transfer_stock_id=$id",
                'inner');
            $collection->joinField('qty_receive',
                'inventory/stocktransferingproduct',
                'qty_receive',
                'product_id=entity_id',
                "{{table}}.transfer_stock_id=$id",
                'inner');
        }
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

            $this->addColumn('product_id', array(
                'header' => Mage::helper('catalog')->__('ID'),
                'align' => 'right',
                'sortable' => true,
                'index' => 'entity_id',
            ));

            $this->addColumn('product_name', array(
                'header' => Mage::helper('catalog')->__('Name'),
                'align' => 'left',
                'sortable' => true,
                'index' => 'name'
            ));

            $this->addColumn('product_sku', array(
                'header' => Mage::helper('catalog')->__('SKU'),
                'align' => 'left',
                'sortable' => true,
                'index' => 'sku'
            ));
            
            $this->addColumn('product_image', array(
                'header' => Mage::helper('catalog')->__('Image'),
                'width' => '90px',
                'index' => 'product_image',
                'filter' => false,
                'renderer' => 'inventory/adminhtml_renderer_productimage'   
            ));
            
            $this->addColumn('qty_warehouse', array(
                'header' => Mage::helper('inventory')->__('Qty in Warehouse'),
                'width' => '120px',
                'index' => 'qty',
                'type' => 'number',
            ));
            
            $this->addColumn('qty_request', array(
                'header' => Mage::helper('inventory')->__('Qty Requested'),
                'width' => '120px',
                'index' => 'qty_request',
                'type' => 'number',
            ));
            
            $this->addColumn('qty_transfer', array(
                'header' => Mage::helper('inventory')->__('Qty Transfered'),
                'width' => '120px',
                'index' => 'qty_transfer',
                'type' => 'number',
            ));
            
            $this->addColumn('qty_receive', array(
                'header' => Mage::helper('inventory')->__('Qty Received'),
                'width' => '120px',
                'index' => 'qty_receive',
                'type' => 'number',
            ));
       
    }

    public function _getSelectedProducts() {
        $products = $this->getProducts();
        if (!is_array($products) || Mage::getModel('admin/session')->getData('stocktransfering_product_import')) {
            $products = array_keys($this->getSelectedProducts());
            Mage::getModel('admin/session')->setData('stocktransfering_product_import', null);
        }
        return $products;
    }

    public function getSelectedProducts() {
        $stocktransfer = $this->getStocktransfering();
        $products = array();
        $productCollection = Mage::getResourceModel('inventory/stocktransferingproduct_collection')
            ->addFieldToFilter('transfer_stock_id', $stocktransfer->getId());
        if (count($productCollection)) {
            foreach ($productCollection as $product) {
                $products[$product->getProductId()] = array(
                    'qty_receive' => $product->getQtyReceive(),
                );
            }
        }

        if ($stocktransferingProductImports = Mage::getModel('admin/session')->getData('stocktransfering_product_import')) {
            $productModel = Mage::getModel('catalog/product');
            foreach ($stocktransferingProductImports as $productImport) {
                $productId = $productModel->getIdBySku($productImport['SKU']);
                if ($productId)
                    $products[$productId] = array('qty_request' => $productImport['QTY_REQUEST'],
                    );
            }
        }
        return $products;
    }

    public function getStocktransfering() {
        return Mage::getModel('inventory/stocktransfering')
                ->load($this->getRequest()->getParam('id'));
    }

    public function getGridUrl() {
        return $this->getUrl('*/*/productsGrid', array(
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

?>
