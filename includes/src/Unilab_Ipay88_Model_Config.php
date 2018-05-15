<?php
/**
 * Common Config
 *
 * @category   Unilab
 * @package    Unilab_Ipay88
 * @copyright  Unilab diszo.sasil@movent.com 
 * @name       Unilab_Ipay88_Model_Config
 */
 
class Unilab_Ipay88_Model_Config extends Mage_Payment_Model_Config {
	
	const _IPAY88_ALLOWED_TESTING_AMOUNT = 15;
			
	protected $_transactionTypes = array(1 => 'Sales', 2 => 'Authorize');
	protected $_ipayment_config = 'payment/ipay88/';
	
	
	public function getPaymentConfig() {
		$configs = array(	'active', 
									'gatewayurl_live',
									'merchant_key_live', 
									'merchant_code_live', 
									'gatewayurl_test', 
									'merchant_key_test', 
									'merchant_code_test',
									'enable_testmode',
								);

		$settings = new Varien_Object();
		foreach ($configs as $config_key) {
			$settings -> setData($config_key, $this -> getStoreConfig($config_key));
			;
		}

		//auto set URL, username, password
		if (!$settings->getEnableTestmode()) {			
			$settings->setPaymentUrl($settings->getGatewayurlLive())			
					->setMerchantKey($settings->getMerchantKeyLive()) 
					->setMerchantCode($settings->getMerchantCodeLive())
					;							
		} else {			
			$settings->setPaymentUrl($settings->getGatewayurlTest())
					 ->setMerchantKey($settings->getMerchantKeyTest()) 
					 ->setMerchantCode($settings->getMerchantCodeTest());
		}
		
        return $settings;
	}

	public function getCurrentStoreId() {
		return Mage::app()->getStore()->getId();
	}

	protected function getStoreConfig($config_id) {
		return Mage::getStoreConfig($this->_ipayment_config . $config_id, $this->getCurrentStoreId());
	}


}
