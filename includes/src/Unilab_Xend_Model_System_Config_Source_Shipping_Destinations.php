<?php
class Unilab_Xend_Model_System_Config_Source_Shipping_Destinations
{ 
	
    public function toOptionArray() {
        $options = array( 
            array('value' => Unilab_Xend_Model_Api_Abstract::SERVICE_TYPE_METROMANILAEXPRESS, 		'label' => Mage::helper("xend")->__("Metro Manila Express")),
            array('value' => Unilab_Xend_Model_Api_Abstract::SERVICE_TYPE_PROVINCIALEXPRESS, 		'label' => Mage::helper('xend')->__('Provincial Express')),
            array('value' => Unilab_Xend_Model_Api_Abstract::SERVICE_TYPE_RIZALMANILAEXPRESS, 		'label' => Mage::helper('xend')->__('RIZAL-MANILA Express')),
            array('value' => Unilab_Xend_Model_Api_Abstract::SERVICE_TYPE_INTLPOSTAL, 				'label' => Mage::helper('xend')->__('International Postal')), 
            array('value' => Unilab_Xend_Model_Api_Abstract::SERVICE_TYPE_INTLEXPRESS, 				'label' => Mage::helper('xend')->__('International Express')), 
            array('value' => Unilab_Xend_Model_Api_Abstract::SERVICE_TYPE_INTLEMS,				 	'label' => Mage::helper('xend')->__('International EMS')), 
        );
        return $options;
    } 
}