<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Store extends CI_Controller {
		
	public function __construct()
	{
		parent::__construct();	
	}
	 
	public function index()
	{
		
		$this->load->model('Storemod');
/*
		$usersession = is_usersession();
		$baseurl = base_url();
		if(empty($usersession)):
			redirect($baseurl);
		endif;
*/
 	
		$getAllStore =$this->Storemod->getAllStore();
		$userData = array(
			'getAllStore'	=>$getAllStore
		);
		$this->load->view('include/liststore',$userData);
	}
}
