<?php 
class Unilab_Healthplus_HealthcreditController extends Mage_Core_Controller_Front_Action
{

	protected function _getConnection()
	{
		$this->_Connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		return $this->_Connection;
	}

	public function AddhealthcreditAction()
    {  
		$session    = Mage::getSingleton('checkout/session');
		$quote_id = $session->getQuoteId(); 
		
		$response = Mage::getModel('healthplus/healthplus')->carthealthcredits($_POST['grandtotal']);
		
		if($response['success'] == true)
		{
			$query 	= "update sales_flat_quote set healthcredit = '1' where entity_id = '$quote_id'";
			$this->_getConnection()->query($query);
			$this->_getConnection()->commit(); 	
		}else{
			$query 	= "update sales_flat_quote set healthcredit = '0' where entity_id = '$quote_id'";
			$this->_getConnection()->query($query);
			$this->_getConnection()->commit(); 	
		}
		
		
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
        
	}

	public function UnusehealthcreditAction(){

		$session    = Mage::getSingleton('checkout/session');
		$quote_id = $session->getQuoteId(); 
				
		$query 	= "update sales_flat_quote set healthcredit = '0' where entity_id = '$quote_id'";
		$this->_getConnection()->query($query);
		$this->_getConnection()->commit(); 
		
		$grandTotal = Mage::getModel('checkout/session')->getQuote()->getGrandTotal();
		
		$response['grandTotal'] = Mage::helper('core')->currency($grandTotal,true,false);	
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
	}
	
	public function checkcartwithHCAction()
	{
		$session    = Mage::getSingleton('checkout/session');
		$quote_id = $session->getQuoteId(); 

		$sqlselect      = "SELECT * FROM sales_flat_quote WHERE entity_id = '$quote_id'";
		$result   		= $this->_getConnection()->fetchRow($sqlselect);
		$this->_getConnection()->commit();  


		$cart = Mage::getModel('checkout/cart')->getQuote();
		$discountAmount = 0;
		foreach ($cart->getAllVisibleItems() as $item) 
		{ 		  
			$prodid = $item->getProduct()->getId();
			$_resource = Mage::getSingleton('catalog/product')->getResource();
			$healthplus = $_resource->getAttributeRawValue($prodid,  ['unilab_healthplus'], Mage::app()->getStore());
		  
		  if ($healthplus == '1') {
			  	$discount = $item->getDiscountAmount();
				$discountAmount = $discountAmount + $discount;
		  }

		}

		$response['healthcredit']		= $result['healthcredit'];
		$response['discountAmount']		= Mage::helper('core')->currency($discountAmount,true,false);
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
	}

}