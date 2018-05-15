<?php
	class RRA_Checkout_Block_Unilabcheckout extends Mage_Core_Block_Template
	{
		public function cartitems()
		{
		
			$cart = Mage::getModel('checkout/cart')->getQuote();
			$cart->getAllItems();
			
			return $cart->getAllItems();
		}
		
	}