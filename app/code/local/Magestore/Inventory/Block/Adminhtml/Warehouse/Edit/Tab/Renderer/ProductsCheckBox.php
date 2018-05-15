<?php
class Magestore_Inventory_Block_Adminhtml_Warehouse_Edit_Tab_Renderer_ProductsCheckBox
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row) 
    {
        return '<input type="checkbox" checked="checked" class="checkbox" value='.$row->getEntityId().' name="" disabled="disabled">';
    } 
}
?>
