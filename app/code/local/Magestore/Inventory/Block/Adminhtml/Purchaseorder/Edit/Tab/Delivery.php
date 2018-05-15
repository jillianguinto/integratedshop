<?php

class Magestore_Inventory_Block_Adminhtml_Purchaseorder_Edit_Tab_Delivery extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('deliveryGrid');
        $this->setDefaultSort('delivery_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        if ($this->getPurchaseOrder() && $this->getPurchaseOrder()->getId()) {
            $this->setDefaultFilter(array('in_deliveries' => 1));
        }
    }

    protected function _prepareLayout() {
        if ($purchaseOrderId = $this->getRequest()->getParam('id')) {
            $purchaseOrder = Mage::getModel('inventory/purchaseorder')->load($purchaseOrderId);
            $resource = Mage::getSingleton('core/resource');
            $readConnection = $resource->getConnection('core_read');
            $installer = Mage::getModel('core/resource_setup');
            $sql = 'SELECT purchase_order_product_id from ' . $resource->getTableName("erp_inventory_purchase_order_product") . ' WHERE (purchase_order_id = ' . $this->getRequest()->getParam("id") . ') AND (qty_recieved < qty)';
            $results = $readConnection->fetchAll($sql);
            if (($purchaseOrder->getStatus() == '6') || !$results || (count($results) < 1))
                return parent::_prepareLayout();
            if ($this->checkCreateNewDelivery())
                $this->setChild('create_delivery_button', $this->getLayout()->createBlock('adminhtml/widget_button')
                                ->setData(array(
                                    'label' => Mage::helper('inventory')->__('Create a new delivery'),
                                    'onclick' => 'setLocation(\'' . $this->getUrl('*/*/newdelivery', array('purchaseorder_id' => $this->getRequest()->getParam('id'), 'action' => 'newdelivery', '_current' => false)) . '\')',
                                    'class' => 'add'
                                ))
                );
            if ($this->checkCreateAllDelivery())
                $this->setChild('create_all_delivery_button', $this->getLayout()->createBlock('adminhtml/widget_button')
                                ->setData(array(
                                    'label' => Mage::helper('inventory')->__('Create all deliveries'),
                                    'onclick' => 'setLocation(\'' . $this->getUrl('*/*/alldelivery', array('purchaseorder_id' => $this->getRequest()->getParam('id'), 'action' => 'alldelivery', '_current' => false)) . '\')',
                                    'class' => 'add'
                                ))
                );
        }
        return parent::_prepareLayout();
    }

    protected function _addColumnFilterToCollection($column) {
        if ($column->getId() == 'in_deliveries') {
            $deliveryIds = $this->_getSelectedDeliveries();
            if (empty($deliveryIds))
                $deliveryIds = 0;
            if ($column->getFilter()->getValue())
                $this->getCollection()->addFieldToFilter('delivery_id', array('in' => $deliveryIds));
            elseif ($deliveryIds)
                $this->getCollection()->addFieldToFilter('delivery_id', array('nin' => $deliveryIds));
            return $this;
        }
        return parent::_addColumnFilterToCollection($column);
    }

    protected function _prepareCollection() {
        $resource = Mage::getSingleton('core/resource_setup');
        $purchaseOrderId = $this->getRequest()->getParam('id');
        $collection = Mage::getModel('inventory/delivery')->getCollection()
                ->addFieldToFilter('purchase_order_id', $purchaseOrderId);

        $filter = $this->getParam($this->getVarNameFilter(), null);
        $condorder = '';
        if ($filter) {
            $data = $this->helper('adminhtml')->prepareFilterString($filter);
            foreach ($data as $value => $key) {
                if ($value == 'delivery_date') {
                    $condorder = $key;
                }
            }
        }
        if ($condorder) {
            $condorder = Mage::helper('inventory')->filterDates($condorder, array('from', 'to'));
            $from = $condorder['from'];
            $to = $condorder['to'];
            if ($from) {
                $from = date('Y-m-d', strtotime($from));
                $collection->addFieldToFilter('delivery_date', array('gteq' => $from));
            }
            if ($to) {
                $to = date('Y-m-d', strtotime($to));
                $to .= ' 23:59:59';
                $collection->addFieldToFilter('delivery_date', array('lteq' => $to));
            }
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $currencyCode = Mage::app()->getStore()->getBaseCurrency()->getCode();

        $this->addColumn('delivery_id', array(
            'header' => Mage::helper('inventory')->__('ID'),
            'width' => '80px',
            'type' => 'text',
            'index' => 'delivery_id',
        ));

        $this->addColumn('delivery_date', array(
            'header' => Mage::helper('catalog')->__('Delivery Date'),
            'sortable' => true,
            'width' => '60',
            'type' => 'date',
            'index' => 'delivery_date',
            'filter_condition_callback' => array($this, 'filterCreatedOn')
        ));

        $this->addColumn('product_name', array(
            'header' => Mage::helper('catalog')->__('Product Name'),
            'align' => 'left',
            'index' => 'product_name',
        ));


        $this->addColumn('product_sku', array(
            'header' => Mage::helper('catalog')->__('Product SKU'),
            'width' => '80px',
            'index' => 'product_sku'
        ));

        $this->addColumn('product_image', array(
            'header' => Mage::helper('catalog')->__('Image'),
            'width' => '90px',
            'renderer' => 'inventory/adminhtml_renderer_productimage',
            'filter' => false,
        ));

        if ($this->getRequest()->getParam('id')) {
            $resource = Mage::getSingleton('core/resource');
            $readConnection = $resource->getConnection('core_read');
            $installer = Mage::getModel('core/resource_setup');
            $sql = 'SELECT distinct(`warehouse_id`),warehouse_name from ' . $resource->getTableName("erp_inventory_purchase_order_product_warehouse") . ' WHERE (purchase_order_id = ' . $this->getRequest()->getParam("id") . ')';
            $results = $readConnection->fetchAll($sql);
            foreach ($results as $result) {
                $this->addColumn('warehouse_' . $result['warehouse_id'], array(
                    'header' => 'Qty Received for ' . $result['warehouse_name'],
                    'name' => 'warehouse_' . $result['warehouse_id'],
                    'type' => 'number',
                    'index' => 'warehouse_' . $result['warehouse_id'],
                    'filter' => false,
                    'editable' => true,
                    'edit_only' => true,
                    'align' => 'right',
                    'sortable' => false,
                    'renderer' => 'inventory/adminhtml_purchaseorder_edit_tab_renderer_delivery_warehouse'
                ));
            }
        }

        $this->addColumn('qty_delivery', array(
            'header' => Mage::helper('inventory')->__('Total Qty Received'),
            'name' => 'qty_delivery',
            'type' => 'number',
            'index' => 'qty_delivery'
        ));

        $this->addColumn('create_by', array(
            'header' => Mage::helper('inventory')->__('Created by'),
            'name' => 'create_by',
            'index' => 'create_by'
        ));
    }

    public function getGridUrl() {
        return $this->getUrl('*/*/deliveryGrid', array(
                    '_current' => true,
                    'id' => $this->getRequest()->getParam('id'),
                    'store' => $this->getRequest()->getParam('store')
                ));
    }

    protected function _getSelectedDeliveries() {
        $deliveries = $this->getDeliveries();
        if (!is_array($deliveries)) {
            $deliveries = array_keys($this->getSelectedRelatedDeliveries());
        }
        return $deliveries;
    }

    public function getSelectedRelatedDeliveries() {
        $deliveries = array();
        $purchaseOrder = $this->getPurchaseOrder();
        $deliveryCollection = Mage::getResourceModel('inventory/delivery_collection')
                ->addFieldToFilter('purchase_order_id', $purchaseOrder->getId());
        foreach ($deliveryCollection as $delivery) {
            $deliveries[$delivery->getDeliveryId()] = array('qty' => $delivery->getQty());
        }
        return $deliveries;
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

    public function getResetFilterButtonHtml() {
        return $this->getChildHtml('create_delivery_button') . $this->getChildHtml('create_all_delivery_button') . parent::getResetFilterButtonHtml();
    }

    public function checkCreateNewDelivery() {
        $canDelivery = false;
        $adminId = Mage::getModel('admin/session')->getUser()->getId();
        if (!$adminId)
            return null;
        $purchaseOrderId = $this->getRequest()->getParam('id');
        $purchaseOrder = Mage::getModel('inventory/purchaseorder')->load($purchaseOrderId);
        $warehouseIds = $purchaseOrder->getWarehouseId();
        $warehouseIds = explode(',', $warehouseIds);
        $warehouseAssigneds = Mage::getModel('inventory/assignment')->getCollection()
                ->addFieldToFilter('admin_id', $adminId)
        ;
        $warehouseAvailableIds = array();
        foreach ($warehouseAssigneds as $warehouseAssigned) {
            if ($warehouseAssigned->getData('can_edit_warehouse')
                    || $warehouseAssigned->getData('can_transfer')
                    || $warehouseAssigned->getData('can_adjust'))
                $warehouseAvailableIds[] = $warehouseAssigned->getWarehouseId();
        }
        foreach ($warehouseIds as $warehouseId) {
            if (in_array($warehouseId, $warehouseAvailableIds)) {
                $canDelivery = true;
                break;
            }
        }
        return $canDelivery;
    }

    public function checkCreateAllDelivery() {
        $canDelivery = true;
        $adminId = Mage::getModel('admin/session')->getUser()->getId();
        if (!$adminId)
            return null;
        $purchaseOrderId = $this->getRequest()->getParam('id');
        $purchaseOrder = Mage::getModel('inventory/purchaseorder')->load($purchaseOrderId);
        $warehouseIds = $purchaseOrder->getWarehouseId();
        $warehouseIds = explode(',', $warehouseIds);
        $warehouseAssigneds = Mage::getModel('inventory/assignment')->getCollection()
                ->addFieldToFilter('admin_id', $adminId)
        ;
        $warehouseAvailableIds = array();
        foreach ($warehouseAssigneds as $warehouseAssigned) {
            if ($warehouseAssigned->getData('can_edit_warehouse')
                    || $warehouseAssigned->getData('can_transfer')
                    || $warehouseAssigned->getData('can_adjust'))
                $warehouseAvailableIds[] = $warehouseAssigned->getWarehouseId();
        }
        foreach ($warehouseIds as $warehouseId) {
            if (!in_array($warehouseId, $warehouseAvailableIds)) {
                $canDelivery = false;
                break;
            }
        }
        return $canDelivery;
    }

    public function filterCreatedOn($collection, $column) {
        return $this;
    }

}