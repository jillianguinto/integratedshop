<?php

/**
 * DragonPay Payment Method
 *
 *
 * @date    2013-02-06
 * @author  diszo.sasil (diszo.sasil@movent.com)
 *
 */
class Unilab_DragonPay_Model_Standard extends Mage_Payment_Model_Method_Abstract
{
    const PROD_GATEWAY_URL = "https://secure.dragonpay.ph/Pay.aspx";
    const TEST_GATEWAY_URL = "http://test.dragonpay.ph/Pay.aspx";
    const SHA1_PREFIX    = "X2";

    protected $_code = 'dragonpay';
    protected $_formBlockType = 'dragonpay/form';
    protected $_config = null;


    protected $_isGateway               = true;
    protected $_canAuthorize            = false;	## Auth Only
    protected $_canCapture              = true;	    ## Sale, Capture
    protected $_canCapturePartial       = true;
    protected $_canRefund               = true;
    protected $_canVoid                 = true;
    protected $_canUseInternal          = true;     ## Creation of a transaction from the admin panel
    protected $_canUseCheckout          = true;


    /**
     * Get dragonpay session namespace
     *
     * @return Mage_Paypal_Model_Session
     */
    public function getSession()
    {
        return Mage::getSingleton('dragonpay/session');
    }

    /**
     * Get checkout session namespace
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Get current quote
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }

    /**
     * Send authorize request to gateway
     */
    public function authorize(Varien_Object $payment, $amount)
    {
        /*
         * TODO: Send Billing Information
         */
        return $this;
    }

    /**
     * Return Order place redirect url
     *
     * @return string
     */

    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('dragonpay/standard/redirect', array('_secure' => true));
    }

    /**
     * Return form field array
     *
     * @return array
     */
    public function getStandardCheckoutFormFields()
    {
        $orderIncrementId = $this->getCheckout()->getLastRealOrderId();
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);

        $result = array();
        $result["merchantid"] = Mage::getStoreConfig('payment/dragonpay/merchantid');
        $result["txnid"] =  $orderIncrementId;
        $result["amount"] = sprintf("%.2f",$order->getGrandTotal());
        $result["ccy"] = $order->getBaseCurrencyCode();
        $result["description"] = $this->getOrderDescription($order);
        $result["email"] = $order->getCustomerEmail();

        $tmpResult = $result;
        $result["digest"] = $this->getSHA1Digest($tmpResult);

        return $result;
    }


    public function getSHA1Digest($params=array()){
        $digester=array();
        foreach($params as $value){
            $digester[] = $value; //.self::SHA1_PREFIX;
        }
        $digester[] = Mage::getStoreConfig('payment/dragonpay/merchantpaswd');
        $merge = implode(":",$digester);
        return sha1($merge);
    }

    public function getOrderDescription($order){
        $orderUrl = Mage::getUrl('sales/order/view',array('order_id' => $order->getId()));
        $message = sprintf( 'Payment from '.Mage::getStoreConfig('general/store_information/name',Mage::app()->getStore()->getId()).'. Order Ref#:%s',$order->getIncrementId());
        $description = Mage::getStoreConfig('payment/dragonpay/payment_description');
        if(empty($description)){
            $description = $message;
        }
        return $description;
    }

}