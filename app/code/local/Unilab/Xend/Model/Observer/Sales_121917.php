<?php
class Unilab_Xend_Model_Observer_Sales
{ 
    public function processWaybill(Varien_Event_Observer $observer){    
		$_order = $observer->getEvent()->getOrder();  
		//if($_order->getShippingCarrier()->getCarrierCode() == 'xend'){
		if($_order->getShippingCarrier()->getCarrierCode() == 'xend' || $_order->getShippingCarrier()->getCarrierCode() == 'minimumordervalue'){
			try{
				$xend_api_waybill = Mage::getModel("xend/api_waybill");
				$store_settings   = $this->getXendHelper()->getConfigSettings($_order->getStoreId());
				$waybill		  = $xend_api_waybill->connect($store_settings)
													 ->createShipment($_order,$store_settings);
				if(is_object($waybill)){
					if($waybill_number = $waybill->getWaybillNumber()){ 
						$_order->setUnilabWaybillNumber($waybill_number)->save();
					}
				}
			}catch(Exception $e){   
				Mage::log($e->getMessage());
			}
		}
    } 
	
	protected function getXendHelper()
	{
		return Mage::helper("xend");
	}
}