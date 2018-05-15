<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Promotions extends CI_Controller {

		
	public function __construct()
	{
		parent::__construct();
		$this->load->library('form_validation');
		$this->load->helper(array('form','url'));

		$this->load->model('Customermod');				
		$this->load->model('Promotionsmod');	
		$this->load->model('Productsmod');
		
	}
	 
	public function index()
	{
		$store_id  		= isstore_id();
		$usersession 	= is_usersession();

		$baseurl = base_url();
		if(empty($usersession)):
			redirect($baseurl);
		endif;		

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
		//print_r($userData);

		$this->load->view('head/head');
		$this->load->view('sidebar/menu',$this->userData);
		$this->load->view('include/promotionlist');
		$this->load->view('footer/footer');

	}
	public function test(){
		$this->load->model('Promotionsmod');	
		$getAllPromo 			= $this->Promotionsmod->getAllPromo($store_id);
		print_r($getAllPromo);
	}
	public function view(){		
		$store_id  		= isstore_id();		// $websiteID = $this->Customermod->getWebsiteIds2($store_id);		// echo $store_id;		// die();
		$usersession 	= is_usersession();
		$baseurl = base_url();
		if(empty($usersession)):
		redirect($baseurl);
		endif;				
		if(isset($_POST['promoid'])):
			$promoid =  $_POST['promoid'];
		else:
			$promoid = 0;
		endif;
		$gettPromobyID = $this->Promotionsmod->getPromobyID($promoid,$store_id);		// print_r($gettPromobyID);		// die();		// echo $this->db->last_query();		// die();
		$getcgbyID 	   = $this->Promotionsmod->getcgbyID($promoid);

		//echo $gettPromobyID;
		//foreach($x as $y=>$z):
		//		$gettPromobyID[$w]->$y = $z;
		//endforeach;
		if(empty($gettPromobyID)):
			$baseurl = base_url().'index.php/Promotions';
		redirect($baseurl);
		endif;
		$userData = array();				
		foreach($gettPromobyID as $key=> $PromoView):
			$userData['promo_info'][$key] = $PromoView;
		endforeach;
		//print_r($userData);
		$customer_group = $this->Customermod->customer_group();	
		foreach($customer_group as $customer_group_key=>$customer_group_value):
			foreach($customer_group_value as $k=>$val):
				$userData['customer_group'][$customer_group_key][$k] = $val;			
			endforeach;				
		endforeach;	
		$ugroup = array();				
		foreach($getcgbyID as $key=> $cgroupv):
			$userData['groupsumm'][$key] = $cgroupv;
		endforeach;
		//print_r(expression)
		//print_r($ugroup);

		//print_r($customer_group);

		$this->load->view('head/head');
		$this->load->view('sidebar/menu',$userData);
		$this->load->view('include/promo/view');
		$this->load->view('footer/footer');
		//print_r($userData);

/*
echo "hohohahahoho";
		*/
	}

	public function update_promo(){
		//$this->load->library('form_validation');
		$this->form_validation->set_rules('rname', 'name','trim|required');
		$this->form_validation->set_rules('rdesc', 'description','trim|required');
		$this->form_validation->set_rules('datepicker1', 'date','trim|required');
		$this->form_validation->set_rules('datepicker2', 'date','trim|required');
		$this->form_validation->set_rules('rdisc', 'discount','trim|required');

		if($this->form_validation->run() == false)
	    {
			$errors = $this->form_validation->error_array();      
           
		    echo json_encode(array('st'=>0, 'msg' => json_encode($errors)));
	    }else{
	    	$items = array();
		    	foreach ($this->input->post('group_id') as $key => $value) {
		    	$items[] = $value;
		    	}

	    	if (count($items)>1){
	    		$it = array();
		    	foreach ($items as $key => $value) {
		    	$it[] = $value;
		    	}
	    	$update_promo = [
				'name' 				=> $this->input->post('rname'),
				'description'		=> $this->input->post('rdesc'),
				'from_date'			=> $this->input->post('datepicker1'),
				'to_date'			=> $this->input->post('datepicker2'),
				'simple_action'		=> $this->input->post('simple_id'),
				//'group_id'			=> $this->input->post('group_id'),
				'discount_amount'	=> $this->input->post('rdisc'),
				'is_active'			=> ($this->input->post('cb')=="on" ? "1" : "0")
			];
			//echo "heheh";
			//print_r($it);
			//die();
	    	$id = $this->input->post('rid');	
			$this->load->model('Promotionsmod');				
			$results = $this->Promotionsmod->update_promo2($id, $update_promo, $it);
			echo json_encode(array('st'=>1, 'msg' => 'Updated Promo'));

	    	}else{
	    		foreach ($this->input->post('group_id[]') as $key => $value) {
		    	$groupid = $value;
		    	}
	    	$update_promo = [
				'name' 				=> $this->input->post('rname'),
				'description'		=> $this->input->post('rdesc'),
				'from_date'			=> $this->input->post('datepicker1'),
				'to_date'			=> $this->input->post('datepicker2'),
				'simple_action'		=> $this->input->post('simple_id'),
				'group_id'			=> $groupid,//$this->input->post('group_id[]'),
				'discount_amount'	=> $this->input->post('rdisc'),
				'is_active'			=> ($this->input->post('cb')=="on" ? "1" : "0")
			];
			//print_r($update_promo);
			$id = $this->input->post('rid');	
	
			$results = $this->Promotionsmod->update_promo($id, $update_promo);
			//echo "hahahah";
			//print_r($results);
			echo json_encode(array('st'=>1, 'msg' => 'Updated Promo'));
	    	}
	    	
		 	
	    }
	}
}
