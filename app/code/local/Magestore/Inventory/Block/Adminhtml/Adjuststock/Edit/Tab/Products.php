<?php

class Magestore_Inventory_Block_Adminhtml_Adjuststock_Edit_Tab_Products extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('productGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        if (($this->getAdjustStock() && $this->getAdjustStock()->getId()) || Mage::getModel('admin/session')->getData('adjuststock_product_import')) {
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
        if ($this->getRequest()->getParam('id')) {
            $id = $this->getRequest()->getParam('id');
            $adjuststock = Mage::getModel('inventory/adjuststock')->load($id);
            $warehouse_id = $adjuststock->getWarehouseId();
            $productIds = array();
            $adjuststockProducts = Mage::getModel('inventory/adjuststockproduct')
                    ->getCollection()
                    ->addFieldToFilter('adjuststock_id', $id);
            foreach ($adjuststockProducts as $adjuststockProduct)
                $productIds[] = $adjuststockProduct->getProductId();
            $collection = Mage::getModel('catalog/product')->getCollection()
                    ->addAttributeToSelect('*')
                    ->addFieldToFilter('entity_id', array('in' => $productIds));
            $collection->joinField('old_qty', 'inventory/adjuststockproduct', 'old_qty', 'product_id=entity_id', '{{table}}.adjuststock_id=' . $id, 'left');
            $collection->joinField('adjust_qty', 'inventory/adjuststockproduct', 'adjust_qty', 'product_id=entity_id', '{{table}}.adjuststock_id=' . $id, 'left');
        } else {
            $productSkus = array();
            if ($adjustStockProducts = Mage::getModel('admin/session')->getData('adjuststock_product_warehouse')) {
//                foreach($adjustStockProducts as $adjustStockProduct)
//                    $productSkus[] = $adjustStockProduct['SKU'];
                $warehouse_id = $adjustStockProducts['warehouse_id'];
                //Mage::getModel('admin/session')->setData('adjuststock_product_import',null);
            }
            $collection = Mage::getModel('catalog/product')->getCollection()
                    ->addAttributeToSelect('*')
            //->addFieldToFilter('sku',array('in'=>$productSkus))
            ;
            $collection->joinField('qty', 'inventory/warehouseproduct', 'qty', 'product_id=entity_id', '{{table}}.warehouse_id=' . $warehouse_id, 'left');
        }

        if ($storeId = $this->getRequest()->getParam('store', 0))
            $collection->addStoreFilter($storeId);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $currencyCode = Mage::app()->getStore()->getBaseCurrency()->getCode();
        if (!$this->getRequest()->getParam('id')) {
            $this->addColumn('in_products', array(
                'header_css_class' => 'a-center',
                'type' => 'checkbox',
                'name' => 'in_products',
                'values' => $this->_getSelectedProducts(),
                'align' => 'center',
                'index' => 'entity_id',
                'use_index' => true,
            ));
        }
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

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
                ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
                ->load()
                ->toOptionHash();
        $this->addColumn('product_status', array(
            'header' => Mage::helper('catalog')->__('Status'),
            'width' => '90px',
            'index' => 'status',
            'type' => 'options',
            'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));
        $this->addColumn('product_sku', array(
            'header' => Mage::helper('catalog')->__('SKU'),
            'width' => '80px',
            'index' => 'sku'
        ));

        if (!$this->_isExport) {
            $this->addColumn('product_image', array(
                'header' => Mage::helper('catalog')->__('Image'),
                'width' => '90px',
                'filter' => false,
                'renderer' => 'inventory/adminhtml_renderer_productimage'
            ));
        }

        $this->addColumn('product_price', array(
            'header' => Mage::helper('catalog')->__('Price'),
            'type' => 'currency',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'index' => 'price'
        ));
        if ($this->getRequest()->getParam('id')) {
            $this->addColumn('old_qty', array(
                'header' => Mage::helper('inventory')->__('Current Qty'),
                'width' => '100px',
                'type' => 'number',
                'index' => 'old_qty',
                'default' => '0',
            ));

            $this->addColumn('adjust_qty', array(
                'header' => Mage::helper('inventory')->__('Qty After Adjusting'),
                'name' => 'adjust_qty',
                'type' => 'number',
                'index' => 'adjust_qty',
                'default' => '0',
            ));
        } else {
            $this->addColumn('qty', array(
                'header' => Mage::helper('inventory')->__('Current Qty'),
                'width' => '100px',
                'type' => 'number',
                'index' => 'qty',
                'default' => '0',
            ));

            $this->addColumn('adjust_qty', array(
                'header' => Mage::helper('inventory')->__('Adjusted Qty'),
                'name' => 'adjust_qty',
                'type' => 'number',
                'index' => 'adjust_qty',
                'editable' => true,
                'edit_only' => true,
                'filter' => false
            ));
        }
    }

    public function getGridUrl() {
        return $this->getUrl('*/*/productGrid', array(
                    '_current' => true,
                    'id' => $this->getRequest()->getParam('id'),
                    'store' => $this->getRequest()->getParam('store')
                ));
    }

    protected function _getSelectedProducts() {
        $productArrays = $this->getProducts();
        $products = '';
        $adjustProducts = array();
        if ($productArrays) {
            $products = array();
            foreach ($productArrays as $productArray) {
                parse_str(urldecode($productArray), $adjustProducts);
                if (count($adjustProducts)) {
                    foreach ($adjustProducts as $pId => $enCoded) {
                        $products[] = $pId;
                    }
                }
            }
        }
        if ((!is_array($products) || Mage::getModel('admin/session')->getData('adjuststock_product_import'))
                && !$this->getRequest()->getParam('id')) {
            $products = array_keys($this->getSelectedRelatedProducts());
        }
        return $products;
    }

    public function getSelectedRelatedProducts() {
        $products = array();
        if ($adjustStockProducts = Mage::getModel('admin/session')->getData('adjuststock_product_import')) {
            $productModel = Mage::getModel('catalog/product');
            foreach ($adjustStockProducts as $adjustStockProduct) {
                $productId = $productModel->getIdBySku($adjustStockProduct['SKU']);
                if ($productId)
                    $products[$productId] = array('adjust_qty' => $adjustStockProduct['QTY']);
            }
        }
        return $products;
    }

    /**
     * get Current Purchase Order
     *
     * @return Magestore_Inventory_Model_Adjuststock
     */
    public function getAdjustStock() {
        return Mage::getModel('inventory/adjuststock')->load($this->getRequest()->getParam('id'));
    }

    /**
     * get currrent store
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore() {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

}