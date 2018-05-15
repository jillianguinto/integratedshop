<?php

class Mage_Payment_Model_System_Config_Source_Region
{
    protected $_options;

    public function toOptionArray($isMultiselect=false)
    {
						
		$countryCode = Mage::getStoreConfig('general/country/default');		
		if(empty($countryCode)){
			$countryCode = "PH";
		}
		
        if (!$this->_options) {
			$this->_options = Mage::getModel('directory/region_api')->items($countryCode);
        }
		
	
		foreach($this->_options  as $_key=>$_dataval){
			
			$options[$_key]['value'] = $_dataval['region_id'];
			$options[$_key]['label'] = $_dataval['name'];		

		}		

        return $options;
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
