<?php 
class Unilab_Sales_Model_Service_Order extends Mage_Sales_Model_Service_Order
{ 
    protected function _initCreditmemoData($creditmemo, $data)
    {
		parent::_initCreditmemoData($creditmemo, $data);  
		
		if (isset($data['fee_amount'])) {
            $creditmemo->setBaseFeeAmount((float)$data['fee_amount']); //movent
        } 
    } 
}
