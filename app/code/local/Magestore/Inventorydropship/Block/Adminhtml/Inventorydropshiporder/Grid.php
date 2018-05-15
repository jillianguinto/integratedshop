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
 * @package     Magestore_Inventorydropship
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Inventorydropship Grid Block
 * 
 * @category    Magestore
 * @package     Magestore_Inventorydropship
 * @author      Magestore Developer
 */
class Magestore_Inventorydropship_Block_Adminhtml_Inventorydropshiporder_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('inventorydropship_order_grid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
//        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }
    
    /**
     * prepare collection for block to display
     *
     * @return Magestore_Inventorydropship_Block_Adminhtml_Inventorydropship_Grid
     */
    protected function _getCollectionClass() {
        return 'sales/order_grid_collection';
    }

    protected function _prepareCollection() {
        $resource = Mage::getSingleton('core/resource');
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $collection->addAttributeToSelect('*');
        $orderIds = array();
        foreach(Mage::getModel('inventorydropship/inventorydropship')->getCollection() as $inventoryDropship){
            if(!in_array($inventoryDropship->getOrderId(),$orderIds))
                $orderIds[] = $inventoryDropship->getOrderId();
        }        
        $collection->addFieldToFilter('entity_id',array('in'=>$orderIds));
//        $collection->getSelect()
//            ->joinLeft(
//                array('inventorydropship_shipment' => $resource->getTableName('inventorydropship/inventorydropship')), 
//                'main_table.entity_id=inventorydropship_shipment.order_id', 
//                array('')
//                array('GROUP_CONCAT(DISTINCT inventorydropship_shipment.supplier_name) AS supplier_name')
//            )
//            ->group('main_table.entity_id')
//        ;
        $this->setCollection($collection);
        try {
            parent::_prepareCollection();
        } catch (Exception $e) {
        }
        return $this;
    }

    protected function _prepareColumns() {

        $this->addColumn('real_order_id', array(
            'header' => Mage::helper('sales')->__('Order #'),
            'width' => '80px',
			'align'     =>'right',
            'type' => 'text',
            'index' => 'increment_id',
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header' => Mage::helper('sales')->__('Purchased From (Store)'),
                'index' => 'store_id',
                'type' => 'store',
				'align' => 'left',
                'store_view' => true,
                'display_deleted' => true,
            ));
        }

        $this->addColumn('created_at', array(
            'header' => Mage::helper('sales')->__('Purchased On'),
            'index' => 'created_at',
            'type' => 'datetime',
			'align'     =>'right',
            'width' => '100px',
        ));

        $this->addColumn('billing_name', array(
            'header' => Mage::helper('sales')->__('Bill to Name'),
			'align' => 'left',
            'index' => 'billing_name',
        ));

        $this->addColumn('shipping_name', array(
            'header' => Mage::helper('sales')->__('Ship to Name'),
			'align' => 'left',
            'index' => 'shipping_name',
        ));

        $this->addColumn('base_grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Base)'),
            'index' => 'base_grand_total',
            'type' => 'currency',
			'align'     =>'right',
            'currency' => 'base_currency_code',
        ));

        $this->addColumn('grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Purchased)'),
            'index' => 'grand_total',
            'type' => 'currency',
			'align'     =>'right',
            'currency' => 'order_currency_code',
        ));
        
        $this->addColumn('status', array(
            'header' => Mage::helper('sales')->__('Status'),
            'index' => 'status',
            'type' => 'options',
			'align' => 'left',
            'width' => '70px',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
        ));
                
        
//        $this->addColumn('names', array(
//            'header' => Mage::helper('sales')->__('Warehouses Shipped'),
//            'index' => 'names',
//			'align' => 'left',
//            'filter_index' => 'inventory_shipment.warehouse_name',
//            'type' => 'options',
//            'options' => Mage::helper('inventory/warehouse')->getAllWarehouseName(),
//            'filter_condition_callback' => array($this, 'filterCallback'),
//            'sortable' => false,
//            'renderer' => 'inventory/adminhtml_report_customerorders_renderer_warehouse'
//        ));

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            $this->addColumn('action',
                array(
                    'header'    =>    Mage::helper('inventorydropship')->__('Action'),
                    'width'        => '100',
                    'type'        => 'action',
                    'getter'    => 'getId',
                    'actions'    => array(
                        array(
                            'caption'    => Mage::helper('inventorydropship')->__('View'),
                            'url'        => array('base'=> 'adminhtml/sales_order/view'),
                            'field'        => 'order_id'
                        )),
                    'filter'    => false,
                    'sortable'    => false,
                    'index'        => 'stores',
                    'is_system'    => true,
            ));
        }
//        $this->addRssList('rss/order/new', Mage::helper('sales')->__('New Order RSS'));
//
//        $this->addExportType('*/*/exportCustomerOrdersCsv', Mage::helper('sales')->__('CSV'));
//        $this->addExportType('*/*/exportCustomerOrdersExcel', Mage::helper('sales')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            return $this->getUrl('adminhtml/sales_order/view', array('order_id' => $row->getId()));
        }
        return false;
    }

    public function getGridUrl() {
//        return $this->getUrl('inventoryadmin/adminhtml_report/customerorders', array('_current' => true));
        return $this->getUrl('*/*/dropshipordersgrid', array('_current'=>true));
    }
}