<?php 
/*
 * Redirect 
 * 
 * @date: 2013-09-19
 * @author: diszo.sasil (diszo.sasil@movent.com) - Movent Inc.
 */
class Unilab_Ipay88_Block_Redirect extends Mage_Core_Block_Abstract
{
	
	const _PAYMENT_FORM = 'ipay88_form_checkout';
	const _PAYMENT_METHOD = 'POST';
	
	
	protected function _toHtml()
    { 	 
		$config =  Mage::getModel('ipay88/method_payment');
		
        $form = new Varien_Data_Form();
        $form->setAction($config->getConfig()->getPaymentUrl())
            ->setId(self::_PAYMENT_FORM)
            ->setName(self::_PAYMENT_FORM)
            ->setMethod(self::_PAYMENT_METHOD)
            ->setUseContainer(true);
		
		$conf = $config->getPaymentCheckoutRequest();
				
		if(isset($conf["MerchantKey"])){
			unset($conf["MerchantKey"]);	
		}			
			
        foreach ($conf as $field=>$value) {
            $form->addField($field, 'hidden', array('name'=>$field, 'value'=>$value));						
        }
		
        $idSuffix = Mage::helper('core')->uniqHash();
        $submitButton = new Varien_Data_Form_Element_Submit(array(
            'value'    => $this->__('Click here if you are not redirected within 5 seconds...'),
        ));
		
        $id = self::_PAYMENT_FORM."_{$idSuffix}";
        $submitButton->setId($id);
		$submitButton->setName($id);
        $form->addElement($submitButton);
		
        $html = '<html><body>';
        $html.= '<div><b>'.$this->__('You will be redirected to iPay88 payment gateway in a few seconds.').'</b></div>';
        $html.= $form->toHtml();
        $html.= '<script type="text/javascript">setTimeout( function(){document.getElementById("'.$id.'").click();}, 5000);</script>';
        $html.= '</body></html>';

        return $html;
    }
    
}
