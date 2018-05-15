<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Login extends CI_Controller {
		
	public function __construct()
	{
		parent::__construct();
		
		$this->load->helper('url');	
	}
	 
	public function index()	{
		
		$this->load->model('Loginmod');
		
		$websiteid = $this->Loginmod->websiteidchecker();
		
		// echo $websiteid;
		// die();
		
		if($websiteid == false):
		
			$this->Loginmod->addcolumnadmin();
			
		endif;
		
		
		if(!empty($_POST['username'])):
		
		//die();
			$dataACcount = $this->Loginmod->loginaccount();
			
		endif;
		
		if(count($dataACcount) == 0 ):
		
			$dataACcount = array();
			
			$dataACcount['username'] = false;
			
		endif;
		
		//header('Content-Type: application/json');
		// print_r($dataACcount);
		echo json_encode( $dataACcount );	
			
	}

	public function loadMenuPage(){
	
		$this->load->view('sidebar/menu_page');
		
	}
}
