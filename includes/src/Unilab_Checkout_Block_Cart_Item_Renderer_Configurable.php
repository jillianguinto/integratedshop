<?php 

class Unilab_Checkout_Block_Cart_Item_Renderer_Configurable extends Mage_Checkout_Block_Cart_Item_Renderer_Configurable
{
	public function getPrescription()
	{
		$prescription = $this->_getPrescription();
		
		return $prescription->getId() ? $prescription : false;
	}
	
	protected function _getPrescription()
	{ 
		$prescription = Mage::getModel("prescription/prescription")->load($this->getItem()->getPrescriptionId());
		
		return $prescription;
	}
}
