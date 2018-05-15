<?php

class Unilab_Perapal_Model_Method_Orderemail extends Mage_Core_Model_Abstract
{
	public function sendemail($orderid)
	{
							
				$order = Mage::getModel('sales/order')->load(Mage::getSingleton('checkout/session')->getLastOrderId());
				
				echo $order->getId() . "test";
			
			die();
		
	}
}
