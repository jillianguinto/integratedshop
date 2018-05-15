<?php

class Unilab_Webservice_Model_Netsuite_Postdata_Cronjobs extends Mage_Core_Model_Abstract
{
	protected function _getConnection()
    {
		$this->_Connection = Mage::getSingleton('core/resource')->getConnection('core_write');	 
		
        return $this->_Connection;
    }	
	
	public function sendCustomer()
	{	
		$StoreId = Mage::app()
				->getWebsite(true)
				->getDefaultGroup()
				->getDefaultStoreId();
		
		$query 	= 'SELECT * FROM  customer_entity WHERE senttoNS = 0 and website_id=15 order by entity_id LIMIT 100'; 
		$result = $this->_getConnection()->fetchAll($query); 

		foreach ($result as $value) 
		{

			$customerid = $value['entity_id'];
			
			$queryCountAddress 	= "SELECT count(*) as count FROM customer_address_entity WHERE parent_id = $customerid";
			$countAddress = $this->_getConnection()->fetchRow($queryCountAddress);

			if($countAddress['count'] > 0)
			{
				$response = Mage::getModel('webservice/netsuite_postdata_customer')->sendCustomer($customerid,$StoreId);

			}elseif ($countAddress['count']== 0) {
			 
				$response =  Mage::getModel('webservice/netsuite_postdata_customer')->sendCustomerWithoutAddress($customerid,$StoreId);
			}
			Mage::log($value['email'], null, 'cron_customers_'.date("Y-m-d").'.log'); 
			print_r($response); 

		}

	}
	
	public function updateCustomer()
	{
		$StoreId = Mage::app()
				->getWebsite(true)
				->getDefaultGroup()
				->getDefaultStoreId();
				
		$query 	= 'SELECT entity_id FROM  customer_entity WHERE senttoNS = 1 and update_senttoNS = 0 and store_id in (1,10,13,20,22) order by entity_id LIMIT 100'; 
		$result = $this->_getConnection()->fetchAll($query); 

		foreach ($result as $value) 
		{
			$customerid = $value['entity_id'];
			
			Mage::getModel('webservice/netsuite_postdata_customer')->updateCustomer($customerid,$StoreId);

		}	
 
	}
	

	public function addAddress()
	{
		$StoreId = Mage::app()
				->getWebsite(true)
				->getDefaultGroup()
				->getDefaultStoreId();
				
		$query 	= "select a.*, store_id  from customer_address_entity a
					left join customer_entity b on a.parent_id = b.entity_id
					where b.senttoNS = 1 and a.senttoNS = 0 and
					store_id in (1,10,13,20,22)
					order by a.entity_id
					limit 100"; 
		$result['data'] = $this->_getConnection()->fetchAll($query); 	

		foreach ($result as $value) 
		{
			$data = Mage::getModel('customer/address')->load($value['entity_id']); 
			$customerId = $value['parent_id'];
			$addressId  = $value['entity_id'];

			Mage::getModel('webservice/netsuite_postdata_customer')->addAddress($customerId,$addressId,$StoreId);

		}

	}
 
	public function updateAddress()
	{
		$StoreId = Mage::app()
				->getWebsite(true)
				->getDefaultGroup()
				->getDefaultStoreId();
				
		$query 	= "select a.*, store_id  from customer_address_entity a
					left join customer_entity b on a.parent_id = b.entity_id
					where b.senttoNS = 1 and a.senttoNS = 1 and a.senttoNS and a.update_senttoNS = 0
					store_id in (1,10,13,20,22)
					order by entity_id
					limit 100"; 
		$result['data'] = $this->_getConnection()->fetchAll($query); 
 
		foreach ($result as $key => $value) 
		{  
			$data = Mage::getModel('customer/address')->load($value['entity_id']); 
			$customerId = $value['parent_id'];
			$addressId  = $value['entity_id'];

			Mage::getModel('webservice/netsuite_postdata_customer')->updateAddress($customerId,$addressId,$StoreId);
		}
	}

	public function sendOrder()
	{	
		$StoreId = Mage::app()
				->getWebsite(true)
				->getDefaultGroup()
				->getDefaultStoreId();
				
		$query 	= 'SELECT entity_id FROM sales_flat_order WHERE senttoNS = 0 and store_id in (1,10,13,20,22) order by entity_id limit 100'; 
		$result = $this->_getConnection()->fetchAll($query); 
		
		foreach ($result as $value) 
		{
			Mage::getModel('webservice/netsuite_postdata_order')->createOrder($value['entity_id'],$storeid);
		}

	}
		 
	
	public function cancelOrder()
	{	
		$StoreId = Mage::app()
				->getWebsite(true)
				->getDefaultGroup()
				->getDefaultStoreId();
				
		$query 	= 'SELECT entity_id FROM  sales_flat_order WHERE state "canceled" AND senttoNS = 1 and store_id in (1,10,13,20,22) order by entity_id LIMIT 100'; 
		$result = $this->_getConnection()->fetchAll($query); 
		
		foreach ($result as $value) 
		{
  
			Mage::getModel('webservice/netsuite_postdata_order')->cancelOrder($value['entity_id'],$StoreId);
	
		}

	}

	public function sendProduct()
		{	
				$settings 				= array();
				$settings['gateway'] 	= Mage::getStoreConfig('webservice/netsuiteregsettings/netsuitereggateway', 1);
				$settings['token']		= Mage::getStoreConfig('webservice/netsuiteregsettings/netsuiteregtoken',1 );
				$settings['sitehost']	= Mage::getStoreConfig('webservice/netsuiteregsettings/netsuiteurl', 1);
				$arr 					= explode('/', Mage:: getBaseUrl());
				$settings['hostname']	= $arr[2];
				
				$query = "Select e.entity_id,
							'22' as `StoreID`,
							e.sku as `SKU`,
							at_name.value as `ProductName`,
							at_gname.value as `GenericName`,
							REPLACE(REPLACE(at_description.value, '\r\n', ' '), '\n', ' ') as `Description`,
							REPLACE(REPLACE(at_short_description.value, '\r\n', ' '), '\n', ' ') as `ShortDescription`,
							at_weight.value as `Weigth`,
							(CASE when at_status.value = 1 THEN 1 ELSE 0 END )as `Status`,
							at_visibility.value as `visibility`,
							'' as `sc_discount`,
							'' as `antibiotic`,
							at_rx.value as `unilab_rx`,
							at_type.value as `unilab_type`,
							at_format.value as `unilab_format`,
							at_benefits.value as `unilab_benefit`,
							at_segment.value as `unilab_segment`,
							at_division.value as `unilab_division`,
							at_group.value as `unilab_group`,
							at_brand.value as `unilab_brand`,
							at_size.value as `unilab_size`,
							'' as `unilab_direction`,
							'' as `unilab_ingredients`,
							at_sort.value as `sort_order`,
							at_unitprice.value as `UnitPrice`,
							at_moq.value as `MOQ`,
							at_price.value as `Price`,
							at_tax_class_id.value as `tax_class_id`,
							at_bimage.value as `BaseImage`,
							at_timage.value as `ThumbnailImage`,
							at_simage.value as `SmallImage`,
							at_qty.manage_stock as `manage_stock`,
							at_qty.qty as `qty`,
							at_qty.is_in_stock as `is_in_stock`,
							(SELECT GROUP_CONCAT(category_id SEPARATOR ', ') FROM catalog_category_product where product_id=e.entity_id) as `StoreCategoryID`,
							'' as `UOM`
							FROM `catalog_product_entity` as e
							LEFT JOIN catalog_product_entity_varchar AS at_name ON (at_name.entity_id = e.entity_id) AND (at_name.attribute_id = 71)
							LEFT JOIN catalog_product_entity_varchar AS at_gname ON (at_gname.entity_id = e.entity_id) AND (at_gname.attribute_id = 174)
							LEFT JOIN catalog_product_entity_text AS at_description ON (at_description.entity_id = e.entity_id) AND (at_description.attribute_id = 72)
							LEFT JOIN catalog_product_entity_text AS at_short_description ON (at_short_description.entity_id = e.entity_id) AND (at_short_description.attribute_id = 73)  
							LEFT JOIN catalog_product_entity_decimal AS at_weight ON (at_weight.entity_id = e.entity_id) AND (at_weight.attribute_id = 80)
							LEFT JOIN catalog_product_entity_int AS at_status ON (at_status.entity_id = e.entity_id) AND (at_status.attribute_id = 96)
							LEFT JOIN catalog_product_entity_int    AS at_visibility ON (at_visibility.entity_id = e.entity_id) AND (at_visibility.attribute_id = 102)
							LEFT JOIN catalog_product_entity_varchar AS at_rx ON (at_rx.entity_id = e.entity_id) AND (at_rx.attribute_id = 167)
							LEFT JOIN catalog_product_entity_int     AS at_type ON (at_type.entity_id = e.entity_id) AND (at_type.attribute_id = 169)
							LEFT JOIN catalog_product_entity_int     AS at_format ON (at_format.entity_id = e.entity_id) AND (at_format.attribute_id = 170)
							LEFT JOIN catalog_product_entity_varchar AS at_benefits ON (at_benefits.entity_id = e.entity_id) AND (at_benefits.attribute_id = 171)
							LEFT JOIN catalog_product_entity_varchar AS at_segment ON (at_segment.entity_id = e.entity_id) AND (at_segment.attribute_id = 168)
							LEFT JOIN catalog_product_entity_varchar AS at_division ON (at_division.entity_id = e.entity_id) AND (at_division.attribute_id = 199)
							LEFT JOIN catalog_product_entity_varchar AS at_group ON (at_group.entity_id = e.entity_id) AND (at_group.attribute_id = 200)
							LEFT JOIN catalog_product_entity_varchar AS at_brand ON (at_brand.entity_id = e.entity_id) AND (at_brand.attribute_id = 217)
							LEFT JOIN catalog_product_entity_varchar AS at_size ON (at_size.entity_id = e.entity_id) AND (at_size.attribute_id = 166)
							LEFT JOIN catalog_product_entity_varchar AS at_sort ON (at_sort.entity_id = e.entity_id) AND (at_sort.attribute_id = 211)
							LEFT JOIN catalog_product_entity_decimal AS at_unitprice ON (at_unitprice.entity_id = e.entity_id) AND (at_unitprice.attribute_id = 184)
							LEFT JOIN catalog_product_entity_varchar AS at_moq ON (at_moq.entity_id = e.entity_id) AND (at_moq.attribute_id = 181)
							LEFT JOIN catalog_product_entity_decimal AS at_price ON (at_price.entity_id = e.entity_id) AND (at_price.attribute_id = 75)
							LEFT JOIN catalog_product_entity_int AS at_tax_class_id ON (at_tax_class_id.entity_id = e.entity_id) AND (at_tax_class_id.attribute_id = 122)
							LEFT JOIN catalog_product_entity_varchar AS at_bimage ON (at_bimage.entity_id = e.entity_id) AND (at_bimage.attribute_id = 85)
							LEFT JOIN catalog_product_entity_varchar AS at_simage ON (at_simage.entity_id = e.entity_id) AND (at_simage.attribute_id = 86)
							LEFT JOIN catalog_product_entity_varchar AS at_timage ON (at_timage.entity_id = e.entity_id) AND (at_timage.attribute_id = 87) 
							LEFT JOIN cataloginventory_stock_item    AS at_qty ON (at_qty.product_id=e.entity_id)
							where (select group_concat(b.store_id separator ',') from catalog_product_website a
							left join core_store b on a.website_id = b.website_id
							where product_id=e.entity_id) is not null and sku is not null and sku<>'20170305BG' 
							group by e.entity_id 
							order by e.entity_id
							limit 800,200";
										
				$result = $this->_getConnection()->fetchAll($query); 
				
				foreach ($result as $value) 
				{

					$productData['storeIds']			= $value['StoreID']; 
					$productData['name']				= $value['ProductName']; 
					$productData['sku']					= $value['SKU'];
					$productData['genericName']			= $value['GenericName'];
					$productData['Description']			= $value['Description'];
					$productData['ShortDescription']	= $value['ShortDescription'];
					$productData['weight']				= $value['Weight'];
					$productData['status']				= $value['Status'];
					$productData['visibility']			= $value['visibility'];
					$productData['sc_discount']			= $value['sc_discount'];
					$productData['antibiotic']			= $value['antibiotic'];
					$productData['unilab_rx']			= $value['unilab_rx'];
					$productData['unilab_type']			= $value['unilab_type'];
					$productData['unilab_format']		= $value['unilab_format'];
					$productData['unilab_benefit']		= $value['unilab_benefit'];
					$productData['unilab_segment']		= $value['unilab_segment'];
					$productData['unilab_division']		= $value['unilab_division'];	
					$productData['unilab_group']		= $value['unilab_group'];
					$productData['unilab_direction']	= $value['unilab_direction'];
					$productData['unilab_ingredient'] 	= $value['unilab_ingredients'];
					$productData['unilab_brand']		= $value['unilab_brand'];
					$productData['unilab_size']			= $value['unilab_size'];
					$productData['unilab_sort'	]		= $value['sort_order'];			
					$productData['unit_price']			= $value['UnitPrice'];
					$productData['unilab_moq']			= $value['MOQ'];
					$productData['price']				= $value['Price'];
					$productData['tax_class_id']		= $value['tax_class_id'];
					$productData['category_ids'] 		= str_replace(' ', '', $value['StoreCategoryID']);
					$productData['uom']					= $value['UOM'];
					$productData['image_base']			= Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) ."catalog/product/".	$value['BaseImage'];
					$productData['image_thumbnail']		= Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) ."catalog/product/".	$value['ThumbnailImage'];
					$productData['image_small']			= Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) ."catalog/product/".	$value['SmallImage'];
					
					$productData['manage_stock']		= $value['manage_stock'];
					$productData['is_in_stock']			= $value['is_in_stock'];
					$productData['qty']					= $value['qty'];


					$productId 							= $value['entity_id'];
					$product 							= Mage::getModel('catalog/product');
					$product ->load($productId);
					
					$imageslist['imageslist']			= $product->getMediaGallery();
					$images 							= $imageslist['imageslist']['images'];
			
					foreach ($images as $key => $value1) {
						$imagefile = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) ."catalog/product/".$value1['file'];


						if ($productData['image_base'] != $imagefile and $productData['image_thumbnail'] != $imagefile and $productData['image_small'] != $imagefile) {
							$productData['images'] = $imagefile;
						}
					}
					
					$gateway = "https://dbm.unilab.com.ph/webservice/apinetsuite/"; 
					$cmdevent 	="CreateProductCron";
					$listImage	= implode(",", $productData['images'] );
				
					$adapter = new Zend_Http_Client_Adapter_Curl();
					$adapter->setCurlOption(CURLOPT_REFERER, $settings['sitehost']);
					$client = new Zend_Http_Client($gateway); 
					$client->setAdapter($adapter);

					$client->setParameterPost('sitehost', $settings['sitehost']); 
					$client->setParameterPost('cmdEvent',$cmdevent ); 
					$client->setParameterPost('token',$settings['token']); 
					$client->setParameterPost('storeid',$productData['storeIds']); 
					$client->setParameterPost('sku', $productData['sku']); 
					$client->setParameterPost('name', $productData['name']); 
					$client->setParameterPost('generic_name', $productData['genericName']); 
					$client->setParameterPost('description', $productData['Description']); 
					$client->setParameterPost('short_description', $productData['ShortDescription']); 
					$client->setParameterPost('weight', $productData['weight']); 
					$client->setParameterPost('status', $productData['status']); 
					$client->setParameterPost('visibility', $productData['visibility']); 
					$client->setParameterPost('sc_discount', $productData['sc_discount']); 
					$client->setParameterPost('antibiotic', $productData['antibiotic']); 
					$client->setParameterPost('unilab_rx', $productData['unilab_rx']); 
					$client->setParameterPost('unilab_type', $productData['unilab_type']);
					$client->setParameterPost('unilab_format', $productData['unilab_format']);
					$client->setParameterPost('unilab_benefit', $productData['unilab_benefit']);
					$client->setParameterPost('unilab_segment', $productData['unilab_segment']);
					$client->setParameterPost('unilab_division', $productData['unilab_division']); 
					$client->setParameterPost('unilab_group', $productData['unilab_group']);
					$client->setParameterPost('unilab_direction', $productData['unilab_direction']);
					$client->setParameterPost('unilab_ingredients', $productData['unilab_ingredients']); 

					$client->setParameterPost('unilab_size', $productData['unilab_size']); 
			 		$client->setParameterPost('unilab_brand', $productData['unilab_brand']);

			 		$client->setParameterPost('sort_order', $productData['sort_order']);
					$client->setParameterPost('unit_price', $productData['unit_price']);
					$client->setParameterPost('unilab_moq', $productData['unilab_moq']);

			 		$client->setParameterPost('price', $productData['price']);
					$client->setParameterPost('tax_class_id', $productData['tax_class_id']);
					$client->setParameterPost('image_base', $productData['image_base']); 
			 		$client->setParameterPost('image_thumbnail', $productData['image_thumbnail']);
					$client->setParameterPost('image_small', $productData['image_small']);
					$client->setParameterPost('images', $listImage); 

					$client->setParameterPost('manage_stock', $productData['manage_stock']);
					$client->setParameterPost('qty', $productData['qty']);
					$client->setParameterPost('is_in_stock', $productData['is_in_stock']); 

					$client->setParameterPost('category', $productData['category_ids'] );
					$client->setParameterPost('related_products', $productData['related_products']);
					$client->setParameterPost('uom', $productData['uom']); 
					
					$storeresponse 		= $client->request(Zend_Http_Client::POST); 
					$StoreData 			= json_decode($storeresponse->getbody());
					$successResponse 	= $StoreData->success;
					
					if ($successResponse == 1) {

						$response['success'] = $productData['sku'] . '---success';

					}else {
						$response['fail'] = $productData['sku'] . '--- fail';
					} 

					echo "<pre>";
					print_r($response);
					

				}

				
				

		}	

		 

}