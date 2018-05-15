<?php
class Magestore_Inventory_Model_Inventorysupplyneeds {
    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label'=>Mage::helper('inventory')->__('EXPONENTIAL SMOOTHING')),
            array('value' => 2, 'label'=>Mage::helper('inventory')->__('AVERAGE')),
        );
    }
}
?>
