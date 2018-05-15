<?php

/**
 * Api Request
 *
 * @category   Unilab
 * @package    Unilab_BpiSecurepay
 * @copyright  diszo.sasil@movent.com 
 * @name      Unilab_BpiSecurepay_Model_Api
 */
 
class Unilab_Ipay88_Model_Api extends Varien_Object {
	
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
	
	public function getConfig(){
		return Mage::getModel('ipay88/config')->getPaymentConfig();
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
            $id = $this->getData('RefNo');
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
		
		$data = $this->getData();
		
		$errorExists = false;		
			
		file_put_contents('./debug-ipay88-payment.txt', print_r($data,1).PHP_EOL,FILE_APPEND);
		
		if(isset($data['Status']) && $data['Status'] == '1' && $data['ErrDesc'] == '' && $this->_getOrder()->getState() == Mage_Sales_Model_Order::STATE_PROCESSING) // Good Transaction
		{
			// Validate Returned Signature			
			/*
			$amount = $data['Amount'];
			if($this->getConfig()->getEnableTestmode()){
				$amount = Unilab_Ipay88_Model_Config::_IPAY88_ALLOWED_TESTING_AMOUNT;
			}
						
			$_retData = array(
				'MerchantKey' 	=> $this->getConfig()->getMerchantKey(),
				'MerchantCode' 	=> $data['MerchantCode'],
				'RefNo' 		=> $data['RefNo'],
				'Amount' 		=> $amount,
				'Currency' 		=> $data['Currency']
			);
			
			
			$returnedSig = Mage::getModel('ipay88/method_payment')->getSignature($_retData);			
			
			file_put_contents('./debug-ipay88-payment.txt', $returnedSig.'=>'.$data['Signature'].PHP_EOL,FILE_APPEND);
			
			
			if($returnedSig == $data['Signature']){
				$this->_registerPaymentCapture();				
				$this->getCoreSession()->addSuccess(Mage::helper('core')->__('Your transaction ID is '.$data['RefNo']))
									->setTxnId($data['RefNo']);
			}else{
				$errorExists = true; // Error on Signature
			}
			 * 
			 * 
			 */
			 
			$this->_registerPaymentCapture();				
			$this->getCoreSession()->addSuccess(Mage::helper('core')->__('Your transaction ID is '.$data['RefNo']))
								->setTxnId($data['RefNo']);
			
		}else{
			$errorExists = true; // Other payment Errors
		}
		
		
		if($errorExists)
		{
			$errMsg = $this->getErrorDetailDescription($data['ErrDesc']);
						
			$this->updatePayment(true,Mage_Sales_Model_Order::STATE_NEW);
	
			$this->_getOrder()->addStatusHistoryComment($errMsg);
			$this->_getOrder()->save();
						
			$orderViewURL = Mage::getUrl('sales/order/view', array('order_id' => $this->_getOrder()->getId()));
			
			//$this->getCheckoutSession()->addError($errMsg.'. '.sprintf('Order has been cancelled (Reference: <a href="%s" alt="">%s</a>).', $orderViewURL, $data['RefNo']));
			
			throw new Exception($errMsg);			
		}
				
	}


	/**
     * Process Update payment either full/partial
     */
	public function updatePayment($isPending=false,$orderStatus=Mage_Sales_Model_Order::STATE_PROCESSING){
		$payment = $this->_getOrder()->getPayment();
        $payment->setTransactionId($this->getData('TransId'))
                ->setParentTransactionId($this->getData('RefNo'))
                ->setShouldCloseParentTransaction(false)
                ->setIsTransactionClosed(false)
                ->setAdditionalInformation('TransactionNo:'.$this->getData('TransId'))
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
			
			$invoice->getOrder()->setIsCustomerNotified(true);
			$invoice->getOrder()->setIsInProcess(true);
			$invoice->getOrder()->addStatusHistoryComment(
                        	Mage::helper('core')->__('Notified customer about invoice.')
                        )
                    	->setIsCustomerNotified(true);
			$invoice->sendEmail();
				
			$transactionSave = Mage::getModel('core/resource_transaction')
			    ->addObject($invoice)
			    ->addObject($invoice->getOrder());
			
			$transactionSave->save();				
					
			Mage::dispatchEvent('unilab_payment_captured_after', array("order"=>$this->_getOrder()));
			/*		  
            $invoice->getOrder()->sendNewOrderEmail()
            			->addStatusHistoryComment(
                        	Mage::helper('core')->__('Notified customer about invoice #%s.', $invoice->getIncrementId())
                        )
                    	->setIsCustomerNotified(true);			
			*/
			/*
			//START Handle Shipment
            $shipment = $this->_getOrder()->prepareShipment();
            $shipment->register();
            $this->_getOrder()->setIsInProcess(true);
            $this->_getOrder()->addStatusHistoryComment(Mage::helper('core')->__('Notified customer about shippment'), false);
            $transactionSave = Mage::getModel('core/resource_transaction')
                ->addObject($shipment)
                ->addObject($shipment->getOrder())
                ->save();
            //END Handle Shipment
			*/					
        }
        catch (Mage_Core_Exception $e) {
            $this->_getOrder()->addStatusHistoryComment('BPI Payment Secure: Exception occurred during _registerPaymentCapture() action. Exception message: '.$e->getMessage(), false);
            throw $e;
        }
        
    }
	
	public function getErrorDetailDescription($error=''){
		
		$errorMsg = array(
			'Duplicate reference number' => 'Reference number must be unique for each transaction.',
			'Invalid merchant' 			=> 'The merchant code does not exist.',
			'Invalid parameters' 		=> 'Some parameter posted to iPay88 is invalid or empty.',
			'Overlimit per transaction' => 'You exceed the amount value per transaction. * For Testing account, only amount RM 1.00 is allowed.',
			'Payment not allowed' 		=> 'The Payment method you requested is not allowed for this merchant code, please contact ipay88 to enable your payment option.',
			'Permission not allow' 		=> 'Referrer URL in for your account registered in Ipay88 does not match. Please register your request and response URL with iPay88.',
			'Signature not match' 		=> 'The Signature generated is incorrect.',
			'Status not approved' 		=> 'Account was suspended or not active.'
			);
		
		if(isset($errorMsg[$error])){
			return $errorMsg[$error];
		}
		
		return $error;
	}

	
	
}