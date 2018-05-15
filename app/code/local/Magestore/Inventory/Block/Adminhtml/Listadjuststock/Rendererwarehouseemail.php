<?php
class Magestore_Inventory_Block_Adminhtml_Listadjuststock_Rendererwarehouseemail extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $data = $row->getData();
        $warehouseId =  $row->getData($this->getColumn()->getIndex());
        $column = 'manager_email';
        $warehouseEmail = Mage::helper('inventory/warehouse')->getAColumnValueInWarehouse($warehouseId,$column);
        return $warehouseEmail;
    }
}
?>