<?php

class Unilab_Webservice_Model_Netsuite_Token extends Mage_Core_Model_Abstract

{

	protected function _construct()

    {

		date_default_timezone_set('Asia/Taipei');

		

		// $fields 				= array();

		// $fields['hostname']		= $_POST['sitehost'];

		// $fields['store']		= $results['name'];

		// $fields['tokenused']	= $_POST['token'];

		// $fields['cmdevent']		= $_POST['cmdEvent'];

		// $fields['dataposted']	= json_encode($_POST);			

		// $fields['created_date']	= date("Y-m-d H:i:s");		

					

		// $this->_getConnection()->insert('unilab_connectionlogs', $fields);

	}

	

	protected function _getConnection()

    {

		$this->_Connection = Mage::getSingleton('core/resource')->getConnection('core_write');	

        return $this->_Connection;

    }	

	

	public function validatetoken()

	{

		

		$token = $_POST['token'];

		$hostname = $_POST['sitehost'];
		


		$this->saveapidatalogs($_POST,22,$hostname );

		$qry 	= "SELECT * FROM unilab_token WHERE token = '$token' and hostname = '$hostname'";

		$result = $this->_getConnection()->fetchRow($qry);

		

		$tokenid = $result['id'];

		$isactive = $result['isactive'];

		

		if($tokenid == "")

		{

			$response['Message'] 	= "Invalid Token, Host name or API is currently disabled. Please contact support.";

			$response['success'] 	= false;

			

		}else{

			

			if($isactive == true)

			{   

				$response['Message'] 	= 'Success';

				$response['success'] 	= true;		

				

			}elseif($isactive == false){

				

				$response['Message'] 	= "This token was inactive. Please contact support.";

				$response['success'] 	= false;			

			}

			

		}

		

		return $response;

	}

	

	public function savepostlogs($postdata,$storeid,$response)

	{

		$qry	= "SELECT name FROM core_store WHERE store_id = $storeid";

		$results = $this->_getConnection()->fetchRow($qry);



		$fields 				= array();

		$fields['store']		= $results['name'];	

		$fields['gatewayurl']	= $postdata['gateway'];

		$fields['tokenused']	= $postdata['token'];

		$fields['cmdevent']		= $postdata['cmdEvent'];

		$fields['dataposted']	= json_encode($postdata);	

		$fields['response']		= json_encode($response);			

		$fields['created_date']	= date("Y-m-d H:i:s");				



		

		$this->_getConnection()->insert('unilab_postlogs', $fields);

		$this->_getConnection()->commit();	

	}

	

	public function saveapidatalogs($postdata,$store_id,$hostname)

	{

		

		$qry	= "SELECT name FROM core_store WHERE store_id = $store_id";
		
		$sresults = $this->_getConnection()->fetchRow($qry);
	

		$fields 				= array();

		$fields['hostname']		= $hostname;

		$fields['tokenused']	= $postdata['token'];

		$fields['cmdevent']		= $postdata['cmdEvent'];

		$fields['dataposted']	= json_encode($postdata);			

		$fields['created_date']	= date("Y-m-d H:i:s");				

		$fields['store']		= $sresults['name'];		
	

		$this->_getConnection()->insert('unilab_connectionlogs', $fields);

		// $this->_getConnection()->commit();	



	}

}