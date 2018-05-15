<?php
class Unilab_Xend_Model_System_Config_Source_Shipping_Shipmenttype{

    public function toOptionArray() {
        $options = array( 
            array('value' => '', 'label' => ''),
            array('value' => 'Document', 'label' => Mage::helper('xend')->__('Document')),
            array('value' => 'Parcel', 'label' => Mage::helper('xend')->__('Parcel'))
        );

        return $options;
    }
	
}