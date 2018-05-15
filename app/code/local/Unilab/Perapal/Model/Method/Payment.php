<?php
/***
KBANK 
 * 
 */
class Unilab_Perapal_Model_Method_Payment extends Mage_Payment_Model_Method_Abstract {

 
	protected $_code  = 'perapal';
    protected $_formBlockType           = 'perapal/form';

    protected $_infoBlockType           = 'perapal/info';

    protected $_order;
	 
	/**
	* Assign data to info model instance
	*
	* @param   mixed $data
	* @return  Mage_Payment_Model_Method_Checkmo
	*/
	public function assignData($data)
	{

		$details = array();
		if ($this->getPayableTo()) {
		$details['payable_to'] = $this->getPayableTo();
		}

		if ($this->getMailingAddress()) {
		$details['mailing_address'] = $this->getMailingAddress();
		}

		if (!empty($details)) {
		$this->getInfoInstance()->setAdditionalData(serialize($details));
		}



	return $this;

	}
	 
	public function getPayableTo()
	{

		return $this->getConfigData('payable_to');

	}
	 
	public function getMailingAddress()
	{

		return $this->getConfigData('mailing_address');

	}

	public function getOrderPlaceRedirectUrl()
  	{

  		
		return Mage::getUrl('checkout/onepage/success', array('_secure' => true));
  		//return Mage::getUrl('kbanktransfer/process/response', array('_secure' => false));
  	}
 
 // 	public function getReturnUrl() {
	// 	return Mage::getUrl('checkout/onepage/success', array('_secure' => true));
	// }
 // $this->_redirect('checkout/onepage/success', array('_secure'=>true));
  	// Mage::getUrl('bpisecurepay/process/response', array('_secure' => true));

}