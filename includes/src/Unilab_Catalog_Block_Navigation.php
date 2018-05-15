<?php


class Unilab_Catalog_Block_Navigation extends Mage_Catalog_Block_Navigation
{
   /**
     * Retrieve child categories of current category
     *
     * @return Mage_Catalog_Model_Resource_Category_Collection
     */
    public function getRootCategories()
    {
        $root_id = Mage::app()->getStore()->getRootCategoryId(); //get selected stores root catalog
    	$category_model = Mage::getModel('catalog/category'); //get category model       
    	$root_category = $category_model->load($root_id);	
		
		return $root_category->getChildrenCategories();
    }
}
