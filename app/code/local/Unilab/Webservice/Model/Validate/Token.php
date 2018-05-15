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
		
		if (strpos($hostname, 'facebook') !== false) {
			
			$referer = $_SERVER['HTTP_REFERER']; 
			$parseUrl = parse_url($referer);
			
			$parsehost = $parseUrl['host'];
			
			$hostexplode = explode('.', $parsehost);
			
			$host = $hostexplode[0];

			$paramArr = array('www', 'lm', 'm', 'l', 'free', 'web');
			
			if (in_array($host, $paramArr)) {
				$hostname = "www." . $hostexplode[1] . "." . $hostexplode[2] ;
			}
			
		}else{
			$hostname = str_replace("www.","", $hostname);
		}
		

		$qry 	= "SELECT * FROM unilab_token WHERE (token='$token' or token='$gettoken') and hostname='$hostname'";
		$result = $this->_getConnection()->fetchRow($qry);

		$tokenid 	= $result['id'];
		$isactive 	= $result['isactive'];
		$categoryid = $result['store']; 
		$storename 	= Mage::getModel('webservice/function_store')->getStorename($categoryid);
		$storeid 	= Mage::getModel('webservice/function_store')->getStoreid($storename);
		
		Mage::log($qry, null, 'jillian.log');


		if($tokenid == "")
		{
			$response['Message'] 	= "Invalid Token, Host name or API is currently disabled. Please contact support.";
			$response['success'] 	= false;
			
		}else{
			
			if($isactive == true)
			{   	
				Mage::getSingleton('core/session')->setCategoryId($categoryid);
				Mage::getSingleton('core/session')->setStoreid($storeid);
				Mage::app()->setCurrentStore($storeid);
				
				$response['Message'] 	= $token;
				$response['storeid']	= $storeid;
				$response['success'] 	= true;		

				
			}elseif($isactive == false){
				
				$response['message'] 	= "This token was inactive. Please contact support.";
				$response['success'] 	= false;	

			}			
			
		}		
		
		return $response;  
	}
	public function validatetokenNetsuite()
	{
		if($_POST["cmdEvent"] || $_GET['cmdEvent']){

		// if($_POST["cmdEvent"] == "PROCESS_ORDER" || $_POST["cmdEvent"] == "COMPLETE_ORDER" || $_POST["cmdEvent"] == "CREATE_PRODUCT" ||$_POST["cmdEvent"] == "UPDATE_PRODUCT" || $_POST["cmdEvent"] == "UPDATE_PRODUCT_PRICE" || $_POST["cmdEvent"] == "UPDATE_PRODUCT_STATUS"){
				$stid 	= $_POST['website'];
				$qry	= "SELECT name FROM core_store WHERE store_id = $stid ";
				$sresults = $this->_getConnection()->fetchRow($qry);
			
				$fields 			= array();
				$fields['hostname']		= $_POST['sitehost'];
				$fields['store']		=$sresults['name'];
				$fields['tokenused']		= $_POST['token'];
				$fields['cmdevent']		= $_POST['cmdEvent'];
				$fields['dataposted']		= json_encode($_POST);			
				$fields['created_date']		= date("Y-m-d H:i:s");		
							
				$this->_getConnection()->insert('unilab_connectionlogs', $fields);
		// }
		
		
		if($_POST['token']){
			$token = $_POST['token'];
		}elseif($_GET['token']){
			$token = $_GET['token'];
		}
		$hostname = $_POST['sitehost'];
		
		$qry 	= "SELECT * FROM unilab_token WHERE token='$token' and hostname='$hostname'";
		
		$result = $this->_getConnection()->fetchRow($qry);
		
		$tokenid	= $result['id'];
		$isactive 	= $result['isactive'];
		$categoryid = $result['store']; 
		$storename 	= Mage::getModel('webservice/function_store')->getStorename($categoryid);
		$storeid 	= Mage::getModel('webservice/function_store')->getStoreid($storename);
		
		
		if($tokenid == "")
		{
			//$response['CreatedDate'] = Date("Y-m-d H:i:s");
			$response['Message'] 	= "Invalid Token, Host name or API is currently disabled. Please contact support.";
			$response['success'] 	= false;
			
		}else{
			
			
			if($isactive == true)
			{   
				$response['Message'] 	= $token;
				$response['storeid']	= $storeid;
				$response['success'] 	= true;		
				
			}elseif($isactive == false){
				
				$response['Message'] 	= "This token was inactive. Please contact support.";
				$response['success'] 	= false;			
			}
			
			
		}
		
				
		// if($response['success'] == false){				
			// echo Mage::helper('core')->jsonEncode($response);		
			// exit();
			
			
			
		// }
		
		}
		else{

				$response['Message'] 	= "Funtion not exist. Please contact Administrator.";
				$response['success'] 	= false;
		}
		return $response;
		

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
		public function saveapidatalogs($postdata){

	
		if( $postdata['orders'] != ""){
				$stid 	= $postdata['orders']['storeid'];
		}elseif ( $postdata['customers'] != "") {
			$stid 	= $postdata['customers']['storeid'];
		}
	
		$qry	= "SELECT name FROM core_store WHERE store_id = $stid ";
		$sresults = $this->_getConnection()->fetchRow($qry);

		$fields 			= array();
		$fields['hostname']		= $postdata['sitehost'];
		$fields['tokenused']		= $postdata['token'];
		$fields['cmdevent']		= $postdata['cmdEvent'];
		$fields['dataposted']		= json_encode($postdata);			
		$fields['created_date']		= date("Y-m-d H:i:s");				
		$fields['store']		= $sresults['name'];			
		$this->_getConnection()->insert('unilab_connectionlogs', $fields);

	}

}