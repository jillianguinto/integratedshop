<?php 
/*
 * Redirect 
 * 
 * @date: 2013-09-19
 * @author: diszo.sasil (diszo.sasil@movent.com) - Movent Inc.
 */
class Unilab_BpiSecurepay_Block_Redirect extends Mage_Core_Block_Abstract
{
	
	const _PAYMENT_FORM = 'bpisecurepay_form_checkout';
	const _PAYMENT_METHOD = 'GET';
	
    protected function _toHtml()
    { 	 
		$config = Mage::getModel('bpisecurepay/method_payment');
		
        $form = new Varien_Data_Form();
        $form->setAction($config->getConfig()->getPaymentUrl())
            ->setName(self::_PAYMENT_FORM)
            ->setMethod(self::_PAYMENT_METHOD)
            ->setUseContainer(true);
			
		$conf = $config->getCheckoutFormFields();
		unset($conf["virtualPaymentClientURL"]); 
		ksort($conf);			
		
		$md5HashData = $config->getConfig()->getMerchantSecureHashSecret();
		
        foreach ($conf as $field=>$value) {
        	if (strlen($value) > 0) {        		
            	$form->addField($field, 'hidden', array('name'=>$field, 'value'=>$value));
				$md5HashData .= $value; // Concatenate none empty value for secure hash
			}
        }
		
		if (strlen($config->getConfig()->getMerchantSecureHashSecret()) > 0) {	
			$form->addField('vpc_SecureHash', 'hidden', array('name'=>'vpc_SecureHash', 'value'=>strtoupper(md5($md5HashData))));
		}
		
        $html = '<html><body onload="document.'.self::_PAYMENT_FORM.'.submit()">';
        $html.= '<div><b>'.$this->__('You will be redirected to BPI Securepay payment gateway in a few seconds...Please wait.').'</b></div>';
        $html.= $form->toHtml();
        $html.= '</body></html>';

        return $html;
    }
}
