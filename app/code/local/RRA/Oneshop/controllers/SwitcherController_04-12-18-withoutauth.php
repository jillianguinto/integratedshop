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
		
		$websiteurl = $_SERVER['HTTP_REFERER']; 

		// if($websiteurl != "https://www.facebook.com/"){
			// $websiteurl = str_replace("www.","", $websiteurl);
		// }

		$categoryid = $exp[0];	
		$token 		= $params['fb'];
		
		
		Mage::log($websiteurl, null, "jillian.log");
		
		if (strpos($websiteurl, 'facebook') !== false) {

            $referer = $websiteurl; 
			$parseUrl = parse_url($referer);
			
			$parsehost = $parseUrl['host'];
			
			$hostexplode = explode('.', $parsehost);
			
			$host = $hostexplode[0];

			$paramArr = array('www', 'l', 'm', 'lm', 'free', 'web');
			
			if (in_array($host, $paramArr)) {
				$websiteurl = "www." . $hostexplode[1] . "." . $hostexplode[2] ;
			}

			// $referer = str_replace("l.","www.", $referer);
			// $referer = str_replace("m.","www.", $referer);
			// $referer = str_replace("lm.","www.", $referer);
			// $referer = str_replace("free.","www.", $referer);
			// $referer = str_replace("web.","www.", $referer);
			// $parseUrl = parse_url($referer);

			// $websiteurl = $parseUrl['host'];
		}else{
			$referer = $websiteurl; 
			$parseUrl = parse_url($referer);
			$websiteurl = $parseUrl['scheme'] . "://". $parseUrl['host'] . "/";
			
			$websiteurl = str_replace("www.","", $websiteurl);
		}


		Mage::log($parseUrl, null, "jillian.log");
		
		$storename = Mage::getModel('webservice/function_store')->getStorename($categoryid);
		//$storeid 	= Mage::getModel('webservice/function_store')->getStoreid($storename);
		
		$storeid 	= Mage::app()->getStore()->getStoreId(); 
		
		$storename = strtolower($storename);
		$homepage =  str_replace(" ","-", $storename) . ".html";
		
		
		$qry 	= "SELECT * FROM rra_oneshop_websiteurl  WHERE categoryid='$categoryid' AND token='$token' and websiteurl='$websiteurl'";
		$result = $this->_getConnection()->fetchRow($qry);
		
		Mage::log($qry, null, "jillian.log");

		if(!empty($result['id'])){

			Mage::getSingleton('core/session')->setId($result['id']);
			Mage::getSingleton('core/session')->setCategoryId($result['categoryid']);
			Mage::getSingleton('core/session')->setToken($result['token']);
			Mage::getSingleton('core/session')->setHttpReferer($websiteurl);
			Mage::getSingleton('core/session')->setStoreid($storeid);
			Mage::getSingleton('core/session')->setHomepage($homepage);
			
			Mage::app()->setCurrentStore($storeid);
			Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getBaseurl() . $homepage);
			
			//setcookie("username", "jillian");
			
			Mage::getModel('core/cookie')->set('oneshop', $storeid, time()+86400);

		
		}else{

			//echo $this->__('Unauthorized Access');
            //$redirectlink = Mage::getBaseurl()."/errors/unauthorized.php";
			// $redirectlink = 'http://' . $_SERVER['HTTP_HOST'] . "/errors/unauthorized.php";
			$errors = array();
			$errors['message'] 		= "Invalid Credentials";
			$errors['categoryid'] 	= $categoryid; 
			$errors['token'] 		= $token;
			$errors['websiteurl']	= $websiteurl;
			$errors['query']		= $qry;

			Mage::log($errors, null, "unauthorized.log");
			$redirectlink = 'http://' . $_SERVER['HTTP_HOST'];
            Mage::app()->getFrontController()->getResponse()->setRedirect($redirectlink);
		} 


		
	}	

}