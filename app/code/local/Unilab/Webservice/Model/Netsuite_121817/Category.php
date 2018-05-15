<?php

class Unilab_Webservice_Model_Netsuite_Category extends Mage_Core_Model_Abstract
{
	protected function _getConnection()
	{
		$this->_Connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		return $this->_Connection;
	}
	
	public function create()
	{
		
		if($_POST['storeid'] !='' AND  $_POST['categoryid'] !='' AND $_POST['name'] !='')
		{
		
			try{
				
				Mage::app()->setCurrentStore($_POST['storeid']);
				$parentId = Mage::app()->getStore()->getRootCategoryId();
				
				$category = Mage::getModel('catalog/category')
							->setName($_POST['name'])
							->setUrlKey($_POST['name'])
							->setIsActive(1)
							->setDisplayMode('PRODUCTS')
							->setStoreId($_POST['storeid']);
							
				$parentCategory = Mage::getModel('catalog/category')->load($parentId);
				$category->setPath($parentCategory->getPath());
				
				$category->save();
				
				Mage::app()->setCurrentStore(1);
				
				$response['message'] = "Data was successfully saved.";
				$response['success']  = true;
				
			}catch(Exception $e){ 
			
				$response['message'] = $e->getMessage();
				$response['success']  = false;
		 
			} 	
			
		}else{
			
			$response['message'] = 'Required fields should not be null!';		
			$response['success'] = false;
		}
		
		return $response;
	}
}