<?php
class Unilab_Fee_Model_Sales_Quote_Address_Total_Fee extends Mage_Sales_Model_Quote_Address_Total_Abstract{
		
	protected $_code = 'fee';

	public function collect(Mage_Sales_Model_Quote_Address $address)
	{
		parent::collect($address);

		$this->_setAmount(0);
		$this->_setBaseAmount(0);

		$items = $this->_getAddressItems($address);
		if (!count($items)) {
			return $this; //this makes only address type shipping to come through
		}

		$quote = $address->getQuote();

		if(Unilab_Fee_Model_Fee::canApply($address)){
				
			$exist_amount = $quote->getFeeAmount();
			
			$fee = Unilab_Fee_Model_Fee::getFee();
			//$balance = $fee - $exist_amount;

			$balance = $fee;

			//$this->_setAmount($balance);
			//$this->_setBaseAmount($balance);

			$address->setFeeAmount($balance);
			$address->setBaseFeeAmount($balance);
				
			$quote->setFeeAmount($balance);

			$address->setGrandTotal($address->getGrandTotal() + $address->getFeeAmount());
			$address->setBaseGrandTotal($address->getBaseGrandTotal() + $address->getBaseFeeAmount());
		}
	}

	public function fetch(Mage_Sales_Model_Quote_Address $address)
	{		
		/* $amt = $address->getFeeAmount();
		if(Unilab_Fee_Model_Fee::canApply($address)){		
			$address->addTotal(array(
					'code'=>$this->getCode(),
					'title'=>Mage::helper('fee')->__('Insurance'),
					'value'=> $amt
			));			
		} */
		return $this;
	}
}