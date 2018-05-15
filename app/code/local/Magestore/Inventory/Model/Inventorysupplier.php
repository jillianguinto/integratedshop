<?php
class Magestore_Inventory_Model_Inventorysupplier {
    
    public function toOptionArray()
    {
        return array(
            array('value' => 'last_purchase_order', 'label'=>Mage::helper('inventory')->__('LAST PURCHASE ORDER')),
            array('value' => 'average', 'label'=>Mage::helper('inventory')->__('AVERAGE'))
        );
    }
}
?>
