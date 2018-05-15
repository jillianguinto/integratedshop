<?php


class Unilab_Prescription_Block_Adminhtml_Prescription_Sales_Order_View_Items
	extends	Mage_Adminhtml_Block_Sales_Order_View_Items
{

	public function getItemsCollection()
    { 
		$items_collection 	=  Mage::getResourceModel('sales/order_item_collection')->setOrderFilter($this->getOrder())
																					->addFieldToFilter('prescription_id',array('notnull'=>true))
																					->addFieldToFilter('prescription_id',array('neq'=> '0'));
																					
		return $items_collection;
	}

}