<?php


echo 1; 

die();

defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

		
	public function __construct()
	{
		parent::__construct();

		$this->load->helper('islogged_helper');	
			$usersession 	= is_usersession();
		$baseurl = base_url();
		if(($usersession)):
				
		redirect($baseurl.'index.php/sumReport/Viewsumm');
		
		endif;	
		
		$store_id  	= isstore_id();

		$this->store_id = $store_id;
		
		
 		
	}
	 
	public function index()
	{
	
		$store_id = $this->store_id;
		
		$this->load->model('Storemod');
	        $this->load->model('Customermod');		
	        $this->load->model('Productsmod');		
		$this->load->model('Salesmod');	
		$this->load->model('Promotionsmod');
		$this->load->model('Summod');
		
		
	   $getallCustomers 		= $this->Customermod->getallCustomers($store_id);
	  

		//$getAllProducts 		= $this->Productsmod->getAllProducts($store_id);
		
		$getAllProducts 	 	= $this->Productsmod->getAllProductslist($store_id);
		
		$getAllsales 			= $this->Salesmod->getAllsales($store_id);
		$getAllshipping 		= $this->Salesmod->getAllshipping($store_id);
		$getAllinvoice 			= $this->Salesmod->getAllinvoice($store_id);
		$getAllPromotions 		= $this->Promotionsmod->getAllPromo($store_id);
		$getAllStore   			= $this->Storemod->getAllStore();

		$errormessage 			= null;
		

		$userData = array(
			'getallCustomers'	=>$getallCustomers,
			'getAllProducts' 	=> $getAllProducts,
			'getAllsales' 		=> $getAllsales,
			'getAllshipping' 	=> $getAllshipping,
			'getAllinvoice' 	=> $getAllinvoice,
			'getAllStore'		=> $getAllStore,
			'getAllPromotions' 	=> $getAllPromotions,
			'errormessage' 		=> $errormessage,
			
		);
		
		//print_r($userData);
		$this->load->view('head/head');
		$this->load->view('sidebar/menu',$userData);
		$this->load->view('index');
		$this->load->view('footer/footer');

	}

	public function test1()
	{
		// echo "test123";
		$this->load->model('Storemod');
		$this->load->model('Customermod');		
		$this->load->model('Productsmod');		
		$this->load->model('Salesmod');	
		$this->load->model('Promotionsmod');
		$this->load->model('Summod');
		
		$store_id  		= isstore_id();
		//$store_id=1;
		//echo $store_id;
		//$store_id  		= 0;

		$getallCustomers 		= $this->Customermod->getallCustomers($store_id);
		//$getAllProducts 		= $this->Productsmod->getAllProducts($store_id);
		$getAllProducts 	 	= $this->Productsmod->getAllProductslist($store_id);
		$getAllsales 			= $this->Salesmod->getAllsales($store_id);
		$getAllshipping 		= $this->Salesmod->getAllshipping($store_id);
		$getAllinvoice 			= $this->Salesmod->getAllinvoice($store_id);
		$getAllPromotions 		= $this->Promotionsmod->getAllPromo($store_id);
		$getAllStore   			= $this->Storemod->getAllStore();
		
		$errormessage 			= null;
		


		$userData = array(
			'getallCustomers'	=>$getallCustomers,
			'getAllProducts' 	=> $getAllProducts,
			'getAllsales' 		=> $getAllsales,
			'getAllshipping' 	=> $getAllshipping,
			'getAllinvoice' 	=> $getAllinvoice,
			'getAllStore'		=> $getAllStore,
			'getAllPromotions' 	=> $getAllPromotions,
			'errormessage' 		=> $errormessage,
			
		);
		
		//print_r($userData);
		$this->load->view('head/head');
		$this->load->view('sidebar/menu_kris',$userData);
		$this->load->view('index2');
		$this->load->view('footer/footer');
	}
	
	
}
