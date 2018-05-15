<?php

	class RRA_Checkout_Model_Observer{
	
			public function checkoutpage($observer){
			
				$checkoutActive = Mage::getStoreConfig('rracheckoutsection/rracheckoutgrounp/active');	
				
				if($checkoutActive == 1):
				
					$url = Mage::getBaseurl()."rracheckout/checkout/onepage";				
					header("Location: ".$url."");
					//Mage::app()->getResponse()->setRedirect(Mage::getUrl($url));
					
				endif;
	 
			}
	} 
?>