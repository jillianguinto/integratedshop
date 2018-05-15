<?php 

class Unilab_Xend_Model_Api_Rate extends Unilab_Xend_Model_Api_Abstract
{ 							   
	const DEFAULT_MINIMUM_WEIGHT = 0.0001;
	const DEFAULT_MINIMUM_DIMENSION = 0.0000;
	//const DEFAULT_PACKAGE_TYPE = Mage::getStoreConfig('carriers/xend/packaging');


	protected $_params = array('ServiceTypeValue'	=> '',
							   'ShipmentTypeValue'	=> '',
							   'DestinationValue'	=> '',
							   'PackagingTypeValue' => '',
							   'Weight'				=> 0.00,
							   'DimensionL'			=> 0.00,
							   'DimensionW'			=> 0.00,
							   'DimensionH'			=> 0.00,
							   'DeclaredValue'		=> 0.00,
							   'AddInsurance'		=> false); 
  
							   
	public function prepareShipments(Varien_Object $request,Varien_Object $config)
	{    
	 	 
		//Get Final dimension (LENGTH, WIDTH AND HEIGHT)
		
		$package_type = Mage::getStoreConfig('carriers/xend/packaging');

		$session = Mage::getSingleton("core/session");
		
		$final_Length	=0.00;
		$final_Height	=0.00;
		$final_Width	=0.00;
		$volMetric 		= 0.00;

		$helper = Mage::helper('checkout/cart');
		$Cartitems = $helper->getCart()->getItems();	
		foreach ($Cartitems as $item) 
			{
				$_prodcut = "";
				$itemProductId = $item->getProductId();
				$_product = Mage::getModel('catalog/product')->loadByAttribute('entity_id', $itemProductId);	

				$final_Height =  $_product['unilab_height'];
				$final_Length = $_product['unilab_length'];
				$final_Width =  $_product['unilab_width'];

				$dimensionval = ($final_Length * $final_Width *  $final_Height);

				$dimensionval = $dimensionval * ($_product['unilab_moq'] * $item->getQty());

				$volMetric = ($dimensionval / 1000000) + $volMetric;

			}	


		// End of Code for dimension		
		
		if($request->getDestCountryId() == self::PH_CODE){
			$destination = Mage::getModel('directory/region')->load($request->getDestRegionId()); 				
		}else{
			$destination = Mage::getModel('directory/country')->loadByCode($request->getDestCountryId());   
		}   
		$this->filterShippingServices($destination->getName(),$config->getDestinations());
		if(count($this->_valid_service_types) < 1){
			return false;
		}  
		
		foreach($this->_valid_service_types as $service_type){	 						
				  
			if ($service_type == 'InternationalEMS')
			{$package_type = 'OwnPackaging';}
			//GET WITHOUT INSURANCE RATE
			$params 					  = $this->_params; 		
			$params['ServiceTypeValue']   = $service_type; 
			$params['ShipmentTypeValue']  = $config->getShipmentType();
			$params['PackagingTypeValue']  = $package_type;
			$params['DestinationValue']   = $destination->getName(); 

			if($package_type = 'OwnPackaging')
			{
				$params['Weight'] 	= $this->getdimconvertocm($volMetric); 

			}else{

				$params['Weight'] 	=  $this->getFinalWeight($this->getConvertedWeight($request->getPackageWeight(),$config->getProductWeightUnit())); 

			}

			$params['DimensionL']   	  = $this->getdimconvertocm(0);
			$params['DimensionW']   	  = $this->getdimconvertocm(0);
			$params['DimensionH']   	  = $this->getdimconvertocm(0);
			$params['DeclaredValue'] 	  = $request->getPackageValue();  
			$exceptionFlag 			 = false;		


			try{	 		
				if(is_null($this->_client)){
					throw new SoapFault('SERVER',Mage::helper('xend')->__('Client not set/failed'));
				}
				
				$result = $this->_client->CalculateByPackaging($params); 	
				
			}catch (SoapFault $soapfault){ 
				$exceptionFlag = true;
				$exception 	   = $soapfault->getMessage();    
				Mage::log($exception, Zend_Log::ERR);
			} 				
	
			if (!$exceptionFlag){
			
			//	$with_insurance_amount 		 = $result->CalculateResult;	
				$with_insurance_amount 		 = $result->CalculateByPackagingResult;	
				$insurance_fee			=  0.00;
				 
				if($request->getPackageValue() > $config->getMaxFreeShippingInsurance()){  
					$insurable_amount     = (($request->getPackageValue() - $config->getMaxFreeShippingInsurance()) * ($config->getInsurancePercentExcess() / 100)); 
					if($insurable_amount>=100){ 
						$insurance_fee   = ceil($insurable_amount/100) * 100;
					}else{  
						 $insurance_fee	 = $insurable_amount;
					} 				 					
				} 
				$this->_rates[$service_type] = array('method_title'  => $this->getShipppingTypeLabel($service_type),
													 'method_rate'   => $with_insurance_amount + $insurance_fee);
													 //'insurance_fee' => sprintf("%.0f",$insurance_fee)); 
			}
		 		 
		}	

		//print_r($params);
		
		return $this;
	}
	
	protected function getFinalWeight($weight = 0)
	{ 
		if($weight < self::DEFAULT_MINIMUM_WEIGHT){
			$final_weight = self::DEFAULT_MINIMUM_WEIGHT;
		}else{
			$final_weight = sprintf("%.4f",$weight);			
		}	
		return $final_weight;
	}
	
	protected function getdimconvertocm($deminsion = 0)
	{ 

		if($deminsion < self::DEFAULT_MINIMUM_DIMENSION){
			$final_dimension = self::DEFAULT_MINIMUM_DIMENSION;
		}else{
			$final_dimension = sprintf("%.4f",$deminsion);			
		}	

		return $final_dimension;
	}
	

}