<?php
class Magestore_Inventory_Model_Inventorynotice {
    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label'=>Mage::helper('inventory')->__('ONLY WAREHOUSE')),
            array('value' => 2, 'label'=>Mage::helper('inventory')->__('ONLY SYSTEM')),
            array('value' => 3, 'label'=>Mage::helper('inventory')->__('BOTH WAREHOUSE AND SYSTEM')),
        );
    }
}
?>
