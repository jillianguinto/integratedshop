<?php 

class Unilab_Webservice_Model_Postdata_Sendresetpassword extends Mage_Core_Model_Abstract    
{

public function sendnewpassword($customerId, $password)
	{
		$websiteId 	 = 1; //Mage::app()->getWebsite()->getId();
		
		$customer = Mage::getModel("customer/customer");
		$customer->setWebsiteId($websiteId);
		$customer->load($customerId);

		$postdata 				= array();
		$postdata['cmdEvent'] 	= 'UpdatePassword';
		$postdata['email'] 		= $customer->getEmail();
		$postdata['password']	= $password;
		
		
		$websettings = $this->webservicesettings();
					
		$gateway 			= $websettings['gateway'];	
		$postdata['token']	= $websettings['token'];
		
		
		$adapter = new Zend_Http_Client_Adapter_Curl();
		$adapter->setCurlOption(CURLOPT_REFERER, $websettings['sitehost']);
		
		$client = new Zend_Http_Client($gateway); 
		$client->setAdapter($adapter);
			
		$client->setParameterPost('token', $postdata['token']); 
		$client->setParameterPost('cmdEvent', $postdata['cmdEvent']); 
		$client->setParameterPost('email', $postdata['email']); 	
		$client->setParameterPost('password', $postdata['password']); 				
					
		$website_response = $client->request(Zend_Http_Client::POST); 
		
		$website_data 	 = json_decode($website_response->getbody());
		
		
		Mage::log($postdata, null, 'wbs_forgotpassword-postdata.log');	
		Mage::log($AHCData, null, 'wbs_forgotpassword_response.log');		
		
		
	}

	public function webservicesettings()
	{
		$settings 				= array();
		$settings['gateway'] = Mage::getStoreConfig('webservice/sitesettings/sitegateway'); 
		$settings['token']		= Mage::getStoreConfig('webservice/websettings/ahctoken');	
		$settings['sitehost']	= 'http://' . $_SERVER['HTTP_HOST'];	
		
		return $settings;
		
	}
}