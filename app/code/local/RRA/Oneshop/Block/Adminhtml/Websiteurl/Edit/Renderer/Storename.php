<?php

class RRA_Oneshop_Block_Adminhtml_Websiteurl_Edit_Renderer_Storename extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 
	public function render(Varien_Object $row)
	{
		$categoryid =  $row->getData($this->getColumn()->getIndex());
		
		
		$connection 	= Mage::getSingleton('core/resource')->getConnection('core_read');
		$connection->beginTransaction();
		
		$selectcategory		=	$connection->select()->from('catalog_category_entity_varchar', array('entity_id','value'))
						->where('attribute_id=?',41)
						->where('entity_id=?',$categoryid); 
						
		$category 		= $connection->fetchRow($selectcategory);
		$categoryname	= $category['value'];

		
		return $categoryname;  

	}
 
}
