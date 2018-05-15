<?php
/**
 * Api Request
 *
 * @category   Movent
 * @package    Movent_InfinitiumEpayment
 * @copyright  Movent - jerick.duguran@movent.com / diszo.sasil@movent.com 
 * @name       Movent_InfinitiumEpayment_Model_Api_Request
 */
class Unilab_Ipay88_Model_Api_Request extends Varien_Object {

	protected $_request = array();
	protected $_payment_config = null;
	protected $_order;
	protected $merchant_trans_id = null;

	
	public function getOrder() {
		$orderIncrementId = $this->getCheckout()->getLastRealOrderId();
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);		
		if($order->getId()){
			$this->_order = $order;
		}else{
			Mage::throwException(Mage::helper('payment')->__('Sales order object not found.'));
		}
		
		return $this->_order;
	}


	/**
	 *  Return URL for customer response
	 *
	 *  @return	  string Return customer URL
	 */
	public function getReturnUrl() {
		return Mage::getUrl('ipay88/process/response', array('_secure' => true));
	}
	
	
	/**
	 *  Backend Return URL for customer response
	 *
	 *  @return	  string Return customer URL
	 */
	public function getBackendUrl() {
		return Mage::getUrl('ipay88/process/backend', array('_secure' => true));
	}
	
	
	public function getPaymentCheckoutRequest() {
		$this -> _request['MerchantKey']	= $this->getPaymentConfig()->getMerchantKey();
		$this -> _request['MerchantCode']	= $this->getPaymentConfig()->getMerchantCode();
		$this -> _request['PaymentId'] 		= 1;
		$this -> _request['RefNo'] 			= $this->getOrder()->getIncrementId();	
		$this -> _request['Amount']			= $this->getAmount();
		$this -> _request['Currency'] 		= $this->getCurrencyCode();
		$this -> _request['ProdDesc'] 		= '';
		$this -> _request['UserName'] 		= $this->getCustomerEmail(); //'diszo.sasil';
		$this -> _request['UserEmail'] 		= $this->getCustomerEmail();
		$this -> _request['UserContact'] 	= $this->getOrder()->getBillingAddress()->getTelephone(); //'5198016';
		$this -> _request['Remark'] 		= $this->getOrderDescription();
		$this -> _request['Signature'] 		= $this->getSignature();
		$this -> _request['ResponseURL'] 	= $this->getReturnUrl();
		$this -> _request['BackendURL'] 	= $this->getBackendUrl();
			
		file_put_contents('./ipay88-debug.txt', 'getPaymentCheckoutRequest():'.print_r($this -> _request,1).PHP_EOL,FILE_APPEND);	
		return $this -> _request;
	}

	protected function getSignature() {			
		$req = $this->getPaymentCheckoutRequest();		
		$source = 	$req['MerchantKey'].
					$req['MerchantCode'].
					$req['RefNo'].
					$req['Amount'].
					$req['Currency'];		
		
		return base64_encode($this->getHex2bin(sha1($source)));
	}

	public function getAmount() {		
		$amount = $this->getOrder()->getGrandTotal();
		return str_replace(array(",","."), "", $amount);		
	}

	protected function getCustomerName() {
		return $this->getOrder()->getCustomerName();
	}

	protected function getCustomerEmail() {
		if (!$customerEmail = $this->getOrder()->getBillingAddress()->getEmail()) {
			$customerEmail = $this->getOrder()->getData('customer_email');
		}
		return $customerEmail;
	}
	
	protected function getCurrencyCode() {
		return $this -> getOrder() -> getBaseCurrencyCode();
	}

	protected function getOrderDescription(){
		$description = sprintf("Payment from unilab onlinestore (Ref:%s)", $this->getCheckout()->getLastRealOrderId());
		return $description;
	}
		
	protected function getHex2bin($hexSource)
	{
		for ($i=0;$i<strlen($hexSource);$i=$i+2)
		{
			$bin .= chr(hexdec(substr($hexSource,$i,2)));
		}
		return $bin;
	}
	
	
}
