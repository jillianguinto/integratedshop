<?php

class Magestore_Inventory_Block_Adminhtml_Purchaseorder_Edit_Tab_Products extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('productGrid');
        if (!$this->getPurchaseOrder()->getId()) {
            $this->setDefaultSort('entity_id');
        } else {
            $this->setDefaultSort('product_id');
        }
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        if (($this->getPurchaseOrder() && $this->getPurchaseOrder()->getId()) || Mage::getModel('admin/session')->getData('purchaseorder_product_import')) {
            $this->setDefaultFilter(array('in_products' => 1));
        }
    }

//    protected function _addColumnFilterToCollection($column){
//    	if ($column->getId() == 'in_products'){
//            $productIds = $this->_getSelectedProducts();
//            if (empty($productIds)) $productIds = 0;
//            if ($column->getFilter()->getValue())
//                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$productIds));
//            elseif ($productIds)
//                $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$productIds));
//            return $this;
//    	}
//    	return parent::_addColumnFilterToCollection($column);
//    }

    protected function _prepareCollection() {
        $supplier_id = $this->getRequest()->getParam('supplier_id');
        if (!$supplier_id) {
            $purchaseOrderId = $this->getRequest()->getParam('id');
            if ($purchaseOrderId) {
                $purchaseOrder = $this->getPurchaseOrder();
                $supplier_id = $purchaseOrder->getSupplierId();
            } else {
                return;
            }
        }
        $supplierProducts = Mage::getModel('inventory/supplierproduct')->getCollection()
                ->addFieldToFilter('supplier_id', $supplier_id);
        $productIds = array();
        if (!$this->getRequest()->getParam('id')) {
            foreach ($supplierProducts as $supplierProduct) {
                $productIds[] = $supplierProduct->getProductId();
            }
        } else {
            $purchaseOrderProducts = Mage::getModel('inventory/purchaseorderproduct')->getCollection()
                    ->addFieldToFilter('purchase_order_id', $this->getRequest()->getParam('id'));
            foreach ($purchaseOrderProducts as $purchaseOrderProduct) {
                $productIds[] = $purchaseOrderProduct->getProductId();
            }
        }
        $collection = Mage::getResourceModel('inventory/product_collection')
                ->addAttributeToSelect('*')
                ->addFieldToFilter('entity_id', array('in' => $productIds))
                ->setIsGroupCountSql(true);
        if ($storeId = $this->getRequest()->getParam('store', 0))
            $collection->addStoreFilter($storeId);
        if (!$this->getRequest()->getParam('id')) {
            $currencyRate = $this->getRequest()->getParam('change_rate');
            $collection->getSelect()
                    ->joinLeft(array('supplierproduct' => $collection->getTable('erp_inventory_supplier_product')), 'e.entity_id=supplierproduct.product_id and supplierproduct.supplier_id IN (' . $supplier_id . ')', array('cost_product' => '(supplierproduct.cost) * ' . $currencyRate,
                        'tax' => 'supplierproduct.tax',
                        'discount' => 'supplierproduct.discount'
                            )
                    )
                    ->group('e.entity_id');
        } else {
            $collection->getSelect()
                    ->joinLeft(array('purchaseorderproduct' => $collection->getTable('erp_inventory_purchase_order_product')), 'e.entity_id=purchaseorderproduct.product_id and purchaseorderproduct.purchase_order_id IN (' . $this->getRequest()->getParam('id') . ')', array('cost_product' => 'purchaseorderproduct.cost',
                        'tax' => 'purchaseorderproduct.tax',
                        'discount' => 'purchaseorderproduct.discount',
                        'qty' => 'purchaseorderproduct.qty',
                        'qty_recieved' => 'purchaseorderproduct.qty_recieved',
                        'qty_returned' => 'purchaseorderproduct.qty_returned'
                            )
                    )
                    ->group('e.entity_id');
            $collection = Mage::getModel('inventory/purchaseorderproduct')->getCollection()
                    ->addFieldToFilter('purchase_order_id', $this->getRequest()->getParam('id'))
                    ->setIsGroupCountSql(true);
        }
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
        if (!$this->getRequest()->getParam('id')) {
            $this->addColumn('entity_id', array(
                'header' => Mage::helper('catalog')->__('ID'),
                'sortable' => true,
                'width' => '60',
                'index' => 'entity_id'
            ));
        } else {
            $this->addColumn('product_id', array(
                'header' => Mage::helper('catalog')->__('ID'),
                'sortable' => true,
                'width' => '60',
                'index' => 'product_id'
            ));
        }

        if (!$this->getRequest()->getParam('id')) {
            $this->addColumn('product_name', array(
                'header' => Mage::helper('catalog')->__('Name'),
                'align' => 'left',
                'index' => 'name',
            ));
        } else {
            $this->addColumn('product_name', array(
                'header' => Mage::helper('catalog')->__('Name'),
                'align' => 'left',
                'index' => 'product_name',
                'renderer' => 'inventory/adminhtml_purchaseorder_edit_tab_renderer_product',
            ));
        }

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
                ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
                ->load()
                ->toOptionHash();
        if (!$this->getRequest()->getParam('id')) {
            $this->addColumn('product_sku', array(
                'header' => Mage::helper('catalog')->__('SKU'),
                'width' => '80px',
                'index' => 'sku'
            ));
        } else {
            $this->addColumn('product_sku', array(
                'header' => Mage::helper('catalog')->__('SKU'),
                'width' => '80px',
                'index' => 'product_sku'
            ));
        }

        $this->addColumn('product_image', array(
            'header' => Mage::helper('catalog')->__('Image'),
            'width' => '90px',
            'filter' => false,
            'renderer' => 'inventory/adminhtml_renderer_productimage'
        ));

        $editable = true;
        if ($this->getRequest()->getParam('id'))
            $editable = false;
        if (!$this->getRequest()->getParam('id')) {
            $currency = $this->getRequest()->getParam('currency');
            $this->addColumn('cost_product', array(
                'header' => Mage::helper('inventory')->__('Cost Price <br />(' . $currency . ')'),
                'name' => 'cost_product',
                'index' => 'cost_product',
                'type' => 'number',
                'filter' => false,
                'editable' => $editable,
                'edit_only' => $editable,
            ));
        } else {
            $currency = Mage::getModel('inventory/purchaseorder')->load($this->getRequest()->getParam('id'))->getCurrency();
            $this->addColumn('cost', array(
                'header' => Mage::helper('inventory')->__('Cost Price <br />(' . $currency . ')'),
                'name' => 'cost',
                'type' => 'currency',
                'currency_code' => (string) $currency,
//                'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
                'index' => 'cost',
                'filter' => false,
                'editable' => $editable,
                'edit_only' => $editable,
            ));
        }
        $this->addColumn('tax', array(
            'header' => Mage::helper('inventory')->__('Tax(%)'),
            'name' => 'tax',
            'type' => 'number',
            'index' => 'tax',
            'filter' => false,
            'editable' => $editable,
            'edit_only' => $editable,
        ));

        $this->addColumn('discount', array(
            'header' => Mage::helper('inventory')->__('Discount(%)'),
            'name' => 'discount',
            'type' => 'number',
            'index' => 'discount',
            'filter' => false,
            'editable' => $editable,
            'edit_only' => $editable,
        ));
        if ($this->getRequest()->getParam('id'))
            $this->addColumn('qty', array(
                'header' => Mage::helper('inventory')->__('Total Qty Ordered'),
                'name' => 'qty',
                'type' => 'number',
                'index' => 'qty',
                'filter' => false
            ));
        if ($warehouseIds = $this->getRequest()->getParam('warehouse_ids')) {
            $warehouseIds = explode(',', $warehouseIds);
            foreach ($warehouseIds as $warehouseId) {
                $this->addColumn('warehouse_' . $warehouseId, array(
                    'header' => 'Qty ordering for <br/>' . $this->getWarehouseById($warehouseId),
                    'name' => 'warehouse_' . $warehouseId,
                    'type' => 'number',
                    'index' => 'warehouse_' . $warehouseId,
                    'filter' => false,
                    'editable' => true,
                    'edit_only' => true,
                    'align' => 'right',
                    'sortable' => false
                ));
            }
        } elseif ($this->getRequest()->getParam('id')) {
            $resource = Mage::getSingleton('core/resource');
            $readConnection = $resource->getConnection('core_read');
            $installer = Mage::getModel('core/resource_setup');
            $sql = 'SELECT distinct(`warehouse_id`),warehouse_name,qty_order from ' . $installer->getTable("erp_inventory_purchase_order_product_warehouse") . ' WHERE (purchase_order_id = ' . $this->getRequest()->getParam("id") . ')';
            $results = $readConnection->fetchAll($sql);
            foreach ($results as $result) {
                $this->addColumn('warehouse_' . $result['warehouse_id'], array(
                    'header' => 'Qty Ordered for <br/> ' . $result['warehouse_name'],
                    'name' => 'warehouse_' . $result['warehouse_id'],
                    'type' => 'number',
                    'index' => 'warehouse_' . $result['warehouse_id'],
                    'filter' => false,
                    'align' => 'right',
                    'sortable' => false,
                    'renderer' => 'inventory/adminhtml_purchaseorder_edit_tab_renderer_warehouse'
                ));
            }
        }

        if ($this->getRequest()->getParam('id')) {
            $this->addColumn('qty_recieved', array(
                'header' => Mage::helper('inventory')->__('Total Qty Received'),
                'name' => 'qty_recieved',
                'type' => 'number',
                'index' => 'qty_recieved',
                'filter' => false,
                'sortable' => false
            ));
        }
        if ($this->getRequest()->getParam('id')) {
            $this->addColumn('qty_returned', array(
                'header' => Mage::helper('inventory')->__('Total Qty Returned'),
                'name' => 'qty_returned',
                'type' => 'number',
                'index' => 'qty_returned',
                'filter' => false,
                'sortable' => false
            ));
        }
    }

    public function getWarehouseById($warehouseId) {
        $warehouse = Mage::getModel('inventory/warehouse')->load($warehouseId);
        return $warehouse->getName();
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
        $warehouseProducts = array();
        if ($productArrays) {
            $products = array();
            foreach ($productArrays as $productArray) {
                parse_str(urldecode($productArray), $purchaseorderProducts);
                if (count($purchaseorderProducts)) {
                    foreach ($purchaseorderProducts as $pId => $enCoded) {
                        $products[] = $pId;
                    }
                }
            }
        }
        if (!is_array($products) || Mage::getModel('admin/session')->getData('purchaseorder_product_import')) {
            $products = array_keys($this->getSelectedRelatedProducts());
        }
        return $products;
    }

    public function getSelectedRelatedProducts() {
        $products = array();
        $purchaseOrder = $this->getPurchaseOrder();
        $productCollection = Mage::getResourceModel('inventory/purchaseorderproduct_collection')
                ->addFieldToFilter('purchase_order_id', $purchaseOrder->getId());
        foreach ($productCollection as $product) {
            $products[$product->getProductId()] = array('qty_order' => $product->getQty());
            $resource = Mage::getSingleton('core/resource');
            $readConnection = $resource->getConnection('core_read');
            $installer = Mage::getModel('core/resource_setup');
            $sql = 'SELECT warehouse_id,qty_order from ' . $installer->getTable("erp_inventory_purchase_order_product_warehouse") . ' WHERE (purchase_order_id = ' . $this->getRequest()->getParam("id") . ') AND (product_id = ' . $product->getProductId() . ')';
            $results = $readConnection->fetchAll($sql);
            foreach ($results as $result) {
                $products[$product->getProductId()]['warehouse_' . $result['warehouse_id']] = $result['qty_order'];
            }
        }
        if ($purchaseOrderProductImports = Mage::getModel('admin/session')->getData('purchaseorder_product_import')) {
            $productModel = Mage::getModel('catalog/product');
            foreach ($purchaseOrderProductImports as $productImport) {
                $productId = $productModel->getIdBySku($productImport['SKU']);
                if ($productId) {
                    foreach ($productImport as $pImport => $p) {
                        if ($pImport != 'SKU') {
                            $pImport = explode('_', $pImport);
                            if ($pImport[1]) {
                                $products[$productId]['warehouse_' . $pImport[1]] = $p;
                            }
                        }
                    }
                    $products[$productId]['cost_product'] = $productImport['COST'];
                    $products[$productId]['tax'] = $productImport['TAX'];
                    $products[$productId]['discount'] = $productImport['DISCOUNT'];
                }
            }
        }
        return $products;
    }

    /**
     * get Current Purchase Order
     *
     * @return Magestore_Inventory_Model_Purchaseorder
     */
    public function getPurchaseOrder() {
        return Mage::getModel('inventory/purchaseorder')->load($this->getRequest()->getParam('id'));
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