<?php 

class RRA_Checkout_SaveaddressController extends Mage_Core_Controller_Front_Action
{

	public function updateBillingAddressAction(){

		
    	if(Mage::getSingleton('customer/session')->isLoggedIn()) {
		    $customer = Mage::getSingleton('customer/session')->getCustomer();
		    $customerId = $customer->getId();
			
		    $data = $customer->getData();

			Mage::log($data, null, 'TANGERS.log');

			$address = Mage::getModel("customer/address")->load($data['default_shipping']);
	        $address->setCustomerId($customerId)
	                ->setShippingId($data['default_shipping'])
	                ->setFirstname($_POST['firstname'])
	                ->setMiddleName($_POST['middlename'])
	                ->setLastname($_POST['lastname'])
	                ->setCountryId($_POST['country_id'])
	                ->setRegionId($_POST['region_id'])
	                ->setPostcode($_POST['postcode'])
	                ->setCity($_POST['city'])
	                ->setTelephone($_POST['telephone'])
	                ->setStreet($_POST['street'])
	                ->setIsDefaultBilling('1')
	                ->setIsDefaultShipping('1')
	                ->setSaveInAddressBook('1');
	        try {

	         	
	            $address->setConfirmation(null);
	            $address->save(); 

	            $response['success'] = true;
	           
	        } catch (Exception $e) {
	        	$response['error'] = $e->getMessage();
				$response['success'] = false;
	        }

		}	


    } 




}