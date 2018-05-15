<?php
/**
 * Common Config
 *
 * @category   Unilab
 * @package    Unilab_BpiSercurepay
 * @copyright  Unilab diszo.sasil@movent.com 
 * @name       Unilab_BpiSercurepay_Model_Config
 */
 
class Unilab_BpiSecurepay_Model_Config extends Mage_Payment_Model_Config {
	
	const _MEGS_PAYMENT_URL = 'https://migs.mastercard.com.au/vpcpay';
	const _MEGS_VPC_VERSION = 1;
	const _MEGS_VPC_COMMAND = 'pay';
	
	const _PAYMENT_DESCRIPTION = 'Payment from unilab onlinestore (Ref:%s)';
		
	
	protected $_transactionTypes = array(1 => 'Sales', 2 => 'Authorize');
	protected $_ipayment_config = 'payment/bpisecurepay/';
	
	
	public function getPaymentConfig() {
		$configs = array(	'active', 									
							'merchant_live', 
							'merchant_test', 
							'merchant_accesscode_live',							
							'merchant_accesscode_test',
							'merchant_secure_hash_secret_test',
							'merchant_secure_hash_secret_live',
							'enable_testmode',
						);

		$settings = new Varien_Object();
		foreach ($configs as $config_key) {
			$settings -> setData($config_key, $this -> getStoreConfig($config_key));
			;
		}


		//auto set URL, username, password
		if (!$settings->getEnableTestmode()) {		
			$settings->setMerchant($settings->getMerchantLive()) 
					->setMerchantAccesscode($settings->getMerchantAccesscodeLive())
					->setMerchantSecureHashSecret($settings->getMerchantSecureHashSecretLive())
					;							
		} else {			
			$settings->setMerchant($settings->getMerchantTest()) 
					 ->setMerchantAccesscode($settings->getMerchantAccesscodeTest())
					 ->setMerchantSecureHashSecret($settings->getMerchantSecureHashSecretTest())
					 ;
		}
		
		$settings->setPaymentUrl(self::_MEGS_PAYMENT_URL);
		$settings->setVpcVersion(self::_MEGS_VPC_VERSION);
		$settings->setVpcCommand(self::_MEGS_VPC_COMMAND);
		
        return $settings;
	}

	public function getCurrentStoreId() {
		return Mage::app() -> getStore() -> getId();
	}

	protected function getStoreConfig($config_id) {
		return Mage::getStoreConfig($this -> _ipayment_config . $config_id, $this -> getCurrentStoreId());
	}
	
}
	