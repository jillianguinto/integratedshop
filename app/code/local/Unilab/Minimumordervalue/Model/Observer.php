<?php
class Unilab_Minimumordervalue_Model_Observer extends Varien_Object
{
	public function setFreeshipping($observer)
    {
    	$quote = $observer->getQuote();
       	$couponcode = $quote->getData('coupon_code');


   		// Powerman Promo Code
		$discountcode = Mage::getStoreConfig('carriers/freeshipping/discountcoupon');
		$discountcode = explode(",", $discountcode);
		Mage::log($discountcode, null, "ccode.log");
		// if($couponcode == $discountcode){
		// 	$address = $quote->getShippingAddress();
		// 	$address->setShippingMethod('freeshipping_freeshipping');
		// }

		if (in_array($couponcode, $discountcode))
		{
			$address = $quote->getShippingAddress();
			$address->setShippingMethod('freeshipping_freeshipping');
		}

	}
	
	
}