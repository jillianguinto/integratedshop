<?php

class Unilab_Ipay88_Helper_Data extends Mage_Core_Helper_Abstract {
	public function getCheckout() {
		return Mage::getSingleton('checkout/session');
	}
}
