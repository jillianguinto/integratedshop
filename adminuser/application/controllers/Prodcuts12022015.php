<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends CI_Controller {

		
	public function __construct()
	{
		parent::__construct();

		//$this->load->helper(array('form','url'));
		$this->load->helper('islogged_helper');	
       	$this->load->library('form_validation');

		$this->load->model('Customermod');		
		$this->load->model('Productsmod');	
		$this->load->model('Categoriesmod');		
	
	}
	 
	public function index()
	{	
				
		$store_id  		= isstore_id();
		$usersession 	= is_usersession();		
		
		$baseurl = base_url();
		if(empty($usersession)):
			redirect($baseurl);
		endif;		

		
		$getallCustomers 	 = $this->Customermod->getallCustomers($store_id);
		//$getAllProducts 	 = $this->Productsmod->getAllProducts($store_id);
		$getProductslist 	 = $this->Productsmod->getAllProductslist($store_id);
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
		$queMe = $this->db->last_query();
        $this->load->dbutil();
        $this->load->helper('file');
        $this->load->helper('download');
        $delimiter = ",";
        $newline = "\r\n";
        $filename = "products.csv";

       	$result = $this->db->query($queMe);
        $data = $this->dbutil->csv_from_result($result, $delimiter, $newline);
        force_download($filename, $data);
        //$tocsv =  $this->dbutil->csv_from_result($getAllcustomer, $delimiter, $newline);
        //force_download($filename, $tocsv);
	}


	public function view()
	{
		$wid=isstore_id();
		$id =  astore_id($wid);		
	
		$store_id  		= isstore_id();
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

		//$userData['products_view'][0]->websiteids = $store_id;  
		if($id == 1){
			//mainsite
			$userData['root']['maincat'] = $this->Categoriesmod->getMainsiteRootcategory(NULL);
		//echo $this->db->last_query();

		}else{
			//specific store
			$userData['root']['maincat'] 		= $this->Productsmod->getRootcategory($id);	
			//echo $this->db->last_query();
		}		
		
		
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
		$wid=isstore_id();
		$id =  astore_id($wid);


		// echo $id; 
		// die();
		
		$this->load->model('Productsmod');	
		$store_id  		= isstore_id();
		$usersession 	= is_usersession();	

		$userData['settings'] = $_POST;
		$userData['settings']['websiteids'] = $id;	

		if($id == 1){
			//mainsite
			$userData['root']['maincat'] = $this->Categoriesmod->getMainsiteRootcategory(NULL);
		//echo $this->db->last_query();

		}else{
			//specific store
			$userData['root']['maincat'] = $this->Productsmod->getRootcategory($id);	
			//echo $this->db->last_query();
		}		

		//$data['root']['maincat']		= $this->Productsmod->getsubmaincategory($id);	
		//$get_subcatcatid 	= $this->Productsmod->getRootcategory($id);
		//$data['settings']['subcat'] = $get_subcatcatid;

			
		//echo '<pre>';
		
		//print_r($data );
		// //echo $productId;
		//echo die();

		$this->load->view('head/head');
		$this->load->view('sidebar/menu', $userData);
		$this->load->view('include/products/add');
		$this->load->view('footer/footer');
		
	}


	
}
