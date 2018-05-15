<?php

class RRA_Adminuser_Model_Products_Show extends Mage_Core_Model_Abstract
{
	
	 public function show(){
		 
		$productDetails = Mage::getModel('catalog/product');
		$response 		= $productDetails->load($_POST['id'])->getData(); 		
			
		return $response;

	}
	
}