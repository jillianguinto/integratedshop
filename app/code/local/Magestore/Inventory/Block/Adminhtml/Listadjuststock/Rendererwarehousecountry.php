<?php
class Magestore_Inventory_Block_Adminhtml_Listadjuststock_Rendererwarehousecountry extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $data = $row->getData();
        $warehouseId =  $row->getData($this->getColumn()->getIndex());
        $column = 'country_id';
        $warehouseCountryId = Mage::helper('inventory/warehouse')->getAColumnValueInWarehouse($warehouseId,$column);
        $warehouseCountryName = Mage::getModel('directory/country')->load($warehouseCountryId)->getName(); 
        return $warehouseCountryName;
    }
}
?>