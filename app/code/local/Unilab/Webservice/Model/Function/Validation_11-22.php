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
		
		$customer_info 					= array();
	    $customer_info['customer_id'] 	= $customer->getId();  
	    $customer_info['firstname'] 	= $customer->getFirstname();  
	    $customer_info['lastname'] 		= $customer->getLastname();  
	    $customer_info['contactnumber'] = $customer->getTelephone();  
	    $customer_info['birthday'] 		= $customer->getDob();  
	    $customer_info['gender'] 		= $customer->getGender();  
 
		
	    return $customer_info; 

	}


	
	public function updateAccountdata()
	{

		if($_POST['email'] !='')
		{
			
			$email = $_POST['email']; 
			
		}
	}

	



	
	

	
	 
	
	
	
	
}