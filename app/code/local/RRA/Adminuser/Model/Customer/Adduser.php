<?php
 
class RRA_Adminuser_Model_Customer_Adduser extends Varien_Object {

	
	protected function _getConnection()
    {
		$this->_Connection = Mage::getSingleton('core/resource')->getConnection('core_write');	
		
        return $this->_Connection;
    }	
		
	
	public function create()
	{
		
		$dataHnlder = $this->getData();
		
		try {

			
			//$store 		= Mage::app()->getStore();
			
			$customer 	= Mage::getModel("customer/customer");
			
			$customer->setWebsiteId($_POST['websiteId'])
			
						->setPrefix($_POST['prefix'])		
						
						->setFirstname($_POST['firstname'])	
						
						->setLastname($_POST['lastname'])	
						
						->setmiddlename($_POST['middlename'])		
						
						->setEmail($_POST['email'])			
						
						->setcontact_number($_POST['contact_number'])		
						
						->setdob(date("Y-m-d", strtotime($_POST['dob'])))	
						
						->setcivil_status($_POST['civil_status'])	
						
						->setgender($_POST['gender'])			
						
						->setagree_on_terms($_POST['agree_on_terms'])	
						
						->setgroup_id($_POST['group_id'])
						
						->setPassword($_POST['password']);

			$customer->save();		
			
			
			$_custom_address = array (
			
				'firstname' => $_POST['firstname'],
				
				'lastname' => $_POST['lastname'],
				
				'street' => array (
					'0' => $_POST['address_street'],
					'1' => '',
				),
			 
				'city' => $_POST['address_city'],
				
				'region_id' => $_POST['address_region_id'],
				
				'region' => $_POST['address_region_id'],
				
				'postcode' => $_POST['address_postcode'],
				
				
				'country_id' => $_POST['country_id'],
				
				'telephone' => $_POST['address_telephone'],
			);			
						
			
		$customAddress = Mage::getModel('customer/address');
		
		
		$customAddress->setData($_custom_address)
		
					->setCustomerId($customer->getId())
					
					->setIsDefaultBilling($_POST['password'])
					
					->setIsDefaultShipping($_POST['password'])
					
					->setSaveInAddressBook('1');
					
			$customAddress->save();		 

							
			$response['success']	= true;
				
			$response['ErrHndlr']	= "Account was successfully save";	
									
			
		}catch(Exception $e){
			
			$response['success']	= false;
				
			$response['ErrHndlr']	= $e->getMessage();	
			
		}
	
				
		return $response;
				
					
	}
	
	

			

	
}