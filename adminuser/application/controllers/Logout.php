<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Logout extends CI_Controller {

		
	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');	
	}
	 
	public function index()
	{
		
		$this->load->model('Logoutmod');	
		$dataACcount = $this->Logoutmod->logoutccount();

		header('Content-Type: application/json');
		echo json_encode( $dataACcount );		

		
	}
}
