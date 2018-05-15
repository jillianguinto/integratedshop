<?php
 
class Unilab_Webservice_Block_Adminhtml_Connectionlogs_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('connectionlogsGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }
 
 
    protected function _prepareCollection()
    {
		$collection = Mage::getModel('webservice/connectionlogs')->getCollection();
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
		
		$this->addColumn('store', array(
            'header'    => Mage::helper('webservice')->__('Store Name'),
            'align'     =>'left',
			'width'     => '180px',	
            'index'     => 'store',
        ));		
		
		
		$this->addColumn('hostname', array(
            'header'    => Mage::helper('webservice')->__('Host Name'),
            'align'     =>'left',
			'width'     => '180px',	
            'index'     => 'hostname',
        ));		
		

		$this->addColumn('tokenused', array(
            'header'    => Mage::helper('webservice')->__('Token Used'),
            'align'     =>'left',
			'width'     => '200px',	
            'index'     => 'tokenused',
        ));	
        
        
		$this->addColumn('cmdevent', array(
            'header'    => Mage::helper('webservice')->__('Command Event'),
            'align'     =>'left',
            'index'     => 'cmdevent',
			'width'     => '200px',	
			
        ));	
        

		$this->addColumn('dataposted', array(
            'header'    => Mage::helper('webservice')->__('Data Posted'),
            'align'     =>'left',
            'index'     => 'dataposted',
			'width'     => '200px',	
        ));	
		
		
		$this->addColumn('response', array(
            'header'    => Mage::helper('webservice')->__('Response'),
            'align'     =>'left',
            'index'     => 'response',
			'width'     => '200px',	
        ));
		
		$this->addColumn('created_date', array(
            'header'    => Mage::helper('webservice')->__('Created Date'),
            'align'     => 'left',
            'width'     => '150px',
            'type'      => 'datetime',
            'index'     => 'created_date',
        ));
 

        return parent::_prepareColumns();
    }
 
 
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/index', array('id' => $row->getId()));
    }
	
 
    public function getGridUrl()
    {
      return $this->getUrl('*/*/grid', array('_current'=>true));
    }
	
 
}