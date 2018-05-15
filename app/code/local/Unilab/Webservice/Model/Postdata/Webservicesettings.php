<?php 

class Unilab_Webservice_Model_Postdata_Webservicesettings extends Mage_Core_Model_Abstract    
{

	public function webservicesettings($tokendata)
	{
		$storeid = Mage::app()->getStore()->getStoreId();//Mage::getSingleton('core/session')->getStoreid();

		$settings 				= array();
		$settings['gateway'] 	= Mage::getStoreConfig('webservice/sitesettings/sitegateway', $storeid); 
		$settings['token']		= Mage::getStoreConfig('webservice/sitesettings/sitetoken', $storeid);	
		$settings['sitehost']	= 'http://' . $_SERVER['HTTP_HOST'];	
		
		return $settings;
		
	}
}