<?php 
/*
KBANK
 */
class Unilab_Bankdeposit_ProcessController extends Mage_Core_Controller_Front_Action
{
 
    public function IndexAction()
    {       

         $this->_redirect('checkout/onepage/success', array('_secure'=>true));

    }
  
}
