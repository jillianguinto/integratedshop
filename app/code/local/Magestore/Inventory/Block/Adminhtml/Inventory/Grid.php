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
class Magestore_Inventory_Block_Adminhtml_Inventory_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('productGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
    }

    protected function _getStore() {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _prepareCollection() {
        $collection = Mage::getResourceModel('inventory/product_collection')
                ->addAttributeToSelect('*');
        $warehouseId = $this->getRequest()->getParam('warehouse');
        $collection->joinField('qty', 'cataloginventory/stock_item', 'qty', 'product_id=entity_id', '{{table}}.stock_id=1', 'inner');
        $collection->joinField('cost_price', 'inventory/inventory', 'cost_price', 'product_id=entity_id', '{{table}}.last_update', 'left');
        if ($warehouseId)
            $collection->getSelect()
                    ->join(
                            array('warehouse_product' => $collection->getTable('inventory/warehouseproduct')), 'e.entity_id=warehouse_product.product_id and warehouse_product.warehouse_id = ' . $warehouseId, array('warehouse_id', 'qty_available')
                    )
                    ->joinLeft(
                            array('supplier_product' => $collection->getTable('inventory/supplierproduct')), 'e.entity_id=supplier_product.product_id', array('supplier_id')
                    )
                    ->joinLeft(
                            array('order_item' => $collection->getTable('sales/order_item')), 'e.entity_id=order_item.product_id', array('qty_ordered', 'qty_canceled', 'qty_shipped')
                    )
                    ->group(array('e.entity_id'));
        else
            $collection->getSelect()
                    ->join(
                            array('warehouse_product' => $collection->getTable('inventory/warehouseproduct')), 'e.entity_id=warehouse_product.product_id', array('warehouse_id', 'qty_available')
                    )
                    ->joinLeft(
                            array('supplier_product' => $collection->getTable('inventory/supplierproduct')), 'e.entity_id=supplier_product.product_id', array('supplier_id')
                    )
                    ->joinLeft(
                            array('order_item' => $collection->getTable('sales/order_item')), 'e.entity_id=order_item.product_id', array('qty_ordered', 'qty_canceled', 'qty_shipped')
                    )
                    ->group(array('e.entity_id'));
        $store = $this->_getStore();
        if ($store->getId()) {
            $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
            $collection->joinAttribute('name', 'catalog_product/name', 'entity_id', null, 'inner', $adminStore);
            $collection->joinAttribute('custom_name', 'catalog_product/name', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $store->getId());
            $collection->addStoreFilter($store);
        }
        $collection->setIsGroupCountSql(true);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare columns for this grid
     *
     * @return Magestore_Inventory_Block_Adminhtml_Inventory_Grid
     */
    protected function _prepareColumns() {
        $this->addColumn('entity_id', array(
            'header' => Mage::helper('catalog')->__('ID'),
            'align' => 'right',
            'sortable' => true,
            'width' => '60',
            'type' => 'number',
            'index' => 'entity_id'
        ));

        $this->addColumn('product_name', array(
            'header' => Mage::helper('catalog')->__('Name'),
            'align' => 'left',
            'index' => 'name',
        ));

        $store = $this->_getStore();
        $this->addColumn('product_sku', array(
            'header' => Mage::helper('catalog')->__('SKU'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'sku'
        ));
        if (!$this->_isExport) {
            $this->addColumn('product_image', array(
                'header' => Mage::helper('catalog')->__('Image'),
                'width' => '90px',
                'renderer' => 'inventory/adminhtml_renderer_productimage',
                'filter' => false,
                'index' => 'product_image'
            ));
        }
        $this->addColumn('product_price', array(
            'header' => Mage::helper('catalog')->__('Price'),
            'align' => 'right',
            'type' => 'currency',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'index' => 'price'
        ));

        $this->addColumn('cost_price', array(
            'header' => Mage::helper('inventory')->__('Cost Price'),
            'align' => 'right',
            'index' => 'cost_price',
            'type' => 'currency',
            'filter_index' => 'cost_price',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
        ));

        $this->addColumn('qty', array(
            'header' => Mage::helper('catalog')->__('Total Qty'),
            'align' => 'right',
            'index' => 'qty',
            'type' => 'number'
        ));

        $this->addColumn('avail_qty', array(
            'header' => Mage::helper('catalog')->__('Available Qty'),
            'align' => 'right',
            'index' => 'warehouse_id',
            'renderer' => 'inventory/adminhtml_inventory_renderer_availqty',
            'filter_condition_callback' => array($this, '_filterAvailCallback'),
            'type' => 'number'
        ));

        $currency_code = Mage::app()->getStore()->getBaseCurrency()->getCode();
        $this->addColumn('total_product_amount', array(
            'header' => Mage::helper('inventory')->__('Total Amount'),
            'index' => 'total_amount',
            'align' => 'right',
            'type' => 'currency',
            'renderer' => 'inventory/adminhtml_inventory_renderer_totalamount',
            'filter_condition_callback' => array($this, '_filterTotalCallback'),
            'currency_code' => $currency_code,
        ));

        $this->addColumn('warehouse_id', array(
            'header' => Mage::helper('catalog')->__('Warehouse') . '<br/>' . Mage::helper('catalog')->__('(Total/ Avail. Qty)'),
            'type' => 'options',
            'options' => Mage::helper('inventory/warehouse')->getAllWarehouseName(),
            'renderer' => 'inventory/adminhtml_inventory_renderer_warehouse',
            'filter_index' => 'inventory.warehouse',
            'filter_condition_callback' => array($this, 'filterCallback'),
            'align' => 'left',
            'index' => 'warehouse_id'
        ));

        $this->addColumn('supplier_id', array(
            'header' => Mage::helper('catalog')->__('Supplier'),
            'type' => 'options',
            'options' => Mage::helper('inventory/supplier')->getAllSupplierName(),
            'renderer' => 'inventory/adminhtml_inventory_renderer_supplier',
            'align' => 'left',
            'index' => 'supplier',
            'filter_index' => 'inventory.supplier',
            'filter_condition_callback' => array($this, 'filterCallbackSupplier'),
        ));

        $this->addColumn('product_status', array(
            'header' => Mage::helper('catalog')->__('Status'),
            'width' => '90px',
            'align' => 'left',
            'index' => 'status',
            'type' => 'options',
            'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('inventory')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('inventory')->__('XML'));

        return parent::_prepareColumns();
    }

    /**
     * get url for each row in grid
     *
     * @return string
     */
    public function getRowUrl($row) {
        return $this->getUrl('*/*/view', array('id' => $row->getId()));
    }

    public function getGridUrl() {
        $warehouseId = $this->getRequest()->getParam('warehouse');
        if ($warehouseId)
            return $this->getUrl('*/adminhtml_inventory/grid', array('warehouse' => $warehouseId));
        else
            return $this->getUrl('*/adminhtml_inventory/grid');
    }

    public function filterCallback($collection, $column) {
        $value = $column->getFilter()->getValue();
        if (!is_null(@$value)) {
            $collection->getSelect()->where('warehouse_product.warehouse_id like ?', '%' . $value . '%');
        }
        return $this;
    }

    public function filterCallbackSupplier($collection, $column) {
        $value = $column->getFilter()->getValue();
        if (!is_null(@$value)) {
            $collection->getSelect()->where('supplier_product.supplier_id like ?', '%' . $value . '%');
        }
        return $this;
    }

    protected function _filterTotalCallback($collection, $column) {
        $filter = $column->getFilter()->getValue();
        foreach ($collection as $item) {
            $total = Mage::helper('inventory')->getTotalValue($item);
            $pass = TRUE;
            if (isset($filter['from']) && $filter['from'] >= 0) {
                if (floatval($total) < floatval($filter['from'])) {
                    $pass = FALSE;
                }
            }
            if ($pass) {
                if (isset($filter['to']) && $filter['to'] >= 0) {
                    if (floatval($total) > floatval($filter['to'])) {
                        $pass = FALSE;
                    }
                }
            }
            if ($pass) {
                $item->setTotalAmount($total);
                $arr[] = $item;
            }
        }
        $temp = Mage::helper('inventory')->_tempCollection(); // A blank collection 
        for ($i = 0; $i < count($arr); $i++) {
            $temp->addItem($arr[$i]);
        }
        $this->setCollection($temp);
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