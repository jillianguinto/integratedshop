<?php

class Unilab_Catalog_Model_Product_Type_Price extends Mage_Catalog_Model_Product_Type_Price{

	public function getFinalPrice($qty = null, $product)
    {   
		/** MOVENT, INC.
		 *	APPLY MOQ  
		 */
			/* $_product = Mage::getModel("catalog/product")->load($product->getId());  	
			if(!$product->hasAddedMoq() &&  $_product->getUnilabMoq()){
				$_new_price = $product->getPrice() * $_product->getUnilabMoq();  
				$product->setPrice($_new_price);  
				$product->setAddedMoq(true);
			} 	 */		
			
			//end Movent codes 
		
        if (is_null($qty) && !is_null($product->getCalculatedFinalPrice())){
            return $product->getCalculatedFinalPrice();
        }

        $finalPrice = $this->getBasePrice($product, $qty);
        $product->setFinalPrice($finalPrice);

        Mage::dispatchEvent('catalog_product_get_final_price', array('product' => $product, 'qty' => $qty));

        $finalPrice = $product->getData('final_price');
        $finalPrice = $this->_applyOptionsPrice($product, $qty, $finalPrice);
        $finalPrice = max(0, $finalPrice);
        $product->setFinalPrice($finalPrice);

        return $finalPrice;
    }
}