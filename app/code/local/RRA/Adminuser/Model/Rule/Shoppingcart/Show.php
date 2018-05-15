<?php

class RRA_Adminuser_Model_Rule_Shoppingcart_Show extends Mage_Core_Model_Abstract
{
	
	protected function _getConnection()
    {
		$this->_Connection = Mage::getSingleton('core/resource')->getConnection('core_write');	
		
        return $this->_Connection;
    }

	public function show()
	{	
		$response	= Mage::getModel('catalogrule/rule')->load($_POST['id']);
		return $response;
		
	}		
			

}