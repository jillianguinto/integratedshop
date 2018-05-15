<?php 
class Unilab_Webservice_TestapiController extends Mage_Core_Controller_Front_Action
{ 

	protected function _construct()
	{	
	
		header("Access-Control-Allow-Origin: *");
		
		header("Access-Control-Allow-Headers: origin, x-requested-with, content-type");
		
		header("Access-Control-Allow-Methods: PUT, GET, POST");
		

	print_r($_POST);
    die();			
	}

	public function indexAction() 
	{
		
	
    
		
	}
	
	
	
}
