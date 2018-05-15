<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends CI_Controller {

		
	public function __construct()
	{
		parent::__construct();

		//$this->load->helper(array('form','url'));
		$this->load->helper('islogged_helper');	
       		$this->load->library('form_validation');
		$store_id  	= isstore_id();
		$this->load->model('Customermod');		
		$this->load->model('Productsmod');	
		$this->load->model('Categoriesmod');		
		$this->store_id = $store_id ;
		
	}
	 
	public function index()
	{	
				
		$store_id  = $this->store_id;
		
		$usersession 	= is_usersession();		
		
		$baseurl = base_url();
		if(empty($usersession)):
			redirect($baseurl);
		endif;		

		
		$getallCustomers 	 = $this->Customermod->getallCustomers($store_id);
		//$getAllProducts 	 = $this->Productsmod->getAllProducts($store_id);
		
		$getProductslist 	 = $this->Productsmod->getAllProductslist($store_id);
		// echo count($getProductslist);
		// die();
		$settings 			 = $this->Productsmod->create_product_settings($entity_type_id = 4);
		

		//$getProductslist 	 = $this->Productsmod->getAllProductslistings($store_id);
		
		$userData = array(
			'getallCustomers'	=>$getallCustomers,
			//'getAllProducts' 	=>$getAllProducts,
			'getProductslist' 	=>$getProductslist,
			'settings'			=>$settings 	
		);
		
		$this->load->view('head/head');
		$this->load->view('sidebar/menu',$userData);
		$this->load->view('include/listproducts');
		$this->load->view('footer/footer');
	}

	function exportProd()
	{
		$this->load->model('Productsmod');
		$store_id  		= isstore_id();
		$getAllProducts = $this->Productsmod->getAllProducts($store_id);
		$queMe 	 = $this->Productsmod->getAllProductslistExport($store_id);
		
        $this->load->dbutil();
        $this->load->helper('file');
        $this->load->helper('download');
        $delimiter = ",";
        $newline = "\r\n";
        $filename = "products.csv";

       	// $result = $this->db->query($queMe);
        $data = $this->dbutil->csv_from_result($queMe, $delimiter, $newline);
        force_download($filename, $data);
        //$tocsv =  $this->dbutil->csv_from_result($getAllcustomer, $delimiter, $newline);
        //force_download($filename, $tocsv);
	}


	public function view()
	{
		$id= $this->store_id;
		// $id =  astore_id($wid);	
		// echo $id;
		// die();
	
		$store_id  		= $this->store_id;
		$usersession 	= is_usersession();	
		
		$baseurl = base_url();
		if(empty($usersession)):
			redirect($baseurl);	
		endif;		

		if(isset($_POST['productId'])):
			$productId =  $_POST['productId'];
		else:
			$productId = 0;
		endif;	
		
		$getProductInformationById 	= $this->Productsmod->getProductInformationById($productId);	
			
		$userData = array();	
		if(!empty($getProductInformationById)){

			foreach($getProductInformationById as $key=> $productsView):
				$userData['products_view'][$key] = $productsView;
			endforeach;		
		}			

 
		// if($id == 1){
			// $userData['root']['maincat'] = $this->Categoriesmod->getMainsiteRootcategory(NULL);
		// }else{
			// $userData['root']['maincat'] 		= $this->Productsmod->getRootcategory($id);	
		// }		
	
		
		
		
		$catid 	= $this->Productsmod->prodCatId($productId);
		
		$i = 0;
		foreach($catid as $x=>$xx){
			$inc = $i++;
			$userData['category_id'][$inc] = $catid[$inc]['category_id'];
		}


		$this->load->view('head/head');
		$this->load->view('sidebar/menu',$userData);
		$this->load->view('include/products/view');
		$this->load->view('footer/footer');	

	}	


	public function add()
	{
		$id = $this->store_id;
		//$id =  astore_id($wid);  
		$this->load->model('Productsmod');	
		$store_id  		= $this->store_id;
		$usersession 	= is_usersession();	

		$userData['settings'] = $_POST;
		$userData['settings']['websiteids'] = $id;	

		// if($id == 1){
			// $userData['root']['maincat'] = $this->Categoriesmod->getMainsiteRootcategory(NULL);
		// }else{
			// $userData['root']['maincat'] = $this->Productsmod->getRootcategory($id);
		// }		


		$this->load->view('head/head');
		$this->load->view('sidebar/menu', $userData);
		$this->load->view('include/products/add');
		$this->load->view('footer/footer');
		
	}


	
}
