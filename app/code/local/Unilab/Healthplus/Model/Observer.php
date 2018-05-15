<?php
class Unilab_Healthplus_Model_Observer extends Varien_Object
{
	public function setDiscount($observer)
    {
		$storeid 		= 	Mage::app()->getStore()->getStoreId();
	
		$healthplusEnabled = Mage::getStoreConfig('healthplus/healthplussettings/enabled', $storeid);
		
		if($healthplusEnabled == 1)
		{
			$quote			= $observer->getEvent()->getQuote();
			$quoteid		= $quote->getId();
			
			$healthcredit 	= Mage::getModel('healthplus/healthplus')->checkcartwithHC();
			
			$response 		= $this->carthealthcredits();
			$discountAmount	= $response['discountAmt'];
							   
			if($quoteid)
			{
				if($healthcredit == 1) 
				{
					$grandtotal = $quote->getGrandTotal();
					
					$total		= $quote->getBaseSubtotal();
					$quote->setSubtotal(0);
					$quote->setBaseSubtotal(0);
					$quote->setSubtotalWithDiscount(0);
					$quote->setBaseSubtotalWithDiscount(0);
					$quote->setGrandTotal(0);
					$quote->setBaseGrandTotal(0);
				
				
					$canAddItems = $quote->isVirtual()? ('billing') : ('shipping');    
					foreach ($quote->getAllAddresses() as $address) 
					{
						
						$address->setSubtotal(0);
						$address->setBaseSubtotal(0);
						$address->setGrandTotal(0);
						$address->setBaseGrandTotal(0);
						$address->collectTotals();
						$quote->setSubtotal((float) $quote->getSubtotal() + $address->getSubtotal());
						$quote->setBaseSubtotal((float) $quote->getBaseSubtotal() + $address->getBaseSubtotal());
						$quote->setSubtotalWithDiscount(
							(float) $quote->getSubtotalWithDiscount() + $address->getSubtotalWithDiscount()
						);
						$quote->setBaseSubtotalWithDiscount(
							(float) $quote->getBaseSubtotalWithDiscount() + $address->getBaseSubtotalWithDiscount()
						);
						$quote->setGrandTotal((float) $quote->getGrandTotal() + $address->getGrandTotal());
						$quote->setBaseGrandTotal((float) $quote->getBaseGrandTotal() + $address->getBaseGrandTotal());
				
						$quote ->save(); 
				
						$quote->setGrandTotal($quote->getBaseSubtotal()-$discountAmount)
							->setBaseGrandTotal($quote->getBaseSubtotal()-$discountAmount)
							->setSubtotalWithDiscount($quote->getBaseSubtotal()-$discountAmount)
							->setBaseSubtotalWithDiscount($quote->getBaseSubtotal()-$discountAmount)
							->save(); 
						   
							
						if($address->getAddressType()==$canAddItems) 
						{
							$address->setSubtotalWithDiscount((float) $address->getSubtotalWithDiscount()-$discountAmount);
							$address->setGrandTotal((float) $address->getGrandTotal()-$discountAmount);
							$address->setBaseSubtotalWithDiscount((float) $address->getBaseSubtotalWithDiscount()-$discountAmount);
							$address->setBaseGrandTotal((float) $address->getBaseGrandTotal()-$discountAmount);
							if($address->getDiscountDescription()){
							$address->setDiscountAmount(-($address->getDiscountAmount()-$discountAmount));
							$address->setDiscountDescription($address->getDiscountDescription().', Health Plus Discount');
							$address->setBaseDiscountAmount(-($address->getBaseDiscountAmount()-$discountAmount));
							}else {
							$address->setDiscountAmount(-($discountAmount));
							$address->setDiscountDescription('Health Plus Discount');
							$address->setBaseDiscountAmount(-($discountAmount));
							}
							$address->save();
						}//end: if
					} //end: foreach
					//echo $quote->getGrandTotal();
					
					foreach($quote->getAllItems() as $item)
					{
						
						$prodid = $item->getProduct()->getId();
						$_resource = Mage::getSingleton('catalog/product')->getResource();
						$healthplus = $_resource->getAttributeRawValue($prodid,  ['unilab_healthplus'], Mage::app()->getStore());
						
						if ($healthplus == '1') {
							
							$item->setDiscountAmount($discountAmount);
							$item->setBaseDiscountAmount($discountAmount)->save(); 
						}
						
					}
					
				} 
			} 

		}
	}
	
	public function carthealthcredits()
	{

		$grandTotal = Mage::getModel('checkout/session')->getQuote()->getGrandTotal();

		$cart = Mage::getModel('checkout/cart')->getQuote();
		$total = 0;
		foreach ($cart->getAllVisibleItems() as $item) 
		{ 		  
			$prodid = $item->getProduct()->getId();
			$_resource = Mage::getSingleton('catalog/product')->getResource();
			$healthplus = $_resource->getAttributeRawValue($prodid,  ['unilab_healthplus'], Mage::app()->getStore());
		  
		  if ($healthplus == '1') {
			  	$qty = $item->getQty();
				$price = $item->getProduct()->getPrice();
				$totalprice = $price * $qty;
				$total = $total + $totalprice;
		  }

		}
		
		$healthplusData = Mage::getModel('healthplus/healthplus')->healthplusData();
		
		if($healthplusData['available_balance'] == $total)
		{
			$discountAmt = $healthplusData['available_balance'];
			$response['success'] = true;

		}elseif($healthplusData['available_balance'] > $total){
			
			$discountAmt = $total;
			$response['success'] = true;
			
		}
		
		$gtotal = $grandTotal - $discountAmt;
		
		$response['discountAmt'] = $discountAmt;
		$response['grandTotal'] = $gtotal;
		

		return $response;
	}


	
}