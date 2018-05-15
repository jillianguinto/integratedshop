<?php 

class Unilab_Xend_Model_Shipping_Carrier_Xend
    extends Mage_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface {

    protected $_code 	      = 'xend'; 
	 
	public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {    
        if (!$this->getConfigFlag('active')) {
            return false;
        }  
		
		if(!$rate_service = $this->_prepareShippingRequirements($request)){
			return false;
		}  
		$shipping_methods  = $rate_service->getRates(); 
		
		$result 		= Mage::getModel('shipping/rate_result'); 
		 
		foreach($shipping_methods as $method_name=>$shipping_method){
			$shipping_price = $this->getShippingPrice();			
			$shipping_rate_services = Mage::getModel('xend/api_rate');
			$method = Mage::getModel('shipping/rate_result_method');
			$method->setCarrier($this->_code);
			$method->setCarrierTitle($this->getConfigData('title'));
			$method->setMethod($method_name);
			$method->setMethodTitle($shipping_method['method_title']); 
			$method->setPrice($shipping_method['method_rate']);
			$method->setCost($shipping_method['method_rate']);
			$method->setInsuranceFee(isset($shipping_method['insurance_fee']) ? $shipping_method['insurance_fee'] : 0);
			$result->append($method);  
		} 
        return $result;
    }
	
    public function getAllowedMethods()
    {
        return array($this->_code =>$this->getConfigData('name'));
    }
	
	protected function _prepareShippingRequirements(Varien_Object $request)
	{		
		try{ 
			$config 	  = $this->getStoreConfig($request->getStoreId()); 				   
			$rate_service = $this->getApiRequest()
								 ->connect($config)
								 ->prepareShipments($request,$config);								 
			return $rate_service;
			
		}catch(Exception $e){		 
			Mage::log($e->getMessage()); 
			return false;
		}  
	} 
	
	protected function getApiRequest()
	{
		return Mage::getSingleton("xend/api_rate");
	}
		
	protected function getStoreConfig($store_id = 0) 
	{	
		$rate_config = Mage::getModel('xend/config')->setType(Unilab_Xend_Model_Config::CONFIG_TYPE_RATE)
													->getConfig($store_id);
		return $rate_config;	
	}
	
	public function getCarrierCode()
	{
		return $this->_code;
	}
	
}