<?php

class Magestore_Inventory_Block_Adminhtml_Report_Purchases_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('purchaseorderGrid');
        $this->setDefaultSort('purchase_order_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        if (!$this->getFilterData())
            $this->setFilterData(new Varien_Object());
        $this->setCountTotals(true);
    }

    protected function _afterLoadCollection() {
        $totalObj = new Magestore_Inventory_Model_Warehousesales_Totals();
        $this->setTotals($totalObj->countTotals($this));
    }

    /**
     * prepare collection for block to display
     *
     * @return Magestore_Inventory_Block_Adminhtml_Report_Purchases_Grid
     */
    protected function _prepareCollection() {
        $supplierId = $this->getRequest()->getParam('supplier_select');
        $periodId = $this->getRequest()->getParam('period');
        $datefrom = $this->getRequest()->getParam('date_from');
        $dateto = $this->getRequest()->getParam('date_to');
        if (!$supplierId) {
            if (!$periodId) {
                $collection = Mage::getModel('inventory/supplier')
                        ->getCollection();
                $collection->getSelect()
                        ->join(array('purchaseorder' => $collection->getTable('inventory/purchaseorder')), 'main_table.supplier_id=purchaseorder.supplier_id', array('total_amount' => 'sum(purchaseorder.total_amount)',
                            'total_products' => 'sum(purchaseorder.total_products)',
                            'total_products_recieved' => 'sum(purchaseorder.total_products_recieved)'
                        ))
                        ->group('main_table.supplier_id');
            } else {
                $collection = Mage::getModel('inventory/purchaseorder')
                        ->getCollection()
                        ->setOrder('purchase_on', 'ASC');
                if ($periodId == 'day') {
                    $collection->getSelect()
                            ->join(array('supplier' => $collection->getTable('inventory/supplier')), 'main_table.supplier_id=supplier.supplier_id', array('total_amount' => 'sum(main_table.total_amount)',
                                'total_products' => 'sum(main_table.total_products)',
                                'total_products_recieved' => 'sum(main_table.total_products_recieved)',
                                'name' => 'supplier.name',
                                'street' => 'supplier.street',
                                'city' => 'supplier.city',
                                'country_id' => 'supplier.country_id',
                                    )
                            )
                            ->group('main_table.purchase_on');
                } elseif ($periodId == 'month') {
                    $collection->getSelect()
                            ->join(array('supplier' => $collection->getTable('inventory/supplier')), 'main_table.supplier_id=supplier.supplier_id', array('total_amount' => 'sum(main_table.total_amount)',
                                'total_products' => 'sum(main_table.total_products)',
                                'total_products_recieved' => 'sum(main_table.total_products_recieved)',
                                'name' => 'supplier.name',
                                'street' => 'supplier.street',
                                'city' => 'supplier.city',
                                'country_id' => 'supplier.country_id',
                                'periods' => 'main_table.purchase_on'
                                    )
                            )
                            ->group('month(main_table.purchase_on)')
                            ->group('supplier.name');
                } elseif ($periodId == 'year') {
                    $collection->getSelect()
                            ->join(array('supplier' => $collection->getTable('inventory/supplier')), 'main_table.supplier_id=supplier.supplier_id', array('total_amount' => 'sum(main_table.total_amount)',
                                'total_products' => 'sum(main_table.total_products)',
                                'total_products_recieved' => 'sum(main_table.total_products_recieved)',
                                'name' => 'supplier.name',
                                'street' => 'supplier.street',
                                'city' => 'supplier.city',
                                'country_id' => 'supplier.country_id',
                                'periods' => 'year(main_table.purchase_on)'
                                    )
                            )
                            ->group('year(main_table.purchase_on)')
                            ->group('supplier.name')
                    ;
                }
            }
        } elseif ($supplierId) {
            $collection = Mage::getModel('inventory/supplierproduct')
                    ->getCollection();
            $purchaseOrderIds = '';
            $purchaseOrders = Mage::getModel('inventory/purchaseorder')
                    ->getCollection()
                    ->addFieldToFilter('supplier_id', $supplierId);
            $i = 0;
            foreach ($purchaseOrders as $purchaseOrder) {
                if ($i == 0) {
                    $purchaseOrderIds .= '\'' . $purchaseOrder->getId() . '\'';
                } else {
                    $purchaseOrderIds .= ',\'' . $purchaseOrder->getId() . '\'';
                }
                $i++;
            }
            $collection->getSelect()
                    ->join(array('purchaseorder' => $collection->getTable('inventory/purchaseorder')), 'purchaseorder.supplier_id=' . $supplierId, array('purchaseorder.supplier_id'));
            $collection->getSelect()
                    ->joinLeft(array('purchaseorderproduct' => $collection->getTable('inventory/purchaseorderproduct')), 'main_table.product_id=purchaseorderproduct.product_id and purchaseorderproduct.purchase_order_id IN (' . $purchaseOrderIds . ')', array('qty' => 'sum(purchaseorderproduct.qty)',
                        'product_name' => 'purchaseorderproduct.product_name',
                        'product_sku' => 'purchaseorderproduct.product_sku'
                    ))
                    ->group('main_table.product_id');
        }
        if ($datefrom) {
            $datefrom = date('Y-m-d H:i:s', strtotime($datefrom));
            if (!$periodId) {
                $collection->getSelect()
                        ->where('purchaseorder.purchase_on >= \'' . $datefrom . '\'');
            } else {
                $collection->getSelect()
                        ->where('main_table.purchase_on >= \'' . $datefrom . '\'');
            }
        }
        if ($dateto) {
            $dateto = date('Y-m-d H:i:s', strtotime($dateto));
            if (!$periodId) {
                $collection->getSelect()
                        ->where('purchaseorder.purchase_on <= \'' . $dateto . '\'');
            } else {
                $collection->getSelect()
                        ->where('main_table.purchase_on <= \'' . $dateto . '\'');
            }
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare columns for this grid
     *
     * @return Magestore_Inventory_Block_Adminhtml_Report_Purchases_Grid
     */
    protected function _prepareColumns() {
        $currencyCode = Mage::app()->getStore()->getBaseCurrency()->getCode();
        $supplierId = $this->getRequest()->getParam('supplier_select');
        $periodId = $this->getRequest()->getParam('period');
        $datefrom = $this->getRequest()->getParam('date_from');
        $dateto = $this->getRequest()->getParam('date_to');
        if (!$supplierId) {
            if ($periodId) {
                if ($periodId == 'day') {
                    $this->addColumn('purchase_on', array(
                        'header' => Mage::helper('inventory')->__('Period'),
                        'align' => 'left',
                        'index' => 'purchase_on',
                        'type' => 'date'
                    ));
                } elseif ($periodId == 'month') {
                    $this->addColumn('periods', array(
                        'header' => Mage::helper('inventory')->__('Period'),
                        'align' => 'left',
                        'index' => 'periods',
                        'type' => 'date',
                        'format' => 'M/Y'
                    ));
                } else {
                    $this->addColumn('periods', array(
                        'header' => Mage::helper('inventory')->__('Period'),
                        'align' => 'left',
                        'index' => 'periods',
                    ));
                }
            }
            $this->addColumn('name', array(
                'header' => Mage::helper('inventory')->__('Supplier Name'),
                'align' => 'left',
                'index' => 'name',
                'totals_label' => Mage::helper('inventory')->__('Total'),
            ));
            $this->addColumn('street', array(
                'header' => Mage::helper('inventory')->__('Street'),
                'align' => 'left',
                'index' => 'street',
            ));
            $this->addColumn('city', array(
                'header' => Mage::helper('inventory')->__('City'),
                'align' => 'left',
                'index' => 'city',
            ));
            $this->addColumn('country_id', array(
                'header' => Mage::helper('inventory')->__('Country'),
                'align' => 'left',
                'index' => 'country_id',
                'type' => 'options',
                'options' => Mage::helper('inventory')->getCountryList()
            ));
            $this->addColumn('total_amount', array(
                'header' => Mage::helper('inventory')->__('Purchase Amount'),
                'align' => 'right',
                'index' => 'total_amount',
                'type' => 'price',
                'currency_code' => $currencyCode,
                'total' => 'sum'
            ));
            $this->addColumn('total_products', array(
                'header' => Mage::helper('inventory')->__('Total Qty Ordered'),
                'align' => 'right',
                'index' => 'total_products',
                'type' => 'number',
                'total' => 'sum'
            ));
            $this->addColumn('total_products_recieved', array(
                'header' => Mage::helper('inventory')->__('Total Qty Received'),
                'align' => 'right',
                'index' => 'total_products_recieved',
                'type' => 'number',
                'total' => 'sum'
            ));
        } elseif ($supplierId) {
            if ($periodId) {
                $this->addColumn('period', array(
                    'header' => Mage::helper('inventory')->__('Period'),
                    'align' => 'left',
                    'index' => 'period',
                ));
            }
            $this->addColumn('product_id', array(
                'header' => Mage::helper('inventory')->__('Id'),
                'align' => 'left',
                'index' => 'product_id',
            ));
            $this->addColumn('product_name', array(
                'header' => Mage::helper('inventory')->__('Product Name'),
                'align' => 'left',
                'index' => 'product_name',
            ));
            $this->addColumn('product_sku', array(
                'header' => Mage::helper('inventory')->__('Product Sku'),
                'align' => 'left',
                'index' => 'product_sku',
            ));
            $this->addColumn('qty', array(
                'header' => Mage::helper('inventory')->__('Qty Purchase'),
                'align' => 'left',
                'index' => 'qty',
                'type' => 'number'
            ));
        }
        $this->addExportType('*/*/exportPurchaseCsv', Mage::helper('inventory')->__('CSV'));
        $this->addExportType('*/*/exportPurchaseXml', Mage::helper('inventory')->__('XML'));

        return parent::_prepareColumns();
    }

}
