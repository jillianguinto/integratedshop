<?php 
class Unilab_Prescription_Block_Adminhtml_Prescription_Sales_Order_View_Items_Renderer_Default extends Mage_Adminhtml_Block_Sales_Order_View_Items_Renderer_Default
{   
    public function getViewPrescriptionUrl()
    {
        return $this->getUrl('prescription/adminhtml_prescription/view', array('item_id' => $this->getItem()->getId()));
    }
 

}
