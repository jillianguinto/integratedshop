<?php
/**
 * Created by JetBrains PhpStorm.
 * User: diszo.sasil
 * Date: 2/5/13
 * Time: 3:30 PM
 * To change this template use File | Settings | File Templates.
 */

class Unilab_DragonPay_StandardController extends Mage_Core_Controller_Front_Action
{

    /**
     * Order instance
     */
    protected $_order;

    /**
     *  Get order
     *
     *  @return	  Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if ($this->_order == null) {
        }
        return $this->_order;
    }

    /**
     * Send expire header to ajax response
     *
     */
    protected function _expireAjax()
    {
        if (!Mage::getSingleton('checkout/session')->getQuote()->hasItems()) {
            $this->getResponse()->setHeader('HTTP/1.1','403 Session Expired');
            exit;
        }
    }

    /**
     * Get singleton with dragonpay strandard order transaction information
     *
     * @return Mage_DragonPay_Model_Standard
     */
    public function getStandard()
    {
        return Mage::getSingleton('dragonpay/standard');
    }

    /**
     * When a customer chooses DragonPay on Checkout/Payment page
     *
     */
    public function redirectAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setDragonPayStandardQuoteId($session->getQuoteId());
        $this->getResponse()->setBody($this->getLayout()->createBlock('dragonpay/redirect')->toHtml());
        $session->unsQuoteId();
        $session->unsRedirectUrl();
    }

    /**
     * When a customer cancel payment from paygate.
     */
    public function cancelAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getDragonPayStandardQuoteId(true));
        if ($session->getLastRealOrderId()) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
            if ($order->getId() && ($order->getState() == Mage_Sales_Model_Order::STATE_PROCESSING || $order->getState() == Mage_Sales_Model_Order::STATE_NEW || $order->getState() == Mage_Sales_Model_Order::STATE_PENDING_PAYMENT) ) {
                $order->cancel()->save();
            }
        }
        $this->_redirect('checkout/cart');
    }

    /**
     * when dragonpay returns
     * The order information at this point is in POST
     * variables.  However, you don't want to "process" the order until you
     * get validation from the IPN.
     */
    public function  successAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getDragonPayStandardQuoteId(true));
        Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();		
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