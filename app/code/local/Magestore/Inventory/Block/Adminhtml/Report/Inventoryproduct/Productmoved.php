<?php 
class Magestore_Inventory_Block_Adminhtml_Report_Inventoryproduct_Productmoved extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct() {
        parent::__construct();
        $this->setId('inventoryproductmovedGrid');
        $this->setDefaultSort('product_id');
        $this->setDefaultDir('DESC');
    }
    
    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }
    
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('inventory/reportproductmoved')->getCollection();
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
       $this->addColumn('product_id', array(
            'header' => Mage::helper('inventory')->__('ID'),
            'sortable' => true,
            'width' => '10',
            'type' => 'number',
			'align'     =>'right',
            'index' => 'product_id'
        ));

        $this->addColumn('product_name', array(
            'header' => Mage::helper('catalog')->__('Product Name'),
            'align' => 'left',
            'index' => 'product_name',
        )); 

        $this->addColumn('product_sku', array(
            'header' => Mage::helper('catalog')->__('Product SKU'),
            'width' => '80px',
			'align' => 'left',
            'index' => 'product_sku'
        ));
        
		if ( ! $this->_isExport ) {
        $this->addColumn('product_image', array(
            'header' => Mage::helper('catalog')->__('Image'),
            'width' => '90px',
            'filter' => false,
            'renderer' => 'inventory/adminhtml_renderer_productimage'   
        ));
        }
		
        $this->addColumn('qty_moved', array(
            'header' => Mage::helper('catalog')->__('Qty Issued'),
            'index' => 'qty_moved',
			'align'     =>'right',
            'type'  => 'number'
        ));
        
        $this->addColumn('moved_at', array(
            'header' => Mage::helper('inventory')->__('Issued on'),
            'type' => 'date',
			'align'     =>'right',
            'index' => 'moved_at'
        ));
        
        $this->addColumn('amount_moved', array(
            'header' => Mage::helper('inventory')->__('Value Issued'),
            'index' => 'amount_moved',
			'align'     =>'right',
            'type' => 'currency',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
        ));
        
        $this->addColumn('moved_type', array(
            'header' => Mage::helper('catalog')->__('Reason for Issuing'),
            'width' => '90px',
			'align' => 'left',
            'index' => 'moved_type',
            'type' => 'options',
            'options' => array('1' => 'Return Order','2' => 'Shipment'),
        ));

        $this->addExportType('*/*/exportCsvProductMoved', Mage::helper('inventory')->__('CSV'));
        $this->addExportType('*/*/exportXmlProductMoved', Mage::helper('inventory')->__('XML'));
        return parent::_prepareColumns();
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
    
}