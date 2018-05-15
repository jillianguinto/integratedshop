<?php
/*
Bankdeposit - Paul Afan
 */
class Unilab_Bankdeposit_Helper_Data extends Mage_Core_Helper_Abstract {		
	public function getCheckout() {
		return Mage::getSingleton('checkout/session');
	}
}