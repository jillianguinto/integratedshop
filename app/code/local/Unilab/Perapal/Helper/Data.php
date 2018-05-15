<?php

class Unilab_Perapal_Helper_Data extends Mage_Core_Helper_Abstract {		
	public function getCheckout() {
		return Mage::getSingleton('checkout/session');
	}
}