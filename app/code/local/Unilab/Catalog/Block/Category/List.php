<?php 

class Unilab_Catalog_Block_Category_List extends Mage_Core_Block_Template
{
	const CATEGORY_LEVEL = 2;
	
    protected function _prepareLayout()
    {
        parent::_prepareLayout(); 
		$this->setTemplate('catalog/category/list.phtml');
    } 
	
	public function getCategoryLists()
	{      $storeId = Mage::app()->getStore()->getId();
               $rootCategoryId = Mage::app()->getStore($storeId)->getRootCategoryId();
		if(!$this->getData('category.list')){
			$categories = Mage::getModel('catalog/category')
								->getCollection()
								->addAttributeToSelect('*')
								->addIsActiveFilter()
								->addAttributeToFilter('level',self::CATEGORY_LEVEL)
								->addAttributeToFilter('path', array('like' => "1/{$rootCategoryId}/%"));
								
			$this->setData('category.list',$categories);
		}		
		return $this->getData('category.list');
	}
}
