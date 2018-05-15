<?php

/**
 * Api Request
 *
 * @category   Unilab
 * @package    Unilab_RcbcPay
 * @copyright  diszo.sasil@movent.com 
 * @name      Unilab_RcbcPay_Model_Api
 */
 
class Unilab_RcbcPay_Model_Api extends Varien_Object {
	
	/*
    * @param Mage_Sales_Model_Order
    */
    protected $_order = null;
		
	
	public function getCoreSession(){
		return Mage::getSingleton('core/session');
	}
	
	
	public function getCheckoutSession(){
		return Mage::getSingleton('checkout/session');
	}
	
	 /**
     * Load and validate order, instantiate proper configuration
     *
     *
     * @return Mage_Sales_Model_Order
     * @throws Exception
     */
    protected function _getOrder()
    {
        if (empty($this->_order)) {
            // get proper order
            $id = $this->getData('vpc_MerchTxnRef');
            $this->_order = Mage::getModel('sales/order')->loadByIncrementId($id);
            if (!$this->_order->getId()) {
                Mage::app()->getResponse()
                    ->setHeader('HTTP/1.1','503 Service Unavailable')
                    ->sendResponse();
                exit;
            }
            // re-initialize config with the method code and store id
            $methodCode = $this->_order->getPayment()->getMethod();

            if(!Mage::getStoreConfig("payment/{$methodCode}/active")){
                throw new Exception(sprintf('Method "%s" is not available.', $methodCode));
            }
        }
        return $this->_order;
    }
	
	
	/**
	 *  Process/validated reponse with errors
	 *
	 */
	public function processResponse() {
		

		$vpc_Txn_Secure_Hash = $this->getData("vpc_SecureHash");
		
		$errorExists = false;
		
		$SECURE_SECRET = Mage::getModel('rcbcpay/config')->getPaymentConfig()->getMerchantSecureHashSecret();
		
		$data = $this->getData();
		
		
		file_put_contents('./debug-bpi-payment.txt', print_r($data,1).PHP_EOL,FILE_APPEND);
		
		
		if (strlen($SECURE_SECRET) > 0 && $this->getData("vpc_TxnResponseCode") != "7" && $this->getData("vpc_TxnResponseCode") != "No Value Returned") 
		{

		    $md5HashData = Mage::getModel('rcbcpay/config')->getPaymentConfig()->getMerchantSecureHashSecret();
			
			unset($data['vpc_SecureHash']);
			
		    // sort all the incoming vpc response fields and leave out any with no value
		    foreach($data as $key => $value) {
		        if ($key != "vpc_SecureHash" or strlen($value) > 0) {
		            $md5HashData .= $value;
		        }
		    }
		    
		    // Validate the Secure Hash (remember MD5 hashes are not case sensitive)
			// This is just one way of displaying the result of checking the hash.
			// In production, you would work out your own way of presenting the result.
			// The hash check is all about detecting if the data has changed in transit.
		    if (strtoupper($vpc_Txn_Secure_Hash) == strtoupper(md5($md5HashData))) {
		        // Secure Hash validation succeeded, add a data field to be displayed
		        // later.
		        $hashValidated = Mage::helper('rcbcpay')->__('Success');
		    } else {
		        // Secure Hash validation failed, add a data field to be displayed
		        // later.
		        $hashValidated = Mage::helper('rcbcpay')->__('Invalid Hash');
		        $errorExists = true;
		    }
		} 
				
				
		$txnResponseCode = $this->null2unknown($data["vpc_TxnResponseCode"]);
		
	
		
		if(!$errorExists && $this->getData("vpc_TxnResponseCode") == "0" && $this->_getOrder()->getState() == Mage_Sales_Model_Order::STATE_PROCESSING)
		{		
			$this->_registerPaymentCapture();				
			$this->getCoreSession()->addSuccess(Mage::helper('core')->__('Your transaction ID is '.$this->getData('vpc_MerchTxnRef')))
									->setTxnId($this->getData('vpc_MerchTxnRef'));
		}
		else
		{
			$errMsg = $this->getResponseDescription($txnResponseCode);
			
			$this->updatePayment(true,Mage_Sales_Model_Order::STATE_NEW);
	
			$this->_getOrder()->addStatusHistoryComment($errMsg);
			$this->_getOrder()->save();
						
			$orderViewURL = Mage::getUrl('sales/order/view', array('order_id' => $this->_getOrder()->getId()));			
			
			$this->getCheckoutSession()->addError($errMsg.'. '.sprintf('Order has been cancelled (Reference: <a href="%s" alt="">%s</a>).', $orderViewURL, $this->getData('vpc_MerchTxnRef')));
			
			throw new Exception($hashValidated);
		}
		
	}

	/**
     * Process Update payment either full/partial
     */
	public function updatePayment($isPending=false,$orderStatus=Mage_Sales_Model_Order::STATE_PROCESSING){
		$payment = $this->_getOrder()->getPayment();
        $payment->setTransactionId($this->getData('vpc_ReceiptNo'))
                ->setParentTransactionId($this->getData('vpc_MerchTxnRef'))
                ->setShouldCloseParentTransaction(false)
                ->setIsTransactionClosed(false)
                ->setAdditionalInformation('TransactionNo:'.$this->getData('vpc_TransactionNo').PHP_EOL.'ReceiptNo:'.$this->getData('vpc_ReceiptNo').PHP_EOL.'Message:'.$this->getData('vpc_Message'))
                ->setStatus($orderStatus)
                ->setIsTransactionPending($isPending)
                ;
	}

	/**
     * Capture payment transaction
     */
    protected function _registerPaymentCapture()
    {	
		$this->updatePayment(false,Mage_Sales_Model_Order::STATE_PROCESSING);
		
		$payment = $this->_getOrder()->getPayment();
		
		$payment->setPreparedMessage('');
		
		$this->_getOrder()->save();
		
        try {
            if(!$this->_getOrder()->canInvoice())
            {
                Mage::throwException(Mage::helper('core')->__('Cannot create an invoice.'));
            }
            $invoice = Mage::getModel('sales/service_order', $this->_getOrder())->prepareInvoice();
            if (!$invoice->getTotalQty()) {
                Mage::throwException(Mage::helper('core')->__('Cannot create an invoice without products.'));
            }
            $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
            $invoice->register();
            $invoice->getOrder()->setCustomerNoteNotify(true);

            $transactionSave = Mage::getModel('core/resource_transaction')
                ->addObject($invoice)
                ->addObject($invoice->getOrder());
			
            $transactionSave->save();
          
            $invoice->getOrder()->sendNewOrderEmail()
            			->addStatusHistoryComment(
                        	Mage::helper('core')->__('Notified customer about invoice #%s.', $invoice->getIncrementId())
                        )
                    	->setIsCustomerNotified(true);
        }
        catch (Mage_Core_Exception $e) {
            $this->_getOrder()->addStatusHistoryComment('BPI Payment Secure: Exception occurred during _registerPaymentCapture() action. Exception message: '.$e->getMessage(), false);
            throw $e;
        }
        
    }
	

	 public function callAPI($method,$params){
        $obj = null;
		try {

            $client = new SoapClient(Unilab_RcbcPay_Model_Config::_RCBC_SERVICE_URL, array('soap_version' => SOAP_1_2));
			
            $result = $client->$method($params);
			
            if($result && is_object($result->{$method.'Result'}) && gettype($result->{$method.'Result'}) == "string"){
                return $result->{$method.'Result'};
            }

        } catch(SoapFault $e) {
			throw $e;
        }
		
		
        return $obj;
    }
	
	
}