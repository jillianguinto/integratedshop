<?php 
/*
 * Redirect 
 * 
 * @date: 2013-09-19
 * @author: diszo.sasil (diszo.sasil@movent.com) - Movent Inc.
 */
class Unilab_RcbcPay_Block_Redirect extends Mage_Core_Block_Abstract
{
	
	const _PAYMENT_FORM = 'rcbcpay_form_checkout';
	const _PAYMENT_METHOD = 'POST';
	
    protected function _toHtml()
    { 	 
		$config = Mage::getModel('rcbcpay/method_payment');
		
        $form = new Varien_Data_Form();
        $form->setAction( Mage::getUrl('rcbcpay/process/savepayment', array('_secure' => true)) )
            ->setName(self::_PAYMENT_FORM)
            ->setMethod(self::_PAYMENT_METHOD)
            ->setUseContainer(true);
			
		$conf = $config->getCheckoutFormFields();
        foreach ($conf as $field=>$value) {
        	if (strlen($value) > 0) {
            	$form->addField($field, 'hidden', array('name'=>$field, 'value'=>$value));
			}
        }
		
        $html = '<html><body onload="document.'.self::_PAYMENT_FORM.'.submit()">';
        $html.= '<div><b>'.$this->__('You will be redirected to BPI Securepay payment gateway in a few seconds...Please wait.').'</b></div>';
        $html.= $form->toHtml();
        $html.= '</body></html>';

        return $html;
    }
}
