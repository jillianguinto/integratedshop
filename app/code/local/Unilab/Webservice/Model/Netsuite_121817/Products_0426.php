<?php

class Unilab_Webservice_Model_Netsuite_Products extends Mage_Core_Model_Abstract
{
	protected function _getConnection()
	{
		$this->_Connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		return $this->_Connection;
	}
	
	

	
    public function create()
	{	
		$currentdate = date("Y-m-d H:i:s");   

						
		try{	
			if($_POST['token'] !='' AND  $_POST['cmdEvent'] !='' AND $_POST['websiteid'] !='' AND $_POST['storeid'] != '' AND $_POST['sku'] != '' AND $_POST['name'] != '' AND $_POST['description'] != '' AND $_POST['short_description'] != '' AND $_POST['status'] != '' AND $_POST['visibility'] != '' AND $_POST['sc_discount'] != '' AND $_POST['antibiotic'] != '' AND $_POST['unilab_rx'] != '' AND $_POST['unilab_type'] != '' AND $_POST['unilab_format'] != '' AND $_POST['unilab_benefit'] != '' AND  $_POST['unit_price'] != '' AND $_POST['unilab_moq'] != '' AND $_POST['price'] != '' AND $_POST['tax_class_id'] != '')
			{			
			
					$catIds = explode(',',$_POST['category']);
			
				Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID); 

					 $storeids = array();
					$storeids = explode(',',$_POST['storeid']);
					$websiteid = array();
				
					foreach ($storeids as $key => $value) {
						$q 				= "SELECT website_id FROM  `core_store` where store_id = $value";
						$getwebsiteid 	= $this->_getConnection()->fetchAll($q);
						$websiteid[]		= $getwebsiteid[0]['website_id'];
						
					}
						$websiteIds = implode(",",$websiteid);
					
					$product = Mage::getModel('catalog/product');
					$product->setWebsiteIds($websiteid) 
					->setAttributeSetId(14) //4 default
					->setTypeId('simple')					
					->setSku($_POST['sku'])
					->setName($_POST['name'])
					->setGenericName($_POST['generic_name'])	
					->setDescription($_POST['description']) 
					->setShortDescription($_POST['short_description'])
					->setWeight($_POST['weight'])
					->setStatus($_POST['status'])
					->setVisibility($_POST['visibility'])
					->setRitemedScDiscount($_POST['sc_discount'])
					->setUnilabAntibiotic($_POST['antibiotic'])	
					->setUnilabRx($_POST['unilab_rx'])
					->setUnilabType($_POST['unilab_type'])
					->setUnilabBenefit($_POST['unilab_benefit'])
					->setUnilabSegment($_POST['unilab_segment'])
					->setUnilabDivision($_POST['unilab_division'])		
					->setUnilabGroup($_POST['unilab_group'])
					->setUnilabFormat($_POST['unilab_format'])
					->setUnilabDirections($_POST['unilab_direction'])
					->setUnilabIngredients($_POST['unilab_ingredients'])
					->setUnilabSort($_POST['sort_order'])				
					->setUnilabUnitPrice($_POST['unit_price'])
					->setUnilabMoq($_POST['unilab_moq'])
					->setPrice($_POST['price'])
					->setTaxClassId($_POST['tax_class_id'])
					->setStockData(array(
								'use_config_manage_stock' => 0,
								'manage_stock'=>$_POST['manage_stock'],
								'min_sale_qty'=>1, 
								'is_in_stock' =>$_POST['is_in_stock'], 
								'qty' =>$_POST['qty']
							 ))
					->setCategoryIds($catIds )
					
				
					->setUom($_POST['uom']); 
					
					$product->save();



							
					
					$product_newid        = Mage::getModel('catalog/product')->getIdBySku($_POST['sku']);
					$productNew           = Mage::getModel("catalog/product")->load($product_newid);  
					$prodid		      = $productNew->getId();	

				
				  	  $resource = Mage::getSingleton('core/resource');
      					$writeConnection = $resource->getConnection('core_write');


      					
      					//Create image start
					$path = array();
					$path[]	      = $_POST['image_base'];
					$path[]      = $_POST['image_thumbnail'];
					$path[]	      = $_POST['image_small'];   
								
      					$p=0;
					foreach($path as $key => $value){
					if($path[$p] != ""){
					$query = "INSERT INTO `catalog_product_entity_media_gallery`(`value_id`, `attribute_id`, `entity_id`, `value`) VALUES (null,88,'$prodid','$value')"; 
					 $this->_getConnection()->query($query);

					}
					$p++;
					} 
					//Create image end
					
					//Create Related products start
					//  $linkData = array();
					// $linkData = explode(',',$_POST['related_products']);
					// $relation=Mage_Catalog_Model_Product_Link::LINK_TYPE_RELATED;
					
					
					// 	foreach($linkData as $key => $value){
				 // $query= "INSERT INTO `catalog_product_link`(`link_id`, `product_id`, `linked_product_id`, `link_type_id`) VALUES (null,'$prodid','$value','$relation')"; 
				 // $this->_getConnection()->query($query);
				  
				 // } 
		
      					//Create Related products end

					$response['message'] = "Data was successfully saved.";
					$response['sku']  = $_POST['sku'];
					$response['success']  = true;
					
					
					Mage::log($response['message'], true, 'addproduct_true.log');
					Mage::log($_POST, true, 'add_product_true.log');
				
			}else{
				
				$response['message'] = 'Required fields should not be null!';		
				$response['success'] = false;
			}

		}catch(Exception $e){ 
		   // $response['message'] = "Sku: ". $_POST['sku'] . " Already Exists!";
			$response['message']  = $e->getMessage();
		  	$response['success']  = false;
			Mage::log($e->getMessage() , true, 'add_product_false.log');
		} 
				
		return $response;

	}
	
	
	
	
	
	public function update()
	{		
		$currentdate = date("Y-m-d H:i:s");
		try{
			
			if($_POST['token'] !='' AND  $_POST['cmdEvent'] !='' AND $_POST['sku'] != '' AND $_POST['name'] != '' AND $_POST['description'] != '' AND $_POST['short_description'] != '' AND $_POST['status'] != '' AND $_POST['visibility'] != '' AND $_POST['sc_discount'] != '' AND $_POST['antibiotic'] != '' AND $_POST['unilab_rx'] != '' AND $_POST['unilab_type'] != '' AND $_POST['unilab_format'] != '' AND $_POST['unilab_benefit'] != '' AND  $_POST['unit_price'] != '' AND $_POST['unilab_moq'] != '' AND $_POST['price'] != '' AND $_POST['tax_class_id'] != '')
			{	
			
				$catIds = explode(',',$_POST['category']);
			
				Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);   

				 $storeids = array();
					$storeids = explode(',',$_POST['storeid']);
					$websiteid = array();
				
					foreach ($storeids as $key => $value) {
						$q 				= "SELECT website_id FROM  `core_store` where store_id = $value";
						$getwebsiteid 	= $this->_getConnection()->fetchAll($q);
						$websiteid[]		= $getwebsiteid[0]['website_id'];
						
					}
						$websiteIds = implode(",",$websiteid);  
				$product = Mage::getModel('catalog/product');
				$productsku = $product->getResource()->getIdBySku($_POST['sku']);
				$product->load($productsku);
				$product->setWebsiteIds(array($websiteIds)) 
					->setAttributeSetId(14) //4 default
					->setTypeId('simple')					
					->setSku($_POST['sku'])
					->setName($_POST['name'])
					->setGenericName($_POST['generic_name'])	
					->setDescription($_POST['description']) 
					->setShortDescription($_POST['short_description'])
					->setWeight($_POST['weight'])
					->setStatus($_POST['status'])
					->setVisibility($_POST['visibility'])
					->setRitemedScDiscount($_POST['sc_discount'])
					->setUnilabAntibiotic($_POST['antibiotic'])	
					->setUnilabRx($_POST['unilab_rx'])
					->setUnilabType($_POST['unilab_type'])
					->setUnilabBenefit($_POST['unilab_benefit'])
					->setUnilabSegment($_POST['unilab_segment'])
					->setUnilabDivision($_POST['unilab_division'])		
					->setUnilabGroup($_POST['unilab_group'])
					->setUnilabFormat($_POST['unilab_format'])
					->setUnilabDirections($_POST['unilab_direction'])
					->setUnilabIngredients($_POST['unilab_ingredients'])
					->setUnilabSort($_POST['sort_order'])				
					->setUnilabUnitPrice($_POST['unit_price'])
					->setUnilabMoq($_POST['unilab_moq'])
					->setPrice($_POST['price'])
					->setTaxClassId($_POST['tax_class_id'])
					->setStockData(array(
								'use_config_manage_stock' => 0,
								'manage_stock'=>$_POST['manage_stock'],
								'min_sale_qty'=>1, 
								'is_in_stock' =>$_POST['is_in_stock'], 
								'qty' =>$_POST['qty']
							 ))
					
					->setCategoryIds($catIds )
					->setUom($_POST['uom']); 
					$product->save();
				
					
					$product_newid        = Mage::getModel('catalog/product')->getIdBySku($_POST['sku']);
					$productNew           = Mage::getModel("catalog/product")->load($product_newid);  
					$prodid		      = $productNew->getId();	
								
					  $resource = Mage::getSingleton('core/resource');
      					$writeConnection = $resource->getConnection('core_write');
      					
      					//Update image starts
				$path = array();
				$path[]	      = $_POST['image_base'];
				$path[]     = $_POST['image_thumbnail'];
				$path []     = $_POST['image_small'];
				$imageData  =  $this->_getConnection()->fetchAll("SELECT `value_id`  FROM `catalog_product_entity_media_gallery` WHERE `entity_id` = $prodid "); 
				
					$p=0;
					foreach($imageData as $key => $value){
					$pathid = $value['value_id'];
					if($path[$p] != ""){
					$query= "UPDATE `catalog_product_entity_media_gallery` SET `value_id`= '$pathid' ,`attribute_id`= 88 ,`entity_id`= '$prodid',`value`= '$path[$p]' WHERE `value_id` = '$pathid'"; 
					 $this->_getConnection()->query($query);
					}
					$p++;
					}
					//Update image ends
					
					
      					//Update Related Product start
      				$linkData = array();
				$linkData = explode(',',$_POST['related_products']);
				$relation=Mage_Catalog_Model_Product_Link::LINK_TYPE_RELATED;
				$prodLink  =  $this->_getConnection()->fetchAll("SELECT `link_id` FROM `catalog_product_link` WHERE `product_id` = $prodid "); 
				$i=0;
				foreach($prodLink as $key => $value){	
				$linkid = $value['link_id'];
				$query= "UPDATE `catalog_product_link` SET `link_id`= '$linkid' ,`product_id`= '$prodid' ,`linked_product_id`= '$linkData[$i]' ,`link_type_id`= $relation WHERE `link_id` = $linkid "; 
				$this->_getConnection()->query($query);
				$i++;
			 	 }
				
      					//Update Related Product end
      					
					$response['message'] = "Data was successfully updated.";
					$response['sku']  = $_POST['sku'];
					$response['success']  = true;
					Mage::log($response['message'], true, 'updateproducttrue.log');
					Mage::log($_POST, true, 'update_product_true.log');
			}else{
				

				$response['message'] = 'Required fields should not be null!';		
				$response['success'] = false;
			}

		}catch(Exception $e){ 
		   
		  $response['message'] = $e->getMessage();
		  $response['success']    = false;

		  Mage::log($e->getMessage() , true, 'update_product_false.log');
		} 
					
		return $response;

	} 
	

	public function updateproductprice()
	
	{	
		$currentdate = date("Y-m-d H:i:s");   
		try{
			if($_POST['token'] !='' AND  $_POST['cmdEvent'] !='' AND $_POST['sku'] != '' AND $_POST['unit_price'] != '' AND $_POST['unilab_moq'] != '' AND $_POST['price'] != '')
			{	
				Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);     
				$product = Mage::getModel('catalog/product');
				$productsku = $product->getResource()->getIdBySku($_POST['sku']);
				$product->load($productsku);
				$product->setUnilabUnitPrice($_POST['unit_price'])
					->setUnilabMoq($_POST['unilab_moq'])
					->setPrice($_POST['price']);
					$product->save();
					
					$product_newid        = Mage::getModel('catalog/product')->getIdBySku($_POST['sku']);
					$productNew           = Mage::getModel("catalog/product")->load($product_newid);  
					
					$response['message']  = "Data was successfully updated.";
					$response['sku'] = $_POST['sku'];
					$response['success']  = true;
							 
				Mage::log($response['message'], true, 'update_product_price_true.log');
				Mage::log($_POST, true, 'updateproductprice_true.log');
			}else{
				$response['message'] = 'Required fields should not be null!';		
				$response['success'] = false;
			}

		}catch(Exception $e){ 
		   
		  $response['message'] = $e->getMessage();
		  $response['success']    = false;

		  Mage::log($e->getMessage() , true, 'update_product_price_false.log');
		} 
					
		return $response;

	}
	
	public function updateproductstatus()
	{
		$currentdate = date("Y-m-d H:i:s");    
			
		try{
			if($_POST['token'] !='' AND  $_POST['cmdEvent'] !='' AND $_POST['sku'] != '' AND $_POST['status'] != '')
			{	Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);     
				$product = Mage::getModel('catalog/product');
				$productsku = $product->getResource()->getIdBySku($_POST['sku']);
				$product->load($productsku);
				$product->setStatus($_POST['status']);
				$product->save();
					$product_newid        = Mage::getModel('catalog/product')->getIdBySku($_POST['sku']);
					$productNew           = Mage::getModel("catalog/product")->load($product_newid);      
					$response['message']  = "Data was successfully updated.";
					$response['sku'] = $_POST['sku'];
					$response['success']  = true;
							 
				Mage::log($response['message'], true, 'update_product_status_true.log');
				Mage::log($_POST, true, 'updateproductstatus_true.log');
				
			}else{	$response['message'] = 'Required fields should not be null!';		
				$response['success'] = false;
			     }

		}catch(Exception $e){ 
		  $response['message'] = $e->getMessage();
		  $response['success']    = false;
		  Mage::log($e->getMessage() , true, 'update_product_status_false.log');
		} 			
		return $response;
	}
	
}