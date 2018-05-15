<?php
class Unilab_Promovouchers_Block_Adminhtml_Promovouchers_Edit_Renderer_Salesrule 
extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 
	public function render(Varien_Object $row)
	{
		$ruleid =  $row->getData($this->getColumn()->getIndex());
		
		
		$connection 	= Mage::getSingleton('core/resource')->getConnection('core_read');
		$connection->beginTransaction();
		
		$selectevent 	=$connection->select()->from('salesrule', array('name'))
						->where('rule_id=?',$ruleid); 
						
		$salesrule 		= $connection->fetchRow($selectevent);
		$rule			= $salesrule['name'];

		
		return $rule; 
	 
	}
 
}
?>  