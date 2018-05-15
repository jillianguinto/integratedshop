<?php 
class Magestore_Inventory_Block_Adminhtml_Report_Inventoryproduct_Productpurchased extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct() {
        parent::__construct();
        $this->setId('inventoryproductrecievedGrid');
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
        $collection = Mage::getModel('inventory/reportproductreceived')->getCollection();
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
			'align'     =>'right',
            'type' => 'number',
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
		
        $this->addColumn('qty_received', array(
            'header' => Mage::helper('catalog')->__('Qty Received'),
            'index' => 'qty_received',
			'align'     =>'right',
            'type'  => 'number'
        ));
        
        $this->addColumn('received_at', array(
            'header' => Mage::helper('inventory')->__('Received On'),
            'type' => 'date',
			'align'     =>'right',
            'index' => 'received_at'
        ));
        
        $this->addColumn('amount_received', array(
            'header' => Mage::helper('inventory')->__('Value Received'),
            'index' => 'amount_received',
			'align'     =>'right',
            'type' => 'currency',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
        ));
        
        $this->addColumn('received_type', array(
            'header' => Mage::helper('catalog')->__('Reason for Receiving'),
            'width' => '90px',
			'align' => 'left',
            'index' => 'received_type',
            'type' => 'options',
            'options' => array('1' => 'Purchase Order Delivery','2' => 'Customer Refund'),
        ));

        $this->addExportType('*/*/exportCsvProductReceived', Mage::helper('inventory')->__('CSV'));
        $this->addExportType('*/*/exportXmlProductReceived', Mage::helper('inventory')->__('XML'));
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