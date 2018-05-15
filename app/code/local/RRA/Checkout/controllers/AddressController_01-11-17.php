<?php 

// This page is create by Richel R. Amante for UNILAB use only.
// Please do not used this software and take with your own risk.

// Thank you!!!


class RRA_Checkout_AddressController extends Mage_Core_Controller_Front_Action
{
    public function addressbillAction()
    { 

  		try{
			
			$id 			= $this->getRequest()->getParam('addressID');
			$addressData 	= Mage::getModel('customer/address')->load($id);
			$country 		= Mage::getModel('directory/country')->loadByCode($addressData['country_id']);

			$city 	   = $addressData['city'];
			
			$read      = Mage::getSingleton('core/resource')->getConnection('core_read'); 
            $result    = $read->fetchRow("select * from unilab_cities where name = '$city'"); 
            $city_id   = $result['city_id'];
			
			$response['firstname'] 		= $addressData['firstname'];
			$response['lastname'] 		= $addressData['lastname'];
			$response['middlename'] 	= $addressData['middlename'];
			$response['street'] 		= $addressData['street'];
			$response['region'] 		= $addressData['region'];
			$response['telephone'] 		= $addressData['telephone'];
			$response['region_id'] 		= $addressData['region_id'];
			$response['postcode'] 		= $addressData['postcode'];
			$response['city'] 			= $city_id; //$addressData['city'];
			$response['country_id'] 	= $addressData['country_id'];
			$response['country_name'] 	= $country->getName();
			$response['success'] 		= true;	
			}

		catch (Mage_Core_Exception $e) {
			$response['error'] = $e->getMessage();
			$response['success'] = false;				
		}		

			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response)); 	
		}		

    public function setshippingAction()
    { 
    	$addressid 			= $this->getRequest()->getParam('addressID');

  		try{
		
			$addressData    = Mage::getModel('customer/address')->load($addressid);
            $addressData  	 ->setIsDefaultShipping('1')
                             ->save();

			$response['success'] 		= true;	
		}

		catch (Mage_Core_Exception $e) {
			$response['error'] = $e->getMessage();
			$response['success'] = false;				
		}	

		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response)); 	
    }	

  
}