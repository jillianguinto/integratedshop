<?php
class Unilab_Xend_Model_Config extends Varien_Object
{
	protected $_type = null;  
	
	const CONFIG_TYPE_RATE 			   = 'RATE';
	const CONFIG_TYPE_SHIPMENT		   = 'SHIPMENT';
	const CONFIG_TYPE_RATE_PATH_PREFIX = 'carriers/xend/';
	
	public function getConfig($store_id = null)
	{
		if(is_null($store_id)){
			$store_id = $this->getStoreId();
		}
		
		$config_settings = new Varien_Object; 
		
		$store_config_ids = array('active',
								  'rate_service_url',
								  'shipment_service_url',
								  'developer_id',
								  'user_token',
								  'title',
								  'name',
								  'shipment_type',
								  'specificerrmsg',
								  'sallowspecific',
								  'specificcountry',
								  'showmethod',
								  'sort_order', 
								  'product_weight_unit',
								  'insurance_percent_excess',
								  'max_free_shipping_insurance',
								  'destinations'
							 );  
		
		foreach($store_config_ids as $config_id){
			$config_settings->setData($config_id,Mage::getStoreConfig(self::CONFIG_TYPE_RATE_PATH_PREFIX. $config_id, $store_id)); 
		}  
		
		if($this->getType() == self::CONFIG_TYPE_RATE){
			$config_settings->setUserToken($config_settings->getDeveloperId());
			$config_settings->setServiceUrl($config_settings->getRateServiceUrl());
		}else{
			$config_settings->setServiceUrl($config_settings->getShipmentServiceUrl());
		}
		
		if($destinations = $config_settings->getDestinations()){
			if(strpos($destinations, ',') === FALSE){
				$config_settings->setDestinations(array($destinations));
			}else{
				$config_settings->setDestinations(explode(",",$destinations));				
			}
		}
		 
		return $config_settings;
	}  
	
	protected function getStoreId()
	{
		return Mage::app()->getStore()->getId();
	} 
	
}


