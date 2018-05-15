<?php

class RRA_Adminuser_Model_Products_Update extends Mage_Core_Model_Abstract
{
	
	public function update()
	{
	
		$DbFilename 			= null;			
		$_POST['countrycode'] 	= Mage::getStoreConfig('general/country/default');
		
		if($_POST['unilab_rx'] == true){
			$_POST['istype'] 	= 10;
		}else{
			$_POST['istype'] 	= 11;			
		}
		
		
		$fullpath 	= $_FILES['ImgFilename']['tmp_name'];
		$filename 	= $_FILES['ImgFilename']['name'];
		//$size 		= $_FILES['ImgFilename']['size'];
		//$ext 		= pathinfo($filename, PATHINFO_EXTENSION);		
					
					
		if($fullpath){
			$DbFilename = Mage::helper('adminuser')->createproductimage($fullpath, $filename);
		}
			
		
		$product_id  = Mage::getModel('catalog/product')->getIdBySku($_POST['sku']);

		if(!$product_id){
			Mage::throwException(Mage::helper('core')->__("SKU Not Exist!"));			
		}
					
		$_POST['countrycode'] 	= Mage::getStoreConfig('general/country/default');
		
		Mage::setIsDeveloperMode(true);
		Mage::app()->setCurrentStore(Mage::getModel('core/store')->load(Mage_Core_Model_App::ADMIN_STORE_ID));

		$product = Mage::getModel("catalog/product")->load($product_id);

			$product
				->setWebsiteIds($_POST['websiteids'])
				->setAttributeSetId($_POST['attributeid'])
				->setTypeId('simple')
				->setCreatedAt(strtotime('now')) 
				->setSku($_POST['sku']) 
				->setName($_POST['name']) 
				->setWeight($_POST['weight'])
				->setStatus($_POST['status'])
				->setTaxClassId($_POST['tax_class_id'])
				->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
				->setNewsFromDate(strtotime('now'))
				->setNewsToDate(strtotime('now'))
				->setgeneric_name($_POST['generic_name'])
				->setunilab_unit_price($_POST['unilab_unit_price'])
				->setunilab_moq($_POST['unilab_moq'])
				->setunilab_rx($_POST['unilab_rx'])
				->setunilab_type($_POST['istype'])
				->setunilab_benefit($_POST['unilab_benefit'])
				->setCountryOfManufacture($_POST['countrycode']) 
				->setPrice($_POST['unilab_moq'] * $_POST['unit_price']) 
				->setCost($_POST['unilab_moq'] * $_POST['unit_price'])

				->setDescription($_POST['description'])
				->setShortDescription($_POST['short_description'])
				//->setMediaGallery (array('images'=>array (), 'values'=>array ()))
				//->addImageToMediaGallery('media/catalog/product/'.$DbFilename, array('image','thumbnail','small_image'), false, false)	
				->setStockData(array(
								   'use_config_manage_stock' => 0,
								   'manage_stock'=>0,
								   'min_sale_qty'=>1, 
								   'is_in_stock' => 1, 
								   'qty' => $_POST['qty']
								   ))->setCategoryIds($_POST['categoryIDs']);
								   
		try { 
		
			$product->save(); 		
		
			$response['success'] 	= true;
			$response['msgHndlr'] 	= "Product was successfully updated.";
				
			
		}catch(Exception $e) { 
		
			$response['success'] 	= false;
			$response['Errhandler'] = $e->getMessage();
			
		} 								   
			
		return $response;	
		
	}
	
	

}