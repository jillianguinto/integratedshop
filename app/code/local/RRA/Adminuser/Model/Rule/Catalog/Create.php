<?php

class RRA_Adminuser_Model_Rule_Catalog_Create extends Mage_Core_Model_Abstract
{
	protected function _getConnection()
    {
		$this->_Connection = Mage::getSingleton('core/resource')->getConnection('core_write');	
		
        return $this->_Connection;
    }

	
	public function create(){
		

		$fromDate 	= strtotime($_POST['rule_from_date']);
		$toDate 	= strtotime($_POST['rule_to_date']);
			
		try {
							

			$catalogPriceRule = Mage::getModel('catalogrule/rule');
					
			$catalogPriceRule->setName($_POST['rule_name'])
							 ->setDescription($_POST['rule_description'])
							 ->setIsActive($_POST['rule_is_active'])
							 ->setWebsiteIds($_POST['rule_website_ids'])
							 ->setCustomerGroupIds($_POST['rule_customer_group_ids'])
							 ->setFromDate(date('Y-m-d',$fromDate))
							 ->setToDate(date('Y-m-d',$toDate))
							 ->setSortOrder($_POST['rule_sort_order'])
							 ->setSimpleAction($_POST['rule_simple_action'])
							 ->setStopRulesProcessing($_POST['rule_stop_rules_processing'])
							 ->setDiscountAmount($_POST['rule_discount_amount']);
							 

			if($_POST['skus']){
				
				$skus = explode(",", $_POST['skus']);
				
				$skuCondition = Mage::getModel('catalogrule/rule_condition_product')
								->setType('catalogrule/rule_condition_product')
								->setAttribute('sku')
								->setOperator('()')
								->setValue($skus);		
								
				$catalogPriceRule->getConditions()->addCondition($skuCondition);							
				
			}						 

			$catalogPriceRule->save();

			$catalogPriceRule->applyAll();	

			Mage::app()->removeCache('catalog_rules_dirty');
			
			Mage::getSingleton('core/session')->setStatussave(0);	

			$response['success'] 	= true;
			$response['id'] 		= $catalogPriceRule->getId();				
			$response['MsgHndler'] 	= "Catalog Price Rule was successfully added.";						

			
		}catch(Exception $e){
			
			$response['success'] 	= false;
			$response['Errhandler'] = $e->getMessage();		
		}

		return $response;
	
	}	
	

}