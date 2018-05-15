<?php

class Unilab_Webservice_Model_Validate_Token extends Mage_Core_Model_Abstract
{
	
	protected function _construct()
    {
		
		date_default_timezone_set('Asia/Taipei');
		
		$RequestURL = $_SERVER['HTTP_REFERER'];
		
		if(empty($RequestURL)){

			$_POST['sitehost']	= $_SERVER['HTTP_HOST'];					
		}else{
			$RequestBaseurl 	= parse_url($RequestURL);

			$_POST['sitehost']	= $RequestBaseurl['host'];					
		}

		$store	    = Mage::app()->getStore();
		$storename  = $store->getName();

		$fields 				= array();
		$fields['store'] 		= $storename;
		$fields['hostname']		= $_POST['sitehost'];
		$fields['tokenused']	= $_POST['token'];
		$fields['cmdevent']		= $_POST['cmdEvent'];
		$fields['dataposted']	= json_encode($_POST);			
		$fields['created_date']	= date("Y-m-d H:i:s");				
					
		$this->_getConnection()->insert('unilab_connectionlogs', $fields);
		$logid = $this->_getConnection()->lastinsertid();
		$this->_getConnection()->commit();	
		
		Mage::getSingleton('core/session')->setlogid($logid);

		//return $logid;
		
	}
	
	protected function _getConnection()
    {
		$this->_Connection = Mage::getSingleton('core/resource')->getConnection('core_write');	
		
        return $this->_Connection;
    }	
	
	
	public function validatetoken()
	{
		
		Mage::getSingleton('core/session')->unsCategoryId();
		Mage::getSingleton('core/session')->unsStoreid();
		
		$token = $_POST['token'];

		$gettoken = $_GET['token'];

		$hostname = $_POST['sitehost'];

		$hostname = str_replace("www.","", $hostname);
		
		$qry 	= "SELECT * FROM unilab_token WHERE (token='$token' or token='$gettoken')  and hostname='$hostname'";
		$result = $this->_getConnection()->fetchRow($qry);

		$tokenid 	= $result['id'];
		$isactive 	= $result['isactive'];

		$categoryid = $result['store']; 

		Mage::log("token" .$token, null, 'what.log');
		Mage::log("gettoken" .$gettoken, null, 'what.log');
		Mage::log("tokenid" .$tokenid, null, 'what.log');
		Mage::log("isactive" .$isactive, null, 'what.log');
		Mage::log("categoryid" .$categoryid, null, 'what.log');

		$storename 	= Mage::getModel('webservice/function_store')->getStorename($categoryid);
		$response['storeid']	= Mage::getModel('webservice/function_store')->getStoreid($storename); 

		if($tokenid == "")
		{
			$response['message'] 	= "Invalid Token!";
			$response['success'] 	= false;

			echo $response['message'];
			die();
			
		}else{
			
			if($isactive == true)
			{   	
				Mage::getSingleton('core/session')->setCategoryId($categoryid);
				Mage::getSingleton('core/session')->setStoreid($response['storeid']);
				Mage::app()->setCurrentStore($response['storeid']);
				$response['success'] 	= true;		
				return $response;
				
			}elseif($isactive == false){
				
				$response['message'] 	= "This token was inactive. Please contact support.";
				$response['success'] 	= false;	

				echo $response['message'];
				die();		
			}			
			
		}		
		
		// if($response['success'] == false){				
			
		// 	return $response;

		// }else{
		
			// return $response;
		// }	

	}
	
	
	public function saveResponse($responsedata)
	{
		$logid = Mage::getSingleton('core/session')->getlogid();
		$response = json_encode($responsedata);
		
		$query 	= "update unilab_connectionlogs set response='$response' where id='$logid'";
		$this->_getConnection()->query($query);
		
		
		$fields		=array();
        $fields['response']  = $response;

    	$where = array($this->_getConnection()->quoteInto('id=?', $logid));

		$this->_getConnection()->update('unilab_connectionlogs', $fields, $where);
        $this->_getConnection()->commit();  	


		Mage::getSingleton('core/session')->unsrefnum();
	}
	

	public function forgotpasswordToken()
	{
		$qry 	= "SELECT * FROM unilab_token WHERE store='1'";
		$result = $this->_getConnection()->fetchRow($qry);

		return $result['token'];
	}

	public function validateForgotpassToken($webtoken) 
	{

		$webtoken = explode(":", $webtoken);

		$token = $webtoken[0];
		$storeid = $webtoken[1];

		$qry 	= "SELECT * FROM unilab_token WHERE store='1' and token='$token'";
		$result = $this->_getConnection()->fetchRow($qry);

		if($result['id'] == "")
		{
			$response['message'] 	= "Invalid Token!";
			$response['success'] 	= false;
			
		}else{
			
			Mage::getSingleton('core/session')->setStoreid($storeid);
			Mage::app()->setCurrentStore($storeid);

			$response['success'] 	= true;		
				
		}	

	}

}