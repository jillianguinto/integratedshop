<?php 
class Unilab_Catalog_Block_Navigation_Left extends Mage_Catalog_Block_Navigation
{
	const CATEGORY_ROOT_LEVEL = 2;
	
    public function getCategoryNavigationMenu()
	{	 $storeId = Mage::app()->getStore()->getId();
               $rootCategoryId = Mage::app()->getStore($storeId)->getRootCategoryId();

		$categories = Mage::getModel('catalog/category')
							->getCollection()
							->addAttributeToSelect('*')
							->addIsActiveFilter()
							->addAttributeToFilter('level',self::CATEGORY_ROOT_LEVEL)
							->addAttributeToFilter('path', array('like' => "1/{$rootCategoryId}/%"));
		return $categories;
	}
	
	public function getCurrentCategory()
	{
		if($category = Mage::registry('current_category')){
			return $category;
		} 
		return false;
	}
	
	public function canExpandMenu($_category)
	{  
		if($current_category = $this->getCurrentCategory()){ 
			if($current_category->getId() == $_category->getId() ||
			   in_array($current_category->getId(),$_category->getAllChildren(true))){
				return true; 
			}
		}
		return false;
	}
	
	
}
