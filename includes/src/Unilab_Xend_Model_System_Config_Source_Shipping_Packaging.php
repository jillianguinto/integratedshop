<?php
class Unilab_Xend_Model_System_Config_Source_Shipping_Packaging
{
	const PACKAGING_LARGEPOUCH	= 'LARGEPOUCH';
	const PACKAGING_XLPOUCH 	= 'XLPOUCH';
	const PACKAGING_CUSTOMPOUCH	= 'CUSTOMPOUCH';
	
    public function toOptionArray(){
	
        $options = array( 
            array('value' => self::PACKAGING_LARGEPOUCH, 	'label' => 'Large Pouch 9 x 14 inches (UNLI WEIGHT)'),
            array('value' => self::PACKAGING_XLPOUCH, 		'label' => 'XL Pouch 12 x 18 inches (UNLI WEIGHT)'),
            array('value' => self::PACKAGING_CUSTOMPOUCH, 	'label' => 'Own Packaging (Does not fit Xend UNLIPAK)'), 
        );

        return $options;
    } 
}