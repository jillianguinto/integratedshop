<?php
 
class RRA_Oneshop_Block_Adminhtml_Slider_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
		
        $this->setId('sliderGrid');
		
        $this->setDefaultSort('id');
		
        $this->setDefaultDir('ASC');
		
        $this->setSaveParametersInSession(true);
		
        $this->setUseAjax(true);
    }
 
    protected function _prepareCollection()
    {
		
		
		$collection = Mage::getModel('oneshop/oneshopslider')->getCollection();

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
	
		$this->addColumn('slider_path', array(
            'header'    => Mage::helper('oneshop')->__('Image'),
            'align'     => 'left',
            'width'     => '50px',
            'index'     => 'slider_path',
        ));		
		
		$this->addColumn('isActive', array(
            'header'    => Mage::helper('oneshop')->__('Active'),
            'align'     => 'left',
            'width'     => '50px',
            'index'     => 'isActive',
        ));	
		
 
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