<?php 

// This page is create by Richel R. Amante for UNILAB use only.
// Please do not used this software and take with your own risk.

// Thank you!!!


class RRA_Checkout_ShippingrateController extends Mage_Core_Controller_Front_Action
{
    public function shippingcostAction()
    {  
		try{		
			
			parse_str($_POST['paymentdata'], $paymentdata);
			
			$shippingCode 		= $paymentdata['shippingmethod'];
			
			parse_str($_POST['shippingAddData'], $shippingdata);
			
			$city_id 	= $shippingdata['shipping']['city'];

			if($city_id != null){
				
				$read       = Mage::getSingleton('core/resource')->getConnection('core_read'); 
            	$result    = $read->fetchRow("select * from unilab_cities where city_id = $city_id");

            	$city 		= $result['name'];
			}
			
			 //$read       = Mage::getSingleton('core/resource')->getConnection('core_read'); 
             //$result    = $read->fetchRow("select * from unilab_cities where city_id = $city_id");
		
			
			$country_id = $shippingdata['shipping']['country_id'];
			
			$postcode   = $shippingdata['shipping']['postcode']; 
			$street   	= $shippingdata['shipping']['street'][0]; 
			$region   	= $shippingdata['shipping']['region']; 
			$region_id  = $shippingdata['shipping']['region_id'];  

			
			$carriername =  explode("_", $shippingCode);

			$carriername = $carriername[0];

			// $customerAddressId 	= Mage::getSingleton('customer/session')->getCustomer()->getDefaultBilling();
			
			// if ($customerAddressId){
				   // $addressdefautl = Mage::getModel('customer/address')->load($customerAddressId);
			// }	

			// $address 	= Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress();
			
			
			// if ($address->getCountryId() == '') $address->setCountryId($addressdefautl->getcountry_id());
			// if ($address->getCity() == '') $address->setCity($addressdefautl->getcity());
			// if ($address->getPostcode() == '') $address->setPostcode($addressdefautl->getpostcode());
			// $address->setStreet($addressdefautl->getstreet());			
			// $address->setRegionId($addressdefautl->getregion_id());
			// $address->setRegion($addressdefautl->getregion());	
			
			$address 	= Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress();
			
			$address->setCountryId($country_id);
			$address->setCity($city);
			$address->setPostcode($postcode);
			$address->setStreet($street);			
			$address->setRegionId($region_id);
			$address->setRegion($region);	

			$address->setShippingMethod($shippingCode)->setCollectShippingRates(true);
			
			Mage::getSingleton('checkout/session')->getQuote()->save();
			
			$rates 			= $address->collectShippingRates()->getGroupedAllShippingRates();
			$shippingrate 	=	0;

			foreach ($rates as $carrier):
				foreach ($carrier as $rate):
				
					 if($carriername == $rate->getData('carrier')){

						$carrier_name	= $rate->getData('carrier');

						$carrier_title 	= $rate->getData('carrier_title'); 

						$carrier_method = $rate->getData('method'); 

						$carrier_method_title = $rate->getData('method_title'); 

						$carrier_code 	= $rate->getData('code');

						$shippingrate 	= $rate->getData('price');	


					}
				endforeach;
			endforeach;

			//Save new rate to quote
			
			$grandT 		= Mage::getSingleton('checkout/cart')->getQuote()->collectTotals()->getGrandTotal(); 
			$grandTotal 	= Mage::helper('core')->currency($grandT, true, false);				
			$TaxAmount 		= Mage::helper('checkout')->getQuote()->getShippingAddress()->getData('tax_amount');
			
			$quote 		= Mage::getModel('checkout/session')->getQuote();
			$quote->getShippingAddress()->setShippingMethod($carrier_code);
			$quote->getShippingAddress()->setTaxAmount($TaxAmount);
			$quote->getShippingAddress()->setShippingMethod($carrier_code);
			$quote->getShippingAddress()->setShippingDescription($carrier_title. ' - ' .$carrier_method_title);
			$quote->getShippingAddress()->setCollectShippingRates(true);
			$quote->getShippingAddress()->collectShippingRates(); 



			$address = $quote->getShippingAddress();
			$address->setShippingAmount($shippingrate);
			$address->setBaseShippingAmount($shippingrate);
			$rates = $address->collectShippingRates()->getGroupedAllShippingRates();

			foreach ($rates as $carrier) {
				foreach ($carrier as $rate) {
					$rate->setPrice($shippingrate);
					$rate->save();

				}
			}
			
			$address->setCollectShippingRates(false);
			$address->save();			
			

			$response['taxAmount'] 				= Mage::helper('core')->currency($TaxAmount,true,false);		
			$response['grandTotal'] 			= $grandTotal;	
			$response['carrier_name'] 			= $carrier_name;	
			$response['carrier_title'] 			= $carrier_title;	
			$response['carrier_method'] 		= $carrier_method;	
			$response['carrier_method_title'] 	= $carrier_method_title;	
			$response['carrier_code'] 			= $carrier_code;	
			$response['shippingrate'] 			= Mage::helper('core')->currency($shippingrate,true,false);	
			$response['success'] 				= true;	
		}

		catch (Mage_Core_Exception $e) 
		{
			$response['error'] = $e->getMessage();
			$response['success'] = false;				
		}
		

		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response)); 			
		
    }	

}
