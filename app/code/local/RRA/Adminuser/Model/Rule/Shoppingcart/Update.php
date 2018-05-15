<?php

class RRA_Adminuser_Model_Rule_Shoppingcart_Update extends Mage_Core_Model_Abstract
{
	
	protected function _getConnection(){
		
		$this->_Connection = Mage::getSingleton('core/resource')->getConnection('core_write');	
		
        return $this->_Connection;
    }

	public function update()
	{
		
		return "No function yet!";
		
	}		

}