<?php
 
class Unilab_Webservice_Block_Adminhtml_Token_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
		
        $this->setId('tokenGrid');
		
        $this->setDefaultSort('id');
		
        $this->setDefaultDir('ASC');
		
        $this->setSaveParametersInSession(true);
		
        $this->setUseAjax(true);
    }
 
    protected function _prepareCollection()
    {
		
		$collection = Mage::getModel('webservice/token')->getCollection();

		$this->setCollection($collection);
		
        return parent::_prepareCollection();
    }
 
    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    => Mage::helper('webservice')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'id',
        ));
		
		$this->addColumn('storename', array(
            'header'    => Mage::helper('webservice')->__('Store Name'),
            'align'     =>'left',	
			'width'     => '100px',
            'index'     => 'store',
			'renderer' => 'oneshop/adminhtml_websiteurl_edit_renderer_storename',
        ));	
		

        // $this->addColumn('storename', array(
            // 'header'    => Mage::helper('webservice')->__('Store Name'),
            // 'align'     => 'left',
            // 'width'     => '100px',
            // 'index'     => 'categoryid',
            // 'type'      => 'options',
            // 'options'   =>  'oneshop/adminhtml_websiteurl_edit_renderer_storename', //Mage::getModel('webservice/values_storename')->getstore(), 
        // ));
		
		
		$this->addColumn('hostname', array(
            'header'    => Mage::helper('webservice')->__('Host Name'),
            'align'     =>'left',
			'width'     => '180px',	
            'index'     => 'hostname',
        ));	
		
		
		$this->addColumn('returnurl', array(
            'header'    => Mage::helper('webservice')->__('Return URL'),
            'align'     =>'left',
			'width'     => '250px',	
            'index'     => 'returnurl',
        ));	
		
		
		$this->addColumn('token', array(
            'header'    => Mage::helper('webservice')->__('API Token'),
            'align'     =>'left',
			'width'     => '200px',	
            'index'     => 'token',
        ));		
		

        $this->addColumn('isactive', array(
            'header'    => Mage::helper('webservice')->__('Status'),
            'align'     => 'left',
            'width'     => '50px',
            'index'     => 'isactive',
            'type'      => 'options',
            'options'   => array(
                1 => 'Active',
                0 => 'Inactive',
            ),
        ));
		
		
		$this->addColumn('createddate', array(
            'header'    => Mage::helper('webservice')->__('Created Date'),
            'align'     => 'left',
            'width'     => '100px',
            'type'      => 'datetime',
            'default'   => '--',
            'index'     => 'createddate',
        ));
		
 
        $this->addColumn('updatedate', array(
            'header'    => Mage::helper('webservice')->__('Updated Date'),
            'align'     => 'left',
            'width'     => '100px',
            'type'      => 'datetime',
            'default'   => '--',
            'index'     => 'updatedate',
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