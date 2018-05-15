<?php
class Magestore_Inventory_Block_Adminhtml_Listadjuststock_Rendererwarehousecontact extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $data = $row->getData();
        $warehouseId =  $row->getData($this->getColumn()->getIndex());
        $column = 'manager_name';
        $warehouseContact = Mage::helper('inventory/warehouse')->getAColumnValueInWarehouse($warehouseId,$column);
        return $warehouseContact;
    }
}
?>