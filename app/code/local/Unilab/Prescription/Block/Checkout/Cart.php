<?php


class Unilab_Prescription_Block_Checkout_Cart extends Mage_Core_Block_Template
{ 	
	protected function _getUser()
	{
		return Mage::getSingleton("customer/session");
	} 
	
	public function getPrescriptions()
	{	  
		if(!$this->hasData('prescriptions')){		
			$prescriptions = Mage::getModel('prescription/prescription')->getCollection()
																		->addFilterByCustomer($this->_getUser());
			$this->setData('prescriptions',$prescriptions);
		}
		return $this->getData('prescriptions');
	} 
	
	public function getPrescriptionType()
	{
		if($this->isPrescriptionReuse()){
			return 'EXISTING';
		}elseif($this->getPrescription()->getScannedRx() == ""){
			return 'NEW';
		}elseif($this->getPrescription()->getScannedRx() != ""){
			return 'SCANNED_RX';
		}else{
			return '';
		}
	}
	
	protected function isPrescriptionReuse()
	{  
		$prescriptions = $this->getPrescriptions();
		$reused 	   = false;
		
		if($prescriptions->count() > 0 ){
			$count_exist = 1;
			foreach($prescriptions as $prescription){
				if($count_exist > 1){
					$reused = true;
					break;
				}				
				if($prescription->getPrescriptionId() == $this->getPrescription()->getPrescriptionId()){
					$count_exist++;
				} 
			}
		}
		
		return  $reused;
		
	}
}