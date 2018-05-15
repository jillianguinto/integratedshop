<?php
/**
 * Redirect Block
 *
 * @category    Movent
 * @package     Unilab_DragonPay
 * @author      Diszo Sasil (diszo.sasil@movent.com)
 * @copyright   2013
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Unilab_DragonPay_Block_Redirect extends Mage_Core_Block_Abstract
{
    protected function _toHtml()
    {
        $standard = Mage::getModel('dragonpay/standard');

        $form = new Varien_Data_Form();

		$actionURL = Unilab_DragonPay_Model_Standard::PROD_GATEWAY_URL."?ts=".time();
        if(Mage::getStoreConfig('payment/dragonpay/test')){
            $actionURL = Unilab_DragonPay_Model_Standard::TEST_GATEWAY_URL."?ts=".time();
        }

        $form->setAction($actionURL)
            ->setId('dragonpay_standard_checkout')
            ->setName('dragonpay_standard_checkout')
            ->setMethod('POST')
            ->setUseContainer(true);

        foreach ($standard->getStandardCheckoutFormFields() as $field=>$value) {
            $form->addField($field, 'hidden', array('name'=>$field, 'value'=>$value));
        }
        $idSuffix = Mage::helper('core')->uniqHash();
        $submitButton = new Varien_Data_Form_Element_Submit(array(
            'value'    => $this->__('Click here if you are not redirected within 10 seconds...'),
        ));
        $id = "submit_to_dragonpay_button_{$idSuffix}";
        $submitButton->setId($id);
        $form->addElement($submitButton);
        $html = '<html><body>';
        $html.= $this->__('You will be redirected to the (DragonPay) website in a few seconds.');
        $html.= $form->toHtml();
        $html.= '<script type="text/javascript">document.getElementById("dragonpay_standard_checkout").submit();</script>';
        $html.= '</body></html>';

        return $html;
    }
}
