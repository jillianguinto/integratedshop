<?php

class Magestore_Inventory_Model_Inventorycostprice {
    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label'=>Mage::helper('inventory')->__('LIFO')),
            array('value' => 2, 'label'=>Mage::helper('inventory')->__('FIFO')),
            array('value' => 3, 'label'=>Mage::helper('inventory')->__('AVERAGE')),
        );
    }
}

?>
