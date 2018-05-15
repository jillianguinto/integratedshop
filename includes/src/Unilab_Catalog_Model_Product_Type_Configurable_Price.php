<?php 
class Unilab_Catalog_Model_Product_Type_Configurable_Price extends Mage_Catalog_Model_Product_Type_Configurable_Price
{ 
	public function getFinalPrice($qty=null, $product)
    {
		
		/** MOVENT **/
			$_product = Mage::getModel("catalog/product")->load($product->getId());  	
			$_new_price = $product->getPrice() * $_product->getUnilabMoq();  
			$product->setPrice($_new_price);  
		/** end Movent codes **/
		
		
        if (is_null($qty) && !is_null($product->getCalculatedFinalPrice())) {
            return $product->getCalculatedFinalPrice();
        }

        $basePrice = $this->getBasePrice($product, $qty);
        $finalPrice = $basePrice;
        $product->setFinalPrice($finalPrice);
        Mage::dispatchEvent('catalog_product_get_final_price', array('product' => $product, 'qty' => $qty));
        $finalPrice = $product->getData('final_price');

        $finalPrice += $this->getTotalConfigurableItemsPrice($product, $finalPrice);
        $finalPrice += $this->_applyOptionsPrice($product, $qty, $basePrice) - $basePrice;
        $finalPrice = max(0, $finalPrice);

        $product->setFinalPrice($finalPrice);
        return $finalPrice;
    }
	
}
