<?php
/***
 * Unilab iPay88 Method Payment
 * @date 2013-11-12
 * @author diszo.sasil@movent.com (Movent Inc.)
 *
 ***/

 
class Unilab_Ipay88_Model_Method_Payment extends Mage_Payment_Model_Method_Abstract {

	protected $_canSaveCc 				= true;
    protected $_isGateway               = true;
    protected $_canAuthorize            = true;		## Auth Only
    protected $_canCapture              = true;	    ## Sale, Capture
    protected $_canCapturePartial       = true;
    protected $_canRefund               = false;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = false;     ## Creation of a transaction from the admin panel
    protected $_canUseCheckout          = true;
	protected $_canFetchTransactionInfo = true;
	protected $_code 					= 'ipay88';
	
	protected $_formBlockType 			= 'ipay88/form';
	protected $_infoBlockType 			= 'ipay88/info';
	
	protected $_order;
		
	
	/**
	 * Get checkout session namespace
	 *
	 * @return Mage_Checkout_Model_Session
	 */
	public function getCheckout() {
		return Mage::getSingleton('checkout/session');
	}
	
	public function getConfig(){
		return Mage::getModel('ipay88/config')->getPaymentConfig();
	}
	
	
	/**
	 *  @return	  string Return cancel URL
	 */
	public function getCancelUrl() {
		return Mage::getUrl('ipay88/process/cancel', array('_secure' => true));
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
	
	/**
	 *  Return Order Place Redirect URL
	 *
	 *  @return	  string Order Redirect URL
	 */
	public function getOrderPlaceRedirectUrl() {
		return Mage::getUrl('ipay88/process/request', array('_secure' => true));
	}
	
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
	
	public function getPaymentCheckoutRequest() {
		$conf = array();
		
		$conf['MerchantKey']	= $this->getConfig()->getMerchantKey();
		$conf['MerchantCode']	= $this->getConfig()->getMerchantCode();
		$conf['PaymentId'] 		= '1';		
		$conf['RefNo'] 			= $this->getOrder()->getIncrementId();	
		$conf['Amount']			= $this->getAmount();
		$conf['Currency'] 		= $this->getCurrencyCode();
		$conf['ProdDesc'] 		= $this->getOrderDescription();
		$conf['UserName'] 		= $this->getUserName();
		$conf['UserEmail'] 		= $this->getCustomerEmail();		
		$conf['UserContact'] 	= $this->getTelephone();
		$conf['Remark'] 		= $this->getOrderDescription();				
		$conf['Signature'] 		= $this->getSignature($conf);		
		$conf['ResponseURL'] 	= $this->getReturnUrl();
		$conf['BackendURL'] 	= $this->getBackendUrl();		
		
		file_put_contents('./ipay88-debug.txt', 'getPaymentCheckoutRequest():'.print_r($conf,1).PHP_EOL,FILE_APPEND);	
		
		return $conf;
	}
	
	public function getSignature($req=array()) {		
		$source = 	$req['MerchantKey'].
					$req['MerchantCode'].
					$req['RefNo'].
					$req['Amount'].
					$req['Currency'];
				
		return base64_encode($this->getHex2bin(sha1($source)));
	}

	public function getAmount() {
		if(!$this->getConfig()->getEnableTestmode()){
			$amount = $this->getOrder()->getGrandTotal();
		}else{
			$amount = Unilab_Ipay88_Model_Config::_IPAY88_ALLOWED_TESTING_AMOUNT;
		}		
		return str_replace(array(",","."), "", $amount);		
	}

	public function getCustomerName() {
		return $this->getOrder()->getCustomerName();
	}
	
	public function getUserName(){
		$email = explode('@',$this->getCustomerEmail());
		if(isset($email[0])){
			return $email[0];
		}
		return $this->getCustomerEmail();
	}
	
	public function getTelephone() {
		if (!$telephone = $this->getOrder()->getBillingAddress()->getTelephone()) {
			$customerData = Mage::getModel('customer/customer')->load($this->getOrder()->getCustomerId())->getData();
			if(isset($customerData['telephone'])){
				$telephone = $customerData['telephone'];
			}			
		}
		return $telephone;
	}

	public function getCustomerEmail() {
		if (!$customerEmail = $this->getOrder()->getBillingAddress()->getEmail()) {
			$customerEmail = $this->getOrder()->getData('customer_email');
		}
		return $customerEmail;
	}
	
	public function getCurrencyCode() {
		return $this->getOrder()->getBaseCurrencyCode();
	}

	public function getOrderDescription(){
		return sprintf("Payment from unilab onlinestore (Ref:%s)", $this->getCheckout()->getLastRealOrderId());
	}
		
	public function getHex2bin($hexSource)
	{
		$bin = '';
		for ($i=0;$i<strlen($hexSource);$i=$i+2)
		{
			$bin .= chr(hexdec(substr($hexSource,$i,2)));
		}
		return $bin;
	}
	
}