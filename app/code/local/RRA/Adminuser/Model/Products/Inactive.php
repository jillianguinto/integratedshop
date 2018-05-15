<?php

class RRA_Aonewebservice_Model_Products_Inactive extends Mage_Core_Model_Abstract
{
	
	
	public function Inactive()
	{
	
		$product_id = Mage::getModel('catalog/product')->getIdBySku($_POST['sku']);

		if($product_id){
			
			Mage::setIsDeveloperMode(true);
			Mage::app()->setCurrentStore(Mage::getModel('core/store')->load(Mage_Core_Model_App::ADMIN_STORE_ID));

			$product = Mage::getModel("catalog/product")->load($product_id);

			$product->setStatus($_POST['status']);

			try { 
			
				if($product->getStatus() == 1){
					$response['Status'] = "Enabled";
				}else{
					$response['Status'] = "Disabled";					
				}
				
				$product->save(); 
				$response['SKU'] 			= $_POST['sku'];
				$response['ProductName'] 	= $product->getName();
				$response['success'] 		= true;
				$response['msgHndlr'] 		= "Product was successfully ". $response['Status']. "!";
				
			}catch(Exception $e) { 
			
				$response['success'] 	= false;
				$response['Errhandler'] = $e->getMessage();
				
			} 
							
		}else{
			
			$response['success'] 	= false;
			$response['Errhandler'] = "SKU Not Exist!";			
		}

		return $response;		
		
	}

}