<?php
/*
 * @author: diszo.sasil
 * @email: diszo.sasil@movent.com
 * @date: 2013-11-21
 */
class Unilab_Perapal_Block_Form extends Mage_Payment_Block_Form{

	protected function _construct() {
		parent::_construct();
		$this -> setTemplate('perapal/payment/form.phtml');
	}
}