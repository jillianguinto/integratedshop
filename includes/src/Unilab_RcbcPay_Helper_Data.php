<?php
/*
 * @author: diszo.sasil
 * @email: diszo.sasil@movent.com
 * @date: 2013-11-21
 */
class Unilab_RcbcPay_Helper_Data extends Mage_Core_Helper_Abstract {		
	public function getCheckout() {
		return Mage::getSingleton('checkout/session');
	}
}
