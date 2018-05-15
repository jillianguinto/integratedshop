<?php

class Magestore_Inventory_Block_Adminhtml_Supplyneeds_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('supplyneedsGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
        if (!$this->getFilterData())
            $this->setFilterData(new Varien_Object());
    }

    protected function _prepareLayout() {
        $this->setChild('export_button', $this->getLayout()->createBlock('adminhtml/widget_button')
                        ->setData(array(
                            'label' => Mage::helper('adminhtml')->__('Export'),
                            'onclick' => 'exportCsv()',
                            'class' => 'task'
                        ))
        );

        $this->setChild('reset_filter_button', $this->getLayout()->createBlock('adminhtml/widget_button')
                        ->setData(array(
                            'label' => Mage::helper('adminhtml')->__('Reset Filter'),
                            'onclick' => $this->getJsObjectName() . '.resetFilter()',
                        ))
        );
        $this->setChild('search_button', $this->getLayout()->createBlock('adminhtml/widget_button')
                        ->setData(array(
                            'label' => Mage::helper('adminhtml')->__('Search'),
                            'onclick' => $this->getJsObjectName() . '.doFilter()',
                            'class' => 'task'
                        ))
        );
    }

    protected function _prepareCollection() {
        $requestData = Mage::helper('adminhtml')->prepareFilterString($this->getRequest()->getParam('top_filter'));
        $warehouse = $requestData['warehouse_select'];
        $supplier = $requestData['supplier_select'];
        $supplierModel = Mage::getModel('inventory/supplier');
        $warehouseModel = Mage::getModel('inventory/warehouse');
        $datefrom = $requestData['date_from'];
        $dateto = $requestData['date_to'];
        if (!$datefrom) {
            $now = now();
            // $datefrom1 = date("Y-m-d", strtotime($now));
            $datefrom = date("Y-m-d", Mage::getModel('core/date')->timestamp($now));
        }
        $collection = Mage::getResourceModel('inventory/order_item_collection');
        $collection->getSelect()->group('main_table.product_id');
        $collection->getSelect()->columns(array('total_order' => 'SUM(`main_table`.`qty_ordered`)'));
        if ($warehouse && !$supplier) {
            $collection->getSelect()
                    ->join(
                            array('warehouse_product' => $warehouseModel->getCollection()->getTable('inventory/warehouseproduct')), "main_table.product_id = warehouse_product.product_id and warehouse_product.warehouse_id = '$warehouse'", array('qty'));
            if ($datefrom && $dateto) {
                if (strtotime($datefrom) <= strtotime($dateto)) {
                    $range = (strtotime($dateto) - strtotime($datefrom)) / (3600 * 24);
                    $x = 0;
                    $y = $range * 10;
                    $today = strftime('%Y-%m-%d', strtotime(date("Y-m-d", strtotime($datefrom)) . " -$x day"));
                    $lastperiod = strftime('%Y-%m-%d', strtotime(date("Y-m-d", strtotime($datefrom)) . " -$y day"));
                    $collection->getSelect()
                            ->join(
                                    array('order' => Mage::getModel('sales/order')->getCollection()->getTable('sales/order')), "main_table.order_id = order.entity_id" .
                                    " where (`order`.`created_at` >= '$lastperiod') AND (`order`.`created_at` <= '$today') ", array())
                    ;
                }
            }
        } elseif ($supplier && !$warehouse) {
            $collection->getSelect()
                    ->join(
                            array('supplier_product' => $supplierModel->getCollection()->getTable('inventory/supplierproduct')), "main_table.product_id = supplier_product.product_id and supplier_product.supplier_id = $supplier", array()
                    )
                    ->join(
                            array('catalog_inventory' => Mage::getModel('cataloginventory/stock_item')->getCollection()->getTable('cataloginventory/stock_item')), "main_table.product_id = catalog_inventory.product_id", array('qty')
                    )
            ;
            if ($datefrom && $dateto) {
                if (strtotime($datefrom) < strtotime($dateto)) {
                    $range = (strtotime($dateto) - strtotime($datefrom)) / (3600 * 24);
                    $x = 0;
                    $y = $range * 10;
                    $today = strftime('%Y-%m-%d', strtotime(date("Y-m-d", strtotime($datefrom)) . " -$x day"));
                    $lastperiod = strftime('%Y-%m-%d', strtotime(date("Y-m-d", strtotime($datefrom)) . " -$y day"));
                    $collection->getSelect()
                            ->join(
                                    array('order' => Mage::getModel('sales/order')->getCollection()->getTable('sales/order')), "main_table.order_id = order.entity_id" .
                                    " where (`order`.`created_at` >= '$lastperiod') AND (`order`.`created_at` <= '$today') ", array())
                    ;
                }
            }
        } elseif ($supplier && $warehouse) {
            $collection->getSelect()
                    ->join(
                            array('warehouse_product' => $warehouseModel->getCollection()->getTable('inventory/warehouseproduct')), "main_table.product_id = warehouse_product.product_id and warehouse_product.warehouse_id = '$warehouse'", array('qty'));

            $collection->getSelect()->join(
                    array('supplier_product' => $supplierModel->getCollection()->getTable('inventory/supplierproduct')), "main_table.product_id = supplier_product.product_id and supplier_product.supplier_id = $supplier", array()
            );
            if ($datefrom && $dateto) {
                if (strtotime($datefrom) < strtotime($dateto)) {
                    $range = (strtotime($dateto) - strtotime($datefrom)) / (3600 * 24);
                    $x = 0;
                    $y = $range * 10;
                    $today = strftime('%Y-%m-%d', strtotime(date("Y-m-d", strtotime($datefrom)) . " -$x day"));
                    $lastperiod = strftime('%Y-%m-%d', strtotime(date("Y-m-d", strtotime($datefrom)) . " -$y day"));
                    $collection->getSelect()
                            ->join(
                                    array('order' => Mage::getModel('sales/order')->getCollection()->getTable('sales/order')), "main_table.order_id = order.entity_id" .
                                    " where (`order`.`created_at` >= '$lastperiod') AND (`order`.`created_at` <= '$today') ", array())
                    ;
                }
            }
        } elseif (!$supplier && !$warehouse) {
            $collection->getSelect()->join(
                    array('catalog_inventory' => Mage::getModel('cataloginventory/stock_item')->getCollection()->getTable('cataloginventory/stock_item')), "main_table.product_id = catalog_inventory.product_id", array('qty')
            );
            if ($datefrom && $dateto) {
                if (strtotime($datefrom) < strtotime($dateto)) {
                    $range = (strtotime($dateto) - strtotime($datefrom)) / (3600 * 24);
                    $x = 0;
                    $y = $range * 10;
                    $today = strftime('%Y-%m-%d', strtotime(date("Y-m-d", strtotime($datefrom)) . " -$x day"));
                    $lastperiod = strftime('%Y-%m-%d', strtotime(date("Y-m-d", strtotime($datefrom)) . " -$y day"));
                    $collection->getSelect()
                            ->join(
                                    array('order' => Mage::getModel('sales/order')->getCollection()->getTable('sales/order')), "main_table.order_id = order.entity_id" .
                                    " where (`order`.`created_at` >= '$lastperiod') AND (`order`.`created_at` <= '$today') ", array())
                            ->group('main_table.product_id')
                    ;
                }
            }
        }

        $filter = $this->getParam($this->getVarNameFilter(), null);
        if ($filter) {
            $data = $this->helper('adminhtml')->prepareFilterString($filter);
            foreach ($data as $value => $key) {
                if ($value == 'product_id') {
                    $condorder = $key;
                }
            }
        }
        if ($condorder) {
            $from = $condorder['from'];
            $to = $condorder['to'];
            if ($from) {
                $collection->getSelect()
                        ->where('main_table.product_id >= \'' . $from . '\'')
                ;
            }
            if ($to) {
                $collection->getSelect()
                        ->where('main_table.product_id <= \'' . $to . '\'')
                ;
            }
        }

        $sort = $this->getRequest()->getParam('sort');
        $collection->setIsGroupCountSql(true);
        if (!Mage::registry('supplyneeds_collection'))
            Mage::register('supplyneeds_collection', $collection);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    public function addExportType($url, $label) {
        $this->_exportTypes[] = new Varien_Object(
                        array(
                            'url' => $this->getUrl($url, array(
                                '_current' => true,
                                'filter' => $this->getParam($this->getVarNameFilter(), null)
                                    )
                            ),
                            'label' => $label
                        )
        );
        return $this;
    }

    protected function _prepareColumns() {
        $this->addColumn('product_id', array(
            'header' => Mage::helper('catalog')->__('ID'),
//            'sortable' => true,
            'width' => '30px',
            'index' => 'product_id',
            'type' => 'number',
            'filter_condition_callback' => array($this, 'filter_custom_column_callback'),
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('catalog')->__('Product Name'),
            'align' => 'left',
            'index' => 'name',
            'width' => '280px',
            'renderer' => 'inventory/adminhtml_supplyneeds_renderer_productname',
//            'filter_condition_callback' => array($this, 'filter_product_name_callback'),
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

        $this->addColumn('qty', array(
            'header' => Mage::helper('catalog')->__('Total Qty in warehouse'),
            'width' => '80px',
            'index' => 'qty',
            'type' => 'number',
        ));

        $this->addColumn('supplier', array(
            'header' => Mage::helper('catalog')->__('Supplier'),
            'renderer' => 'inventory/adminhtml_supplyneeds_renderer_supplier',
            'width' => '200px',
            'sortable' => false,
            'filter' => false,
        ));

        $this->addColumn('qty_for_supply', array(
            'header' => Mage::helper('catalog')->__('Min/Max Qty Needed More'),
            'width' => '80px',
            'type' => 'text',
            'filter' => false,
            'sortable' => false,
            'renderer' => 'inventory/adminhtml_supplyneeds_renderer_qtyforsupply'
        ));

        $this->addColumn('supply_needs', array(
            'header' => Mage::helper('catalog')->__('Qty to Purchase More'),
            'width' => '80px',
            'type' => 'number',
            'filter' => false,
            'sortable' => false,
            'renderer' => 'inventory/adminhtml_supplyneeds_renderer_supplyneeds',
        ));
        $this->addExportType('/*/*/', Mage::helper('inventory')->__('CSV'));
        return parent::_prepareColumns();
    }

    public function getGridUrl() {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    public function getRowUrl() {
        
    }

    protected function filter_custom_column_callback($collection, $column) {
        return $this;
    }

    protected function filter_product_name_callback($collection, $column) {
        $value = $column->getFilter()->getValue();
        if (!is_null(@$value)) {
            $collection->getSelect()->where('main_table.product_name like ?', '%' . $value . '%');
        }
        return $this;
    }

}

?>
