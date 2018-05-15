<?php

date_default_timezone_set('Asia/Taipei');

class Unilab_Webservice_Model_Netsuite_Postdata_Customer extends Mage_Core_Model_Abstract
{
	protected function _getConnection()
    {
		$this->_Connection = Mage::getSingleton('core/resource')->getConnection('core_write');	 
		
        return $this->_Connection;
    }	
    
    
    public function netsuitesettings($storeid)
	{
		$settings 				= array();
		$settings['gateway'] 	= Mage::getStoreConfig('webservice/netsuiteregsettings/netsuitereggateway', $storeid);
		$settings['token']		= Mage::getStoreConfig('webservice/netsuiteregsettings/netsuiteregtoken', $storeid);
		$settings['sitehost']	= Mage::getStoreConfig('webservice/netsuiteregsettings/netsuiteurl', $storeid);
		$arr 					= explode('/', Mage:: getBaseUrl());
		$settings['hostname']	= $arr[2];
		
		return $settings;
	}
	
	public function sendCustomer($customerId, $storeid)
	{	
		try{
			
			$senttoNS = $this->getCustomersent($customerId);
			
			if($senttoNS == 1)
			{
				$response['message'] = "Customer already sent";
				
				return $response;
			}
			
			$currentdate = date("Y-m-d H:i:s");  

			$netsettings = $this->netsuitesettings($storeid);
			
			$postdata 				= array();
			$postdata['gateway'] 	= $netsettings['gateway'];
			$postdata['token'] 		= $netsettings['token'];
			$postdata['sitehost'] 	= $netsettings['sitehost'];
			$postdata['cmdEvent'] 	= 'CreateCustomer';
			
			$postdata['hostname']	= $netsettings['hostname'];
			
			$customers 				= $this->getCustomerinfo($customerId);
			$postdata['customers'] 	= $customers;
			
			$addressId 				= $customers['customeraddressid'] ;
			
			$adapter = new Zend_Http_Client_Adapter_Curl();
			$adapter->setCurlOption(CURLOPT_REFERER, $postdata['sitehost']);
			
			$client = new Zend_Http_Client($postdata['gateway']); 
			$client->setAdapter($adapter);
			
			$client->setParameterPost('sitehost', $postdata['sitehost']); 
			$client->setParameterPost('token', $postdata['token']); 
			$client->setParameterPost('cmdEvent', $postdata['cmdEvent']); 
			$client->setParameterPost('hostname', $postdata['hostname']); 
			
			$client->setParameterPost('email', $customers['email']); 
			$client->setParameterPost('password', $customers['password']); 
			$client->setParameterPost('firstname', $customers['firstname']); 
			$client->setParameterPost('middlename', ''); 
			$client->setParameterPost('lastname', $customers['lastname']); 
			$client->setParameterPost('civil_status', $customers['civil_status']); 
			$client->setParameterPost('gender', $customers['gender']); 
			$client->setParameterPost('dob', $customers['dob']); 
			$client->setParameterPost('created_at', $customers['created_at']);
			$client->setParameterPost('storeid', $customers['storeid']);
			$client->setParameterPost('groupid',  $customers['groupid']);
			$client->setParameterPost('customer_id', $customers['customer_id']);
			$client->setParameterPost('agree_on_terms', $customers['agree_on_terms']); 
			$client->setParameterPost('is_subscribed', $customers['is_subscribed']); 

			$client->setParameterPost('country_id', $customers['country_id']); 
			$client->setParameterPost('province', $customers['province']); 
			$client->setParameterPost('city', $customers['city']); 
			$client->setParameterPost('company', $customers['company']); 
			$client->setParameterPost('address', $customers['address']); 
			$client->setParameterPost('postcode', $customers['postcode']); 
			$client->setParameterPost('landline', $customers['landline']);
			$client->setParameterPost('mobile', $customers['mobile']);
			$client->setParameterPost('websiteid', $customers['websiteid']); 
			$client->setParameterPost('customeraddressid', $customers['customeraddressid']);
			$client->setParameterPost('isshipping', $customers['isshipping']); 
			$client->setParameterPost('isbilling', $customers['isbilling']);
		
			$_apiResponse 			= $client->request(Zend_Http_Client::POST); 
			
			$apiResponse 	 		=  json_decode($_apiResponse->getbody());
		
				foreach($apiResponse as $key => $value)
				{
				    $api_ResponseData[$key] = (array) $value;
				} 
			$this->createlogs( $customerId,"DBM",$postdata['cmdEvent'],$api_ResponseData['dbm']['success'],json_encode($api_ResponseData['dbm']['message']) );
 
			if($api_ResponseData['dbm']['success'] == 1){
			
				$fields 						= array();
				$fields['senttoNS']				= $api_ResponseData['dbm']['success'];
				$fields['senttoNS_date']		= $currentdate;
				$fields['update_senttoNS']		= $api_ResponseData['dbm']['success'];
				$fields['update_senttoNS_date']	= $currentdate;
				$fields['sent_dbm_netsuite']	= $api_ResponseData['dbmtonetsuitestatus'][0]->sent;


				$where = array($this->_getConnection()->quoteInto('entity_id=?',$customerId));
				$this->_getConnection()->update('customer_entity', $fields, $where);
				$this->_getConnection()->commit();	

				$fields['sent_dbm_netsuite']	= $api_ResponseData['dbmtonetsuitestatusaddress'][0]->sent;

				$whereAdd = array($this->_getConnection()->quoteInto('entity_id=?',$addressId));
				$this->_getConnection()->update('customer_address_entity', $fields, $whereAdd);
				$this->_getConnection()->commit();
				if (isset($api_ResponseData['netsuite'])) {

				$this->createlogs( $customerId,"Netsuite",$postdata['cmdEvent'],$api_ResponseData['netsuite']['status'],json_encode($api_ResponseData['netsuite']['message']." ".json_encode($api_ResponseData['netsuite']['data'])) );
				}
				if($addressId){
					
					$this->createlogs( $addressId,"DBM","CreateAddress",$api_ResponseData['dbm_address']['success'],json_encode($api_ResponseData['dbm_address']['message']) );
					if (isset($api_ResponseData['netsuite_address'])) {
					$this->createlogs( $addressId,"Netsuite","CreateAddress",$api_ResponseData['netsuite_address']['status'],json_encode($api_ResponseData['netsuite_address']['message'] ." ".json_encode($api_ResponseData['netsuite_address']['data'])));
				}
				}
					
				
				$response['message']		= $api_ResponseData['dbm']['message'];
				$response['customer_id'] 	= $customers['customer_id'];
				$response['success']		= $api_ResponseData['dbm']['success'];
 
			}else{
				
				$response['message']		= $api_ResponseData['dbm']['message'];
				$response['customer_id'] 	= $customers['customer_id'];
				$response['success']		= $api_ResponseData['dbm']['success'];
			}

			

 				 
			Mage::log($postdata, true, 'sendCustomer_post_true_'.date("Y-m-d").'.log');
			Mage::log($api_ResponseData, true, 'sendCustomer_response_true_'.date("Y-m-d").'.log');

		}catch(Exception $e){ 
		   
		  $response['message'] 				 = $e->getMessage();
		  $response['success']    			 = false;

		  Mage::log($e->getMessage() , true, 'sendCustomer_false_'.date("Y-m-d").'.log');
		} 
					
		return $response;

	}
	
	public function getCustomerinfo($customerId)
	{	
	
		$customer = Mage::getModel("customer/customer");
		$customer->load($customerId);
				
		$customerAddress 	= Mage::getModel('customer/address')->load($customer->getDefaultBilling());
		$street 	 		= $customerAddress->getStreet();
		$provinceid	 		= $customerAddress->getregion_id();
		$cityid 	 		= $customerAddress->getCity();
		$cityname	 		= $customerAddress['city'];
		$customerAddressId 	= $customerAddress->getId();

		$cityname	 		= $customerAddress->getCity();
		$read		 		= Mage::getSingleton('core/resource')->getConnection('core_read'); 
		$rscity 	 		= $read->fetchRow("select city_id from unilab_cities where name = '$cityname'"); 
		$subcription		= $read->fetchRow("SELECT * FROM  newsletter_subscriber WHERE  customer_id ='$customerId'"); 
		
		$results['email'] 				= $customer->getEmail();
		$results['password'] 			= $customer->getPasswordHash();
		$results['firstname']			= $customer->getFirstname();
		$results['middlename'] 			= $customer->getMiddlename();
		$results['lastname'] 			= $customer->getLastname();
		$results['civil_status'] 		= $customer->getCivilStatus(); 
		$results['gender']	 			= $customer->getGender();
		$results['dob']					= $customer->getDob();
		$results['created_at']			= $customer->getCreatedAt();
		
		$results['storeid']				= $customer->getStoreId();
		$results['groupid']				= $customer->getGroupId();
		$results['websiteid']			= $customer->getWebsiteId();
		$results['customer_id']			= $customer->getId();
		//address
		$results['company']				= $customerAddress->getCompany();
		$results['country_id'] 			= $customerAddress->getCountry_id(); //'PH';
		$results['province'] 			= $customerAddress->getRegion();
		$results['city'] 				= $customerAddress->getCity();
		$results['address'] 			= $street[0];
		$results['postcode'] 			= $customerAddress->getpostcode();	
		$results['landline'] 			= $customerAddress->gettelephone();
		$results['mobile'] 				= $customerAddress->getmobile();
		$results['agree_on_terms'] 		= $customer->getAgreeOnTerms();
		$results['is_subscribed'] 		= $subcription['subscriber_status'];
		$results['customeraddressid'] 	= $customerAddress->getId();

		$bill = $customer->getDefaultBilling();
		$ship = $customer->getDefaultShipping();
	
		if ($ship != ""){

			$results['isshipping'] = 1;

		}else{
			$results['isshipping'] = 0;

		}
		if ($bill != ""){

			$results['isbilling'] = 1;
			
		}else{
			$results['isbilling'] = 0;

		}

		return $results;
	}
	
	
	public function updateCustomer($customerId, $storeid)
	{	
		$currentdate = date("Y-m-d H:i:s");  	
	
		$netsettings = $this->netsuitesettings($storeid);
		
		try{
		
			$postdata 				= array();
			$postdata['gateway'] 	= $netsettings['gateway'];
			$postdata['token'] 	    = $netsettings['token'];
			$postdata['sitehost'] 	= $netsettings['sitehost'];
			$postdata['cmdEvent'] 	= 'UpdateCustomer';
			$postdata['hostname']	= $netsettings['hostname'];	
			$customers 				= $this->getCustomerinfo($customerId);
			$postdata['customers'] 	= $customers;
			

			$adapter = new Zend_Http_Client_Adapter_Curl();
			$adapter->setCurlOption(CURLOPT_REFERER, $postdata['sitehost']);
			
			$client = new Zend_Http_Client($postdata['gateway']); 
			$client->setAdapter($adapter);
			
			$client->setParameterPost('sitehost', $postdata['sitehost']); 
			$client->setParameterPost('token', $postdata['token']); 
			$client->setParameterPost('cmdEvent', $postdata['cmdEvent']); 
			$client->setParameterPost('websiteid', $customers['websiteid']); 
			$client->setParameterPost('hostname', $postdata['hostname']); 
			$client->setParameterPost('email', $customers['email']); 
			$client->setParameterPost('password', $customers['password']); 
			$client->setParameterPost('firstname', $customers['firstname']); 
			$client->setParameterPost('middlename', ''); 
			$client->setParameterPost('lastname', $customers['lastname']); 
			$client->setParameterPost('civil_status', $customers['civil_status']); 
			$client->setParameterPost('gender', $customers['gender']); 
			$client->setParameterPost('dob', $customers['dob']); 
			$client->setParameterPost('updated_at', $customers['created_at']);
			$client->setParameterPost('storeid', $customers['storeid']);
			$client->setParameterPost('groupid', $customers['groupid']);
			$client->setParameterPost('customer_id', $customers['customer_id']);
			$client->setParameterPost('agree_on_terms', $customers['agree_on_terms']); 
 
			$_apiResponse 		= $client->request(Zend_Http_Client::POST); 
			
			$apiResponse 	 		=  json_decode($_apiResponse->getbody());

				foreach($apiResponse as $key => $value)
				{
				    $api_ResponseData[$key] = (array) $value;
				} 
	 
				$this->createlogs( $customerId,"DBM",$postdata['cmdEvent'],$api_ResponseData['dbm']['success'],json_encode($api_ResponseData['dbm']['message']) );
 			
			if($api_ResponseData['dbm']['success'] == 1){
			
				$fields 						= array();  
				$fields['update_senttoNS']		= $api_ResponseData['dbm']['success'];
				$fields['update_senttoNS_date']	= $currentdate;

				$where = array($this->_getConnection()->quoteInto('entity_id=?',$customerId));
				$this->_getConnection()->update('customer_entity', $fields, $where);
				$this->_getConnection()->commit();	

				$response['message']		= $api_ResponseData['dbm']['message'];
				$response['customer_id'] 	= $customers['customer_id'];
				$response['success']		= $api_ResponseData['dbm']['success'];


				$this->createlogs( $customerId,"Netsuite",$postdata['cmdEvent'],$api_ResponseData['netsuite']['status'],json_encode($api_ResponseData['netsuite']['message']." ".json_encode($api_ResponseData['netsuite']['data'])) );	
				
 
			}else{
				$fields 						= array();  
				$fields['update_senttoNS']		= $api_ResponseData['dbm']['success'];
				$fields['update_senttoNS_date']	= $currentdate;

				$where = array($this->_getConnection()->quoteInto('entity_id=?',$customerId));
				$this->_getConnection()->update('customer_entity', $fields, $where);
				$this->_getConnection()->commit();	

				$response['message']		= $api_ResponseData['dbm']['message'];
				$response['customer_id'] 	= $customers['customer_id'];
				$response['success']		= $api_ResponseData['dbm']['success'];
			}

				Mage::log($api_ResponseData, true, 'updateCustomer_response_true_'.date("Y-m-d").'.log');
				Mage::log($postdata, true, 'update_Customer_post_true_'.date("Y-m-d").'.log');
			
		}catch(Exception $e){ 
		   
		  $response['message'] 				 = $e->getMessage();
		  $response['success']    			 = false;

		  Mage::log($e->getMessage() , true, 'updateCustomer_false_'.date("Y-m-d").'.log');
		} 
					
		return $response;
	
	}
	
	public function addAddress($customerId,$addressId,$storeid)
	{

		$currentdate = date("Y-m-d H:i:s");  	
		
		try{
			$read       = Mage::getSingleton('core/resource')->getConnection('core_read'); 
			
			$issent     = $read->fetchRow("select * from customer_address_entity where entity_id = $addressId"); 

			if($issent['sent_dbm_netsuite'] == 0)
			{	
				
				$netsettings 					= $this->netsuitesettings($storeid);
				$postdata 						= array();
				$postdata['gateway'] 			= $netsettings['gateway'];
				$postdata['token'] 				= $netsettings['token'];
				$postdata['sitehost'] 			= $netsettings['sitehost'];
				$postdata['hostname']			= $netsettings['hostname'];
				$postdata['cmdEvent'] 			= 'CreateAddress';
				$postdata['customeraddressid'] 	= $addressId;
		
				$customers 						= $this->getAddressCustomerinfo($customerId,$addressId);
				
				$postdata['customers'] 			= $customers;
				
				
				$adapter = new Zend_Http_Client_Adapter_Curl();
				$adapter->setCurlOption(CURLOPT_REFERER, $postdata['sitehost']);
				
				$client = new Zend_Http_Client($postdata['gateway']); 
				$client->setAdapter($adapter);
				
				$client->setParameterPost('sitehost', $postdata['sitehost']); 
				$client->setParameterPost('token', $postdata['token']); 
				$client->setParameterPost('cmdEvent', $postdata['cmdEvent']); 
				$client->setParameterPost('hostname', $postdata['hostname']); 
				
				$client->setParameterPost('customeraddressid', $postdata['customeraddressid']);
				 
				$client->setParameterPost('websiteid', $customers['websiteid']); 
				$client->setParameterPost('storeid', $customers['storeid']);
				$client->setParameterPost('email', $customers['email']); 
				$client->setParameterPost('firstname', $customers['firstname']); 
				$client->setParameterPost('lastname', $customers['lastname']); 
				$client->setParameterPost('created_at', $customers['created_at']);
				$client->setParameterPost('mobile', $customers['mobile']);
				$client->setParameterPost('landline', $customers['landline']);
				$client->setParameterPost('country_id' , $customers['country']);
				$client->setParameterPost('province' , $customers['province']);
				$client->setParameterPost('city' , $customers['city']);
				$client->setParameterPost('address' , $customers['address']);
				$client->setParameterPost('postcode' , $customers['postcode']);
				$client->setParameterPost('department' , $customers['department']);
				$client->setParameterPost('remarks' , $customers['remarks']);
				$client->setParameterPost('isbilling'  ,$customers['isbilling']);
				$client->setParameterPost('isshipping'  ,$customers['isshipping']);
				$client->setParameterPost('created_at', $customers['created_at']);
				$client->setParameterPost('customer_id', $customers['customer_id']);
			
				$_apiResponse 		= $client->request(Zend_Http_Client::POST); 

				$apiResponse 	 		=  json_decode($_apiResponse->getbody());

				foreach($apiResponse as $key => $value)
				{
				    $api_ResponseData[$key] = (array) $value;
				} 
	 					 
				$this->createlogs( $addressId,"DBM",$postdata['cmdEvent'],$api_ResponseData['dbm']['success'],json_encode($api_ResponseData['dbm']['message']) );

				if($api_ResponseData['dbm']['success'] == 1){
				
					$fields 						= array();
					$fields['senttoNS']				= $api_ResponseData['dbm']['success'];
					$fields['senttoNS_date']		= $currentdate;
					$fields['update_senttoNS']		= $api_ResponseData['dbm']['success'];
					$fields['update_senttoNS_date']	= $currentdate;
					$fields['sent_dbm_netsuite']	= $api_ResponseData['dbmtonetsuitestatusaddress'][0]->sent;

	 
					$whereAdd = array($this->_getConnection()->quoteInto('entity_id=?',$addressId));
					$this->_getConnection()->update('customer_address_entity', $fields, $whereAdd);
					$this->_getConnection()->commit();

					$response['message']		= $api_ResponseData['dbm']['message'];
					$response['customer_id'] 	= $customers['customer_id'];
					$response['success']		= $api_ResponseData['dbm']['success'];

					if (isset($api_ResponseData['netsuite'])) {
						$this->createlogs( $addressId,"Netsuite",$postdata['cmdEvent'],$api_ResponseData['netsuite']['status'],json_encode($api_ResponseData['netsuite']['message']." ".json_encode($api_ResponseData['netsuite']['data'])) );
					}
					
	 
				}else{
					$response['message']		= $api_ResponseData['dbm']['message'];
					$response['customer_id'] 	= $customers['customer_id'];
					$response['success']		= $api_ResponseData['dbm']['success'];
				}
		

				Mage::log($api_ResponseData , true, 'addAddress_response_true_'.date("Y-m-d").'.log');
				Mage::log($postdata, true, 'addAddress_post_true_'.date("Y-m-d").'.log');
			} 
	
		}catch(Exception $e){ 
		   
		  $response['message'] 				 = $e->getMessage();
		  $response['success']    			 = false;

		  Mage::log($e->getMessage() , true, 'addAddress_false_'.date("Y-m-d").'.log');
			
		}	
		return $response;
	}
	
	
	
	public function getAddressCustomerinfo($customerId,$addressId)
	{	
		$customer 	 				= Mage::getModel("customer/customer");
		$customer->load($customerId);
		$customerAddress 			= Mage::getModel("customer/address");
		$addressinfo				= $customerAddress->load($addressId);
	
		$results['email'] 			= $customer->getEmail();
		$results['firstname']		= $addressinfo['firstname'];
		$results['lastname'] 		= $addressinfo['lastname'];
		$results['created_at']		= $addressinfo['created_at'];
		$results['customer_id']		= $customer->getId();
		$results['storeid']			= $customer->getStoreId();
		$results['websiteid']		= $customer->getWebsiteId();
		
		//address
		$results['country'] 		= $addressinfo['country_id']; //'PH';
		$results['province'] 		= $addressinfo['region'];
		$results['city'] 			= $addressinfo['city'];
		$results['address'] 		= $addressinfo['street'];
		$results['postcode'] 		= $addressinfo['postcode'];	
		$results['landline'] 		= $addressinfo['telephone'];
		$results['mobile'] 			= $addressinfo['mobile'];
		$results['department'] 		= $addressinfo['company'];
		$results['remarks'] 		= $customer->getRemarks();
		
		$results['reference_id'] 	= $customer->getReference_id();

		$bill = $customer->getDefaultBilling();
		$ship = $customer->getDefaultShipping();

		if ($ship == $addressId){

			$results['isshipping'] = 1;

		}else{
			$results['isshipping'] = 0;

		}
		if ($bill == $addressId){

			$results['isbilling'] = 1;
			
		}else{
			$results['isbilling'] = 0;

		}
		
		return $results;
		
	}
	
	public function updateAddress($customerId,$addressId,$storeid)
	{
		$currentdate = date("Y-m-d H:i:s");  	
		try{
			
			$netsettings = $this->netsuitesettings($storeid);
				
			$postdata 						= array();
			$postdata['gateway'] 			= $netsettings['gateway'];
			$postdata['token'] 				= $netsettings['token'];
			$postdata['sitehost'] 			= $netsettings['sitehost'];
			$postdata['cmdEvent'] 			= 'UpdateAddress';
			$postdata['customeraddressid'] 	= $addressId;
			$postdata['hostname']			= $netsettings['hostname'];
			
			$customers 						= $this->getAddressCustomerinfo($customerId,$addressId);
			$postdata['customers'] 			= $customers;
			
			
			$adapter = new Zend_Http_Client_Adapter_Curl();
			$adapter->setCurlOption(CURLOPT_REFERER, $postdata['sitehost']);
			
			$client = new Zend_Http_Client($postdata['gateway']); 
			$client->setAdapter($adapter);
			
			
			$client->setParameterPost('sitehost', $postdata['sitehost']); 
			$client->setParameterPost('token', $postdata['token']); 
			$client->setParameterPost('cmdEvent', $postdata['cmdEvent']); 
			$client->setParameterPost('hostname', $postdata['hostname']); 
			
			$client->setParameterPost('customeraddressid', $postdata['customeraddressid']);
			 
			$client->setParameterPost('websiteid', $customers['websiteid']); 
			$client->setParameterPost('storeid', $customers['storeid']);
			$client->setParameterPost('email', $customers['email']); 
			$client->setParameterPost('firstname', $customers['firstname']); 
			$client->setParameterPost('lastname', $customers['lastname']); 
			$client->setParameterPost('mobile', $customers['mobile']);
			$client->setParameterPost('landline', $customers['landline']);
			$client->setParameterPost('country_id' , $customers['country']);
			$client->setParameterPost('province' , $customers['province']);
			$client->setParameterPost('city' , $customers['city']);
			$client->setParameterPost('address' , $customers['address']);
			$client->setParameterPost('postcode' , $customers['postcode']);
			$client->setParameterPost('department' , $customers['department']);
			$client->setParameterPost('remarks' , $customers['remarks']);
			$client->setParameterPost('isbilling'  ,$customers['isbilling']);
			$client->setParameterPost('isshipping'  ,$customers['isshipping']);
			$client->setParameterPost('reference_id'  , $customers['reference_id']);
			$client->setParameterPost('customer_id', $customers['customer_id']);

			$_apiResponse 		= $client->request(Zend_Http_Client::POST); 
		 
			$apiResponse 	 		=  json_decode($_apiResponse->getbody());
		
				foreach($apiResponse as $key => $value)
				{
				    $api_ResponseData[$key] = (array) $value;
				} 
			$this->createlogs( $addressId,"DBM",$postdata['cmdEvent'],$api_ResponseData['dbm']['success'],json_encode($api_ResponseData['dbm']['message']) );
	 		
			if($api_ResponseData['dbm']['success'] == 1){
			
				$fields 						= array();  
				$fields['update_senttoNS']		= $api_ResponseData['dbm']['success'];
				$fields['update_senttoNS_date']	= $currentdate;

				$where = array($this->_getConnection()->quoteInto('entity_id=?',$addressId));
				$this->_getConnection()->update('customer_address_entity', $fields, $where);
				$this->_getConnection()->commit();	

				$response['message']		= $api_ResponseData['dbm']['message'];
				$response['customer_id'] 	= $customers['customer_id'];
				$response['success']		= $api_ResponseData['dbm']['success'];


				$this->createlogs( $addressId,"Netsuite",$postdata['cmdEvent'],$api_ResponseData['netsuite']['status'],json_encode($api_ResponseData['netsuite']['message']." ".json_encode($api_ResponseData['netsuite']['data'])) );
				
 
			}else{
				$fields 						= array();  
				$fields['update_senttoNS']		= $api_ResponseData['dbm']['success'];
				$fields['update_senttoNS_date']	= $currentdate;

				$where = array($this->_getConnection()->quoteInto('entity_id=?',$addressId));
				$this->_getConnection()->update('customer_address_entity', $fields, $where);
				$this->_getConnection()->commit();	

				$response['message']		= $api_ResponseData['dbm']['message'];
				$response['customer_id'] 	= $customers['customer_id'];
				$response['success']		= $api_ResponseData['dbm']['success'];
			}
 				
			Mage::log($api_ResponseData , true, 'updateAddress_response_true_'.date("Y-m-d").'.log');
			Mage::log($postdata, true, 'updateAddress_post_true_'.date("Y-m-d").'.log');
			
		}catch(Exception $e){ 
		   
			$response['message'] 				 = $e->getMessage();
			$response['success']    			 = false;

			Mage::log($e->getMessage() , true, 'updateAddress_false_'.date("Y-m-d").'.log');
		} 
			
		return $response;
	
	}
		
	
	public function getCustomersent($customerId)
	{
		$query 	= "SELECT sent_dbm_netsuite from customer_entity WHERE entity_id='$customerId'"; 
		$result = $this->_getConnection()->fetchRow($query); 
		
		return $result['sent_dbm_netsuite'];	
	}
	

	public function sendCustomerWithoutAddress($customerId, $storeid)
	{
		try{
			$senttoNS = $this->getCustomersent($customerId);
			
			if($senttoNS == 1)
			{
				$response['message'] = "Customer already sent";
				return $response;
			}
			$currentdate 			= date("Y-m-d H:i:s");  

			$netsettings 			= $this->netsuitesettings($storeid);
			 
			$postdata 				= array();
			$postdata['gateway'] 	= $netsettings['gateway'];
			$postdata['token'] 		= $netsettings['token'];
			$postdata['sitehost'] 	= $netsettings['sitehost'];
			$postdata['hostname']	= $netsettings['hostname'];
			$postdata['cmdEvent'] 	= 'CreateCustomer';
			
			$customers 				= $this->getCustomerinfo($customerId);
			$postdata['customers'] 	= $customers;
			
			
			$adapter = new Zend_Http_Client_Adapter_Curl();
			$adapter->setCurlOption(CURLOPT_REFERER, $postdata['sitehost']);
			
			$client = new Zend_Http_Client($postdata['gateway']); 
			$client->setAdapter($adapter);
			
			$client->setParameterPost('sitehost', $postdata['sitehost']); 
			$client->setParameterPost('token', $postdata['token']); 
			$client->setParameterPost('cmdEvent', $postdata['cmdEvent']); 
			$client->setParameterPost('hostname', $postdata['hostname']); 
			
			$client->setParameterPost('email', $customers['email']); 
			$client->setParameterPost('password', $customers['password']); 
			$client->setParameterPost('firstname', $customers['firstname']); 
			$client->setParameterPost('middlename', ''); 
			$client->setParameterPost('lastname', $customers['lastname']); 
			$client->setParameterPost('civil_status', $customers['civil_status']); 
			$client->setParameterPost('gender', $customers['gender']); 
			$client->setParameterPost('dob', $customers['dob']); 
			$client->setParameterPost('created_at', $customers['created_at']);
			$client->setParameterPost('storeid', $customers['storeid']);
			$client->setParameterPost('groupid',  $customers['groupid']);
			$client->setParameterPost('customer_id', $customers['customer_id']);
			$client->setParameterPost('agree_on_terms', $customers['agree_on_terms']); 
			$client->setParameterPost('is_subscribed', $customers['is_subscribed']); 

			$_apiResponse 			= $client->request(Zend_Http_Client::POST); 
			
			$apiResponse 	 		=  json_decode($_apiResponse->getbody());
	 
				foreach($apiResponse as $key => $value)
				{
				    $api_ResponseData[$key] = (array) $value;
				} 
	 		
			$this->createlogs( $customerId,"DBM",$postdata['cmdEvent'],$api_ResponseData['dbm']['success'],json_encode($api_ResponseData['dbm']['message']) );
		 			
			if($api_ResponseData['dbm']['success'] == 1){
			
				$fields 							= array();
				$fields['senttoNS']					= $api_ResponseData['dbm']['success'];
				$fields['senttoNS_date']			= $currentdate;
				$fields['update_senttoNS']			= $api_ResponseData['dbm']['success'];
				$fields['update_senttoNS_date']		= $currentdate;
				$fields['sent_dbm_netsuite']		= $api_ResponseData['dbmtonetsuitestatus'][0]->sent;


				$where = array($this->_getConnection()->quoteInto('entity_id=?',$customerId));
				$this->_getConnection()->update('customer_entity', $fields, $where);
				$this->_getConnection()->commit();	
 				if (isset($api_ResponseData['netsuite'])) {
				$this->createlogs( $customerId,"Netsuite",$postdata['cmdEvent'],$api_ResponseData['netsuite']['status'],json_encode($api_ResponseData['netsuite']['message']." ".json_encode($api_ResponseData['netsuite']['data'])) );
 				}
				
				$response['message']		= $api_ResponseData['dbm']['message'];
				$response['customer_id'] 	= $customers['customer_id'];
				$response['success']		= $api_ResponseData['dbm']['success'];
 
			}else{
				
				$response['message']		= $api_ResponseData['dbm']['message'];
				$response['customer_id'] 	= $customers['customer_id'];
				$response['success']		= $api_ResponseData['dbm']['success'];
			}
		 
				
			Mage::log($api_ResponseData, true, 'sendCustomerWithoutAddress_response_true_'.date("Y-m-d").'.log');
			Mage::log($postdata, true, 'sendCustomerWithoutAddress_post_true_'.date("Y-m-d").'.log');
			
		}catch(Exception $e){ 
		   
		  $response['message'] 				 = $e->getMessage();
		  $response['success']    			 = false;

		  Mage::log($e->getMessage() , true, 'sendCustomerWithoutAddressfalse_'.date("Y-m-d").'.log');
		} 
					
		return $response;

	}
	
	public function createlogs($id,$from,$cmdEvent,$status,$message){
		$currentdate = date("Y-m-d H:i:s");  

		if ($status == null || $status == '') {
			$status = 0 ;
		}
		$data = array(
					'transaction_id' 	=>  $id,
					'cmdEvent'			=>	$cmdEvent,
					'data_from'			=>	$from,
					'status' 			=>  $status,
					'logs'				=>  $message,
					'date'				=>	$currentdate,
					'type'				=>  'send'
					 );

 		$this->_getConnection()->insert('unilab_logs_netsuite', $data);
	}
	
}