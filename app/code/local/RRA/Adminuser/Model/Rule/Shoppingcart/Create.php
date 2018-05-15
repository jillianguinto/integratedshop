<?php

class RRA_Adminuser_Model_Rule_Shoppingcart_Create extends Mage_Core_Model_Abstract
{
	protected function _getConnection(){
		
		$this->_Connection = Mage::getSingleton('core/resource')->getConnection('core_write');	
		
        return $this->_Connection;
    }

	public function create(){
		

		return "No function yet!";
	
	}	
	

}