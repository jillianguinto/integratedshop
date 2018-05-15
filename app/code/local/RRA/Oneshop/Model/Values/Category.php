<?php

class RRA_Oneshop_Model_Values_Category extends Mage_Core_Model_Abstract
{
	public function toOptionArray()
	{
		$storeId 		= Mage::app()->getStore()->getId();
		$qry 	= "SELECT cv.value AS  'Text', c2.value AS  'Link', ce.parent_id, cv.entity_id
			FROM catalog_category_entity_varchar cv
			INNER JOIN catalog_category_entity ce ON cv.entity_id = ce.entity_id
			INNER JOIN catalog_category_entity_varchar c2 ON cv.entity_id = c2.entity_id and c2.attribute_id = 57 
			WHERE ce.level = 2
			and ce.parent_id = 207
			AND cv.entity_id
			AND cv.attribute_id =41
			and cv.value not like '%Default%' 
			group by cv.value,c2.value";
		$result = $this->_getConnection()->fetchAll($qry);
	
	$eventArray[] = array(
				'value'		=>	'',
				'label'		=>	'Please select store'
			);
		foreach ($result as $category){
		$eventArray[] = array(
				'value'		=>	$category['entity_id'],
				'label'		=>	$category['Text']
			);
		}

		return $eventArray;

	}
	
	public function _getConnection()
	{
		$this->_Connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		return $this->_Connection;
	}
}