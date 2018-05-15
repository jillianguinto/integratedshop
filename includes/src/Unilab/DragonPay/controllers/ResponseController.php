<?php
/*
 * @category    Movent
 * @package     Unilab_DragonPay
 * @author      Diszo Sasil (diszo.sasil@movent.com)
 * @copyright   Copyright (c) 2013 Movent Inc. (http://www.movent.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Unified Response controller for DragonPay
 */
class Unilab_DragonPay_ResponseController extends Mage_Core_Controller_Front_Action
{
	
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
     * Receive dragonpay postback data
     */
    public function indexAction()
    {
        $expectedKeys = array('txnid','refno','message','digest','status');
        $data = $this->getRequest()->getParams();

        if (count(array_keys($data)) != count($expectedKeys)){
            $this->_redirect('dragonpay/standard/cancel'); // Cancel the order
        }

        try {
            Mage::getModel('dragonpay/handler')->processRequest($data);
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError('DragonPay Error: '.$e->getMessage());
            Mage::logException($e);
			$this->_redirect('dragonpay/standard/failed'); 
        }
    }
}