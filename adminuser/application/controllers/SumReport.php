<?php
defined('BASEPATH') OR exit('No direct script access allowed');


//controller
class SumReport extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
			
		$this->load->helper('islogged_helper');	
		$usersession 	= is_usersession();
		$baseurl = base_url();
		if(empty($usersession)):
			redirect($baseurl);
		endif;	
		$this->load->library('form_validation');
		$this->load->helper(array('form','url'));
		$this->load->model('Customermod');				
		$this->load->model('Promotionsmod');	
		$this->load->model('Productsmod');
		$this->load->model('Summod');
		$store_id  	= isstore_id();
		$this->store_id = $store_id;
		// echo $this->store_id;
		// die();
		
		
	}
	public function index()
	{
		
		$usersession 	= is_usersession();

		$baseurl = base_url();
		if(empty($usersession)):
			redirect($baseurl);
		endif;	
		$store_id  		= isstore_id();
		$getDefault 			= $this->Summod->getDefault();
		//$getDefault 			= $this->Summod->getDet0();
		$getdet 			= $this->Summod->getDet();
		$getallCustomers 		= $this->Customermod->getallCustomers($store_id);
		$getAllPromo 			= $this->Promotionsmod->getAllPromo($store_id);
		$customer_group 		= $this->Customermod->customer_group();
		$getProductslist 	 	= $this->Productsmod->getAllProductslist($store_id);

		$userData = array(
			'getallCustomers'	=>$getallCustomers,
			'getAllPromo' 		=> $getAllPromo,
			'getProductslist' 	=>$getProductslist,
		);

		$this->userData = $userData;
		$userData = array(
			'getDefault'	=>$getDefault,
		);

		$this->userData = $userData;
		//print_r($userData);
		$this->load->view('head/head');
		$this->load->view('sidebar/menu',$this->userData);
		$this->load->view('reports/sumReportView');
		$this->load->view('footer/footer');
	}

	function getYear($store_ids){


		return $this->Summod->getYear($store_ids);
	} 

	function getMonth($store_ids){

		return $this->Summod->getMonth($store_ids);
	} 

	function getGender($store_ids){


		return $this->Summod->getGender($store_ids);

	} 

	//getCountry
	function getCountry($store_ids){


		$getCountry = $this->Summod->getCountry($store_ids);
		return $getCountry;
	
	} 

	function getStatus($store_ids){


		$getStatus = $this->Summod->getStatus($store_ids);
		return $getStatus;	
		
	
	} 

	//getCity

	function getCity($store_ids){


		$getCity = $this->Summod->getCity($store_ids);
		return $getCity;

		
	
	} 
	function getPromo($store_ids){


		$getPromo = $this->Summod->getPromo($store_ids);
		return $getPromo;

		
	
	} 
	
	
	function postYear($v){
		echo 'year';
	}
		

	function viewSumm(){
	$store_id  	= $this->store_id;
		
		if(isset($_POST['year']))
		{		$year = $_POST['year'];	}
		else{$year = false ;}
		
		if(isset($_POST['gender'])){$gender = $_POST['gender'];}
		else{$gender = false ;}
		if(isset($_POST['sort'])){$sort = $_POST['sort'];}
		else{$sort = false ;}
		if(isset($_POST['month'])){$month = $_POST['month'];}
		else{$month = false ;}
		if(isset($_POST['city'])){$city = $_POST['city'];}
		else{$city = false ;}
		if(isset($_POST['status'])){$status = $_POST['status'];}
		else{$status = false ;}
		if(isset($_POST['promo'])){$promo = $_POST['promo'];}
		else{$promo = false ;}
		if(isset($_POST['sortBy'])){$sortBy = $_POST['sortBy'];}
		else{$sortBy = false ;}	
		if(isset($_POST['date1'])){$between = $_POST['date1'];}
		else{$between = false ;}
		if(isset($_POST['date2'])){$and = $_POST['date2'];}
		else{$and = false ;}
		// echo $between;
		// die();
		if(isset($_POST['submit'])){
		$getDefault 			= $this->Summod->getsum0($store_id,$month,$gender,$sort,$sortBy,$city,$status,$promo,$between,$and,$year);
		
		}
		else
		{
		$getDefault 			= $this->Summod->getsum0($store_id,0,0,0,0,0,0,0,0,0,0);
		}
		
		
		
		// $getdet 			 	= $this->Summod->getDett($store_id);
// echo 'asdasd';
		// die();
		$getallCustomers 		= $this->Customermod->getallCustomers($store_id);
		$getAllPromo 			= $this->Promotionsmod->getAllPromo($store_id);
		$customer_group 		= $this->Customermod->customer_group();
		$getProductslist 	 	= $this->Productsmod->getAllProductslist($store_id);

		$userData = array(
			'getallCustomers'	=>$getallCustomers,
			'getAllPromo' 		=> $getAllPromo,
			'getProductslist' 	=>$getProductslist
		);

		$this->userData = $userData;


		$userData = array(
			'getDefault'	=>$getDefault,
			'getYear'   	=>$this->getYear($store_id),
			'getMonth'		=>$this->getMonth($store_id),	
			'getGender'		=>$this->getGender($store_id),
			'getStatus'		=>$this->getStatus($store_id),
			'getCountry'	=>$this->getCountry($store_id),
			'getCity'		=>$this->getCity($store_id),
			'getPromo'		=>$this->getPromo($store_id),
			'yearPost'      => $year,
			'monthPost'      => $month,
			'genderPost'      => $gender,
			'statusPost'      => $status,
			'promoPost'      => $promo,
			'sortPost'      => $sort,
			'sortByPost'      => $sortBy,
			'betweenPost'      => $between,
			'andPost'      => $and,
			'cityPost'      => $city
		);



		$this->userData = $userData;
		
		
		// echo '<pre>'; 
		// //print_r($this->getYear());
		// print_r($userData);
		// die();

		$this->load->view('head/head');
		$this->load->view('sidebar/menu',$this->userData);
		$this->load->view('reports/sumReportView');
		$this->load->view('footer/footer');
	}

	function getdYear(){
		$true_store_id =  store_id(isstore_id());
		$getdYear= $this->Summod->getdYear($true_store_id);
		print_r($getdYear);	
	}	

	

	function Viewdetails(){
	
		
		$store_id  	= $this->store_id;
		if(isset($_POST['year']))
		{		$year = $_POST['year'];	}
		else{$year = false ;}
		
		if(isset($_POST['gender'])){$gender = $_POST['gender'];}
		else{$gender = false ;}
		if(isset($_POST['sort'])){$sort = $_POST['sort'];}
		else{$sort = false ;}
		if(isset($_POST['month'])){$month = $_POST['month'];}
		else{$month = false ;}
		if(isset($_POST['city'])){$city = $_POST['city'];}
		else{$city = false ;}
		if(isset($_POST['status'])){$status = $_POST['status'];}
		else{$status = false ;}
		if(isset($_POST['promo'])){$promo = $_POST['promo'];}
		else{$promo = false ;}
		if(isset($_POST['sortBy'])){$sortBy = $_POST['sortBy'];}
		else{$sortBy = false ;}	
		if(isset($_POST['itemName'])){$itemName = $_POST['itemName'];}
		else{$itemName = false ;}	
		if(isset($_POST['date1'])){$between = $_POST['date1'];}
		else{$between = false ;}
		if(isset($_POST['date2'])){$and = $_POST['date2'];}
		else{$and = false ;}

		
		//

		$usersession 	= is_usersession();
		// $sid =  store_id(isstore_id());
		
		$baseurl = base_url();
		if(empty($usersession)):
			redirect($baseurl);
		endif;	


		
		if(isset($_POST['submit'])){
		// echo $month;
		// die();
		
		// $getdet 			= $this->Summod->getDett($store_id,"$month","$gender","$sort","$sortBy","$city","$status","$promo","$between","$and","$year","$itemName");
		$getdet 			= $this->Summod->getDett($store_id,$month,$gender,$sort,$sortBy,$city,$status,$promo,$between,$and,$year,$itemName);
		// print_r($getdet);
		// die();
				// echo $month;
		// die();
		
		}
		else
		{
		 // echo $between;
		// die();
		$getdet 			= $this->Summod->getDett($store_id,0,0,0,0,0,0,0,0,0,0,0);
		// $getDefault 			= $this->Summod->getsum0($store_id,0,0,0,0,0,0,0,0,0,0);
		}
		
		
		 $getallCustomers 		= $this->Customermod->getallCustomers($store_id);


		
		$getAllPromo 			= $this->Promotionsmod->getAllPromo($store_id);


		$customer_group 		= $this->Customermod->customer_group();


		$getProductslist 	 	= $this->Productsmod->getAllProductslist($store_id);
		
	$userData = array(
			'getallCustomers'	=>$getallCustomers,
			'getAllPromo' 		=> $getAllPromo,
			'getProductslist' 	=>$getProductslist
			//'customer_group'	=> $customer_group,

		);
		$this->userData = $userData;
			$userData = array(
			'getdet'	=>$getdet,
			'getdYear' 	=>$this->Summod->getdYear($store_id),
			'getdMonth'	=>$this->Summod->getdMonth($store_id),
			'getdPaymentStatus'	=>$this->Summod->getdPaymentStatus($store_id),
			// 'getdGender'		=>$this->Summod->getdGender($store_id)	
			'getdCountry'	=> $this->Summod->getdCountry($store_id),
			'getdStatus' => $this->Summod->getdStatus($store_id),
			'getdCity'=>$this->Summod->getdCity($store_id),
			'getdPromo' => $this->Summod->getdPromo($store_id),
			'getdItemName' => $this->Summod->getdItemName($store_id),
			'yearPost'      => $year,
			'monthPost'      => $month,
			'genderPost'      => $gender,
			'statusPost'      => $status,
			'promoPost'      => $promo,
			'sortPost'      => $sort,
			'sortByPost'      => $sortBy,
			'betweenPost'      => $between,
			'andPost'      => $and,
			'cityPost'      => $city,
			'itemNamePost' => $itemName
		);


	
	

		$this->userData = $userData;
	
		$this->load->view('head/head');
		$this->load->view('sidebar/menu',$this->userData);
		$this->load->view('reports/Detrepview');
		$this->load->view('footer/footer');
		
	}
	
	
	function exportToExcel(){
	
	
		$data = $_POST['data'];
		
		$this->load->dbutil();
        $this->load->helper('file');
        $this->load->helper('download');
		
        $filename = "detailedReport.csv";
		
		force_download($filename, $data);	
		
		// die();
		
			// $file="detailsreport.csv";
			// header("Content-Type: application/vnd.ms-excel;charset=UTF-8");
			// header("Content-Transfer-Encoding: binary");
			// header("Content-Language: en");
			// header("Expires: 0");
			// header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			// header("Content-Disposition: attachment; filename=$file");
			// echo $data; 
			// header("Content-Disposition: attachment; filename=\"$filename\"");
			// header("Content-Type: application/vnd.ms-excel");
			// echo $data; 
			
	// header('Content-Disposition: attachment; filename='.$filename.'.xls');
    // header('Content-type: application/force-download');
    // header('Content-Transfer-Encoding: binary');
    // header('Pragma: public');
    // print "\xEF\xBB\xBF"; // UTF-8 BOM
	// echo $data;
		
	}



	function detbyDate(){
		$store_id  		= isstore_id();
		$usersession 	= is_usersession();

		$baseurl = base_url();
		if(empty($usersession)):
			redirect($baseurl);
		endif;	

		$from =  $_POST['datepicker1'];
		$to   =  $_POST['datepicker2'];

		$getDefault 			= $this->Summod->getDetbydate($from,$to);

		$getallCustomers 		= $this->Customermod->getallCustomers($store_id);
		$getAllPromo 			= $this->Promotionsmod->getAllPromo($store_id);
		$customer_group 		= $this->Customermod->customer_group();
		$getProductslist 	 	= $this->Productsmod->getAllProductslist($store_id);
		
		

		$userData = array(
			'getallCustomers'	=>$getallCustomers,
			'getAllPromo' 		=> $getAllPromo,
			'getProductslist' 	=>$getProductslist,
			//'customer_group'	=> $customer_group,
		);
	$this->userData = $userData;
	$userData = array(
	'getdet'	=>$getdet,
	);

		$this->userData = $userData;
		//print_r($userData);
		$this->load->view('head/head');
		$this->load->view('sidebar/menu',$this->userData);
		$this->load->view('reports/Detrepview');
		$this->load->view('footer/footer');
	}
	function sumbyDate(){
		$store_id  	= isstore_id();
		$usersession 	= is_usersession();
		$baseurl = base_url();

		$from =  $_POST['datepicker1'];
		$to   =  $_POST['datepicker2'];

		$getDefault 			= $this->Summod->getsummbyDate($from,$to);

		$getallCustomers 		= $this->Customermod->getallCustomers($store_id);
		$getAllPromo 			= $this->Promotionsmod->getAllPromo($store_id);
		$customer_group 		= $this->Customermod->customer_group();
		$getProductslist 	 	= $this->Productsmod->getAllProductslist($store_id);
		
		

		$userData = array(
			'getallCustomers'	=>$getallCustomers,
			'getAllPromo' 		=> $getAllPromo,
			'getProductslist' 	=>$getProductslist,
			//'customer_group'	=> $customer_group,
		);

		foreach($customer_group as $customer_group_key=>$customer_group_value):
			foreach($customer_group_value as $k=>$val):
				$userData['customer_group'][$customer_group_key][$k] = $val;			
			endforeach;				
		endforeach;
		
	$this->userData = $userData;
		$userData = array(
			'getDefault'	=>$getDefault,
		);
		$this->userData = $userData;



		$this->load->view('head/head');
		$this->load->view('sidebar/menu',$this->userData);
		$this->load->view('reports/sumReportView');
		$this->load->view('footer/footer');
	}

	function exportSum(){
	/*
	$this->load->dbutil();
        $this->load->helper('file');
        $this->load->helper('download');
        $delimiter = ",";
        $newline = "\r\n";
        $filename = "summaryReport.csv";
	/*
        $query = "SELECT sql_query FROM cleansql_report where title='Order Summary'";
       	$resultt = $this->db->query($query);
       	$toCsv = $resultt->result();
       	$userData = $toCsv;
       	//print_r($userData[0]->sql_query);
       	//die();
       	*/
       	/*
       	$getDefault 		= $this->Summod->getsum0Export();
		$st			= $this->db->last_query();
       	//die();
       	//$toCsv = $this->db->query($userData[0]->sql_query);
       	$toCsv = $this->db->query($st);
       	
        $data = $this->dbutil->csv_from_result($toCsv, $delimiter, $newline);
       	// print_r($data);
       	//die();
        force_download($filename, $data);
        */
	}
	
	function exportDet()
	{
        $this->load->dbutil();
        $this->load->helper('file');
        $this->load->helper('download');
        $delimiter = ",";
        $newline = "\r\n";
        $filename = "detailedReport.csv";
        //$query = "SELECT sql_query FROM cleansql_report where title='Order Details'";
       	//$resultt = $this->db->query($query);
       	$getDefault	= $this->Summod->getDett();
		$st	   = $this->db->last_query();
       	$toCsv = $this->db->query($st);
       	//$toCsv = $resultt->result();
       	//$userData = $toCsv;
       	//$toCsv = $this->db->query($userData[0]->sql_query);
       	//print_r($toCsv->result_array);
       	//die();
        $data = $this->dbutil->csv_from_result($toCsv, $delimiter, $newline);
        force_download($filename, $data);
	}
	function testFun(){
	$this->load->dbutil();
        $this->load->helper('file');
        $this->load->helper('download');
	$query = "SELECT sql_query FROM cleansql_report where title='Order Details'";
       	$resultt = $this->db->query($query);
       	$toCsv = $resultt->result();
       	$userData = $toCsv;
       	$toCsv = $this->db->query($userData[0]->sql_query);
       	echo '<pre>';
       	print_r($toCsv->result());
       	die();
	}
	
}