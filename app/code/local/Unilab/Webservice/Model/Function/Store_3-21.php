<?php 

class Unilab_Webservice_Model_Function_Store extends Mage_Core_Model_Abstract    
{
	protected function _getConnection()
    {
		$this->_Connection = Mage::getSingleton('core/resource')->getConnection('core_write');	
        return $this->_Connection;
    }	
	
	public function redirect()
    {		
		$token = $_GET['token'];
		//$websiteurl =parse_url($_SERVER['HTTP_REFERER']); 
		//$hostname	= $websiteurl['host'];		
		
		//$hostname = $_SERVER['HTTP_REFERER'];
		
		$hostname = $_POST['sitehost'];
		
		
		if (strpos($hostname, 'facebook') !== false) 
		{
			$referer = $_SERVER['HTTP_REFERER']; //$hostname; 
			$parseUrl = parse_url($referer);
			
			$parsehost = $parseUrl['host'];
			
			$hostexplode = explode('.', $parsehost);
			
			$host = $hostexplode[0];

			$paramArr = array('www', 'lm', 'm', 'l', 'free', 'web');
			
			if (in_array($host, $paramArr)) {
				$hostname = "www." . $hostexplode[1] . "." . $hostexplode[2] ;
			}

			
		}else{
			$hostname = str_replace("www.","", $hostname);
		}
		
		
		$qry 	= "SELECT * FROM unilab_token WHERE token='$token' and hostname='$hostname' and isactive=1";
		$result = $this->_getConnection()->fetchRow($qry);
		
		$tokenid 	= $result['id'];
		$categoryid = $result['store']; 
		$storename 	= $this->getStorename($categoryid);
		$storeid 	= $this->getStoreid($storename);  

		if($tokenid){
			
			Mage::getSingleton('core/session')->setId($tokenid);
			Mage::getSingleton('core/session')->setCategoryId($categoryid);
			Mage::getSingleton('core/session')->setToken($token);
			Mage::getSingleton('core/session')->setHttpReferer($websiteurl);
			Mage::getSingleton('core/session')->setStoreid($storeid);		
			Mage::getSingleton('core/session')->setStorename($storename);	 		
			
			Mage::app()->setCurrentStore($storeid);
			Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getBaseurl());
		}else{
			Mage::app()->getFrontController()->getResponse()->setRedirect($_SERVER['HTTP_REFERER']);
		}
		
	}
	
	public function productview()
	{
		$link = $_POST['link'];
		
		$token = $_POST['token'];
		$websiteurl =parse_url($_SERVER['HTTP_REFERER']); 
		$hostname	= $websiteurl['host'];		
		
		$qry 	= "SELECT * FROM unilab_token WHERE token='$token' and hostname='$hostname' and isactive=1";
		$result = $this->_getConnection()->fetchRow($qry);
		
		$tokenid 	= $result['id'];
		$categoryid = $result['store']; 
		$storename 	= $this->getStorename($categoryid);
		$storeid 	= $this->getStoreid($storename);
		
		if($tokenid){
			
			Mage::getSingleton('core/session')->setId($tokenid);
			Mage::getSingleton('core/session')->setCategoryId($categoryid);
			Mage::getSingleton('core/session')->setToken($token);
			Mage::getSingleton('core/session')->setHttpReferer($websiteurl);	
			Mage::getSingleton('core/session')->setStoreid($storeid);		
			Mage::getSingleton('core/session')->setStorename($storename);	
			
			Mage::app()->setCurrentStore($storeid);
			Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getBaseurl().$link);
		}else{
			//echo $_SERVER['HTTP_REFERER'];
			Mage::app()->getFrontController()->getResponse()->setRedirect($_SERVER['HTTP_REFERER']);
		}
		
	}
	
	public function getStorename($categoryid)
	{
		$qry 	= "SELECT * FROM catalog_category_entity_varchar WHERE attribute_id=41 and entity_id='$categoryid'";
		$result = $this->_getConnection()->fetchRow($qry);
		
		$storename	= $result['value'];
		
		return $storename;
	}
	
	public function getStoreid($storename)
	{
		$qry 	= "SELECT * FROM core_store WHERE name='$storename'";
		$result = $this->_getConnection()->fetchRow($qry);
		
		$storeid	= $result['store_id'];
		
		return $storeid;
	}
	
	public function forgotpasswordRedirect()
    {		
		$token = $_GET['token'];
		$websiteurl =parse_url($_SERVER['HTTP_REFERER']); 
		$hostname	= $websiteurl['host'];		
		
		$RequestURL = $_SERVER['HTTP_REFERER'];
		
		if(empty($RequestURL)){

			$hostname	= $_SERVER['HTTP_HOST'];					
		}else{
			$RequestBaseurl = parse_url($RequestURL);

			$hostname	= $RequestBaseurl['host'];					
		}
		
		$qry 	= "SELECT * FROM unilab_token WHERE token='$token' and hostname='$hostname' and isactive=1";
		$result = $this->_getConnection()->fetchRow($qry);
		
		$tokenid 	= $result['id'];
		$categoryid = $result['store']; 
		$storename 	= $this->getStorename($categoryid);
		$storeid 	= $this->getStoreid($storename);  

		Mage::log($_GET, null, 'response_' . $_GET['cmdEvent'] . '_'. $date .'.log');

		if($tokenid){
			
			Mage::getSingleton('core/session')->setId($tokenid);
			Mage::getSingleton('core/session')->setCategoryId($categoryid);
			Mage::getSingleton('core/session')->setToken($token);
			Mage::getSingleton('core/session')->setHttpReferer($websiteurl);
			Mage::getSingleton('core/session')->setStoreid($storeid);		
			Mage::getSingleton('core/session')->setStorename($storename);	 		
			
			Mage::app()->setCurrentStore($storeid);
			Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getBaseurl()."customer/account/forgotpassword/");
		}else{
			Mage::app()->getFrontController()->getResponse()->setRedirect($_SERVER['HTTP_REFERER']);
		}
		
	}


	
	
}