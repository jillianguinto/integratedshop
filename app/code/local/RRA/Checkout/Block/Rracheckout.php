<?php
	class RRA_Checkout_Block_Rracheckout extends Mage_Core_Block_Template
	{
		public function cartitems()
		{
		
			$cart = Mage::getModel('checkout/session')->getQuote();
			$cart->getAllItems();
			
			return $cart->getAllItems();
		}
		
		function QouteSession()
		{
			return Mage::getModel('checkout/session')->getQuote();
		}
		
		public function getshipppingAddress()
		{
				
			$shipppingAddress 	= $this->QouteSession()->getShippingAddress()->getData();
			Mage::getSingleton('core/session')->setShippingadd($shipppingAddress);	
			
			return;
		}
		
		public function getsetting()
		{
			$setting = array();
			$setting ['subtotalincl'] 	= Mage::getStoreConfig('rracheckoutsection/rracheckouttaxdetails/subtotalincl');	
			$setting ['taxAmount'] 		= Mage::getStoreConfig('rracheckoutsection/rracheckouttaxdetails/taxAmount');	
			$setting ['grandtotalincl'] = Mage::getStoreConfig('rracheckoutsection/rracheckouttaxdetails/grandtotalincl');			
			Mage::getSingleton('core/session')->setSetting($setting);
			
			return ;
		}
		
		
		public function getaAmountdata()
		{
			
			$items = $this->QouteSession()->getAllItems();
			foreach ($items as $item) {
				$priceInclVat 	+= $item->getRowTotalInclTax();
				$discountAmount += $item->getDiscountAmount();
			}
	
			$dataAmount = array();
			$dataAmount['SubtotalinclTax'] 	=	$priceInclVat;
			$dataAmount['SubtotalExclTax'] 	=	Mage::getModel('checkout/cart')->getQuote()->collectTotals()->getSubtotal();
			$dataAmount['grandTotal']		= 	Mage::getSingleton('checkout/cart')->getQuote()->collectTotals()->getGrandTotal(); 
			$dataAmount['discount']			= 	$discountAmount; 
   			Mage::getSingleton('core/session')->setdataAmount($dataAmount);
			
			return;	
			
		}
		
		
		
		public function getpaymentmethod()
		{
			try{
				$methodcode = $this->QouteSession()->getPayment()->getMethodInstance()->getCode();
			}
			catch (Mage_Core_Exception $e) 			{
				$methodcode = '';			
			}

			Mage::getSingleton('core/session')->setPaymentMethod($methodcode);		
			
			return;
		}
		
		
		public function getdefualtAddress()
		{		
			$defaultbillingaddress = Mage::getSingleton('customer/session')->getCustomer()->getDefaultBilling();			
			Mage::getSingleton('core/session')->setdefaultbillingaddress($defaultbillingaddress);			
			return;
		}
		
		public function loadAddress()
		{
			$customer = Mage::getSingleton('customer/session')->getCustomer();
			$addressData = array ();
			$count = 1;
			foreach ($customer->getAddresses() as $address) {			
				$addressData['entity_id'][$count] 	= $address['entity_id'];
				$addressData['street'][$count]		= $address['street'];
				$count++;
			}
			
			Mage::getSingleton('core/session')->setLoadAddress($addressData);	
			
			return;
		}
		
	}