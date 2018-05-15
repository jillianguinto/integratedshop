<?php

class Unilab_Minimumordervalue_Model_Resource_Cities extends Mage_Core_Model_Abstract
{
	public function toOptionArray()
	{
		
		$connection 	= Mage::getSingleton('core/resource')->getConnection('core_read');
		$connection->beginTransaction();
		
		$qryCity 		=	$connection->select()->from('unilab_cities', array('*'));
		$rsCity 		= 	$connection->fetchAll($qryCity);

		$cityArray = array();
		$cityArray[] = array(
				'value'		=> '',
				'label'		=> 'Please Select'
			);
	  
		$i = 0;
		foreach ($rsCity as $_rsCity){
		$cityArray[] = array(
				'value'		=>	$_rsCity['city_id'],
				'label'		=>	$_rsCity['name']
			);
		}

		return $cityArray;

	}
}