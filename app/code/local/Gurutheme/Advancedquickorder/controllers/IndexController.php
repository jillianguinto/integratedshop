<?php

class Gurutheme_Advancedquickorder_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        
        if(Mage::helper('advancedquickorder')->_getStoreConfig('general/loggedin') == 1)
        {
            if(!Mage::getSingleton('customer/session')->isLoggedIn()) {
                Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('customer/account/login/'));
                return;
            }
        }
       
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__(Mage::helper('advancedquickorder')->_getTitle()));
        $this->renderLayout();
      
    }
}
?>
