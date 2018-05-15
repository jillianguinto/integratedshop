<?php 
/*
 * @date: 2013-11-12
 * @author: diszo.sasil (diszo.sasil@movent.com)
 */
class Unilab_BpiSecurepay_ProcessController extends Mage_Core_Controller_Front_Action
{
 
	 /**
     * Send expire header to ajax response
     *
     */
    protected function _expireAjax()
    {
        if (!$this->_getCheckout()->getQuote()->hasItems()) {
            $this->getResponse()->setHeader('HTTP/1.1','403 Session Expired');
            exit;
        }
    } 
 
    /**
     * Get singleton of Checkout Session Model
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }  
	
	/*
	 * Reload Cart Items if transaction was canceled
	 */
	protected function reloadOrderItems()
	{
		$order = Mage::getModel('sales/order')->loadByIncrementId($this->_getCheckout()->getLastRealOrderId());
		if(is_object($order) && $order->getId()){
			$cart = Mage::getSingleton('checkout/cart');    		
			$items = $order->getItemsCollection();
			foreach ($items as $item) {
				try {
					$cart->addOrderItem($item,$item->getQty());
				}
				catch (Mage_Core_Exception $e){
					if ($this->_getCheckout()->getUseNotice(true)) {
						$this->_getCheckout()->addNotice($e->getMessage());
					}
					else {
						$this->_getCheckout()->addError($e->getMessage());
					}
				}
				catch (Exception $e) {
					$this->_getCheckout()->addException($e,
					Mage::helper('checkout')->__('Cannot add the item to shopping cart.')
					);
				}
			}
			$cart->save();
		}
		unset($order);
	}
	
	
	public function responseAction() 
	{
		try
		{						
			$process = Mage::getModel('bpisecurepay/api')
							->addData($_GET)
							->processResponse();
							
			 $this->getResponse()->setRedirect(Mage::getBaseUrl() . "bpisecurepay/process/success");
			
		}
		catch(Exception $e){
			
			/*			
			$this->reloadOrderItems();
			if ($this->_getCheckout()->getLastRealOrderId()) {
	            $order = Mage::getModel('sales/order')->loadByIncrementId($this->_getCheckout()->getLastRealOrderId());
	            if ($order->getId() && ($order->getState() == Mage_Sales_Model_Order::STATE_PROCESSING || $order->getState() == Mage_Sales_Model_Order::STATE_NEW || $order->getState() == Mage_Sales_Model_Order::STATE_PENDING_PAYMENT) ) {
	                $order->cancel()->save();
	            }
	        }
			 */
	        $this->_redirect('bpisecurepay/process/failed');
		}
	} 
 
    public function redirectAction()
    {       
        $this->_getCheckout()->setPaymentQuoteId($this->_getCheckout()->getQuoteId());
        $this->getResponse()->setBody($this->getLayout()->createBlock('bpisecurepay/redirect')->toHtml());
        $this->_getCheckout()->unsQuoteId();
        $this->_getCheckout()->unsRedirectUrl();
    }

    /**
     * When a customer cancel payment.
     */
    public function cancelAction()
    {
        $this->_getCheckout()->setQuoteId($this->_getCheckout()->getPaymentQuoteId(true));
        if ($this->_getCheckout()->getLastRealOrderId()) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($this->_getCheckout()->getLastRealOrderId());
            if ($order->getId() && ($order->getState() == Mage_Sales_Model_Order::STATE_PROCESSING || $order->getState() == Mage_Sales_Model_Order::STATE_NEW || $order->getState() == Mage_Sales_Model_Order::STATE_PENDING_PAYMENT) ) {
                $order->cancel()->save();
            }
        }
        $this->_redirect('checkout/cart');
    }

    /**
     * when BPI returns
     * The order information at this point is in GET
     * variables.  However, you don't want to "process" the order until you
     */
    public function successAction()
    {        
        $this->_getCheckout()->setQuoteId($this->_getCheckout()->getPaymentQuoteId(true));
        $this->_getCheckout()->getQuote()->setIsActive(false)->save();	
        $this->_redirect('checkout/onepage/success', array('_secure'=>true));
    }	
	
	public function failedAction()
    {
    	$this->_getCheckout()->setQuoteId($this->_getCheckout()->getPaymentQuoteId(true));
        $this->_getCheckout()->getQuote()->setIsActive(false)->save();	       
        $this->loadLayout();
        $this->renderLayout();
    }
  
}
