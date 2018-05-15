<?php

class Unilab_Webservice_Model_Netsuite_Products extends Mage_Core_Model_Abstract
{
	protected function _getConnection()
	{
		$this->_Connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		return $this->_Connection;
	}

	public function getProduct()
	{
// 		$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$_POST['sku']);
		
    	$product = Mage::getModel('catalog/product');
        $id = Mage::getModel('catalog/product')->getResource()->getIdBySku($_POST['sku']);
        if ($id) {
            $product->load($id);
        }
        		
		return print_r($product);
		
	}
   
    public function create()
	{	
		$currentdate = date("Y-m-d H:i:s");   



		if (!is_dir('media/catalog/product/webservice/')) 
		{
     		mkdir('media/catalog/product/webservice', 0777, true); 	
		}
				
		try{	
			if($_POST['token'] !='' AND  $_POST['cmdEvent'] !='' AND $_POST['websiteid'] !='' AND $_POST['storeid'] != '' AND $_POST['sku'] != '' AND $_POST['name'] != '' AND $_POST['status'] != '' AND $_POST['visibility'] != '' 	AND  $_POST['unit_price'] != '' AND $_POST['unilab_moq'] != '' AND $_POST['price'] != '' AND $_POST['tax_class_id'] != '')
			{			
			
				Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID); 

				$catIds 		= explode(',',$_POST['category']);
				$imageslist 	= explode(',',$_POST['images']);
				$storeids 		= array();
				$storeids 		= explode(',',$_POST['storeid']);
				$websiteid 		= array();
				
					foreach ($storeids as $key => $value) {
						$q 				= "SELECT website_id FROM  core_store where store_id = $value";
						$getwebsiteid 	= $this->_getConnection()->fetchAll($q);
						$websiteid[]	= $getwebsiteid[0]['website_id'];
						
					}

				$websiteIds 	= implode(",",$websiteid);
				$product 		= Mage::getModel('catalog/product');
				$name			= sha1($_POST['name'])."_".$_POST['sku'];
				$imagename		= str_replace(" ","_",$name);

					if ($_POST['image_base'] != "") 
					{
						$path['image_base']	     	= $_POST['image_base'];

					}	
						if ($_POST['image_thumbnail'] != "") 
					{
						$path['image_thumbnail']	     	= $_POST['image_thumbnail'];

					}	
						if ($_POST['image_small'] != "") 
					{
						$path['image_small']	     	= $_POST['image_small'];

					}	
						
			
					 	
				$p=0;

				foreach($path as $key => $value){

						$completeSaveLoc 	= $_SERVER['DOCUMENT_ROOT'].'/media/catalog/product/webservice/'.$imagename.$p. '.jpg';
						$SaveLoc 			= "/webservice/".$imagename.$p. ".jpg";
						file_put_contents($completeSaveLoc, file_get_contents($value));
				
						if ($key == 'image_small'){

							$imagelocsmall = $completeSaveLoc;

						}
						if ($key == 'image_thumbnail'){
							$imagelocthumbnail = $completeSaveLoc;
							
						}
						if ($key == 'image_base'){
							$imagelocbase = $completeSaveLoc;
							
						}
					
						$p++;
				} 

				$product->setWebsiteIds($websiteIds) 
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
					->setUnilabBrand($_POST['unilab_brand'])
					->setUnilabSize($_POST['unilab_size'])
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
					->setCategoryIds($catIds)
					->setUom($_POST['uom']); 

					if ($_POST['image_base'] != "") {
						 $product->addImageToMediaGallery($imagelocbase, 'image', false,false);

					}	
					if ($_POST['image_thumbnail'] != "") {
						 $product->addImageToMediaGallery($imagelocthumbnail,'thumbnail', false,false);

					}	
					if ($_POST['image_small'] != "") {
				
 						$product->addImageToMediaGallery($imagelocsmall,'small_image', false,false);
					}		
			
				$i = $p;
				if($_POST['images'] != ""){

				foreach($imageslist as $key => $value){
						if ($value){
							$completeSaveLoc 	= $_SERVER['DOCUMENT_ROOT'].'/media/catalog/product/webservice/'.$imagename.$p. '.jpg';
							$SaveLoc 			= "/webservice/".$imagename.$i. ".jpg";//'/c/h/'.$imagename.$p.'.png';
							
							file_put_contents($completeSaveLoc, file_get_contents($value));
							$product->addImageToMediaGallery($completeSaveLoc, '', false,false);
						}
					}
						$i++;
				} 

				



				$product->save();
				
				$product_newid    = Mage::getModel('catalog/product')->getIdBySku($_POST['sku']);
				$productNew       = Mage::getModel("catalog/product")->load($product_newid);  
				$prodid		      = $productNew->getId();	

				

				// //Create image end
				
				// //Create Related products start
				// $linkData = array();
				// $linkData = explode(',',$_POST['related_products']);
				// $relation=Mage_Catalog_Model_Product_Link::LINK_TYPE_RELATED;
			
				// foreach($linkData as $key => $value)
				// {
				// 	$query= "INSERT INTO catalog_product_link (product_id, linked_product_id, link_type_id) VALUES ('$prodid','$value','$relation')"; 
				// 	$this->_getConnection()->query($query);
				// } 
	
				// //Create Related products end
				$response['message'] 	= "Data was successfully saved.";
				$response['sku']  		= $_POST['sku'];
				$response['success']  	= true;
				
				
				Mage::log($response['message'], true, 'addproduct_true.log');
				Mage::log($_POST, true, 'add_product_true.log');
				
			}else{
				
				$response['message'] = 'Required fields should not be null!';		
				$response['success'] = false;
			}

		}catch(Exception $e){  
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
			
				
			
				Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);   
				$catIds 		= explode(',',$_POST['category']);
				$imageslist 	= explode(',',$_POST['images']);
				$storeids 		= array();
				$storeids 		= explode(',',$_POST['storeid']);
				$websiteid 		= array();
				
				foreach ($storeids as $key => $value) {
					$q 				= "SELECT website_id FROM  `core_store` where store_id = $value";
					$getwebsiteid 	= $this->_getConnection()->fetchAll($q);
					$websiteid[]	= $getwebsiteid[0]['website_id'];
					
				}
			
				$websiteIds = implode(",",$websiteid);  
				$product 	= Mage::getModel('catalog/product');
				$productsku = $product->getResource()->getIdBySku($_POST['sku']);

			if($productsku){
				
				$name			= sha1($_POST['name'])."_".$_POST['sku'];
				$imagename		= str_replace(" ","_",$name);

				if ($_POST['image_base'] != "") {
					$path['image_base']	     	= $_POST['image_base'];

				}	
					if ($_POST['image_thumbnail'] != "") {
					$path['image_thumbnail']	= $_POST['image_thumbnail'];

				}	
					if ($_POST['image_small'] != "") {
					$path['image_small']	   	= $_POST['image_small'];

				}	
						
			
				$p=0;
				foreach($path as $key => $value){

						$completeSaveLoc 	= $_SERVER['DOCUMENT_ROOT'].'/media/catalog/product/webservice/'.$imagename.$p. '.jpg';
						$SaveLoc= "/webservice/".$imagename.$p. ".jpg";//'/c/h/'.$imagename.$p.'.png';
						file_put_contents($completeSaveLoc, file_get_contents($value));
				
							if ($key == 'image_small')
							{

								$imagelocsmall = $completeSaveLoc;

							}
							if ($key == 'image_thumbnail')
							{
								$imagelocthumbnail = $completeSaveLoc;
								
							}
							if ($key == 'image_base')
							{
								$imagelocbase = $completeSaveLoc;
								
							}
					
						$p++;
				} 


				$product->load($productsku);
                /**
                 * BEGIN REMOVE EXISTING MEDIA GALLERY
                 */
                $attributes = $product->getTypeInstance ()->getSetAttributes ();
                if (isset ( $attributes ['media_gallery'] )) 
                {
                    $gallery 		= $attributes ['media_gallery'];
                    //Get the images
                    $galleryData 	= $product->getMediaGallery ();

                    foreach ( $galleryData ['images'] as $image ) {
                        //If image exists
                        if ($gallery->getBackend ()->getImage ( $product, $image ['file'] )) {
                            $gallery->getBackend ()->removeImage ( $product, $image ['file'] );
                        }
                    }

                    $product->save ();
                }
          			
                /**
                 * END REMOVE EXISTING MEDIA GALLERY
                 */


				
				
			$product->setWebsiteIds($websiteIds) 
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
					->setUnilabBrand($_POST['unilab_brand'])
					->setUnilabSize($_POST['unilab_size'])
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
					->setCategoryIds($catIds)
					->setUom($_POST['uom']); 
					if ($_POST['image_base'] != "") {
						 $product->addImageToMediaGallery($imagelocbase, 'image', false,false);

					}	
					if ($_POST['image_thumbnail'] != "") {
						 $product->addImageToMediaGallery($imagelocthumbnail, 'thumbnail', false,false);

					}	
					if ($_POST['image_small'] != "") {
				
 						$product->addImageToMediaGallery($imagelocsmall, 'small_image', false,false);
					}		
			
						$i=$p;
				foreach($imageslist as $key => $value){

						$completeSaveLoc 	= $_SERVER['DOCUMENT_ROOT'].'/media/catalog/product/webservice/'.$imagename.$p. '.jpg';
						$SaveLoc 			= "/webservice/".$imagename.$i. ".jpg"; 
						file_put_contents($completeSaveLoc, file_get_contents($value));
				
						$product->addImageToMediaGallery($completeSaveLoc, '', false,false);
					
						$i++;
				} 

				$product->save();
				
				$product_newid    = Mage::getModel('catalog/product')->getIdBySku($_POST['sku']);
				$productNew       = Mage::getModel("catalog/product")->load($product_newid);  
				$prodid		      = $productNew->getId();	

  
      			
				$response['message'] 	= "Data was successfully updated.";
				$response['sku']  	 	= $_POST['sku'];
				$response['success']  	= true;
				
				Mage::log($response['message'], true, 'updateproducttrue.log');
				Mage::log($_POST, true, 'update_product_true.log');

				}else{
						$response['message']  = "Product does not exists!.";
						$response['sku'] 	  = $_POST['sku'];
						$response['success']  = true;
									 
						Mage::log($response['message'], true, 'update_product_false.log');
						Mage::log($_POST, true, 'updateproduct_false.log');
				}
			}else{
				

				$response['message'] = 'Required fields should not be null!';		
				$response['success'] = false;
			}

		}catch(Exception $e){ 
		   
		  $response['message']    = $e->getMessage();
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
				if($productsku){

					$product 	->load($productsku);
					$product 	->setUnilabUnitPrice($_POST['unit_price'])
								->setUnilabMoq($_POST['unilab_moq'])
								->setPrice($_POST['price']);
					$product->save();
						
					$product_newid        = Mage::getModel('catalog/product')->getIdBySku($_POST['sku']);
					$productNew           = Mage::getModel("catalog/product")->load($product_newid);  
					
					$response['message']  = "Data was successfully updated.";
					$response['sku'] 	  = $_POST['sku'];
					$response['success']  = true;
								 
					Mage::log($response['message'], true, 'update_product_price_true.log');
					Mage::log($_POST, true, 'updateproductprice_true.log');

				}else{
						$response['message']  = "Product does not exists!.";
						$response['sku'] 	  = $_POST['sku'];
						$response['success']  = true;
									 
						Mage::log($response['message'], true, 'update_product_price_false.log');
						Mage::log($_POST, true, 'updateproductprice_false.log');
				}

			}else{
				$response['message'] = 'Required fields should not be null!';		
				$response['success'] = false;
			}

		}catch(Exception $e){ 
		   
		  $response['message'] 	  = $e->getMessage();
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
			{	
				Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);     
				
				$product = Mage::getModel('catalog/product');
				$productsku = $product->getResource()->getIdBySku($_POST['sku']);

				if($productsku){
					
				$product->load($productsku);
				$product->setStatus($_POST['status']);
				$product->save();
				
				$product_newid        = Mage::getModel('catalog/product')->getIdBySku($_POST['sku']);
				$productNew           = Mage::getModel("catalog/product")->load($product_newid);     
				
				$response['message']  = "Data was successfully updated.";
				$response['sku'] 	  = $_POST['sku'];
				$response['success']  = true;
							 
				Mage::log($response['message'], true, 'update_product_status_true.log');
				Mage::log($_POST, true, 'updateproductstatus_true.log');
			
				}else{
						$response['message']  = "Product does not exists!.";
						$response['sku'] = $_POST['sku'];
						$response['success']  = true;
									 
						Mage::log($response['message'], true, 'update_product_status_false.log');
						Mage::log($_POST, true, 'updateproductstatus_false.log');
				}
				
				
			}else{	
				
				$response['message'] = 'Required fields should not be null!';		
				$response['success'] = false;
			}

		}catch(Exception $e){ 
			$response['message'] 	= $e->getMessage();
			$response['success']    = false;
			Mage::log($e->getMessage() , true, 'update_product_status_false.log');
		} 			
		
		return $response;
	}
	
}