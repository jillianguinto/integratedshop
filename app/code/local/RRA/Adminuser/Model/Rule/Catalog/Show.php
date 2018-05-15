<?php

class RRA_Aonewebservice_Model_Price_List_Show extends Mage_Core_Model_Abstract
{
	
	protected function _getConnection()
    {
		$this->_Connection = Mage::getSingleton('core/resource')->getConnection('core_write');	
		
        return $this->_Connection;
    }

	public function showdata()
	{	
		$response	= Mage::getModel('catalogrule/rule')->load($_POST['id']);
		return $response;
		
	}		
			

}