<?php

class Unilab_Promovouchers_Model_Values_Customers extends Mage_Core_Model_Abstract
{
	protected function _getConnection()
    {
		$this->_Connection = Mage::getSingleton('core/resource')->getConnection('core_write');	
		
        return $this->_Connection;
    }
	
	public function toOptionArray()
	{		
		$Sqlselect = "select distinct email from customer_entity where is_active=1 order by email asc";
		
		$collection = $this->_getConnection()->fetchAll($Sqlselect);
		
		$customerArray = array();
		$customerArray[] = array(
				'value'		=> '',
				'label'		=> 'Please Select'
			);
	  
		
		
		foreach ($collection as $_collection){
		$customerArray[] = array(
				'value'		=>	$_collection['email'],
				'label'		=>	$_collection['email']
			);
		}
		
		
		return $customerArray;

	}
}