<?php

class Unilab_Prescription_Model_Mysql4_Prescription_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('prescription/prescription');
    }
	
	protected function _getCart()
	{
		return Mage::getSingleton("checkout/cart");
	}
	
	protected function _getUser()
	{
		return Mage::getSingleton("customer/session");
	}
	
	protected function _getQuote()
    {
        return $this->_getCart()->getQuote();
    } 
	
	protected function _getOrderCollection()
    {
        return Mage::getResourceModel('sales/order_collection');
    }  
	
	public function addFilterByCustomer($customer,$exclude = false)
	{  
		$prescription_ids = array_merge($this->getQuotePrescriptionIds(),$this->getOrderPrescriptionIds($customer));   		
		
		$this->addFieldToFilter('prescription_id',array('in'=> $prescription_ids));
		if($exclude){		
			$this->addFieldToFilter('prescription_id',array('neq'=> $exclude));
		}
		
		return $this; 
	}
	
	public function getQuotePrescriptionIds()
	{	
		$quote 			  = $this->_getQuote();
		$prescription_ids = array();
		
		if($quote->hasItems()){   		
			foreach($quote->getAllItems() as $_item){
				if($prescription_id = $_item->getPrescriptionId()){
					$prescription_ids[$prescription_id] = $prescription_id;
				} 
			}	    
		}  
		return $prescription_ids;
	}
	
	public function getOrderPrescriptionIds($customer)
	{
		$prescription_ids = array();  
		
		if($customer->isLoggedIn())
		{
			$orders = $this->_getOrderCollection()
						   ->addFieldToSelect('entity_id')
						   ->addFieldToFilter('customer_id', $customer->getCustomer()->getId())
						   ->addFieldToFilter('state', array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()))
						   ->setOrder('created_at', 'desc');
						   
			if($orders->count() > 0){		

				$current_date = date("Y-m-d", Mage::getModel('core/date')->timestamp(time()));
				foreach($orders as $order){ 
				
					$items_collection = Mage::getResourceModel('sales/order_item_collection')
												 ->setOrderFilter($order)
												 ->addFieldToFilter('main_table.prescription_id',array('notnull'=>true))
												 ->addFieldToFilter('main_table.prescription_id',array('neq'=> '0')); 
												 
					$items_collection->getSelect()->join(array('p'=>$this->getTable('prescription/prescription')),
											  'main_table.prescription_id=p.prescription_id',
											   array('prescription_table_id'=>'p.prescription_id'))
											   ->where('(consumed IS NULL OR consumed = 0) AND status !=\'INVALID\' AND (expiry_date IS NULL OR expiry_date >= \''.$current_date.'\' )') 
											   ->having('prescription_table_id IS NOT NULL')
											   ->having('prescription_table_id != 0');
											   
					foreach($items_collection as $item){
						$prescription_ids[$item->getPrescriptionId()] = $item->getPrescriptionId();
					} 
				}
			} 		
		}  
		return $prescription_ids;
	}
	
}