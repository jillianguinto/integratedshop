<?php

class Unilab_CityDropdown_Block_Adminhtml_Citydropdown_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	public function __construct() { 
		parent::__construct();
		$this -> setId('cityGrid');
		$this -> setDefaultSort('city_id');
		$this -> setDefaultDir('DESC');
		$this -> setSaveParametersInSession(true);
	}

	protected function _prepareCollection() {
		$collection = Mage::getModel('citydropdown/citydropdown') -> getCollection();
		/* $collection->getSelect()->joinLeft(array('region'=>Mage::getSingleton('core/resource')->getTableName(				'directory/country_region_name')), 
					'main_table.region_id = region.region_id',
					array('region_name'=>'region.name','region.name')); */
		$collection->addFieldToFilter("country_id",array('PH'));

																  
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns() { 
  

	/* 	$this -> addColumn('country_id', array('header' 	=> Mage::helper('citydropdown') -> __('Country'), 
											   'align' 		=> 'left',
											   'index' 		=> 'country_id', 
											   'width' 		=> '100px', 
											   'type'       => 'string'
											   
		)); */
		
		$region_options = array();
		 $region_lists = array();
	  
		$region_collection = Mage::getResourceModel('directory/region_collection')->addCountryFilter('PH')->load(); 
		foreach($region_collection as $region){ 
			$region_lists[$region->getRegionId()] =  $region->getName();
		} 

		$this -> addColumn('region_id', array('header' => Mage::helper('citydropdown') -> __('Region'), 
											  'align'    => 'left', 
											  'index'    => 'region_id', 
											  'width'    => '500px', 
											  'type'     => 'options', 
											  'options'   => $region_lists,
											  'renderer'  => 'Unilab_CityDropdown_Block_Adminhtml_Citydropdown_Grid_Column_Renderer_Region'
		));
 
		$this -> addColumn('name', array('header' => Mage::helper('citydropdown') -> __('City Name'),
										 'align' => 'left',
										 'index' => 'name',
										 'type' => 'string', 
										 'width' => '400px', 
		)); 
		
		$this -> addExportType('*/*/exportCsv', Mage::helper('citydropdown') -> __('CSV'));
		$this -> addExportType('*/*/exportXml', Mage::helper('citydropdown') -> __('XML'));

		return parent::_prepareColumns();
	}

	protected function _prepareMassaction() {
		$this -> setMassactionIdField('city_id');
		$this -> getMassactionBlock() -> setFormFieldName('citydropdown');

		$this -> getMassactionBlock() -> addItem('delete', array('label' => Mage::helper('citydropdown') -> __('Delete'), 'url' => $this -> getUrl('*/*/massDelete'), 'confirm' => Mage::helper('citydropdown') -> __('Are you sure?')));

	 
		return $this;
	}

	public function getRowUrl($row) {
		return $this -> getUrl('*/*/edit', array('city_id' => $row -> getCityId())); 
	}

}
