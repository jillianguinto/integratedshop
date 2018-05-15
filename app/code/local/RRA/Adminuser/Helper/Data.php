<?php

class RRA_Adminuser_Helper_Data extends Mage_Catalog_Helper_Product
{
	
	
	//** Delete Update Add Products
	
	
	const ADD_PRODUCT 				= 'newProdCmd';

	const UPDATE_PRODUCT 			= 'updateProdCmd';

	const DELETE_PRODUCT 			= 'deleteProdCmd';

	const SHOW_PRODUCT 				= 'showProdCmd';


	
	//** Delete Update Add Customer	
	
	const ADD_CUSTOMER 				= 'newCusCmd';

	const UPDATE_CUSTOMER 			= 'updateCusCmd';

	const DELETE_CUSTOMER 			= 'deleteCusCmd';

	const SHOW_CUSTOMER 			= 'showCusCmd';	
	

	
	//** Delete Update Add Promotion
	
	const ADD_SHOPPING_RULE			= 'newShopRuleCmd';

	const UPDATE_SHOPPING_RULE		= 'updateShopRuleCmd';

	const DELETE_SHOPPING_RULE		= 'deleteShopRuleCmd';

	const SHOW_SHOPPING_RULE		= 'showShopRuleCmd';	
	
	
	const ADD_CATALOG_RULE			= 'newCatRuleCmd';

	const UPDATE_CATALOG_RULE		= 'updateCatRuleCmd';

	const DELETE_CATALOG_RULE		= 'deleteCatRuleCmd';

	const SHOW_CATALOG_RULE			= 'showCatRuleCmd';		
	


	public function getfilename($url){
	
		$filename = basename($url,"*");
		
		return $filename;
		
	}

	

	public function createImgDir($url=null, $filename=null){

		$dirname 		= Mage::getBaseDir('media');

		$firstletter	= $this->getfirstletter($filename);

		$seconndletter 	= $this->getsecondletter($filename);

		$path 			= $dirname . DS . "catalog/product". DS . $firstletter . DS . $seconndletter;
		

		if (!file_exists($path)) {

			mkdir($dirname . DS . "catalog/product". DS . $firstletter , 0777);

			mkdir($path, 0777);

		}			
		
		return $path;

	}

	

	public function getfirstletter($filename){

		return substr($filename, 0, 1);	

	}

	

	public function getsecondletter($filename){

		return substr($filename, 1, 1);	

	}

	

	public function createproductimage($url = null, $filename = null){
		

		$imgDirLoc 			= $this->createImgDir($url, $filename);

	//	$filename 			= $this->getfilename($filename);

		$filenameloc 		= $imgDirLoc . DS . $filename;

		$getimagecontent 	= file_get_contents($url);

		$fp 				= fopen($filenameloc, "w");
		
		fwrite($fp, $getimagecontent);

		fclose($fp);		

		$actualfileloc		= $this->getfirstletter($filename) . DS . $this->getsecondletter($filename) . DS . $filename;
		
		return $actualfileloc; 		

	}	

	
	
}