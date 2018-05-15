<?php
class Unilab_Xend_Model_System_Config_Source_Shipping_Purposeofexporttype
{
    public function toOptionArray() {
        $options = array( 
            array('value' => 'Gift', 			 'label' => Mage::helper('xend')->__('Gift')),
            array('value' => 'Personal', 		 'label' => Mage::helper('xend')->__('Personal')),
            array('value' => 'Documents', 		 'label' => Mage::helper('xend')->__('Documents')),
            array('value' => 'CommercialSample', 'label' => Mage::helper('xend')->__('Commercial Sample')),
            array('value' => 'Other', 			 'label' => Mage::helper('xend')->__('Other')),
            array('value' => 'None', 			 'label' => Mage::helper('xend')->__('None'))
        );

        return $options;
    } 
}