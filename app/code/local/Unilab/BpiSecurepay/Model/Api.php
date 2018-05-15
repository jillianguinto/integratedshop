<?php

/**
 * Api Request
 *
 * @category   Unilab
 * @package    Unilab_BpiSecurepay
 * @copyright  diszo.sasil@movent.com 
 * @name      Unilab_BpiSecurepay_Model_Api
 */
 
class Unilab_BpiSecurepay_Model_Api extends Varien_Object {
	
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
	public function processResponse() 
	{
		$vpc_Txn_Secure_Hash = $this->getData("vpc_SecureHash");
		
		$errorExists = false;
		
		$SECURE_SECRET = Mage::getModel('bpisecurepay/config')->getPaymentConfig()->getMerchantSecureHashSecret();
		
		$data = $this->getData();
		
		
		file_put_contents('./debug-bpi-payment.txt', print_r($data,1).PHP_EOL,FILE_APPEND);
		
		
		if (strlen($SECURE_SECRET) > 0 && $this->getData("vpc_TxnResponseCode") != "7" && $this->getData("vpc_TxnResponseCode") != "No Value Returned") 
		{

		    $md5HashData = Mage::getModel('bpisecurepay/config')->getPaymentConfig()->getMerchantSecureHashSecret();
			
			unset($data['vpc_SecureHash']);
			unset($data['vpc_SecureHashType']);
			
		    // sort all the incoming vpc response fields and leave out any with no value
		    foreach($data as $key => $value) {
		        if ($key != "vpc_SecureHash" and strlen($value) > 0) {
					
					$HashData .= $key . "=" . $value . "&";
		            //$md5HashData .= $value;
		        }
		    }
			
			$concathashdata = rtrim($HashData,"&");
			$securhash = strtoupper(hash_hmac('SHA256',$concathashdata, pack("H*",$md5HashData)));
		    
		    // Validate the Secure Hash (remember MD5 hashes are not case sensitive)
			// This is just one way of displaying the result of checking the hash.
			// In production, you would work out your own way of presenting the result.
			// The hash check is all about detecting if the data has changed in transit.
		    if (strtoupper($vpc_Txn_Secure_Hash) == $securhash) {
		        // Secure Hash validation succeeded, add a data field to be displayed
		        // later.
		        $hashValidated = Mage::helper('bpisecurepay')->__('Success');
		    } else {
		        // Secure Hash validation failed, add a data field to be displayed
		        // later.
		        $hashValidated = Mage::helper('bpisecurepay')->__('Invalid Hash');
		        $errorExists = true;
		    }
		} 
				
				
		$txnResponseCode = $this->null2unknown($data["vpc_TxnResponseCode"]);
			
		/*
		$amount          = $this->null2unknown($data["vpc_Amount"]);
		$locale          = $this->null2unknown($data["vpc_Locale"]);
		$batchNo         = $this->null2unknown($data["vpc_BatchNo"]);
		$command         = $this->null2unknown($data["vpc_Command"]);
		$message         = $this->null2unknown($data["vpc_Message"]);
		$version         = $this->null2unknown($data["vpc_Version"]);
		$cardType        = $this->null2unknown($data["vpc_Card"]);
		$orderInfo       = $this->null2unknown($data["vpc_OrderInfo"]);
		$receiptNo       = $this->null2unknown($data["vpc_ReceiptNo"]);
		$merchantID      = $this->null2unknown($data["vpc_Merchant"]);
		$authorizeID     = $this->null2unknown($data["vpc_AuthorizeId"]);
		$merchTxnRef     = $this->null2unknown($data["vpc_MerchTxnRef"]);
		$transactionNo   = $this->null2unknown($data["vpc_TransactionNo"]);
		$acqResponseCode = $this->null2unknown($data["vpc_AcqResponseCode"]);
		
		// 3-D Secure Data
		$verType         = array_key_exists("vpc_VerType", $data)          ? $data["vpc_VerType"]          : "No Value Returned";
		$verStatus       = array_key_exists("vpc_VerStatus", $data)        ? $data["vpc_VerStatus"]        : "No Value Returned";
		$token           = array_key_exists("vpc_VerToken", $data)         ? $data["vpc_VerToken"]         : "No Value Returned";
		$verSecurLevel   = array_key_exists("vpc_VerSecurityLevel", $data) ? $data["vpc_VerSecurityLevel"] : "No Value Returned";
		$enrolled        = array_key_exists("vpc_3DSenrolled", $data)      ? $data["vpc_3DSenrolled"]      : "No Value Returned";
		$xid             = array_key_exists("vpc_3DSXID", $data)           ? $data["vpc_3DSXID"]           : "No Value Returned";
		$acqECI          = array_key_exists("vpc_3DSECI", $data)           ? $data["vpc_3DSECI"]           : "No Value Returned";
		$authStatus      = array_key_exists("vpc_3DSstatus", $data)        ? $data["vpc_3DSstatus"]        : "No Value Returned";		
		*/		
		
		if(!$errorExists && $this->getData("vpc_TxnResponseCode") == "0" && $this->_getOrder()->getState() == Mage_Sales_Model_Order::STATE_PROCESSING)
		{	
	
			
			$payment = $this->_getOrder()->getPayment();	
			
			Mage::getSingleton('core/session')->setTransactionNo($this->getData('vpc_ReceiptNo'));
		
			$payment->setTransactionId($this->getData('vpc_ReceiptNo'))		
			
			->setParentTransactionId($this->getData('vpc_MerchTxnRef'))	
			
			->setShouldCloseParentTransaction(false)	
			
			->setIsTransactionClosed(false)		
			
			->setAdditionalInformation('TransactionNo:'.$this->getData('vpc_TransactionNo').PHP_EOL.'ReceiptNo:'.$this->getData('vpc_ReceiptNo').PHP_EOL.'Message:'.$this->getData('vpc_Message'))					
			
			->setStatus($orderStatus);		

			$payment->setPreparedMessage('');		
			
			$this->_getOrder()->save();				
			
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
			
			
			$transactionSave = Mage::getModel('core/resource_transaction')						
			->addObject($invoice)		
			
			->addObject($invoice->getOrder());		
			
			$transactionSave->save();	
			
			
			$this->sendemailto();
			
			
			Mage::dispatchEvent('unilab_payment_captured_after', array("order"=>$this->_getOrder()));		

			//$this->_registerPaymentCapture();		
			
			$this->getCoreSession()->addSuccess(Mage::helper('core')->__('Your transaction ID is '.$this->getData('vpc_ReceiptNo')))
									->setTxnId($this->getData('vpc_ReceiptNo'));
		}
		else
		{
			$errMsg = $this->getResponseDescription($txnResponseCode);
			
			$this->updatePayment(true,Mage_Sales_Model_Order::STATE_NEW);
	
			$this->_getOrder()->addStatusHistoryComment($errMsg);
			$this->_getOrder()->save();
						
			$orderViewURL = Mage::getUrl('sales/order/view', array('order_id' => $this->_getOrder()->getId()));			
			
			//$this->getCheckoutSession()->addError($errMsg.'. '.sprintf('Order has been cancelled (Reference: <a href="%s" alt="">%s</a>).', $orderViewURL, $this->getData('vpc_MerchTxnRef')));
			
			throw new Exception($hashValidated);
		}
		
	}
	
	protected function sendemailto()
	{
			
		$invoice = Mage::getModel('sales/service_order', $this->_getOrder())->prepareInvoice();		
		
		$invoice->getOrder()->setIsCustomerNotified(true);
		
		$invoice->getOrder()->setIsInProcess(true);
		
		$invoice->getOrder()->addStatusHistoryComment(
						Mage::helper('core')->__('Notified customer about invoice.')
					)
					->setIsCustomerNotified(true);
					
		$invoice->sendEmail();	

		if($this->_getOrder()->canInvoice())				
		{				   
	
			Mage::getModel('aonewebservice/order_sendtosap')->send($this->getData('vpc_MerchTxnRef'), "N");		
		
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
                ->setIsTransactionPending($isPending);
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
               // Mage::throwException(Mage::helper('core')->__('Cannot create an invoice.'));
            }
            $invoice = Mage::getModel('sales/service_order', $this->_getOrder())->prepareInvoice();
            if (!$invoice->getTotalQty()) {
               // Mage::throwException(Mage::helper('core')->__('Cannot create an invoice without products.'));
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
	

	/*
	 *	This method uses the QSI Response code retrieved from the Digital
	 *	Receipt and returns an appropriate description for the QSI Response Code
	 *
	 * @param $responseCode String containing the QSI Response Code
	 *
	 * @return String containing the appropriate description
	 *
	 **/
	public function getResponseDescription($responseCode) {
	    switch ($responseCode) {
	        case "0" : $result = "Transaction Successful"; break;
	        case "?" : $result = "Transaction status is unknown"; break;
	        case "1" : $result = "Unknown Error"; break;
	        case "2" : $result = "Bank Declined Transaction"; break;
	        case "3" : $result = "No Reply from Bank"; break;
	        case "4" : $result = "Expired Card"; break;
	        case "5" : $result = "Insufficient funds"; break;
	        case "6" : $result = "Error Communicating with Bank"; break;
	        case "7" : $result = "Payment Server System Error"; break;
	        case "8" : $result = "Transaction Type Not Supported"; break;
	        case "9" : $result = "Bank declined transaction (Do not contact Bank)"; break;
	        case "A" : $result = "Transaction Aborted"; break;
	        case "C" : $result = "Transaction Cancelled"; break;
	        case "D" : $result = "Deferred transaction has been received and is awaiting processing"; break;
	        case "F" : $result = "3D Secure Authentication failed"; break;
	        case "I" : $result = "Card Security Code verification failed"; break;
	        case "L" : $result = "Shopping Transaction Locked (Please try the transaction again later)"; break;
	        case "N" : $result = "Cardholder is not enrolled in Authentication scheme"; break;
	        case "P" : $result = "Transaction has been received by the Payment Adaptor and is being processed"; break;
	        case "R" : $result = "Transaction was not processed - Reached limit of retry attempts allowed"; break;
	        case "S" : $result = "Duplicate SessionID (OrderInfo)"; break;
	        case "T" : $result = "Address Verification Failed"; break;
	        case "U" : $result = "Card Security Code Failed"; break;
	        case "V" : $result = "Address Verification and Card Security Code Failed"; break;
	        default  : $result = "Unable to be determined"; 
	    }
	    return $result;
	}

	/*
	* This method uses the verRes status code retrieved from the Digital
	* Receipt and returns an appropriate description for the QSI Response Code
	*
	* @param statusResponse String containing the 3DS Authentication Status Code
	* @return String containing the appropriate description
	*/	
	public function getStatusDescription($statusResponse) {
	    if ($statusResponse == "" || $statusResponse == "No Value Returned") {
	        $result = "3DS not supported or there was no 3DS data provided";
	    } else {
	        switch ($statusResponse) {
	            Case "Y"  : $result = "The cardholder was successfully authenticated."; break;
	            Case "E"  : $result = "The cardholder is not enrolled."; break;
	            Case "N"  : $result = "The cardholder was not verified."; break;
	            Case "U"  : $result = "The cardholder's Issuer was unable to authenticate due to some system error at the Issuer."; break;
	            Case "F"  : $result = "There was an error in the format of the request from the merchant."; break;
	            Case "A"  : $result = "Authentication of your Merchant ID and Password to the ACS Directory Failed."; break;
	            Case "D"  : $result = "Error communicating with the Directory Server."; break;
	            Case "C"  : $result = "The card type is not supported for authentication."; break;
	            Case "S"  : $result = "The signature on the response received from the Issuer could not be validated."; break;
	            Case "P"  : $result = "Error parsing input from Issuer."; break;
	            Case "I"  : $result = "Internal Payment Server system error."; break;
	            default   : $result = "Unable to be determined"; break;
	        }
	    }
	    return $result;
	}
	
	/*
	 * If input is null, returns string "No Value Returned", else returns input
	 */
	public function null2unknown($data) {
	    if ($data == "") {
	        return "No Value Returned";
	    } else {
	        return $data;
	    }
	}
	
}