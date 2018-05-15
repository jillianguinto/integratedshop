<?php 


class RRA_Oneshop_SwitcherController extends Mage_Core_Controller_Front_Action
{


	protected function _getConnection()
	{
		$this->_Connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		return $this->_Connection;
	} 

	public function authAction()
	{

		$params = $this->getRequest()->getParams();
		
		$separator = md5("unilab");
		
		$exp = explode($separator, $params['fb']);
		
		$referer = $_SERVER['HTTP_REFERER']; 
		
		$categoryid = $exp[0];	
		$token 		= $params['fb'];

                $referer = str_replace("l.","www.", $referer);

                if($referer == "m.facebook.com"){
                	$referer = str_replace("m.","www.", $referer);
                }
               
                $referer = str_replace("lm.","www.", $referer);
                $parseUrl = parse_url($referer);
                
                $websiteurl = $parseUrl['host'];
		
		$storename = Mage::getModel('webservice/function_store')->getStorename($categoryid);
		//$storeid 	= Mage::getModel('webservice/function_store')->getStoreid($storename);
		
		$storeid 	= Mage::app()->getStore()->getStoreId(); 
		
		$storename = strtolower($storename);
		$homepage =  str_replace(" ","-", $storename) . ".html";
		
		
		$qry 	= "SELECT * FROM rra_oneshop_websiteurl  WHERE categoryid='$categoryid' AND token='$token' and websiteurl='$websiteurl'";
		$result = $this->_getConnection()->fetchRow($qry);
		
		Mage::log($qry, null, "jillian.log");

		//Mage::log($websiteurl, null, "jillian.log");
	

		if(!empty($result['id'])){

			Mage::getSingleton('core/session')->setId($result['id']);
			Mage::getSingleton('core/session')->setCategoryId($result['categoryid']);
			Mage::getSingleton('core/session')->setToken($result['token']);
			Mage::getSingleton('core/session')->setHttpReferer($websiteurl);
			Mage::getSingleton('core/session')->setStoreid($storeid);
			Mage::getSingleton('core/session')->setHomepage($homepage);
			
			Mage::app()->setCurrentStore($storeid);
			Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getBaseurl() . $homepage);

		
		}else{

			//echo $this->__('Unauthorized Access');
            $redirectlink = Mage::getBaseurl()."oneshop/unauthorized";
            Mage::app()->getFrontController()->getResponse()->setRedirect($redirectlink);
		} 


		
	}	

}