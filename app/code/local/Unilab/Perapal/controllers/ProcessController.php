<?php 
/*
KBANK
 */
class Unilab_Perapal_ProcessController extends Mage_Core_Controller_Front_Action
{
 
    public function IndexAction()
    {       

                 
        // $this->_getCheckout()->setPaymentQuoteId($this->_getCheckout()->getQuoteId());
        // die();
        // $this->getResponse()->setBody($this->getLayout()->createBlock('bpisecurepay/redirect')->toHtml());
        // $this->_getCheckout()->unsQuoteId();
        // $this->_getCheckout()->unsRedirectUrl();
         $this->_redirect('checkout/onepage/success', array('_secure'=>true));

        //          $this->loadLayout();          

 

        // $block = $this->getLayout()->createBlock(

        //     'Mage_Core_Block_Template',

        //     'ulahevents',

        //     array('template' => 'ulahevents/sites.phtml')

        // );

        

        // $this->getLayout()->getBlock('root')->setTemplate('page/unilab-2columns-right.phtml');

        // $this->getLayout()->getBlock('content')->append($block);

        // $this->_initLayoutMessages('core/session'); 

        // $this->renderLayout();


         /**send email 

         **/
    }
  
}
