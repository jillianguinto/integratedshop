<?php 
class Unilab_Xend_Model_Api_Waybill extends Unilab_Xend_Model_Api_Abstract
{      
	protected $_params = array('ServiceTypeValue' 		=> '',
								'ShipmentTypeValue' 	=> '',
								'Weight' 				=> 0.00,
								'DimensionL' 			=> 0.00,
								'DimensionW' 			=> 0.00,
								'DimensionH' 			=> 0.00,
								'DeclaredValue' 		=> 0.00,
								'RecipientName' 		=> '',
								'RecipientCompanyName'  => '',
								'RecipientAddress1' 	=> '',
								'RecipientAddress2' 	=> '',
								'RecipientCity' 		=> '',
								'RecipientProvince' 	=> '',
								'RecipientCountry'  	=> '',
								'PurposeOfExportValue' 	=> 'None',
								'DateCreated'			=> null,
								'DatePrinted'			=> null,
								'IsInsured' 			=> true,
								'InsuranceFee' 			=> 0.00,
								'SpecialInstructions' 	=> '',
								'Description' 			=> '',
								'ShippingFee' 			=> 0.00,
								'ClientReference' 		=> ''); 
	
    protected $_waybill_number = null;
	
	public function createShipment(Mage_Sales_Model_Order $order,Varien_Object $config)
	{     
		if($order->getShippingAddress()->getCountryId() == self::PH_CODE){
			$destination = Mage::getModel('directory/region')->load($order->getShippingAddress()->getRegionId());   		
				
		}else{
			$destination = Mage::getModel('directory/country')->loadByCode($order->getShippingAddress()->getCountryId());   
		}   
		$this->filterShippingServices($destination->getName(),$config->getDestinations());
		
		if(count($this->_valid_service_types) < 1){
			return false;
		}  
		
		
		foreach($this->_valid_service_types as $service_type){	 
			
			$contacts = "";
			
			if($telephone = $order->getShippingAddress()->getTelephone()){
				$contacts = $telephone;
			}
			if($mobile = $order->getShippingAddress()->getMobile()){
				$contacts .= "/ ". $mobile;
			}
			$params 					    = $this->_params; 		
			$params['ServiceTypeValue']     = $service_type; 
			$params['ShipmentTypeValue']    = $config->getShipmentType(); 
			$params['Weight'] 			    = $this->getConvertedWeight($order->getWeight(),$config->getProductWeightUnit()); 
			$params['DeclaredValue'] 	    = $order->getGrandTotal() - $order->getShippingAmount(); 
			$params['RecipientName'] 		= $order->getShippingAddress()->getName();
			$params['RecipientCompanyName'] = $order->getShippingAddress()->getCompany();
			$params['RecipientAddress1']    = $order->getShippingAddress()->getStreet1(); 
			$params['RecipientAddress2']    = $order->getShippingAddress()->getStreet2(); 
			$params['RecipientCity']    	= $order->getShippingAddress()->getCity();
			$params['RecipientProvince']    = $order->getShippingAddress()->getRegion();
			$params['RecipientCountry']    	= $order->getShippingAddress()->getCountryModel()->getName();
			$params['RecipientEmailAddress']= $order->getShippingAddress()->getEmail(); 
			$params['RecipientPhoneNo']     = $contacts ;
			$params['DateCreated']          = date("Y-m-d", Mage::getModel('core/date')->timestamp(time()));
			$params['DatePrinted']          = date("Y-m-d", Mage::getModel('core/date')->timestamp(time()));
			 
			$exceptionFlag 				    = false;		
			
			try{		
				$result = $this->_client->Create(array('shipment' => $params)); 	
			}catch (SoapFault $soapfault){ 
				$exceptionFlag = true;
				$exception 	   = $soapfault->getMessage(); 
				throw new Exception($exception);
			}  
			  
			if (!$exceptionFlag){
				$this->_waybill_number = $result->CreateResult; 
			} 	
			
			break;
		}   
		
		return $this;
	}
	
	public function getWaybillNumber()
	{ 
		return $this->_waybill_number;
	}
}