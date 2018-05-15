<?php
/*
 * @author: Richel.R.Amante
 * @email: richelramante@gmail.com
 * @date: 2015-04-05
 */
class RRA_Admincontroller_Helper_Data extends Mage_Core_Helper_Abstract {		

	public function getCheckout() {

		return Mage::getSingleton('checkout/session');

	}


}
