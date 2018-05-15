<?php 

class Unilab_Adminhtml_Model_Observer_Product extends Varien_Object
{

	public function applyMOQ(Varien_Event_Observer $observer)
	{
		$product     = $observer->getProduct();
		
		if($product->getTypeId() == 'simple')
		{
			$unit_price  = $product->getUnilabUnitPrice();			
			if($moq = $product->getUnilabMoq()){
				$unit_price = $unit_price * $moq;
			} 		
			$product->setPrice($unit_price);
		}
	} 
}

?>