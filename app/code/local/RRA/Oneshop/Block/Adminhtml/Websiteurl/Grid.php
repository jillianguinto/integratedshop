<?php
 
class RRA_Oneshop_Block_Adminhtml_Websiteurl_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
		
        $this->setId('websiteurlGrid');
		
        $this->setDefaultSort('id');
		
        $this->setDefaultDir('ASC');
		
        $this->setSaveParametersInSession(true);
		
        $this->setUseAjax(true);
    }
 
    protected function _prepareCollection()
    {
		
		
		$collection = Mage::getModel('oneshop/oneshopurl')->getCollection();

		$this->setCollection($collection);
		
        return parent::_prepareCollection();
    }
 
    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
		
            'header'    => Mage::helper('oneshop')->__('ID'),
			
            'align'     =>'right',
			
            'width'     => '50px',
			
            'index'     => 'id',
        ));
		
		$this->addColumn('websiteurl', array(
            'header'    => Mage::helper('oneshop')->__('Website URL'),
            'align'     =>'left',
			'width'     => '180px',	
            'index'     => 'websiteurl',
        ));		
		
		$this->addColumn('storename', array(
            'header'    => Mage::helper('oneshop')->__('Store Name'),
            'align'     =>'left',
			'width'     => '250px',	
            'index'     => 'categoryid',
			'renderer' => 'oneshop/adminhtml_websiteurl_edit_renderer_storename',
        ));	
		
		$this->addColumn('categoryid', array(
            'header'    => Mage::helper('oneshop')->__('Category ID'),
            'align'     =>'left',
			'width'     => '250px',	
            'index'     => 'categoryid',
        ));	
	
		$this->addColumn('token', array(
            'header'    => Mage::helper('oneshop')->__('Token'),
            'align'     => 'left',
            'width'     => '50px',
            'index'     => 'token',
        ));		
		
		// $this->addColumn('banner', array(
            // 'header'    => Mage::helper('oneshop')->__('Banner'),
            // 'align'     =>'left',
			// 'width'     => '200px',	
            // 'index'     => 'banner',
        // ));		

		
		// $this->addColumn('created_date', array(
            // 'header'    => Mage::helper('oneshop')->__('Created Date'),
            // 'align'     => 'left',
            // 'width'     => '100px',
            // 'type'      => 'datetime',
            // 'default'   => '--',
            // 'index'     => 'created_date',
        // ));
 
        // $this->addColumn('updated_date', array(
            // 'header'    => Mage::helper('oneshop')->__('Updated Date'),
            // 'align'     => 'left',
            // 'width'     => '100px',
            // 'type'      => 'datetime',
            // 'default'   => '--',
            // 'index'     => 'updated_date',
        // ));   
		
		$this->addExportType('*/*/exportXLS', Mage::helper('oneshop')->__('Excel XLS'));
 
        return parent::_prepareColumns();
    }
 
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
 
    public function getGridUrl()
    {
      return $this->getUrl('*/*/grid', array('_current'=>true));
    }
	
 
}