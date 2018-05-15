<?php 

class Unilab_Webservice_Model_Function_Validation extends Mage_Core_Model_Abstract    
{

	
	public function CheckEmail()
    {
		
		if($_POST['email'] !='')
		{
			
			$email = $_POST['email']; 
			 
			$websiteId  = Mage::app()->getWebsite()->getId();
			$store	    = Mage::app()->getStore(); 
			
			$customerdata = $this->checkCustomerByEmail($email, $websiteId);
			
			if($customerdata['customer_id'])
			{

				try
				{
					$response['message'] 		= 'Email address '.$email.' has an existing record';
					$response['success'] 		= true;
					$response['customerdata'] 	= $customerdata; 
				}
				
				catch(Exception $e)
				{            
					$response['message']    = $e->getMessage();
					$response['success']    = false;           
				}

			}else{
				
				$response['message'] = 'Email address '.$email.' does not exist';
				$response['success'] = false;

				
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
        $passwordhash 					= $customerentity['value']; 

		$customer_info 					= array();
	    $customer_info['customer_id'] 	= $customer->getId(); 
	    $customer_info['email'] 		= $email;
	    $customer_info['password'] 		= $passwordhash;  
	    $customer_info['firstname'] 	= $customer->getFirstname();  
	    $customer_info['lastname'] 		= $customer->getLastname();  
	    $customer_info['contactnumber'] = $customer->getTelephone();  
	    $customer_info['birthday'] 		= $customer->getDob();  
	    $customer_info['gender'] 		= $customer->getGender();  
 
		
	    return $customer_info; 

	}


	
	public function saveAccountdata()
	{

		if($_POST['customer_id'] !='')
		{
			
			$customerid = $_POST['customer_id'];
			$shopperwebsiteid =  12; //static websiteid for Heymom

			try{

				$insertquery 	= "INSERT INTO unilab_shopperswebsite (userid, storeviewid) VALUES ('$customerid','$shopperwebsiteid')";
				$this->_getConnection()->Query($insertquery);

				$response['message'] = 'Account successfully saved';		
				$response['success'] = true;

			}
				
			catch(Exception $e)
			{            
				$response['message']    = $e->getMessage();
				$response['success']    = false;           
			}
			
		}
		else{
			$response['message'] = 'Required fields should not be null!';		
			$response['success'] = false;
		}

		return $response;   
	}

	


	public function _getConnection()
	{
		$this->_Connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		return $this->_Connection;
	}
	
	

	
	 
	
	
	
	
}