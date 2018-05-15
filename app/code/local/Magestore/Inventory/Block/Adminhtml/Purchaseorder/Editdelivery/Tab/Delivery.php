<?php

class Magestore_Inventory_Block_Adminhtml_Purchaseorder_Editdelivery_Tab_Delivery extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('productGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        if (($this->getPurchaseOrder() && $this->getPurchaseOrder()->getId()) || Mage::getModel('admin/session')->getData('delivery_purchaseorder_product_import'))
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
        $purchaseorder_id = $this->getRequest()->getParam('purchaseorder_id');
        $purchaseorderProducts = Mage::getModel('inventory/purchaseorderproduct')->getCollection()
                ->addFieldToFilter('purchase_order_id', $purchaseorder_id);
        $productIds = array();
        foreach ($purchaseorderProducts as $purchaseorderProduct) {
            if ($purchaseorderProduct->getQtyRecieved() < $purchaseorderProduct->getQty())
                $productIds[] = $purchaseorderProduct->getProductId();
        }
        $collection = Mage::getResourceModel('inventory/product_collection')
                ->addAttributeToSelect('*')
                ->addFieldToFilter('entity_id', array('in' => $productIds))
                ->setIsGroupCountSql(true);

        if ($storeId = $this->getRequest()->getParam('store', 0))
            $collection->addStoreFilter($storeId);
        $collection->getSelect()
                ->joinLeft(array('purchaseorderproduct' => $collection->getTable('erp_inventory_purchase_order_product')), 'e.entity_id=purchaseorderproduct.product_id 
							and purchaseorderproduct.purchase_order_id IN (' . $this->getRequest()->getParam('purchaseorder_id') . ')', array('cost_price' => 'purchaseorderproduct.cost',
                    'tax' => 'purchaseorderproduct.tax',
                    'discount' => 'purchaseorderproduct.discount',
                    'qty' => 'purchaseorderproduct.qty',
                    'qty_recieved' => 'purchaseorderproduct.qty_recieved',
                        )
                )
                ->group('e.entity_id');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $currencyCode = Mage::app()->getStore()->getBaseCurrency()->getCode();

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

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
                ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
                ->load()
                ->toOptionHash();


        $this->addColumn('product_sku', array(
            'header' => Mage::helper('catalog')->__('SKU'),
            'width' => '80px',
            'index' => 'sku'
        ));

        $this->addColumn('cost_price', array(
            'header' => Mage::helper('inventory')->__('Cost'),
            'name' => 'cost_price',
            'type' => 'currency',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'index' => 'cost_price',
        ));

        $this->addColumn('tax', array(
            'header' => Mage::helper('inventory')->__('Tax(%)'),
            'name' => 'tax',
            'type' => 'number',
            'index' => 'tax',
            'filter' => false
        ));

        $this->addColumn('discount', array(
            'header' => Mage::helper('inventory')->__('Discount(%)'),
            'name' => 'discount',
            'type' => 'number',
            'index' => 'discount',
            'filter' => false
        ));

        $this->addColumn('qty', array(
            'header' => Mage::helper('inventory')->__('Total Qty Ordered'),
            'name' => 'qty',
            'type' => 'number',
            'index' => 'qty',
            'filter' => false
        ));
        $this->addColumn('qty_recieved', array(
            'header' => Mage::helper('inventory')->__('Total Qty Received'),
            'name' => 'qty_recived',
            'type' => 'number',
            'index' => 'qty_recieved',
            'filter' => false
        ));

        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $installer = Mage::getModel('core/resource_setup');
        $sql = 'SELECT distinct(`warehouse_id`),warehouse_name,qty_order from ' . $installer->getTable("erp_inventory_purchase_order_product_warehouse") . ' WHERE (purchase_order_id = ' . $this->getRequest()->getParam("purchaseorder_id") . ')';
        $results = $readConnection->fetchAll($sql);
        $warehouseAvailable = $this->getWarehouseAvailable();
        foreach ($results as $result) {
            if (in_array($result['warehouse_id'], $warehouseAvailable))
                $this->addColumn('warehouse_' . $result['warehouse_id'], array(
                    'header' => 'Delivery of ' . $result['warehouse_name'],
                    'name' => 'warehouse_' . $result['warehouse_id'],
                    'type' => 'number',
                    'index' => 'warehouse_' . $result['warehouse_id'],
                    'filter' => false,
                    'editable' => true,
                    'edit_only' => true,
                    'align' => 'right',
                    'sortable' => false,
                ));
        }
    }

    public function getGridUrl() {
        return $this->getUrl('*/*/preparedeliveryGrid', array(
                    '_current' => true,
                    'id' => $this->getRequest()->getParam('id'),
                    'store' => $this->getRequest()->getParam('store')
                ));
    }

    public function _getSelectedProducts() {
        $products = $this->getProducts();
        if (!is_array($products) || Mage::getModel('admin/session')->getData('delivery_purchaseorder_product_import')) {
            $products = array_keys($this->getSelectedProducts());
        }
        return $products;
    }

    public function getSelectedProducts() {
        $purchaseOrder = $this->getPurchaseOrder();
        $products = array();
        $purchaseOrderProducts = Mage::getResourceModel('inventory/purchaseorderproduct_collection')
                ->addFieldToFilter('purchase_order_id', $this->getRequest()->getParam('purchaseorder_id'));

        if ($deliveryProductImports = Mage::getModel('admin/session')->getData('delivery_purchaseorder_product_import')) {
            $productModel = Mage::getModel('catalog/product');
            foreach ($deliveryProductImports as $productImport) {
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
                }
            }
        } else {
            if (count($purchaseOrderProducts)) {
                foreach ($purchaseOrderProducts as $product) {
                    $qtyOrder = $product->getQty();
                    $qtyReceived = $product->getQtyRecieved();
                    $maxReceive = $qtyOrder - $qtyReceived;
                    $resource = Mage::getSingleton('core/resource');
                    $readConnection = $resource->getConnection('core_read');
                    $installer = Mage::getModel('core/resource_setup');
                    $sql = 'SELECT warehouse_id,qty_order,qty_received from ' . $installer->getTable("erp_inventory_purchase_order_product_warehouse") . ' WHERE (purchase_order_id = ' . $this->getRequest()->getParam("purchaseorder_id") . ') AND (product_id = ' . $product->getProductId() . ')';
                    $results = $readConnection->fetchAll($sql);
                    foreach ($results as $result) {
                        $qtyDefault = $result['qty_order'] - $result['qty_received'];
                        if ($qtyDefault < 0)
                            $qtyDefault = 0;
                        $qtyDefault = min($qtyDefault, $maxReceive);
                        $products[$product->getProductId()]['warehouse_' . $result['warehouse_id']] = $qtyDefault;
                    }
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
        return Mage::getModel('inventory/purchaseorder')->load($this->getRequest()->getParam('purchaseorder_id'));
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

    public function getWarehouseAvailable() {
        $adminId = Mage::getModel('admin/session')->getUser()->getId();
        if (!$adminId)
            return null;
        $warehouseAssigneds = Mage::getModel('inventory/assignment')->getCollection()
                ->addFieldToFilter('admin_id', $adminId)
        ;
        $warehouseIds = array();
        foreach ($warehouseAssigneds as $warehouseAssigned) {
            if ($warehouseAssigned->getData('can_edit_warehouse')
                    || $warehouseAssigned->getData('can_transfer')
                    || $warehouseAssigned->getData('can_adjust'))
                $warehouseIds[] = $warehouseAssigned->getWarehouseId();
        }
        return $warehouseIds;
    }

}