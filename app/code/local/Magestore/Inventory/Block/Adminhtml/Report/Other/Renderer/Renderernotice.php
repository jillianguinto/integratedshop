<?php
class Magestore_Inventory_Block_Adminhtml_Report_Other_Renderer_Renderernotice extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
        {
        $value =  $row->getData($this->getColumn()->getIndex());
        $read = $row->getData('status');
        $column = $this->getColumn()->getIndex();
        if($column == 'description'){
                $renderer =  htmlspecialchars_decode($value);
        }
        if($column == 'status'){
            if($read==0){
                $renderer =  '<span class="grid-severity-critical" style="margin-top:15px"><span>Unread</span></span>';
            }else{
                $renderer =  '<span class="grid-severity-notice" style="margin-top:15px"><span>Read</span></span>';    
            }
        }
        return $renderer;
        }
}
?>