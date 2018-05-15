<?php

class Unilab_Prescription_Model_Observer{
	 
		public function checkCartItemsPrescriptions(Varien_Event_Observer $observer){  			 
			$item = $observer->getEvent()->getItem();  
			$quote = $item->getQuote(); 

			// Quote not loaded, do nothing  
			if (!$quote) return;	
			$has_error = false;
			foreach($quote->getAllItems() as $item){ 
			
				if(!($item->getId())){
					continue;
				}
				$buy_request_per_item = unserialize($item->getOptionByCode('info_buyRequest')->getValue()); 				
				if(isset($buy_request_per_item['cpid'])){			
					$_product = Mage::getModel("catalog/product")->load($buy_request_per_item['cpid']); 
					if($_product->getUnilabRx() && !$this->getHelper()->isValidPrescription($item->getPrescriptionId())){ 
						 $item->addErrorInfo('prescription','RX',Mage::helper('prescription')->__('This product requires prescription.')); 
						 $has_error = true;
					} 
				}
			} 
			
			if($has_error){
					$quote->addErrorInfo('error','prescription','RX',Mage::helper('cataloginventory')->__('Some of your products requires prescriptions.'));
			} 	
		}  
		
		protected function getHelper()
		{
			return Mage::helper("prescription"); 
		}
} 
?>