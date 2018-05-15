<?php
 
class RRA_Admincontroller_Model_importcouponcode extends Varien_Object {

	protected $coupon_code 		= null;


	protected function _getConnection()
    {
		$this->_Connection = Mage::getSingleton('core/resource')->getConnection('core_write');	
		
        return $this->_Connection;
    }	
	
	
    public function processData()
    {	
	
	
		$csv  		= $this->getData('csv');
		$head 		= $this->getData('head');
		$couponId = $this->getData('id');

		//*** Convert header from space to _ ***///
		
		foreach ($head as $key => $value):

			  $key 			= strtolower(str_replace(' ', '_', $key));		  
			  $head[$key] 	= strtolower(str_replace(' ', '_', $value));		  
			  
		endforeach;			
			
		$csvResult 		 = array_map("array_combine", array_fill(0, count($csv), $head), $csv);
		

		//**** Save file in Temp. Table ***///		
			
		$saveTempProduct = $this->_saveTemp($csvResult, $couponId);		
		

		
		//**** Save file in Temp. Table - End ***///	

		return $saveTempProduct;
	
	}
	
	
	protected function _saveTemp($csvResult, $couponId)
	{
	
	

		$userUsername 	= 'admin';	

		$count 			= 0;
		$countSave		= 0;
		$countBreak 	= 15;
		$alreadysave	= 0;
		$getData 		= array();
		$resData		= array();
		
		Mage::getSingleton('core/session')->unsRecords();
		$records		= count($csvResult);
	
		$filecount = Mage::getBaseDir('var'). DS. 'cache'. DS .'mage--csv' . DS. 'importcouponcodecount';
		$SaveCount = file_get_contents($filecount);	
		
		if(empty($SaveCount)):
			$SaveCount = 0;
		endif;
		

		
		foreach($csvResult as $_key=>$_value):
		
			$fieldValue 					= null;
			$getData  						= null;
			$getData['coupon_code']			= null;
				

			if($count >= $SaveCount):
			
				$getData['id'] = $couponId;
				
				foreach($_value as $key=>$value):
				
					if ($key == 'coupon_code'):
					
						$getData['coupon_code'] = $value;
						
					endif;

				endforeach;
				
				
				if(!empty($couponId)):
				

					$coupon_rule 					= $this->_isChecker($couponId, $getData['coupon_code']);
					$getData['rule_id']				= $coupon_rule['rule_id'];
					$getData['usage_per_customer']	= $coupon_rule['uses_per_customer'];
					$getData['usage_limit'] 		= $coupon_rule['uses_per_coupon'];
					$getData['name']				= $coupon_rule['name'];
					$getData['from_date']			= $coupon_rule['from_date'];
					$getData['expiration_date']		= $coupon_rule['to_date'];
					$getData['type']				= 1;
						
					
					if(isset($getData['rule_id'])):
						
						$couponcount = $count + 1;
						$this->_saveData($getData);
						$resData[] = $couponcount .' : Name: '. $getData['name'].' / Coupon Code: '.$getData['coupon_code'].' - <span style="color:green;">Uploaded!</span>';
						Mage::getSingleton('core/session')->setStatussave(0);
						Mage::log($couponCode.' - '. $coupon_rule['rule_id'], null, date("Y-m-d").'_couponcodesaved.log');
						
					else:
						
						$couponcount = $count + 1;
						$resData[] = $couponcount .' : Name: '. $getData['name'].' / Coupon Code: '.$getData['coupon_code'].' - <span style="color:red;">Exist!</span>';
						Mage::getSingleton('core/session')->setStatussave(0);
						Mage::log($keyCode.' - '. $coupon_rule['rule_id'], null, date("Y-m-d").'_couponcodefailed.log');
																	
					endif;
					
					$dataSave = true;
					$countSave++;	
					Mage::getSingleton('core/session')->setRecordsave($resData);
					
			
				endif;
				
			endif;
			
			$count++;					
			$remainingRec  				= array();
			$remainingRec['Allrecords']	= $records;
			$remainingRec['Savecount']	= $count;		
			
			Mage::getSingleton('core/session')->setRecords($remainingRec);							

			if($dataSave == true && $countSave == $countBreak):
				$countSave = 0;
				break;
			endif;					
				
		endforeach;	
		
		
		return $this;

	}
	

	protected function _isChecker($couponId, $couponCode)
	{
		

	
		$sql 				= "SELECT * FROM salesrule WHERE rule_id='$couponId'";						
		$coupon_rule 		= $this->_getConnection()->fetchRow($sql);	
		
		$ruleSql 			= "SELECT code FROM salesrule_coupon WHERE rule_id='$couponId' AND code='$couponCode'";						
		$coupon_code 		= $this->_getConnection()->fetchRow($ruleSql);	
		
		
		if(!empty($coupon_code['code'])):
		
			$coupon_rule = null;
			
		endif;
		
		return $coupon_rule;
		
	}
	
	
	
	
	protected function _saveData($getData)
	{
	
		try {
		
			$this->_getConnection()->beginTransaction();		
			
			$expiration_date 	= strtotime($getData['expiration_date']);
			
			//*** Insert data to salesrule_coupon Coupon Code
			$fields 							= array();
			$fields['rule_id']					= $getData['rule_id'];
			$fields['usage_per_customer']		= $getData['usage_per_customer'];
			$fields['usage_limit']				= $getData['usage_limit'];
			$fields['code']						= $getData['coupon_code'];
			$fields['type']						= $getData['type'];
			$fields['expiration_date']			= $expiration_date;
			$fields['created_at']				= date("Y-m-d H:i:s");
			$this->_getConnection()->insert('salesrule_coupon', $fields);
			$this->_getConnection()->commit();				
		
			
			}catch(Exception $e){
			
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('admincontroller')->__($e->getMessage()));
				Mage::log($e->getMessage() ,null, 'importcouponcode.log');	
			}	
				
					
	}
			

	
}