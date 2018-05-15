<?php
/*
 * @author: diszo.sasil (2013-02-07)
 *
 * DragonPay Postback processor model
*/
class Unilab_DragonPay_Model_Handler extends Mage_Payment_Model_Method_Abstract
{

    const PAYMENT_STATUS_SUCCESS = "completed";
    /*
    * @param Mage_Sales_Model_Order
    */
    protected $_order = null;


    /**
     *
     * @var Mage_Paypal_Model_Config
     */
    protected $_config = null;

    /**
     * PayPal info instance
     *
     * @var Mage_Paypal_Model_Info
     */
    protected $_info = null;

    /**
     * IPN request data
     * @var array
     */
    protected $_request = array();


    /**
     * postback/returned request data getter
     *
     * @param string $key
     * @return array|string
     */
    public function getRequestData($key = null)
    {
        if (null === $key) {
            return $this->_request;
        }
        return isset($this->_request[$key]) ? $this->_request[$key] : null;
    }

    /**
     * Get data from dragonpay and validate
     *
     * @param array $request
     * @throws Exception
     */
    public function processRequest(array $request)
    {
        $this->_request   = $request;
        $forDigest = $this->_request;
        unset($forDigest['digest']);

        $computedSha1 = Mage::getModel('dragonpay/standard')->getSHA1Digest($forDigest);

        if (count($this->_request['digest'])>0 && $computedSha1 != $this->_request['digest'])
        {
            throw new Exception("Invalid Postback Data");
        }
        else
        {
            try
            {
                $this->_getOrder();
                $this->_processOrder(); // TODO: Handle all type of dragonpay status and action

                // We redirect directly if failure
                if($this->_request['status'] == Unilab_DragonPay_Model_Info::STATUS_FAILURE){
                    Mage::getSingleton('core/session')->addError('DragonPay Error: '.$this->_request['message']);
                    Mage::app()->getFrontController()
                        ->getResponse()
                        ->setRedirect(Mage::getUrl('dragonpay/standard/cancel'));
                }else{
                    Mage::app()->getFrontController()
                        ->getResponse()
                        ->setRedirect(Mage::getUrl('dragonpay/standard/success'));
                }

            }catch(Exception $e){
                throw $e;
            }
        }
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
            $id = $this->_request['txnid'];
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
     * DragonPay Postback workflow implementation
     */
    protected function _processOrder()
    {
        $this->_order = null;
        $this->_getOrder();

        try {
            // handle payment_status
            $paymentStatus = $this->_request['status'];

            switch ($paymentStatus) {
                // paid
                case Unilab_DragonPay_Model_Info::STATUS_SUCCESS:
                    $this->_registerPaymentCapture();
                    break;
					
				case Unilab_DragonPay_Model_Info::STATUS_PENDING:
					//$this->_registerPaymentCapture();
                    break;
					
				/*
				// We only accept success status. otherwise throw silent exception
				
                case Unilab_DragonPay_Model_Info::STATUS_AUTHORIZED:
                    break;

                case Unilab_DragonPay_Model_Info::STATUS_FAILURE:
                    break;

                case Unilab_DragonPay_Model_Info::STATUS_CHARGEBACK:
                    break;              

                case Unilab_DragonPay_Model_Info::STATUS_REFUND:
                    break;

                case Unilab_DragonPay_Model_Info::STATUS_UNKNOWN:
                    break;

                case Unilab_DragonPay_Model_Info::STATUS_VOID:
                    break;
				*/

                default:
                    throw new Exception("Cannot handle payment status '{$paymentStatus}'.");
            }
        } catch (Mage_Core_Exception $e) {
            throw $e;
        }
    }

    /**
     * Process completed payment (either full or partial)
     */
    protected function _registerPaymentCapture()
    {
       
        $payment = $this->_order->getPayment();

        if ($this->_order->getState() == Mage_Sales_Model_Order::STATE_PROCESSING) {

            $payment->setTransactionId($this->getRequestData('txnid'))
                    ->setParentTransactionId($this->getRequestData('refno'))
                    ->setShouldCloseParentTransaction(false)
                    ->setIsTransactionClosed(false)
                    ->setAdditionalInformation($this->getRequestData('message'))
                    ->setStatus(Mage_Sales_Model_Order::STATE_PROCESSING)
                    ->setIsTransactionPending(false)
                    ->setPreparedMessage($this->_createIpnComment(''))
                    ;

            $this->_order->setDragonpayRefno($this->getRequestData('refno'));
            $this->_order->save();
			
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
				
                //START Handle Shipment
                //$shipment = $this->_order->prepareShipment();
                //$shipment->register();
                //$this->_order->setIsInProcess(true);
                //$this->_order->addStatusHistoryComment('Shipment Done.', false);
                //$transactionSave = Mage::getModel('core/resource_transaction')
                //    ->addObject($shipment)
                //    ->addObject($shipment->getOrder())
                //    ->save();
                //END Handle Shipment		
							
				
            }
            catch (Mage_Core_Exception $e) {
                $this->_order->addStatusHistoryComment('DragonPay: Exception occurred during _registerPaymentCapture() action. Exception message: '.$e->getMessage(), false);
                $this->_order->cancel()->save();
            }
        }
    }

    /**
     * Generate an additional explanation.
     * Returns the generated comment or order status history object
     *
     * @param string $comment
     * @param bool $addToHistory
     * @return string|Mage_Sales_Model_Order_Status_History
     */
    protected function _createIpnComment($comment = '', $addToHistory = false)
    {
        $paymentStatus = $this->getRequestData('message');
        $message = Mage::helper('core')->__('%s.', $paymentStatus);
        if ($comment) {
            $message .= ' ' . $comment;
        }
        if ($addToHistory) {
            $message = $this->_order->addStatusHistoryComment($message);
            $message->setIsCustomerNotified(null);
        }
        return $message;
    }
}