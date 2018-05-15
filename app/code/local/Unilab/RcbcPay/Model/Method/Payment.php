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
class Unilab_RcbcPay_Model_Method_Payment extends Mage_Payment_Model_Method_Abstract {

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
	protected $_code 					= 'rcbcpay'; // NOTE: this should also be the code name in system.xml and config.xml
	
	protected $_formBlockType 			= 'rcbcpay/form';
	protected $_infoBlockType 			= 'rcbcpay/info';
		
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
		return Mage::getModel('rcbcpay/config')->getPaymentConfig();
	}			
	
	/**
	 *  Return URL for customer response
	 *
	 *  @return	  string Return customer URL
	 */
	public function getReturnUrl() {
		return Mage::getUrl('rcbcpay/process/response', array('_secure' => true));
	}
	
	/**
	 *  Return Order Place Redirect URL
	 *
	 *  @return	  string Order Redirect URL
	 */
	public function getOrderPlaceRedirectUrl() {
		return Mage::getUrl('rcbcpay/process/redirect', array('_secure' => true));
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
		$conf['merchantCode']		= '001'; $this->getConfig()->getMerchantCode();
		$conf['merchantName']		= 'Merchant'; //$this->getConfig()->getMerchantName();
		$conf['refno']				= $this->getOrder()->getIncrementId();
		$conf['total_amt']			= $this->getOrder()->getGrandTotal(); //$this->getAmount();	
		$conf['custcode']			= '123'; 
		$conf['custEmail']			= 'diszo.sasil@movent.com';
		$conf['custMobile']			= '12345678';
		$conf['status']				= 'PENDING';
		return $conf;
	}

	public function getAmount() {
		return (int) str_replace(array('.',','),'',$this->getOrder()->getGrandTotal());
	}
	
}