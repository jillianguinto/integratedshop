<?php

class Gurutheme_Advancedquickorder_Model_Adminhtml_Config_Quickdisplay
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(            
            array('value' => 1, 'label'=>Mage::helper('adminhtml')->__('Quick Order Popup')),
            array('value' => 2, 'label'=>Mage::helper('adminhtml')->__('Quick Order Page')),
            //array('value' => 3, 'label'=>Mage::helper('adminhtml')->__('Both')),
        );
    }

    

}
