<?php

class RRA_Oneshop_Model_Orderincrement_Check extends Mage_Core_Model_Abstract
{
	
	public function getlastincrementorder(){
		
		
		$userID = Mage::getSingleton('customer/session')->getId();								
		$Sqlselect = $this->_getConnection()->select()
				->from('sales_flat_quote', array('entity_id')) 
				->order(array('entity_id DESC'))
				->where('customer_id=?',$userID);   
				
		$getEntityIDs 	= $this->_getConnection()->fetchRow($Sqlselect);	
		$this->_getConnection()->commit();			
		
		foreach($getEntityIDs as $_value){
			$getEntityID =  $_value;
		}
		
		
		if($getEntityID){
			// $this->_getConnection()->beginTransaction();				
			// $fields 						= array();
			// $fields['reserved_order_id'] 	= null;
			// $where = array($this->_getConnection()->quoteInto('entity_id=?',$getEntityID));
			// $this->_getConnection()->update('sales_flat_quote', $fields, $where);		
			// $this->_getConnection()->commit();		
			
			$_condition = array($this->_getConnection()->quoteInto('entity_id=?',$getEntityID));
			$this->_getConnection()->delete('sales_flat_quote', $_condition);
			$this->_getConnection()->commit();	
			
		}
			
		return "Something error while saving your order. Sorry but we need to clear your transaction. Please order again. Goto home page";
		//return "select * from sales_flat_quote where entity_id=$getEntityID";
		
	}
	
	protected function _getConnection()
    {
		$this->_Connection = Mage::getSingleton('core/resource')->getConnection('core_write');	
		
        return $this->_Connection;
    }		
	


}