<?php

class Unilab_Priorityshipping_Block_Adminhtml_Priorityshipping_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('minimumordervalueGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }
	
	
	protected function _prepareCollection()
    {
        $collection = Mage::getModel('minimumordervalue/movshipping')->getCollection();

		$this->setCollection($collection);
		
        return parent::_prepareCollection();
    }
	
	protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    => Mage::helper('minimumordervalue')->__('ID'),
            //'align'     =>'left',
            'width'     => '50px',
            'index'     => 'id',
        ));
		
		$this->addColumn('group', array(
            'header'    => Mage::helper('minimumordervalue')->__('Group'),
            //'align'     =>'right',
            'width'     => '100px',
            'index'     => 'group',
        ));
		
		$this->addColumn('listofcities', array(
            'header'    => Mage::helper('minimumordervalue')->__('List of Cities'),
            //'align'     =>'right',
            'width'     => '300px',
            'index'     => 'listofcities',
			'renderer' => 'minimumordervalue/adminhtml_minimumordervalue_edit_renderer_cities',
        ));
		
		$this->addColumn('greaterequal_mov', array(
            'header'    => Mage::helper('minimumordervalue')->__('Greater Equal MOV'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'greaterequal_mov',
        ));
		
		$this->addColumn('lessthan_mov', array(
            'header'    => Mage::helper('minimumordervalue')->__('Less than MOV'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'lessthan_mov',
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