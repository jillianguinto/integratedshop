<?php 

class Unilab_Webservice_Model_Function_Migration extends Mage_Core_Model_Abstract    
{
	protected function _getConnection()
    {
		$this->_Connection = Mage::getSingleton('core/resource')->getConnection('core_write');	
        return $this->_Connection;
    }
	
	public function AddCustomer($tokendata) 
    {
		$date = date("Y-m-d");
			
		//$storeid 	= 12; //heymom; 
		
		$storeid	= $_POST['storeid']; 
		$web_userid = $_POST['web_userid']; 

		$email 		= $_POST['email']; 
		
		$websiteId  = Mage::app()->getWebsite()->getId();
		$store	    = Mage::app()->getStore(); 
		
		$customerid = $this->checkCustomerByEmail($email, $websiteId);
		
		
		if($customerid)
		{
			$response['message'] = "Email address $email already exist.";
			$response['success'] = "existing";
			
			return $response;
			
		}else{
			
			try{
				
				$customer   = Mage::getModel("customer/customer"); 
				$customer   ->setWebsiteId($websiteId)
							->setStore($store)
							->setEmail($email)
							->setPassword($_POST['password'])
							->setFirstname($_POST['firstname'])
							->setLastname($_POST['lastname']) 
							->setGender($_POST['gender'])
							->setCivil_status($_POST['civil_status'])
							->setDob($_POST['dob'])
							->setCreatedAt($_POST['created_at']);
				$customer->save();
				$lastcustomerid = $customer->getId();
				
				$this->addShopperwebsite($lastcustomerid, $storeid);
				
				if($_POST['country_id'] !='' AND $_POST['provinceid'] !='' AND $_POST['cityid'] !='' AND $_POST['address'] !='' AND $_POST['postcode'] !='')
				{
					$this->AddCustomerAddress($_POST);
				}
				
				$response['message'] = 'Data was successfully saved!';
				$response['success'] = 1; 

				
			}catch(Exception $e){            
				$response['message']    = $e->getMessage();
				$response['success']    = 0; 
      
			}
			
			return $response;
		}
	}
	
	public function AddCustomerAddress($post)
    {
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
	}
	
	public function checkCustomerByEmail($email, $websiteId) 
	{
	    $customer = Mage::getModel("customer/customer");
	    $customer->setWebsiteId($websiteId);
	    $customer->loadByEmail($email);	

	    $customerid 	= $customer->getId(); 
       
		
	    return $customerid; 

	}
	
	public function addShopperwebsite($lastcustomerid, $storeid)
	{
		$fields					= array();
		$fields['userid'] 		= $lastcustomerid;
		$fields['storeviewid'] 	= $storeid;

		try{
			$this->_getConnection()->insert('unilab_shopperswebsite', $fields);				
			$guestid = $this->_getConnection()->lastinsertid();
			$this->_getConnection()->commit(); 	

		}catch (Exception $e) {

				Mage::log($e->getMessage(), null, 'addShopperwebsite' . '_'. $date .'.log');

		}

	}
}