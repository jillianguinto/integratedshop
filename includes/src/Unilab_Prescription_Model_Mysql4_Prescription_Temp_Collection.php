<?php

class Unilab_Prescription_Model_Mysql4_Prescription_Temp_Rx_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('prescription/prescription_temp_rx');
    }
	
	protected function _getCart()
	{
		return Mage::getSingleton("checkout/cart");
	}
	
	protected function _getUser()
	{
		return Mage::getSingleton("customer/session");
	}
	
	protected function _getQuote()
    {
        return $this->_getCart()->getQuote();
    } 
	 	
}