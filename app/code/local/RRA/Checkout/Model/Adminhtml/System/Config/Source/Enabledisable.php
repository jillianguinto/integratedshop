<?php

	
class RRA_Checkout_Model_Adminhtml_System_Config_Source_Enabledisable{

    public function toOptionArray()
    {
        $options = array(
            array('value' => 0, 'label'=>Mage::helper('rracheckout')->__('No')),
        );
        
        $websites = Mage::helper('rracheckout')->getAvailableWebsites();
        
        if(!empty($websites)){
        	$options[] = array('value' => 1, 'label'=>Mage::helper('rracheckout')->__('Yes'));
        }
        
        return $options;
    }

}