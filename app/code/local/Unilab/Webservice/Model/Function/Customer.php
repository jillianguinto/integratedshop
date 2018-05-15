<?php 

class Unilab_Webservice_Model_Function_Customer extends Mage_Core_Model_Abstract    
{

	protected function _getConnection()
    {
		$this->_Connection = Mage::getSingleton('core/resource')->getConnection('core_write');	
        return $this->_Connection;
    }
	
	public function AddCustomer($tokendata) 
    {

		if($_POST['email'] !='' AND $_POST['password'] !='' AND $_POST['firstname'] !='' AND $_POST['lastname'] !=''
		AND $_POST['dob'] !='' AND $_POST['mobile'] !='')
		{

			$date = date("Y-m-d");
			
			$storeid 	= $tokendata['storeid']; 
			$web_userid = $_POST['web_userid']; 
			$return_url = $_POST['return_url']; 
			$email 		= $_POST['email']; 
			 
			$websiteId  = Mage::app()->getWebsite()->getId();
			$store	    = Mage::app()->getStore(); 

			$websiteUrl  = Mage::getStoreConfig("webservice/sitesettings/websiteurl", $storeid);
			
			$customerid = $this->checkCustomerByEmail($email, $websiteId);

			
			if($customerid['customer_id'])
			{
				$response['success'] = "existing";

				Mage::log($_POST, null, $_POST['cmdEvent'].'_existings'.$date.'.log');
				Mage::log($response, null, 'response_' . $_POST['cmdEvent'] . '_'. $date .'.log');

				Mage::app()->getFrontController()->getResponse()->setRedirect($return_url."?success=".$response['success']."&web_userid=".$web_userid);  

			}else{
				
				try
				{
					$customer   = Mage::getModel("customer/customer"); 
					$customer   ->setWebsiteId($websiteId)
								->setStore($store)
								->setEmail($email)
								->setPassword($_POST['password'])
								->setFirstname($_POST['firstname'])
								->setLastname($_POST['lastname']) 
								->setGender($_POST['gender'])
								->setCivil_status($_POST['civil_status'])
								->setDob($_POST['dob']);
					$customer->save();
					$lastcustomerid = $customer->getId();

					$this->addShopperwebsite($lastcustomerid, $storeid);

					 //Netsuite API
		            $customerId = $customer->getId();

		            $storeidapi         = Mage::app()->getStore()->getStoreId();
		            $read         = Mage::getSingleton('core/resource')->getConnection('core_read'); 
		            $addressCount = $read->fetchAll("SELECT count(*) FROM customer_address_entity WHERE parent_id = '$customerId'"); 
		            $netsuiteApiEnabled = Mage::getStoreConfig('webservice/netsuiteregsettings/netsuiteregenabled',$storeidapi);

		            if($netsuiteApiEnabled == 1 ){

		                if($addressCount > 0)
		                {
		                    $response = Mage::getModel('webservice/netsuite_postdata_customer')->sendCustomer($customerId, $storeidapi);   
		                }else
		                {
		                    $response = Mage::getModel('webservice/netsuite_postdata_customer')->sendCustomerWithoutAddress($customerId, $storeidapi);
		                }

		            }
					
					if($_POST['country_id'] !='' AND $_POST['provinceid'] !='' AND $_POST['cityid'] !='' AND $_POST['address'] !='' AND $_POST['postcode'] !='')
					{
						$this->AddCustomerAddress($_POST);
					}
					
					$qrytemplate 	= "select value from core_config_data where path='customer/create_account/email_template' and scope_id ='0'";						
					$rstemplate 	= $this->_getConnection()->fetchRow($qrytemplate);
					
					$templateId = 81; //$rstemplate['value'];
					
					$this->sendemail($templateId, $_POST['email'], $_POST['password'], $_POST['firstname']); 
					
					
					$response['message'] = 'Data was successfully saved!';
					$response['success'] = 1; 

					Mage::log($response, null, 'response_' . $_POST['cmdEvent'] . '_'. $date .'.log');

					$this->LoginCustomer($tokendata, 1);
					
					
				}
				catch(Exception $e)
				{            
					$response['message']    = $e->getMessage();
					$response['success']    = 0; 

					Mage::log($response, null, 'response_' . $_POST['cmdEvent'] . '_'. $date .'.log');  

					Mage::app()->getFrontController()->getResponse()->setRedirect($return_url."?success=".$response['success']."&web_userid=".$web_userid);          
				}
				
			}
		
		}else{

			$response['message'] = 'Required fields should not be null!';		
			$response['success'] = 0;

			Mage::log($response, null, 'response_' . $_POST['cmdEvent'] . '_'. $date .'.log');

			Mage::app()->getFrontController()->getResponse()->setRedirect($return_url."?success=".$response['success']."&web_userid=".$web_userid);  
		}

		
		
	}
	
	public function AddCustomerAddress($post)
    {
		$date = date("Y-m-d");

		$websiteId  = Mage::app()->getWebsite()->getId();

		$customer = Mage::getModel("customer/customer");
	    $customer->setWebsiteId($websiteId);
	    $customer->loadByEmail($post['email']);	


		$cityid = $post['cityid'];
		$read = Mage::getSingleton('core/resource')->getConnection('core_read'); 
		$rscity = $read->fetchRow("select name from unilab_cities where city_id = '$cityid'");

		$_custom_address = array (
			'firstname' => $post['firstname'],
			'lastname'  => $post['lastname'],
			'street'    => array (
				'0'     => $post['address']
			),
			'city'      => $rscity['name'],
			'postcode'  => $post['postcode'],
			'country_id'=> $post['country_id'], 
			'region_id' => $post['provinceid'], 

			'telephone' => $post['landline'],
			'mobile'    => $post['mobile'],                

		);
		
		$customAddress = Mage::getModel('customer/address')->load($customer->getDefaultBilling());
		$customAddress->setData($_custom_address)
					->setCustomerId($customer->getId())
					->setIsDefaultBilling('1')
					->setIsDefaultShipping('1')
					->setSaveInAddressBook('1');
		$customAddress->save();

		$customerId 			= $customer->getId();
        $addressId 				= $customAddress->getId();
        $storeidapi      		= Mage::app()->getStore()->getStoreId();
        $netsuiteApiEnabled   	= Mage::getStoreConfig('webservice/netsuiteregsettings/netsuiteregenabled',$storeidapi);

	        if($netsuiteApiEnabled == 1 ){

	        	//Mage::getModel('webservice/netsuite_postdata_customer')->addAddress($customerId,$addressId,$storeidapi);

	        }

	}

	public function UpdateCustomer()
	{
		// if($_POST['email'] !='' AND $_POST['firstname'] !='' AND $_POST['lastname'] !='' AND $_POST['dob'] !='' 
		// 	AND $_POST['gender'] !='' AND $_POST['mobile'] !='' AND $_POST['country_id'] !='' AND $_POST['provinceid'] !='' 
		// 	AND $_POST['cityid'] !='' AND $_POST['address'] !='' AND $_POST['postcode'] !='') 

		if($_POST['email'] !='' AND $_POST['firstname'] !='' AND $_POST['lastname'] !='' AND $_POST['dob'] !='')
		{
			
			$date = date("Y-m-d");

			$return_url = $_POST['return_url']; 
			$email = $_POST['email']; 
			 
			$websiteId  = Mage::app()->getWebsite()->getId();
			$store      = Mage::app()->getStore(); 
			
			$customerid = $this->checkCustomerByEmail($email, $websiteId);
			
			if(!$customerid['customer_id']){
        
				$response['message'] = "Email address $email does not exists";
				$response['success'] = false;

				Mage::log($response, null, 'response_' . $_POST['cmdEvent'] . '_'. $date .'.log');

				Mage::app()->getFrontController()->getResponse()->setRedirect($return_url."?success=".$response['success']); 

			}else{
				
				try
				{
				
					$customer = Mage::getModel('customer/customer');
					$customer->setWebsiteId($websiteId);
					$customer->loadByEmail($email);
					
					$customer->setEmail($email); 
					$customer->setFirstname($_POST['firstname']); 
					$customer->setLastname($_POST['lastname']); 
					//$customer->setGender($_POST['gender']);
					//$customer->setDob($_POST['dob']);
					//$customer->setAgreeOnTerms($_POST['agree_on_terms']);

					if($_POST['gender'] != "0"):
						$customer->setGender($_POST['gender']);
					endif;

					if($_POST['dob'] != "0"):
						$customer->setDob($_POST['dob']);
					endif;

					if($_POST['civil_status'] != ""):
						$customer->setCivil_status($_POST['civil_status']);
					endif;

					if($_POST['password'] != ""):
						$customer->setPassword($_POST['password']);
					endif;

					$customer->save();
					$customerId = $customer->getId();
	                $storeidapi      = Mage::app()->getStore()->getStoreId();
	                $netsuiteApiEnabled   = Mage::getStoreConfig('webservice/netsuiteregsettings/netsuiteregenabled',$storeidapi);
	                if($netsuiteApiEnabled == 1 ){
	                Mage::getModel('webservice/netsuite_postdata_customer')->updateCustomer($customerId,$storeidapi); 
	                }
					
					$cityid = $_POST['cityid'];
					$read = Mage::getSingleton('core/resource')->getConnection('core_read'); 
					$rscity = $read->fetchRow("select name from unilab_cities where city_id = '$cityid'");

					if($_POST['country_id'] !='' AND $_POST['provinceid'] !='' AND $_POST['cityid'] !='' AND $_POST['address'] !='' AND $_POST['postcode'] !='')
					{
						if($customer->getDefaultBilling() == ""){

							$_custom_address = array (
								'firstname' => $_POST['firstname'],
								'lastname'  => $_POST['lastname'],
								'street'    => array (
									'0'     => $_POST['address']
								),
								'city'      => $rscity['name'],
								'postcode'  => $_POST['postcode'],
								'country_id'=> $_POST['country_id'], 
								'region_id' => $_POST['provinceid'], 

								'telephone' => $_POST['landline'],
								'mobile'    => $_POST['mobile'],                

							);

							$customAddress = Mage::getModel("customer/address");
							$customAddress->setData($_custom_address)
									->setCustomerId($customer->getId())
									->setIsDefaultBilling('1')
									->setIsDefaultShipping('1')
									->setSaveInAddressBook('1');
							$customAddress->save();

							$customerId 			= $customer->getId();
					        $addressId 				= $customAddress->getId();
					        $storeidapi      		= Mage::app()->getStore()->getStoreId();
					        $netsuiteApiEnabled   	= Mage::getStoreConfig('webservice/netsuiteregsettings/netsuiteregenabled',$storeidapi);

						        if($netsuiteApiEnabled == 1 ){

						      	  Mage::getModel('webservice/netsuite_postdata_customer')->addAddress($customerId,$addressId,$storeidapi);

						        }

						}else{

							$customerAddress = Mage::getModel('customer/address')->load($customer->getDefaultShipping()); 
							$customerAddress->setfirstname($_POST['firstname'])
											->setmiddlename($_POST['middlename'])
											->setlastname($_POST['lastname'])
											->setcountry_id($_POST['country_id'])
											->setregion_id($_POST['provinceid'])
											->setcity($rscity['name'])
											->setstreet($_POST['address']);
							$customerAddress->save();
							
							$customerId 			= $customer->getId();
	                        $addressId 				= $customerAddress->getId();
	                        $storeidapi      		= Mage::app()->getStore()->getStoreId();
	                        $netsuiteApiEnabled   	= Mage::getStoreConfig('webservice/netsuiteregsettings/netsuiteregenabled',$storeidapi);

		                        if($netsuiteApiEnabled == 1 ){

		                      	  Mage::getModel('webservice/netsuite_postdata_customer')->updateAddress($customerId,$addressId,$storeidapi);

		                        }
	                    }
					}


					$response['message'] = 'Data was successfully update!';
					$response['id']      = $customer->getId(); 
					$response['success'] = 1;  

					Mage::log($response, null, 'response_' . $_POST['cmdEvent'] . '_'. $date .'.log');

					Mage::app()->getFrontController()->getResponse()->setRedirect($return_url);  
				
				}
				catch(Exception $e)
				{            
					$response['message']    = $e->getMessage();
					$response['success']    = 0;    

					Mage::log($response, null, 'response_' . $_POST['cmdEvent'] . '_'. $date .'.log');

					Mage::app()->getFrontController()->getResponse()->setRedirect($return_url."?success=".$response['success']); 
				}
					
			}
			
			
		}else{

			$response['message'] = 'Required fields should not be null!';		
			$response['success'] = 0;

			Mage::log($response, null, 'response_' . $_POST['cmdEvent'] . '_'. $date .'.log');

			Mage::app()->getFrontController()->getResponse()->setRedirect($return_url."?success=".$response['success']); 
		}

        //return $response;    
    }

	public function LoginCustomer($tokendata, $fromReg) 
    { 

		if($_POST['email'] !='' AND $_POST['password'] !='')
		{
			try
			{
				$date = date("Y-m-d");

				$storeid 	= $tokendata['storeid']; 

				$email      = $_POST['email'];
				$password   = $_POST['password'];
				$userid 	= $_POST['user_id'];
				Mage::getSingleton('core/session')->setuserid($userid);

				$websiteUrl     = Mage::getStoreConfig("webservice/sitesettings/websiteurl", $storeid);

				Mage::getSingleton("core/session", array("name" => "frontend"));
				$session = Mage::getSingleton('customer/session');
				$session->login($email, $password);
				$session->setCustomerAsLoggedIn($session->getCustomer());
				
				$response['websiteUrl']     = $websiteUrl;
				$response['message']        = 'Successful login!';
                $response['success']        = 1; 


                Mage::log($response, null, 'response_' . $_POST['cmdEvent'] . '_'. $date .'.log'); 


                if($fromReg == 1):
                	Mage::app()->getFrontController()->getResponse()->setRedirect($websiteUrl."?success=".$response['success']);
				else:
					$websiteUrl = $_POST['returnurl'];
					Mage::app()->getFrontController()->getResponse()->setRedirect($websiteUrl);
				endif;
				//Mage::app()->getFrontController()->getResponse()->setRedirect($websiteUrl."?success=".$response['success']);
			}
			catch(Exception $e)
			{            
				$response['message']    = $e->getMessage();
				$response['success']    = 0;
				$response['login'] 		= 'failed';  
				//$websiteUrl     		= Mage::getStoreConfig("webservice/sitesettings/loginurl", $storeid);
				Mage::log($response, null, 'response_' . $_POST['cmdEvent'] . '_'. $date .'.log');

				// Mage::app()->getFrontController()->getResponse()->setRedirect($websiteUrl);
				Mage::app()->getFrontController()->getResponse()->setRedirect($websiteUrl."?success=".$response['success']."&login=".$response['login']);

			}
			
		}else{

			$response['message'] = 'Required fields should not be null!';		
			$response['success'] = false;
			$websiteUrl     		= Mage::getStoreConfig("webservice/sitesettings/logouturl", $storeid);
			Mage::log($response, null, 'response_' . $_POST['cmdEvent'] . '_'. $date .'.log');

			Mage::app()->getFrontController()->getResponse()->setRedirect($websiteUrl);
		}

	}

	public function LoginCustomerFB($tokendata) 
    { 

		if($_POST['email'] !='')
		{
			try
			{
				$date = date("Y-m-d");

				$storeid 	= $tokendata['storeid']; 

				$email      = $_POST['email'];
				$password   = $_POST['password'];

				$websiteUrl     = Mage::getStoreConfig("webservice/sitesettings/websiteurl", $storeid);

				$websiteId  = Mage::app()->getWebsite()->getId();
			    $customer = Mage::getModel('customer/customer');
			    $customer->setWebsiteId($websiteId);

    			$customer->loadByEmail($email);
    			$session = Mage::getSingleton('customer/session');
		        $session->setCustomerAsLoggedIn($customer);
				
				$response['websiteUrl']     = $websiteUrl;
				$response['message']        = 'Successful login!';
                $response['success']        = true; 

                Mage::log($response, null, 'response_' . $_POST['cmdEvent'] . '_'. $date .'.log'); 

				Mage::app()->getFrontController()->getResponse()->setRedirect($websiteUrl);
			}
			catch(Exception $e)
			{            
				$response['message']    = $e->getMessage();
				$response['success']    = false;  

				Mage::log($response, null, 'response_' . $_POST['cmdEvent'] . '_'. $date .'.log');

				Mage::app()->getFrontController()->getResponse()->setRedirect($websiteUrl."?success=".$response['success']);   

			}
			
		}else{

			$response['message'] = 'Required fields should not be null!';		
			$response['success'] = false;

			Mage::log($response, null, 'response_' . $_POST['cmdEvent'] . '_'. $date .'.log');

			Mage::app()->getFrontController()->getResponse()->setRedirect($websiteUrl."?success=".$response['success']);  

		}

	}


	public function LogoutCustomer($tokendata)
	{
		if($_POST['email'] !='')
		{
		
			try{        
			
				$storeid 	= $tokendata['storeid']; 
				$return_url = $_POST['return_url']; 

				$websiteUrl  = Mage::getStoreConfig("webservice/sitesettings/websiteurl", $storeid);

				Mage::getSingleton('customer/session')->logout();			
			   
				$response['message'] = 'You are now logged out';
				$response['success'] = 1;    
						   
				Mage::app()->getFrontController()->getResponse()->setRedirect($websiteUrl);     

			}catch(Exception $e){           

				$response['message']  = $e->getMessage();   
				$response['success']  = 0;
				Mage::app()->getFrontController()->getResponse()->setRedirect($return_url."?success=".$response['success']);         
				
			}
			
		}else{

			$response['message'] = 'Required fields should not be null!';		
			$response['success'] = 0;

			Mage::app()->getFrontController()->getResponse()->setRedirect($return_url."?success=".$response['success']);   
		}

	}

	public function setforgotpassword()
	{
		if($_POST['email'] !='')
		{
			
			$email      = $_POST['email']; 
			$websiteId  = Mage::app()->getWebsite()->getId();
						
			$customerid = $this->checkCustomerByEmail($email, $websiteId);
			
			if(!$customerid['customer_id']){
        
				$response['message'] = "Email address $email does not exists";
				$response['success'] = false;
			}else{
				
				try {
					
					$customer = Mage::getModel('customer/customer')
								->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
								->loadByEmail($email);
					
					$newResetPasswordLinkToken =  Mage::helper('customer')->generateResetPasswordLinkToken();
					$customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);
					$customer->sendPasswordResetConfirmationEmail();
					
					
					$response['message'] = 'Forgot Password email sent.';
					$response['success'] =  true;  
					
				} catch (Exception $exception) {
                        $response['message']    = $e->getMessage();
						$response['success']    = false;    
                }
			}

			
		}else{

			$response['message'] = 'Required fields should not be null!';		
			$response['success'] = false;
		}
		
		return $response;
	}
	
	public function checkCustomerByEmail($email, $websiteId) 
	{
	    $customer = Mage::getModel("customer/customer");
	    $customer->setWebsiteId($websiteId);
	    $customer->loadByEmail($email);	

	    $customerid 					= $customer->getId(); 
        $db_read                    	= Mage::getSingleton('core/resource')->getConnection('core_read');
        $sqlselect_password      		= "SELECT value FROM customer_entity_varchar WHERE attribute_id=12 AND entity_id='$customerid'";
        $customerentity                	= $db_read->fetchRow($sqlselect_password);
        $db_read->commit();

        $passwordhash 					= $customerentity['value']; 
		
		$customer_info 					= array();
	    $customer_info['customer_id'] 	= $customer->getId();  
	    $customer_info['email'] 		= $customer->getEmail(); 

	    $customer_info['password'] 		= $passwordhash;
	    $customer_info['firstname'] 	= $customer->getFirstname();  
	    $customer_info['lastname'] 		= $customer->getLastname();  
	    $customer_info['gender'] 		= $customer->getGender();  
	    $customer_info['dob'] 			= $customer->getDob();  

	    $customerAddress = Mage::getModel('customer/address')->load($customer->getDefaultBilling());

		$cityname = $customerAddress->getCity();
		$read = Mage::getSingleton('core/resource')->getConnection('core_read'); 
		$rscity = $read->fetchRow("select city_id from unilab_cities where name = '$cityname'"); 

	    $customer_info['mobile'] 		= $customerAddress->getMobile();  
	    $customer_info['landline'] 		= $customerAddress->getTelephone();  
	    $customer_info['country_id'] 	= $customerAddress->getCountryId();  
	    $customer_info['provinceid'] 	= $customerAddress->getRegionId(); 
	    $customer_info['cityid'] 		= $rscity['city_id'];
	    $customer_info['postcode'] 		= $customerAddress->getPostcode(); 
	    $customer_info['address'] 		= $customerAddress->getStreet(1); 
	    
		
	    return $customer_info; 

	}
	
	public function sendemail($templateId, $email, $password, $firstname)
	{ 
		$variables = array(
			'email' 	=> $email,
			'firstname' => $firstname,
			'password' 	=> $password
		);
		
		$storeId 		= Mage::app()->getStore()->getStoreId();
		
		$senderName 	= Mage::getStoreConfig('trans_email/ident_sales/name',$storeId);
		$senderEmail 	= Mage::getStoreConfig('trans_email/ident_sales/email',$storeId);
		
		
		$email_template = Mage::getModel('core/email_template')->load($templateId);
		$email_template->setSenderName($senderName);
		$email_template->setSenderEmail($senderEmail);   

		$email_template->send($email, $firstname, $variables); 
    } 
	
	public function ChangePassword()
	{
		if($_POST['email'] !='' AND $_POST['password'] !='')
		{
			$email = $_POST['email']; 
			 
			$websiteId  = Mage::app()->getWebsite()->getId();
			$store      = Mage::app()->getStore(); 
			
			$customerid = $this->checkCustomerByEmail($email, $websiteId);
			
			if(!$customerid['customer_id']){
        
				$response['message'] = "Email address $email does not exists";
				$response['success'] = false;
			}else{
				
				try
				{
					$customer = Mage::getModel('customer/customer');
					$customer->setWebsiteId(Mage::app()->getWebsite()->getId());
					$customer->loadByEmail($email);
					
					$customer->setPassword($_POST['password']);
					$customer->save();
		
					$response['message'] = 'Data was successfully update!';
					$response['id']      = $customer->getId(); 
					$response['success'] = true; 
				
				}
				catch(Exception $e)
				{            
					$response['message']    = $e->getMessage();
					$response['success']    = false;    
				}
				
			}
			
		}else{
			
			$response['message'] = 'Required fields should not be null!';		
			$response['success'] = false;
		}
		
		return $response;
		
	}

	public function existingCustomer($tokendata)
	{
		$websiteId  = Mage::app()->getWebsite()->getId();
		$storeid 	= $tokendata['storeid'];
		$email 		= $_GET['email'];

		$customerdata = $this->checkCustomerByEmail($email, $websiteId);
		$customerdata['username'] 	= $_GET['username'];
		$customerdata['web_userid'] = $_GET['web_userid'];
		
		$this->addShopperwebsite($customerdata['customer_id'], $storeid);

		return $customerdata;

	}

	public function addShopperwebsite($lastcustomerid, $storeid)
	{
		// $storeid   = Mage::app()->getStore()->getStoreId();
		$categoryid = Mage::getSingleton('core/session')->getCategoryId();
		

		$fields					= array();
		$fields['userid'] 		= $lastcustomerid;
		$fields['storeviewid'] 	= $storeid;
		$fields['categoryid'] 	= $categoryid;

		try{
			$this->_getConnection()->insert('unilab_shopperswebsite', $fields);				
			$guestid = $this->_getConnection()->lastinsertid();
			$this->_getConnection()->commit(); 	

		}catch (Exception $e) {

				Mage::log($e->getMessage(), null, 'addShopperwebsite' . '_'. $date .'.log');

		}

	}

	public function addShopperwebsite2($customerid, $storeid)
	{
		$categoryid = Mage::getSingleton('core/session')->getCategoryId();
		$categoryname =  Mage::getModel('webservice/function_store')->getStorename($categoryid);
		$weburl = Mage::getSingleton('core/session')->getHttpReferer();
		//$categoryname = Mage::getSingleton('core/session')->getStorename();

		$fields					= array();
		$fields['userid'] 		= $customerid;
		$fields['storeviewid'] 	= $storeid;
		$fields['url'] 			= $weburl;
		$fields['categoryname'] = $categoryname;
		$fields['categoryid'] 	= $categoryid;
		// echo $weburl;
		// echo "-".$storeid;
		// echo "-".$categoryname;
		//echo "-".Mage::getSingleton('core/session')->getCategoryId();
		//die();

		Mage::log($fields, null, 'fields.log');
		try{

			$this->_getConnection()->insert('unilab_shopperswebsite', $fields);				
			$guestid = $this->_getConnection()->lastinsertid();
			$this->_getConnection()->commit(); 	

		}catch (Exception $e) {

				Mage::log($e->getMessage(), null, 'addShopperwebsite2' . '_'. $date .'.log');

		}

	}
	
	
} 