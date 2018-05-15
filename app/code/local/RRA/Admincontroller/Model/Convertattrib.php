<?php
 // Paul Daniel Afan
//  06-28-2017
class RRA_Admincontroller_Model_Convertattrib extends Mage_Core_Model_Abstract {

	
    public function idtotext($productid, $attribute_data, $attrib_raw)
    {	
	  $storeid    = Mage::app()->getStore()->getStoreId();
	  $data_IDS = explode(',', $attribute_data);
	  $attrib_array = array();

	  foreach($data_IDS as $d_IDS)
	  {
			$attribute_option_id = Mage::getResourceModel('catalog/product')->getAttributeRawValue($productid, $attrib_raw, $storeid);
		  $product = Mage::getModel('catalog/product')
			  ->setStoreId($storeid)
			  ->setData($attrib_raw, $d_IDS);

		  $attrib_totext = $product->getAttributeText($attrib_raw);
		  $attrib_array[] = $attrib_totext;
		  $unilab_attrib_text = implode('|', $attrib_array);
	  }

	  return $unilab_attrib_text;

    }

}