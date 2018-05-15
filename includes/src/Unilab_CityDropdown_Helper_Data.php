<?php
class Unilab_CityDropdown_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getCities($region='')
    {
    			
		$cities = Mage::getModel('citydropdown/citydropdown')->getCollection()
						->addFieldToFilter("country_id", array("eq" => "PH"))
						->addAttributeToSort("name", "asc")
						->getData();
		
		// TO DO: Include this to cache
		$countryCities = array();			
		if(count($cities)>0){			
			foreach($cities as $city){
				$countryCities[] = array("region_id" => $city["region_id"],
										 "name" => str_replace("'","", $city["name"]),
										 "country" =>  $city["country_id"]
										);			
			}		
		}		
		
		/*
        $cities = array(
            array("region_id" => 532, "name" => $helper->__('Makati'),"country" => "PH"),
            array("region_id" => 532, "name"  => $helper->__('Taguig'),"country" => "PH"),
            array("region_id" => 532, "name"  => $helper->__('Mandaluyong'),"country" => "PH"),
            array("region_id" => 532, "name"  => $helper->__('Ortigas'),"country" => "PH"),
            array("region_id" => 485, "name"  => $helper->__('Tubo'),"country" => "PH"),
            array("region_id" => 485, "name"  => $helper->__('Tineg'),"country" => "PH"),
            array("region_id" => 486, "name"  => $helper->__('Buenavista'),"country" => "PH"),
            array("region_id" => 486, "name"  => $helper->__('Butuan City'),"country" => "PH"),
        );
		*/
		
        return $countryCities;
    }
 
    public function getCitiesAsDropdown($region='', $selectedCity = '')
    {
        $cities = $this->getCitiesByRegion($region);
        
        $options = '';
        
        foreach($cities as $city){
            $isSelected = $selectedCity == $city ? ' selected="selected"' : null;
            $options .= '<option value="' . $city . '"' . $isSelected . '>' . $city . '</option>';
        }
		
        return $options;
    }
}
?>