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
class Magestore_Inventory_Block_Adminhtml_Report_Sales_Case1 extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('productGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
    }

    protected function _getStore() {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToSelect('*');
        if ($warehouseId = $this->getRequest()->getParam('warehouse_select')) {
            $collection->joinField('qty', 'inventory/warehouseproduct', 'qty', 'product_id=entity_id', '{{table}}.qty>0 AND warehouse_id = ' . $warehouseId, 'inner');
        } else {
            $collection->joinField('qty', 'cataloginventory/stock_item', 'qty', 'product_id=entity_id', '{{table}}.stock_id=1', 'inner');
        }
        $store = $this->_getStore();
        if ($store->getId()) {
            //$collection->setStoreId($store->getId());
            $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
            $collection->joinAttribute('name', 'catalog_product/name', 'entity_id', null, 'inner', $adminStore);
            $collection->joinAttribute('custom_name', 'catalog_product/name', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $store->getId());
            $collection->addStoreFilter($store);
        }
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
            'sortable' => true,
            'width' => '60',
            'index' => 'entity_id',
            'filter' => false,
            'sortable' => false
        ));

        $this->addColumn('product_name', array(
            'header' => Mage::helper('catalog')->__('Name'),
            'align' => 'left',
            'index' => 'name',
            'filter' => false,
            'sortable' => false
        ));

        $store = $this->_getStore();
        if ($store->getId()) {
            $this->addColumn('custom_name', array(
                'header' => Mage::helper('catalog')->__('Name in %s', $store->getName()),
                'index' => 'custom_name',
                'filter' => false,
                'sortable' => false
            ));
        }

        $this->addColumn('product_sku', array(
            'header' => Mage::helper('catalog')->__('SKU'),
            'width' => '80px',
            'index' => 'sku',
            'filter' => false,
            'sortable' => false
        ));

        $this->addColumn('product_price', array(
            'header' => Mage::helper('catalog')->__('Price'),
            'type' => 'currency',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'index' => 'price',
            'filter' => false,
            'sortable' => false
        ));



        $this->addColumn('qty', array(
            'header' => Mage::helper('catalog')->__('Qty'),
            'index' => 'qty',
            'type' => 'number',
            'filter' => false,
            'sortable' => false
        ));

        $this->addColumn('supplier_id', array(
            'header' => Mage::helper('catalog')->__('Supplier'),
            'renderer' => 'inventory/adminhtml_inventory_renderer_supplier',
            'filter' => false,
            'sortable' => false
        ));

        $this->addColumn('qty_sold', array(
            'header' => Mage::helper('catalog')->__('Qty Sold'),
            'index' => 'qty_sold',
            'type' => 'number',
            'filter' => false,
            'sortable' => false,
            'renderer' => 'inventory/adminhtml_report_sales_renderer_sales'
        ));
        $this->addColumn('sales_total', array(
            'header' => Mage::helper('catalog')->__('Sales Total'),
            'index' => 'sales_total',
            'type' => 'currency',
            'currency' => 'base_currency_code',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'filter' => false,
            'sortable' => false,
            'renderer' => 'inventory/adminhtml_report_sales_renderer_sales'
        ));

        $this->addColumn('sales_rate', array(
            'header' => Mage::helper('catalog')->__('Avg. Sales (item/day)'),
            'index' => 'sales_rate',
            'type' => 'number',
            'filter' => false,
            'sortable' => false,
            'renderer' => 'inventory/adminhtml_report_sales_renderer_sales'
        ));

        $this->addColumn('product_status', array(
            'header' => Mage::helper('catalog')->__('Status'),
            'width' => '90px',
            'index' => 'status',
            'type' => 'options',
            'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
            'filter' => false,
            'sortable' => false
        ));

        $this->addExportType('*/*/exportCase1Csv', Mage::helper('inventory')->__('CSV'));
        $this->addExportType('*/*/exportCase1Xml', Mage::helper('inventory')->__('XML'));

        return parent::_prepareColumns();
    }

    /**
     * get url for each row in grid
     *
     * @return string
     */
    public function getRowUrl($row) {
        return;
    }

    public function getGridUrl() {
        return $this->getUrl('inventoryadmin/adminhtml_report/gridcase1');
    }

}