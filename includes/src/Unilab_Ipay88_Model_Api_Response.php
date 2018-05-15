<?php

/**
 * Api Request
 *
 * @category   Unilab
 * @package    Unilab_Ipay88
 * @copyright  diszo.sasil@movent.com 
 * @name       Unilab_Ipay88_Model_Api_Response
 */
 
class Unilab_Ipay88_Model_Api_Response extends Varien_Object {
	
	/*
    * @param Mage_Sales_Model_Order
    */
    protected $_order = null;
		
	
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
}