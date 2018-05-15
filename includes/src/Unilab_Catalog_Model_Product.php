<?php

class Unilab_Catalog_Model_Product extends Mage_Catalog_Model_Product
{
	public function getFinalPriceMovent($qty=null)
    { 
        return $this->getPriceModel()->getFinalPrice($qty, $this);
    }  	
	public function getMinimalPrice()
    {    
		if($minimal_product = Mage::helper("catalog/product")->getChildProductWithLowestPrice($this,'finalPrice')){  
			$child_product =  Mage::getModel("catalog/product")->load($minimal_product->getId()); 
			if($moq = $child_product->getUnilabMoq()){
				return max($child_product->getFinalPrice(), 0);
			}  
		} 
        return max($this->_getData('minimal_price'), 0);
    }
	

}


?>