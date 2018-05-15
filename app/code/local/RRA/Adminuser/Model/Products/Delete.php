<?php

class RRA_Adminuser_Model_Products_Delete extends Mage_Core_Model_Abstract
{
	
	public function delete()
	{
		
		$product_id = Mage::getModel('catalog/product')->getIdBySku($_POST['sku']);
		Mage::register("isSecureArea", 1);
		
		if(!$product_id){
			Mage::throwException(Mage::helper('core')->__("SKU no exist!"));
		}
		
		try{
			Mage::getModel("catalog/product")->load($product_id)->delete();
			
			$response['success'] 	= true;
			$response['Errhandler'] = "Product was successfully deleted!";
			
		}catch(Exception $e){
			
			$response['success'] 	= false;
			$response['Errhandler'] = $e->getMessage();			
		}
			
			
		return $response;

	}

}