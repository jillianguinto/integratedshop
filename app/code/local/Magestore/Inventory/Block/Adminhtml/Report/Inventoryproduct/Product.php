<?php
class Magestore_Inventory_Block_Adminhtml_Report_Inventoryproduct_Product extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct() {
        parent::__construct();
        $this->setId('inventoryproductinfoGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }
    
    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }
    
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('inventory/product_collection')
            ->addAttributeToSelect('*');
          
         $collection ->getSelect()
                    ->join(
                        array('stock_item' => $collection->getTable('cataloginventory/stock_item')), 
                        'e.entity_id=stock_item.product_id AND stock_item.stock_id=1',
                        array('qty')
                    )
                    ->joinLeft(
                        array('inventory' => $collection->getTable('inventory/inventory')), 
                        'e.entity_id=inventory.product_id AND inventory.last_update',
                        array('cost_price')
                    )
                    ->joinLeft(
                        array('warehouse_product' => $collection->getTable('inventory/warehouseproduct')), 
                        'e.entity_id=warehouse_product.product_id',
                        array('warehouse_id')
                    )
                    ->joinLeft(
                        array('supplier_product' => $collection->getTable('inventory/supplierproduct')), 
                        'e.entity_id=supplier_product.product_id',
                        array('supplier_id')
                    )
                    ->group(array('e.entity_id'))
                ;         
         $store = $this->_getStore();
         if ($store->getId()) {
            $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
            $collection->joinAttribute('name', 'catalog_product/name', 'entity_id', null, 'inner', $adminStore);
            $collection->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $store->getId());
            $collection->addStoreFilter($store);
        }
        $collection->getSelect()->columns(array(
            'product_amount'=> "(stock_item.qty * inventory.cost_price)"
            ));
        $collection->setIsGroupCountSql(true);
        
        $sort = $this->getRequest()->getParam('sort');
        $dir = $this->getRequest()->getParam('dir');
        if($sort == 'cost_price'){
            $collection->getSelect()->order('inventory.cost_price '.$dir);
        }elseif($sort == 'qty'){
            $collection->getSelect()->order('stock_item.qty '.$dir);
        }elseif($sort == 'product_amount'){
            $collection->getSelect()->order('product_amount '.$dir);
        }        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    /**
     * prepare columns for this grid
     *
     * @return Magestore_Inventory_Block_Adminhtml_Inventory_Grid
     */
    protected function _prepareColumns()
    {
       $this->addColumn('entity_id', array(
            'header' => Mage::helper('catalog')->__('ID'),
            'sortable' => true,
            'width' => '60',
			'align'     =>'right',
            'type' => 'number',
            'index' => 'entity_id'
        ));

        $this->addColumn('product_name', array(
            'header' => Mage::helper('catalog')->__('Name'),
            'align' => 'left',
            'index' => 'name',
        )); 

        $this->addColumn('product_sku', array(
            'header' => Mage::helper('catalog')->__('SKU'),
            'width' => '80px',
			'align' => 'left',
            'index' => 'sku'
        ));
        
		if ( ! $this->_isExport ) {
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
			'align'     =>'right',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'index' => 'price'
        ));
        
        $this->addColumn('cost_price', array(
            'header' => Mage::helper('inventory')->__('Cost price'),
            'index' => 'cost_price',
            'type' => 'currency',
			'align'     =>'right',
            'filter_index'=>'cost_price',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'filter_condition_callback' => array($this, '_filterCostPriceCallback')
        ));
        
        $this->addColumn('qty', array(
            'header' => Mage::helper('catalog')->__('Qty'),
            'index' => 'qty',
			'align'     =>'right',
            'type'  => 'number',
            'filter_condition_callback' => array($this, '_filterQtyCallback')
        ));
        
        $this->addColumn('product_amount', array(
            'header' => Mage::helper('catalog')->__('Total Amount'),
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'index' => 'product_amount',
            'type' => 'currency',
			'align'     =>'right',
            'filter_index' =>'(stock_item.qty * inventory.cost_price)',
            'filter_condition_callback' => array($this, '_filterProductAmountCallback'),
        ));
        
        $this->addColumn('warehouse_id', array(
            'header' => Mage::helper('catalog')->__('Stock Availability by Warehouse'),
            'type'  => 'options',
			'align' => 'left',
            'options' => Mage::helper('inventory/warehouse')->getAllWarehouseName(),
            'renderer' => 'inventory/adminhtml_report_inventoryproduct_renderer_warehouse',
            'filter_index' => 'inventory.warehouse',
            'filter_condition_callback' => array($this, 'filterCallback'),
        ));
        
        $this->addColumn('supplier_id', array(
            'header' => Mage::helper('catalog')->__('Supplier'),
            'type'  => 'options',
			'align' => 'left',
            'options' => Mage::helper('inventory/supplier')->getAllSupplierName(),
            'renderer' => 'inventory/adminhtml_inventory_renderer_supplier',
            'index' => 'supplier',
            'filter_index' => 'inventory.supplier',
            'filter_condition_callback' => array($this, 'filterCallbackSupplier'),
        ));
        
        $this->addColumn('product_status', array(
            'header' => Mage::helper('catalog')->__('Status'),
            'width' => '90px',
            'index' => 'status',
			'align' => 'left',
            'type' => 'options',
            'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));
        $this->addExportType('*/*/exportCsvProductInfo', Mage::helper('inventory')->__('CSV'));
        $this->addExportType('*/*/exportXmlProductInfo', Mage::helper('inventory')->__('XML'));
        return parent::_prepareColumns();
    }
    protected function _filterProductAmountCallback($collection, $column) {
        $filter = $column->getFilter()->getValue();
        if (isset($filter['from']) && $filter['from'] ) {
            $collection->getSelect()->where('(stock_item.qty * inventory.cost_price) >= ?', $filter['from']);
        }
        if (isset($filter['to']) && $filter['to'] ) {
            $collection->getSelect()->where('(stock_item.qty * inventory.cost_price) <= ?', $filter['to']);
        }
    }
    
    protected function _filterCostPriceCallback($collection, $column) {        
        $filter = $column->getFilter()->getValue();
        if (isset($filter['from']) && $filter['from'] ) {
            $collection->getSelect()->where('(inventory.cost_price) >= ?', $filter['from']);
        }
        if (isset($filter['to']) && $filter['to'] ) {
            $collection->getSelect()->where('(inventory.cost_price) <= ?', $filter['to']);
        }
    }
    
    protected function _filterQtyCallback($collection, $column) {
        $filter = $column->getFilter()->getValue();
        if (isset($filter['from']) && $filter['from'] ) {
            $collection->getSelect()->where('(stock_item.qty) >= ?', $filter['from']);
        }
        if (isset($filter['to']) && $filter['to'] ) {
            $collection->getSelect()->where('(stock_item.qty) <= ?', $filter['to']);
        }
    }
    /**
     * get url for each row in grid
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return false;
    }
    
    public function getGridUrl(){
        return ;
    }
    
    public function filterCallback($collection, $column)
    {
        $value = $column->getFilter()->getValue();
        if (!is_null(@$value)) {
            $collection->getSelect()->where('warehouse_product.warehouse_id like ?', '%'.$value.'%');
        }
        return $this;
    }
    
    public function filterCallbackSupplier($collection, $column){
        $value = $column->getFilter()->getValue();
        if (!is_null(@$value)) {
            $collection->getSelect()->where('supplier_product.supplier_id like ?', '%'.$value.'%');
        }
        return $this;
    }
    public static function AscSort($a, $b) {
        return $a->getProductAmount() > $b->getProductAmount();
    }

    public static function DescSort($a, $b) {
        return $a->getProductAmount() < $b->getProductAmount();
    }

}