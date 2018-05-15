<?php

class RRA_Adminuser_Model_Rule_Shoppingcart_Delete extends Mage_Core_Model_Abstract
{
	

	protected function _getConnection()  {
		
		$this->_Connection = Mage::getSingleton('core/resource')->getConnection('core_write');	
		
        return $this->_Connection;
    }

	public function delete(){
		
		
		$this->_getConnection()->beginTransaction();	
		
		$catalogruleID		= Mage::getModel('catalogrule/rule')->load($_POST['id']);
		$response['Name'] 	= $catalogruleID->getName();

		try{
			
			if($catalogruleID->getId()){
				
				$where 					= array($this->_getConnection()->quoteInto('rule_id=?',$_POST['id']));
				$this->_getConnection()->delete('catalogrule', $where);
				$this->_getConnection()->commit();	
				$response['success'] 	= true;
				$response['MsgHndler'] 	= "Price List was successfully deleted!";
				
			}else{
				
				$response['success'] 	= false;
				$response['Errhandler'] = "Price List Not Exists!";
			
			}
			
		}catch(Exception $e){
			
			$response['success'] 	= false;
			$response['Errhandler'] = $e->getMessage();
		}	

		
		return $response;

	}
}