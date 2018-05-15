<?php
class Unilab_Sales_Model_Observer
{
	public function calculateInsurance(Varien_Event_Observer $observer){ 
		 $_quote_address   = $observer->getEvent()->getQuoteAddress(); 
		 $shipping_address = $_quote_address->getQuote()->getShippingAddress();
         $method 		  = $shipping_address->getShippingMethod();
		 
		 $reset_insurance = true; 
		 $insurance_fee   = 0;
		 
		 if ($method) {
            foreach ($shipping_address->getAllShippingRates() as $rate) {
				$compact_method = $rate->getCarrier()."_".$rate->getMethod();
				if ($compact_method == $method &&  $rate->getCarrier() == 'xend') { 
					$insurance_fee   = $rate->getInsuranceFee();
					$reset_insurance = false; 
					break;
                }  
            } 
			$_quote_address->getQuote()->setUnilabInsurance($insurance_fee)->save();  
        } 
	}
}