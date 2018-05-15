<?php

class Unilab_Prescription_Helper_Data extends Mage_Core_Helper_Abstract
{ 	 
    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    } 
	
    protected function _getQuote()
    {
        return $this->_getCart()->getQuote();
    }
	
	public function isProductInCart(Mage_Catalog_Model_Product $_product)
	{  
		$isProductInCart = false; 
		$quote		     = $this->_getQuote();
		
		if($quote->hasItems()){ 
			foreach($quote->getAllItems() as $_item) {
				if($_item->getProductId() == $_product->getId()){
					$isProductInCart = $_item;
					break;
				}
			}
		}
		
		return $isProductInCart;
	}
	
	public function isValidPrescription($prescription_id)
	{  
	    $prescription = Mage::getModel("prescription/prescription")->load($prescription_id);
		
		if(!$prescription->getId() || 
		   ($prescription->getStatus() == Unilab_Prescription_Model_Prescription::STATUS_INVALID) || 
		   ($prescription->getConsumed()))
		{
			return false;
		}
		
		return true;
	}
	 
}