<?php

class Unilab_Promovouchers_Model_Values_Salesrule extends Mage_Core_Model_Abstract
{
	protected function _getConnection()
    {
		$this->_Connection = Mage::getSingleton('core/resource')->getConnection('core_write');	
		
        return $this->_Connection;
    }
	
	public function toOptionArray()
	{
		
		$Sqlselect = $this->_getConnection()->select()
				->from('salesrule', array('*')) 
				->where('is_active=?', '1')
				->order('rule_id', 'DESC');  
				
		$salesrulecollection = $this->_getConnection()->fetchAll($Sqlselect);
		
		$salesrulearray = array();
		$salesrulearray[] = array(
				'value'		=> '',
				'label'		=> 'Please Select'
			);
	  
		
		
		foreach ($salesrulecollection as $salesrule){
		$salesrulearray[] = array(
				'value'		=>	$salesrule['rule_id'],
				'label'		=>	$salesrule['name']
			);
		}
		
		
		return $salesrulearray;

	}
}