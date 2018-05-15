<?php

class Unilab_Fee_Model_Fee extends Varien_Object{
		
	public static function getFee(){		
		$quote = Mage::getSingleton('checkout/cart')->getQuote();		
		return $quote->getUnilabInsurance() > 0 ? $quote->getUnilabInsurance() : 0;
	}
	
	public static function canApply($address){
		
		if($address->getAddressType() == 'billing'){
			return false;
		}	
		
		if(strpos( $address->getShippingMethod() , 'xend_') !== false ){
			return true;	
		}
		
		
		/*	
		$quote = Mage::getSingleton('checkout/cart')->getQuote();
		if ($quote->getItemsCount() && $quote->getPayment()->getMethod() == "dragonpay") {
			return true;
		}
		*/
		
		return false;
	}
}