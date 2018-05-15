<?php

class RRA_Checkout_Model_System_Config_Source_Yesnocheck
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 2, 'label'=>Mage::helper('rracheckout')->__('Yes and isChecked')),
            array('value' => 1, 'label'=>Mage::helper('rracheckout')->__('Yes')),
            array('value' => 0, 'label'=>Mage::helper('rracheckout')->__('No')),
        );
    }


    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            0 => Mage::helper('rracheckout')->__('No'),
            1 => Mage::helper('rracheckout')->__('Yes'),
        );
    }

}
