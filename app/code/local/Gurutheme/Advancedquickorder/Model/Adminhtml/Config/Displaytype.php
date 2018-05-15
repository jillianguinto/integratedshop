<?php

class Gurutheme_Advancedquickorder_Model_Adminhtml_Config_Displaytype
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label'=>Mage::helper('adminhtml')->__('Handle')),
            array('value' => 0, 'label'=>Mage::helper('adminhtml')->__('Best Seller')),
        );
    }

    

}
