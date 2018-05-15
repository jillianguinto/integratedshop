<?php
  
class Unilab_Customer_Helper_Data extends Mage_Customer_Helper_Data
{  
    public function getCivilStatusOptions($store = null)
    {
        return $this->_prepareCivilStatusOptions(
            Mage::helper('customer/address')->getConfig('civil_status', $store)
        );
    }
	
	protected function _prepareCivilStatusOptions($options)
    {
        $options = trim($options);
        if (empty($options)) {
            return false;
        }
        $result = array();
        $options = explode(';', $options);
        foreach ($options as $value) {
            $value = $this->escapeHtml(trim($value));
            $result[$value] = $value;
        }
        return $result;
    } 
}