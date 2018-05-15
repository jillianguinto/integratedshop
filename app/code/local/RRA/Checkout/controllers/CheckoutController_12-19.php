<?php 

// This page is create by Richel R. Amante for UNILAB use only.
// Please do not used this software and take with your own risk.

// Thank you!!!


class RRA_Checkout_CheckoutController extends Mage_Core_Controller_Front_Action
{
    public function onepageAction()
    {  
    	

		$totalItemsInCart = Mage::helper('checkout/cart')->getItemsCount();
		
		if ($totalItemsInCart > 0):	
		
			if(Mage::getSingleton('customer/session')->isLoggedIn()){
				$this->loadLayout();
				$this->renderLayout();
			}else{
				
				
				$siteenabled=  Mage::getStoreConfig("webservice/sitesettings/siteenabled");
				$checkouturl = Mage::getBaseUrl() . "rracheckout/checkout/onepage";
		
				if($siteenabled==1){
					
					$loginurl     = Mage::getStoreConfig("webservice/sitesettings/loginurl") . "&returnurl=" . $checkouturl;
					Mage::app()->getFrontController()->getResponse()->setRedirect($loginurl);
					
				}else{
					
					$url = Mage::getBaseurl()."customer/account/login/";
					Mage::app()->getResponse()->setRedirect($url)->sendResponse();
				}
				
			}

		else:
			
			$url = Mage::getBaseurl();
			Mage::app()->getResponse()->setRedirect($url)->sendResponse();
			
		endif;
		
    }	




}
