<?php

class Mage_Payment_Model_System_Config_Source_Cities
{
    protected $_options;

    public function toOptionArray(){
				
		$regionlist = Mage::getStoreConfig('payment/cashondelivery/specificregion', $this->getCurrentStoreId());	
				
		if($regionlist)	{
			
			$countRegion = 0;
			
			foreach(explode(",",$regionlist) as $_regionId){
				
				$Sqlselect = $this->_getConnection()->select()
						->from('unilab_cities', array('city_id','name', 'region_code')) 
						->where('region_id=?',$_regionId)
						->order('name ASC');   
						
				$citieiresult = $this->_getConnection()->fetchAll($Sqlselect);	
				
				$RegionName = $this->_getRegionById($_regionId);
				$options[$countRegion]['value'] = $citieiresult[0]['region_code'];
				$options[$countRegion]['label'] = '<>-- For '. $RegionName . ' Please Select Below --<>';	
							
				foreach($citieiresult  as $_key=>$_dataval){

					$countRegion++;				
					$options[$countRegion]['value'] = $_dataval['name'];
					$options[$countRegion]['label'] = $_dataval['name'];


				}				
				
			}

			
		}else{
			
			
			$options[$countRegion]['value'] = 0;
			$options[$countRegion]['label'] = '-- Please Set Region in Default Config --';	
			$options = null;
			
		}
	
			

        return $options;
    }
	
	protected function _getConnection()
    {
		$this->_Connection = Mage::getSingleton('core/resource')->getConnection('core_write');	
		
        return $this->_Connection;
    }	
	
	protected function _getRegionById($region_id=null)
	{
		
		$countryCode = Mage::getStoreConfig('general/country/default');	
		
		if(empty($countryCode)){
			$countryCode = "PH";
		}
		
		$regionList =  Mage::getModel('directory/region_api')->items($countryCode);
	
		foreach($regionList  as $_key=>$_dataval){			
			if($region_id == $_dataval['region_id']){
				$regionName = $_dataval['name'];				
			}
		}
		
		return $regionName;
	}
	
	
	protected function getCurrentStoreId(){
		
		if (strlen($code = Mage::getSingleton('adminhtml/config_data')->getStore())){
			$store_id = Mage::getModel('core/store')->load($code)->getId();
		}elseif (strlen($code = Mage::getSingleton('adminhtml/config_data')->getWebsite())){
			$website_id = Mage::getModel('core/website')->load($code)->getId();
			$store_id = Mage::app()->getWebsite($website_id)->getDefaultStore()->getId();
		}else{
			$store_id = 0;
		}	
		
		return $store_id;
		
	}
	
	
}
