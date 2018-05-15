<?php
class Magestore_Inventory_Block_Adminhtml_Listadjuststock_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct() {
        parent::__construct();
        $this->setId('listadjuststockGrid');
        $this->setDefaultSort('adjuststock_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
    }
    
    /**
     * prepare collection for block to display
     *
     * @return Magestore_Inventory_Block_Adminhtml_Adjuststock_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('inventory/adjuststock')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    /**
     * prepare columns for this grid
     *
     * @return Magestore_Inventory_Block_Adminhtml_Adjuststock_Grid
     */
    protected function _prepareColumns()
    {
       $this->addColumn('adjuststock_id', array(
            'header' => Mage::helper('inventory')->__('ID'),
            'sortable' => true,
            'width' => '60',
            'index' => 'adjuststock_id'
        ));
       
       $this->addColumn('created_at', array(
            'header' => Mage::helper('inventory')->__('Date Adjust'),
            'type'  => 'date',
            'width'     => '150px',
            'index' => 'created_at'
        ));
       
      $this->addColumn('warehouse_name', array(
            'header' => Mage::helper('inventory')->__('Warehouse Adjusted'),
            'width'     => '150px',
            'index' => 'warehouse_name'
        ));
      

      $this->addColumn('warehouse_contact', array(
            'header' => Mage::helper('inventory')->__('Warehouse Contact'),
            'width'     => '150px',
            'index' => 'warehouse_id',
            'renderer' => 'Magestore_Inventory_Block_Adminhtml_Listadjuststock_Rendererwarehousecontact'
        )); 
      
      $this->addColumn('warehouse_email', array(
            'header' => Mage::helper('inventory')->__('Warehouse Email'),
            'width'     => '150px',
            'index' => 'warehouse_id',
            'renderer' => 'Magestore_Inventory_Block_Adminhtml_Listadjuststock_Rendererwarehouseemail'
        )); 
      
      $this->addColumn('warehouse_phone', array(
            'header' => Mage::helper('inventory')->__('Warehouse Phone'),
            'width'     => '150px',
            'index' => 'warehouse_id',
            'renderer' => 'Magestore_Inventory_Block_Adminhtml_Listadjuststock_Rendererwarehousetelephone'
        )); 
      
      $this->addColumn('warehouse_country', array(
            'header' => Mage::helper('inventory')->__('Warehouse Country'),
            'width'     => '150px',
            'index' => 'warehouse_id',
            'renderer' => 'Magestore_Inventory_Block_Adminhtml_Listadjuststock_Rendererwarehousecountry'
        ));

        $this->addColumn('action',
            array(
                'header'    =>    Mage::helper('inventory')->__('Action'),
                'width'        => '100',
                'type'        => 'action',
                'getter'    => 'getId',
                'actions'    => array(
                    array(
                        'caption'    => Mage::helper('inventory')->__('View'),
                        'url'        => array('base'=> '*/*/edit'),
                        'field'        => 'id'
                    )),
                'filter'    => false,
                'sortable'    => false,
                'index'        => 'stores',
                'is_system'    => true,
        ));

        return parent::_prepareColumns();
    }
    
    /**
     * get url for each row in grid
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
    
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid');
    }
    
    public function rendertest($row)
    {
        return 'test';
    }
    
}