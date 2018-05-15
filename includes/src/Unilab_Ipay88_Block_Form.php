<?php
class Unilab_Ipay88_Block_Form extends Mage_Payment_Block_Form{

	protected function _construct() {
		parent::_construct();
		$this -> setTemplate('ipay88/payment/form.phtml');
	}
}