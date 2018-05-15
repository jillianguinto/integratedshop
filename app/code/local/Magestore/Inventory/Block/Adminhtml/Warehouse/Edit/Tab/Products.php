<?php

class Magestore_Inventory_Block_Adminhtml_Warehouse_Edit_Tab_Products extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('productGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
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
        $warehouseId = $this->getWarehouse()->getId();
        $collection = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToSelect('*');
        $collection->joinField('qty', 'inventory/warehouseproduct', 'qty', 'product_id=entity_id', "{{table}}.warehouse_id=$warehouseId", 'inner');
        $collection->joinField('qty_available', 'inventory/warehouseproduct', 'qty_available', 'product_id=entity_id', "{{table}}.warehouse_id=$warehouseId", 'inner');
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
        ));


        $this->addColumn('entity_id', array(
            'header' => Mage::helper('catalog')->__('ID'),
            'sortable' => true,
            'width' => '60',
            'index' => 'entity_id',
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
            'renderer' => 'inventory/adminhtml_renderer_productimage',
            'index' => 'product_image',
            'filter' => false
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
        $check = Mage::helper('inventory/warehouse')->canEdit($adminId, $warehouse);
        if ($check == true) {
            $this->addColumn('qty', array(
                'header' => Mage::helper('catalog')->__('Qty'),
                'width' => '80px',
                'index' => 'qty',
                'type' => 'number',
                'editable' => true,
                'default' => 0
            ));
        } else {
            $this->addColumn('qty', array(
                'header' => Mage::helper('catalog')->__('Total Qty'),
                'width' => '80px',
                'index' => 'qty',
                'type' => 'number',
                'default' => 0
            ));
        }
        $this->addColumn('avail_qty', array(
            'header' => Mage::helper('catalog')->__('Avail. Qty'),
            'width' => '80px',
            'type' => 'number',
            'index' => 'qty_available',
        ));
        return parent::_prepareColumns();
    }

    public function _getSelectedProducts() {
        $productArrays = $this->getProducts();
        $products = '';
        $warehouseProducts = array();
        if ($productArrays) {
            $products = array();
            foreach ($productArrays as $productArray) {
                parse_str(urldecode($productArray), $warehouseProducts);
                if (count($warehouseProducts)) {
                    foreach ($warehouseProducts as $pId => $enCoded) {
                        $products[] = $pId;
                    }
                }
            }
        }
        if (!is_array($products)) {
            $products = array_keys($this->getSelectedProducts());
        }
        return $products;
    }

    public function getSelectedProducts() {
        $warehouse = $this->getWarehouse();
        $products = array();
        $productCollection = Mage::getResourceModel('inventory/warehouseproduct_collection')
                ->addFieldToFilter('warehouse_id', $warehouse->getId());
        if (count($productCollection)) {
            foreach ($productCollection as $product) {
                $products[$product->getProductId()] = array('qty' => $product->getQty());
            }
        }
        return $products;
    }

    public function getWarehouse() {
        return Mage::getModel('inventory/warehouse')
                        ->load($this->getRequest()->getParam('id'));
    }

    public function getGridUrl() {
        return $this->getUrl('*/*/productsGrid', array(
                    '_current' => true
                ));
    }

    public function _filterAvailCallback($collection, $column) {
        $filter = $column->getFilter()->getValue();
        foreach ($collection as $item) {
            $avail = Mage::helper('inventory')->getAvailQty($item);
            $pass = TRUE;
            if (isset($filter['from']) && $filter['from'] >= 0) {
                if (floatval($avail) < floatval($filter['from'])) {
                    $pass = FALSE;
                }
            }
            if ($pass) {
                if (isset($filter['to']) && $filter['to'] >= 0) {
                    if (floatval($avail) > floatval($filter['to'])) {
                        $pass = FALSE;
                    }
                }
            }
            if ($pass) {
                $item->setAvailQty($avail);
                $arr[] = $item;
            }
        }
        $temp = Mage::helper('inventory')->_tempCollection(); // A blank collection 
        for ($i = 0; $i < count($arr); $i++) {
            $temp->addItem($arr[$i]);
        }
        $this->setCollection($temp);
    }

}

