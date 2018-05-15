<?php
/***
 * Unilab BPI Securepay Payment Method
 * @date 2013-11-12
 * @author diszo.sasil@movent.com (Movent Inc.)
 *
 ***/
/*
 * 
 * This API is uses 3-Party Payment model describe in the documentation
 * 3-Party does not support POST data transfer
 * 
 */
class Unilab_BpiSecurepay_Model_Method_Payment extends Mage_Payment_Model_Method_Abstract {

	protected $_canSaveCc 				= true;
    protected $_isGateway               = true;
    protected $_canAuthorize            = true;		// Auth Only
    protected $_canCapture              = true;	    // Sale, Capture
    protected $_canCapturePartial       = true;
    protected $_canRefund               = false;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = false;     // Creation of a transaction from the admin panel
    protected $_canUseCheckout          = true;
	protected $_canFetchTransactionInfo = true;
	protected $_code 					= 'bpisecurepay'; // NOTE: this should also be the code name in system.xml and config.xml
	
	protected $_formBlockType 			= 'bpisecurepay/form';
	protected $_infoBlockType 			= 'bpisecurepay/info';
		
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
		return Mage::getModel('bpisecurepay/config')->getPaymentConfig();
	}			
	
	/**
	 *  Return URL for customer response
	 *
	 *  @return	  string Return customer URL
	 */
	public function getReturnUrl() {
		return Mage::getUrl('bpisecurepay/process/response', array('_secure' => true));
	}
	
	/**
	 *  Return Order Place Redirect URL
	 *
	 *  @return	  string Order Redirect URL
	 */
	public function getOrderPlaceRedirectUrl() {
		return Mage::getUrl('bpisecurepay/process/redirect', array('_secure' => true));
		
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
	
	public function getCheckoutFormFields() {
		return $this->getPaymentCheckoutRequest();
	}
		
	public function getPaymentCheckoutRequest() {
		$conf = array();		
		$conf['vpc_Merchant']		= $this->getConfig()->getMerchant();
		$conf['vpc_AccessCode']		= $this->getConfig()->getMerchantAccesscode();
		$conf['vpc_Version']		= $this->getConfig()->getVpcVersion();
		$conf['vpc_Command']		= $this->getConfig()->getVpcCommand();		
		$conf['vpc_MerchTxnRef']	= $this->getOrder()->getIncrementId();		
		$conf['vpc_Amount']			= (string) $this->getAmount();
		$conf['vpc_Locale']			= 'en';
		$conf['vpc_ReturnURL']		= $this->getReturnUrl();
		$conf['vpc_VirtualPaymentClientURL']= $this->getConfig()->getPaymentUrl();
		
		file_put_contents('./debug-bpi-payment.txt', print_r($conf,1).PHP_EOL,FILE_APPEND);
		
		return $conf;
	}

	public function getAmount() {		
		$amount = sprintf("%.2f",$this->getOrder()->getGrandTotal());	
		if($this->getConfig()->getEnableTestmode()){
			return str_replace(array('.',','),'',$amount);
		}
		return $amount; 
	}
	
}