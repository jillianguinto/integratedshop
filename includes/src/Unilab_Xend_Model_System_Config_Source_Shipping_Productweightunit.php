<?php
class Unilab_Xend_Model_System_Config_Source_Shipping_Productweightunit
{
	const WEIGHT_UNIT_KILOGRAM  = 'KL';
	const WEIGHT_UNIT_POUND     = 'LBS';
	const WEIGHT_UNIT_GRAM 		= 'G';
	const WEIGHT_UNIT_MILLIGRAM = 'MG'; 
	
    public function toOptionArray() {
        $options = array( 
            array('value' => self::WEIGHT_UNIT_KILOGRAM, 	'label' => Mage::helper('xend')->__('Kilogram (KG)')),
            array('value' => self::WEIGHT_UNIT_POUND, 		'label' => Mage::helper('xend')->__('Pound (LBS)')),
            array('value' => self::WEIGHT_UNIT_GRAM, 		'label' => Mage::helper('xend')->__('Gram (G)')),
            array('value' => self::WEIGHT_UNIT_MILLIGRAM, 	'label' => Mage::helper('xend')->__('Milligram (MG)')), 
        );
        return $options;
    } 
}