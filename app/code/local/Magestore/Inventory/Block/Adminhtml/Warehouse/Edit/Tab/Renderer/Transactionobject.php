<?php

class Magestore_Inventory_Block_Adminhtml_Warehouse_Edit_Tab_Renderer_Transactionobject extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $warehouse = Mage::getModel('inventory/warehouse')->load($this->getRequest()->getParam('id'));
        $html = '';
        if ($row->getToName() == $warehouse->getName()) {
            $html = $row->getFromName();
        }else{
            $html = $row->getToName();
        }
        return $html;
    }

}

?>
