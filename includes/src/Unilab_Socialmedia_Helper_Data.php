<?php

class Unilab_Socialmedia_Helper_Data extends Mage_Core_Helper_Abstract
{
	protected $_socialmedia_path = 'socialmedia/mediaurls/';
	
	public function getMediaUrls()
	{
		$media_urls = array('facebook',
							'twitter',
							'youtube');
		
		$url_object = new Varien_Object();
		
		foreach($media_urls as $media_url){
			$url_object->setData($media_url,Mage::getStoreConfig($this->_socialmedia_path . $media_url, $this->getCurrentStoreId()));
		}		
		return $url_object;
	}
	
	protected function getCurrentStoreId()
	{
		return Mage::app()->getStore()->getId();
	}
}

