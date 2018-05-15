<?php 
class Unilab_Adminhtml_Block_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid
{    
	 

    protected function _prepareColumns()
    {  
        $this->addColumnAfter('unilab_waybill_number', array(
            'header'=> Mage::helper('sales')->__('Waybill #'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'unilab_waybill_number',
        ),'status');
		 
        return parent::_prepareColumns();
    } 
 
}
