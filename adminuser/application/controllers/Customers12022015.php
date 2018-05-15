<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Customers extends CI_Controller {


		
	public function __construct()
	{
		parent::__construct();
		
		$this->load->helper(array('form','url'));
       	$this->load->library('form_validation');
		$this->load->model('Customermod');	
		
		$store_id  		= isstore_id();
		$usersession 	= is_usersession();

		$baseurl = base_url();
		if(empty($usersession)):
			redirect($baseurl);
		endif;			
		
		$this->store_id 	= $store_id;
	}


	// public function index()
	// {	
		 		
	// 	//$store_id  		= isstore_id();
	// 	$store_id  		= store_id(isstore_id());		
	// 	$usersession 	= is_usersession();
	// 	$baseurl = base_url();
	// 	if(empty($usersession)):
	// 		redirect($baseurl);
	// 	endif;
		
	// 	$getAllcustomer	= $this->Customermod->getcustomerinfo($store_id);
	// 	$errormessage 	= null;
	// 	$errortype 		= 'warning';

	// 	$userData = array(
	// 		'getAllcustomer' =>$getAllcustomer,
	// 		'errormessage'	 =>$errormessage,
	// 		'errortype'		 =>$errortype	
	// 	);	

	// 	$datax = array();
	// 	$i = 0;
	// 	foreach($getAllcustomer as $x=>$j){

	// 	 	$jx = $i++;
	// 		$ids  = $getAllcustomer[$jx]->entity_id;
		
	// 		$query = $this->db->query("SELECT * FROM log_customer WHERE customer_id = '{$ids}' ORDER BY login_at DESC LIMIT 1");

	// 		foreach ($query->result_array() as $row)
	// 		{
	// 		    $userData['getAllcustomer'][$jx]->last_login = $row['login_at'];			      
	// 		}	
	// 	}
		
	// 	//division
	// 	$get_division	= $this->Customermod->get_division();

	// 	foreach($get_division as $k=>$v){
	// 		$unilabdivision_option_id['option_id'][] 		= $get_division[$k]->option_id;
	// 		$unilabdivision_name['unilabdivision_name'][] 	= $get_division[$k]->unilabdivision_name;
	// 	}
	
	// 	$userData['get_division_data'] = array_merge($unilabdivision_option_id,$unilabdivision_name);	
	

	// 	//group
	// 	$get_group	= $this->Customermod->get_group();

	// 	foreach($get_group as $k=>$v){
	// 		$unilabgroup_name_option_id['option_id'][] 	= $get_group[$k]->option_id;
	// 		$unilabgroup_name['unilabgroup_name'][] 	= $get_group[$k]->unilabgroup_name;
	// 	}

	// 	$userData['get_group_data'] = array_merge($unilabgroup_name_option_id,$unilabgroup_name);	
		
	// 	// echo '<pre>';
	// 	// print_r($userData);

	// 	// die();
	// 	$this->load->view('head/head');
	// 	$this->load->view('sidebar/menu',$userData);
	// 	$this->load->view('include/customerlist');
	// 	$this->load->view('footer/footer');

	// }

	public function index()
	{	
		/*		
		isstore_id():			 used for website id
		store_id(isstore_id()):  used for store id	 
		*/
			 		
		$store_id  		= isstore_id(); 
		$usersession 	= is_usersession();
		$baseurl = base_url();
		if(empty($usersession)):
			redirect($baseurl);
		endif;
		
		$getAllcustomer	= $this->Customermod->getcustomerinfo($store_id);
		$errormessage 	= null;
		$errortype 		= 'warning';

		$userData = array(
			'getAllcustomer' =>$getAllcustomer,
			'errormessage'	 =>$errormessage,
			'errortype'		 =>$errortype
		
		);
	

		$datax = array();
		$i = 0;
		foreach($getAllcustomer as $x=>$j){

		 	$jx = $i++;
			$ids  = $getAllcustomer[$jx]->entity_id;
		
			$query = $this->db->query("SELECT * FROM log_customer WHERE customer_id = '{$ids}' ORDER BY login_at DESC LIMIT 1");

			foreach ($query->result_array() as $row)
			{
			    $userData['getAllcustomer'][$jx]->last_login = $row['login_at'];			      
			}	
		}

		// 	//division
		$get_division	= $this->Customermod->get_division();

		foreach($get_division as $k=>$v){
			$unilabdivision_option_id['option_id'][] 		= $get_division[$k]->option_id;
			$unilabdivision_name['unilabdivision_name'][] 	= $get_division[$k]->unilabdivision_name;
		}
	
		$userData['get_division_data'] = array_merge($unilabdivision_option_id,$unilabdivision_name);	
	

		//group
		$get_group	= $this->Customermod->get_group();

		foreach($get_group as $k=>$v){
			$unilabgroup_name_option_id['option_id'][] 	= $get_group[$k]->option_id;
			$unilabgroup_name['unilabgroup_name'][] 	= $get_group[$k]->unilabgroup_name;
		}

		$userData['get_group_data'] = array_merge($unilabgroup_name_option_id,$unilabgroup_name);	
			
		
		
		$this->load->view('head/head');
		$this->load->view('sidebar/menu',$userData);
		$this->load->view('include/customerlist');
		$this->load->view('footer/footer');
	}

		
	public function view()
	{
		
		/*		
		isstore_id():			 used for website id
		store_id(isstore_id()):  used for store id	 
		*/

		$store_id  		= isstore_id();
		//$store_id  		= store_id(isstore_id());
		$usersession 	= is_usersession();

		$baseurl = base_url();
		if(empty($usersession)):
			redirect($baseurl);
		endif;				
		
		if(isset($_POST['customerId'])):
			$customerId =  $_POST['customerId'];
		else:
			$customerId = 0;
		endif;		
				
		$customerId;		
				
		$getCustomerByCustomerId = $this->Customermod->get_account_information($customerId, $store_id); 
		$getCustomerByCustomerId[0]->address_information = $getCustomerAddressByCustomerId = $this->Customermod->get_customer_address($customerId);	

		$userData = array();				
		foreach($getCustomerByCustomerId as $key=> $customerView){
			$userData['customer_information'][$key] = $customerView;
		}	
		

		$j = 0;
		foreach($getCustomerByCustomerId as $jx=>$k){
			$xx = $j++;	

			//division
			$get_division	= $this->Customermod->get_division();

			$unilabdivision_option_id = array();
			$unilabdivision_name = array();

			foreach($get_division as $k=>$v){
				$unilabdivision_option_id['option_id'][] 		= $get_division[$k]->option_id;
				$unilabdivision_name['unilabdivision_name'][] 	= $get_division[$k]->unilabdivision_name;
			}
		
			$get_division_data = array_merge($unilabdivision_option_id,$unilabdivision_name);	
			$userData['customer_information'][$xx]->unilabdivision = $get_division_data;

			//group
			$get_group	= $this->Customermod->get_group();

			$unilabgroup_name_option_id = array();
			$unilabgroup_name = array();

			foreach($get_group as $k=>$v){
				$unilabgroup_name_option_id['option_id'][] 	= $get_group[$k]->option_id;
				$unilabgroup_name['unilabgroup_name'][] 	= $get_group[$k]->unilabgroup_name;
			}

			$get_group_data = array_merge($unilabgroup_name_option_id,$unilabgroup_name);	
			$userData['customer_information'][$xx]->unilabgroup = $get_group_data;
		}		

		$website_id = $this->Customermod->core_store();				
		foreach($website_id as $webid_key=>$webid_value){
			foreach($webid_value as $k=>$val){
				$userData['website_id'][$webid_key][$k] = $val;			
			}				
		}		
		
		$customer_group = $this->Customermod->customer_group();	
		foreach($customer_group as $customer_group_key=>$customer_group_value){
			foreach($customer_group_value as $k=>$val){
				$userData['customer_group'][$customer_group_key][$k] = $val;			
			}				
		}	


		$userData['divisiongroup'] = $this->Customermod->getSerialize($customerId);

		// echo '<pre>';
		// print_r($userData);
		// die();			

		$this->load->view('head/head');
		$this->load->view('sidebar/menu',$userData);
		$this->load->view('include/customer/view');
		$this->load->view('footer/footer');		
	}	


 
	public function add_customer_account(){	

		/*		
		isstore_id():			 used for website id
		store_id(isstore_id()):  used for store id	 
		*/

		$this->load->model('Customermod');	
	
		$store_id  		= isstore_id();
		$usersession 	= is_usersession();
		$baseurl = base_url();
		if(empty($usersession)):
			redirect($baseurl);
		endif;			
		
		$website_ids = isstore_id();						
		//customer account
		$this->form_validation->set_rules('firstname', 'First Name',  'trim|required'); 
		//$this->form_validation->set_rules('lastname', 'Last Name',  'trim|required'); 
		
		//$this->form_validation->set_rules('email', 'Email',  'trim|required'); 
		//$this->form_validation->set_rules('dob', 'Date of Birth',  'trim|required'); 
		//$this->form_validation->set_rules('gender', 'Gender',  'trim|required'); 
		//$this->form_validation->set_rules('civil_status', 'Civil Status',  'trim|required'); 
		//$this->form_validation->set_rules('password', 'Password',  'trim|required'); 

		//address
		//$this->form_validation->set_rules('address_street', 'Street Address',  'trim|required'); 
		//$this->form_validation->set_rules('address_city', 'City',  'trim|required'); 
		//$this->form_validation->set_rules('address_postcode', 'Zip/Postal Code',  'trim|required'); 

	    if($this->form_validation->run() == false)
	    {	     
			$errors = $this->form_validation->error_array();         
           
		    echo json_encode(array('st'=>0, 'msg' => json_encode($errors)));
	    }
	    else
	    {			
	    	############# customer_entity					
	    	$customer_entity = array(
					'entity_id' 				=> NULL,
					'entity_type_id'			=> 1,
					'attribute_set_id' 			=> 0,
					'website_id' 				=> $website_ids,
					'email' 					=> $this->input->post('email'),
					'group_id' 					=> $this->input->post('group_id'),
					'increment_id' 				=> '',
					'store_id' 					=> $store_id,
					'created_at' 				=> date("Y-m-d H:i:s", strtotime('now')),
					'updated_at' 				=> date("Y-m-d H:i:s", strtotime('now')),
					'is_active' 				=> 1,
					'disable_auto_group_change' => 0			
				);
			$insert_id = $this->Customermod->customer_entity($customer_entity);	


			$eav_attribute_customer_entity = array(						
				// 'prcnumber'					=> $this->input->post('prcnumber'),
				'disable_auto_group_change' => $this->input->post('disable_auto_group_change'),
				'prefix' 					=> $this->input->post('prefix'),
				'firstname'					=> $_POST['firstname'],
				'middlename'				=> $this->input->post('middlename'),
				'lastname'					=> $this->input->post('lastname'),
				'suffix' 					=> $this->input->post('suffix'),
				'dob'						=> $this->input->post('dob'),
				'gender' 					=> $this->input->post('gender'),
				'agree_on_terms' 			=> $this->input->post('agree_on_terms'),
				'civil_status' 				=> $this->input->post('civil_status'),
				'unilabdivision'			=> implode(',',$_POST['unilabdivision']),
				'unilabgroup'				=> implode(',',$_POST['unilabgroup']),		 
				//'numofkids'				=> $this->input->post('numofkids'),			
				//'medprefix'				=> $this->input->post('medprefix'),
				//'website_id' 				=> $this->input->post('website_id'),
				// 'medfname' 				=> $this->input->post('medfname'),
				//'group_id' 				=> $this->input->post('group_id'),
				// 'medlname' 				=> $this->input->post('medlname'),				
				//'taxvat' 					=> $this->input->post('taxvat'),				
				'password_hash' 			=> $this->input->post('password')
			);			
		
			$hash = $eav_attribute_customer_entity['password_hash'];
			//use wnen Send Auto-Generated Password checkbox clicked
			if($this->input->post('password_hash') > 0){
				$hash = get_random_password();
				$to 		= $this->input->post('email');
				$firstname 	= $this->input->post('firstname');
				$lastname 	= $this->input->post('lastname');
				$get_random_password = get_random_password();
				$this->sendAutoGeneratedPassword($to,$firstname, $lastname, $get_random_password);
			}


			//SELECT * FROM customer_entity WHERE website_id = ? AND entity_id = ? ORDER BY entity_id DESC LIMIT 1
			$customer_entity_last_id = $this->Customermod->customer_entity_last_id($insert_id, $website_ids);	
		

			// $this->post_customer_entity_varchar($customer_entity_last_id, $eav_attribute_customer_entity);		
			$this->post_customer_entity_varchar($customer_entity_last_id,$eav_attribute_customer_entity);
			############# -- customer_entity	 


			############# customer_address_entity		
			$customer_address_entity = array(
					'entity_id' 				=>NULL,
					'entity_type_id'			=>2,
					'attribute_set_id'			=>0,
					'increment_id'				=>'',
					'parent_id'					=>$insert_id,
					'created_at'				=>date("Y-m-d H:i:s", strtotime('now')),
					'updated_at'				=>date("Y-m-d H:i:s", strtotime('now')),
					'is_active'					=>1
				);

			$this->Customermod->customer_address_entity($customer_address_entity);	
			############# -- customer_address_entity


			//add address
			if(!empty($_POST['address_firstname']) && !empty($_POST['address_lastname'])):						

				//SELECT * FROM customer_address_entity WHERE parent_id =?
				$customer_address_entity_last_id = $this->Customermod->customer_address_entity_last_id($insert_id, $website_ids);

				$eav_attribute_customer_address_entity = array(
					'prefix'		 			=> $this->input->post('address_prefix'),
				   	'firstname' 				=> $this->input->post('address_firstname'),
				    'middlename' 				=> $this->input->post('address_middlename'),
				    'lastname' 					=> $this->input->post('address_lastname'),
				    'suffix' 					=> $this->input->post('address_suffix'),
				    'company' 					=> $this->input->post('address_company'),
				    'street' 					=> $this->input->post('address_street'),
				    'city' 						=> $this->input->post('address_city'),
				    'country_id' 				=> $this->input->post('country_id'),
				    'region_id'					=> $this->input->post('address_region_id'),
				    'postcode' 					=> $this->input->post('address_postcode'), 
				    'telephone' 				=> $this->input->post('address_telephone'),
				    'address_fax' 				=> $this->input->post('address_fax'),			    
					'default_billing' 			=> $this->input->post('default_billing'),
					'default_shipping' 			=> $this->input->post('default_shipping')
				 );    
					
				//$this->post_customer_address_entity_varchar($customer_address_entity_last_id, $eav_attribute_customer_address_entity);
				$this->post_customer_address_entity_varchar($customer_address_entity_last_id,$eav_attribute_customer_address_entity);
				
			endif;
		
						
			echo json_encode(array('st'=>1, 'msg' => 'New Account was added', 'parent_id' =>$insert_id));	
		
		}
	}
	
	//customer_entity_varchar
	public function post_customer_entity_varchar($customer_entity_last_id,$eav_attribute_customer_entity){

	

		$entity_id = $customer_entity_last_id[0]->entity_id;
		$entity_type_id = $customer_entity_last_id[0]->entity_type_id;	
		

		$data['eav_attribute'] = $this->Customermod->getAllEav();
		$data['customer_information'] = $eav_attribute_customer_entity;



		$attribute_code =array();		
		$backend_type =array();	
		$entity_type_id =array();	
		$attribute_id =array();
			
		foreach($data['eav_attribute'] as $etk=>$etval){
			array_push($attribute_code, $data['eav_attribute'][$etk]->attribute_code);
			array_push($backend_type, $data['eav_attribute'][$etk]->backend_type);
			array_push($entity_type_id, $data['eav_attribute'][$etk]->entity_type_id);
			array_push($attribute_id, $data['eav_attribute'][$etk]->attribute_id);
		}

		
		foreach($attribute_code as $k=>$val){
			foreach ($eav_attribute_customer_entity as $postkey => $postvalue) {
				
				
				if($postkey == $attribute_code[$k] AND $entity_type_id[$k] == 1){
				
					$type = $backend_type[$k];


					if($type == 'varchar'){
						$datas = array(
							'value_id' =>NULL, 
							'entity_type_id' =>$entity_type_id[$k],
							'attribute_id' =>$attribute_id[$k],
							'entity_id' =>$entity_id,
							'value' => $postvalue
						);
						
						$this->db->insert('customer_entity_varchar', $datas);

					}elseif($type == 'datetime') {

						$datas = array(
							'value_id' =>NULL, 
							'entity_type_id' =>$entity_type_id[$k],
							'attribute_id' =>$attribute_id[$k],
							'entity_id' =>$entity_id,
							'value' => $postvalue
						);
						
						$this->db->insert('customer_entity_datetime', $datas);
						
					}elseif($type == 'int'){

						$datas = array(
							'value_id' =>NULL, 
							'entity_type_id' =>$entity_type_id[$k],
							'attribute_id' =>$attribute_id[$k],
							'entity_id' =>$entity_id,
							'value' => $postvalue
						);
						
						$this->db->insert('customer_entity_int', $datas);

					}			

				}							
			}	
		}
	}


	//customer_address_entity_varchar
	public function post_customer_address_entity_varchar($customer_address_entity_last_id,$eav_attribute_customer_address_entity){
	

		$entity_id = $customer_address_entity_last_id[0]->entity_id;
		$entity_type_id = $customer_address_entity_last_id[0]->entity_type_id;	


			
		$data['eav_attribute'] = $this->Customermod->getAllEav();
		$data['customer_information'] = $eav_attribute_customer_address_entity;

		$attribute_code =array();		
		$backend_type =array();	
		$entity_type_id =array();	
		$attribute_id =array();
			
		foreach($data['eav_attribute'] as $etk=>$etval){
			array_push($attribute_code, $data['eav_attribute'][$etk]->attribute_code);
			array_push($backend_type, $data['eav_attribute'][$etk]->backend_type);
			array_push($entity_type_id, $data['eav_attribute'][$etk]->entity_type_id);
			array_push($attribute_id, $data['eav_attribute'][$etk]->attribute_id);
		}				
			
		foreach($attribute_code as $k=>$val){
			foreach ($eav_attribute_customer_address_entity as $postkey => $postvalue) {

				
				if($postkey == $attribute_code[$k] AND $entity_type_id[$k] == 2){
				

					$type = $backend_type[$k];

					if($type == 'varchar'){
						$datas = array(
							'value_id' =>NULL, 
							'entity_type_id' =>$entity_type_id[$k],
							'attribute_id' =>$attribute_id[$k],
							'entity_id' =>$entity_id,
							'value' => $postvalue
						);
						
						$this->db->insert('customer_address_entity_varchar', $datas);

					}elseif($type == 'text') {

						$datas = array(
							'value_id' =>NULL, 
							'entity_type_id' =>$entity_type_id[$k],
							'attribute_id' =>$attribute_id[$k],
							'entity_id' =>$entity_id,
							'value' => $postvalue
						);
						
						$this->db->insert('customer_address_entity_text', $datas);
						
					}elseif($type == 'int'){

						$datas = array(
							'value_id' =>NULL, 
							'entity_type_id' =>$entity_type_id[$k],
							'attribute_id' =>$attribute_id[$k],
							'entity_id' =>$entity_id,
							'value' => $postvalue
						);
						
						$this->db->insert('customer_address_entity_int', $datas);

					}	
				}
			}	
		}			
	}


	public function update_customer_account(){	


		
		//customer account
		$this->form_validation->set_rules('firstname', 'First Name',  'trim|required'); 
		// $this->form_validation->set_rules('lastname', 'Last Name',  'trim|required'); 
		
		// $this->form_validation->set_rules('email', 'Email',  'trim|required'); 
		// $this->form_validation->set_rules('dob', 'Date of Birth',  'trim|required'); 
		// $this->form_validation->set_rules('gender', 'Gender',  'trim|required'); 
		// $this->form_validation->set_rules('civil_status', 'Civil Status',  'trim|required'); 
		// $this->form_validation->set_rules('password', 'Password',  'trim|required'); 


		if($this->form_validation->run() == false)
	    {	 
	    
			$errors = $this->form_validation->error_array();      
           
		    echo json_encode(array('st'=>0, 'msg' => json_encode($errors)));
	    }
	    else
	    {	    



			$update_customer_entity = [	
				'entity_type_id'  	=> 1,
				'attribute_set_id' 	=> $this->input->post('attribute_set_id'), 
				'website_id'		=> $this->input->post('website_id'), 
				'email'				=> $this->input->post('email'),	
				'group_id'			=> $this->input->post('group_id'),
				'increment_id'		=> $this->input->post('increment_id'), 
				'created_at'		=> $this->input->post('created_at'), 
				'updated_at'		=> $this->input->post('updated_at'), 
				'is_active'			=> 1,			
				'disable_auto_group_change' => $this->input->post('disable_auto_group_change')
			];	

		 	$id = $this->input->post('entity_id');	
			$this->load->model('Customermod');				
			$results = $this->Customermod->update_customer_entity($id, $update_customer_entity);		


	
			$eav_attribute_customer_entity = [							
				// 'prcnumber'					=> $this->input->post('prcnumber'),
				'lastname'					=> $this->input->post('lastname'),
				'agree_on_terms' 			=> $this->input->post('agree_on_terms'),
				// 'numofkids'					=> $this->input->post('numofkids'),
				'suffix' 					=> $this->input->post('suffix'),
				// 'medprefix'					=> $this->input->post('medprefix'),
				'website_id' 				=> $this->input->post('website_id'),
				// 'medfname' 					=> $this->input->post('medfname'),
				'group_id' 					=> $this->input->post('group_id'),
				// 'medlname' 					=> $this->input->post('medlname'),
				'disable_auto_group_change' => $this->input->post('disable_auto_group_change'),
				'prefix' 					=> $this->input->post('prefix'),
				//'taxvat' 					=> $this->input->post('taxvat'),
				'firstname' 				=> $this->input->post('firstname'),
				'unilabdivision'			=> implode(',',$_POST['unilabdivision']),
				'unilabgroup'				=> implode(',',$_POST['unilabgroup']),
				'gender' 					=> $this->input->post('gender'),
				'middlename' 				=> $this->input->post('middlename'),
				'civil_status' 				=> $this->input->post('civil_status'),
				'password_hash' 					=> $this->input->post('password')
			];		

			if($this->input->post('password_hash') > 0){
					$eav_attribute_customer_entity['password_hash'] = get_random_password();

					//add email to send data					
					$to 		= $this->input->post('email');
					$firstname 	= $this->input->post('firstname');
					$lastname 	= $this->input->post('lastname');
					$get_random_password = get_random_password();
					$this->sendAutoGeneratedPassword($to,$firstname, $lastname, $get_random_password);
			}

				 
		
			$attribute_ids 	= $this->Customermod->get_all_eav_attribute(1);
			$this->updateCustomerInfoVarchar($eav_attribute_customer_entity, $attribute_ids, $_POST['entity_id']);			
		

			if(isset($_POST['addressid'])){
					
				$this->update_customer_account_address($_POST,  $_POST['entity_id']);

			}	



			//echo json_encode(array('st'=>1, 'msg' => 'Updated Account'));	
		}	

	}


	public function updateCustomerInfoVarchar($eav_attribute_customer_entity, $attribute_ids, $parent_id){

		foreach($eav_attribute_customer_entity  as $eav_attribute_key => $eav_attribute_value):

				foreach($attribute_ids as $eav):												
				
					if(isset($eav_attribute_key) && $eav->attribute_code == $eav_attribute_key):								

						$this->load->model('Customermod');		
					
					
						$type = $eav->backend_type;

						if($type == 'varchar'){

							$datas = array(							
								'entity_type_id' 	=>1,							
								'value'				=>$eav_attribute_value
							);							

							$this->db->where('entity_id', $parent_id);
						    $this->db->where('attribute_id', $eav->attribute_id);
						    $this->db->update('customer_entity_varchar', $datas);

						}elseif($type= 'int'){

							$datas = array(							
								'entity_type_id' 	=>1,							
								'value'				=>$eav_attribute_value
							);							
							
							$this->db->where('entity_id', $parent_id);
						    $this->db->where('attribute_id', $eav->attribute_id);
						    $this->db->update('customer_entity_int', $datas);
						}

						

							
						
					endif;			

				endforeach;
		endforeach;	
	}
	
	public function update_customer_account_address($address, $parent_ids){
			
		$store_id  		= isstore_id();
		//$store_id  		= store_id(isstore_id());
		$usersession 	= is_usersession();
		$baseurl 		= base_url();
			
		$website_ids = 	$store_id;	

		$this->load->model('Customermod');		

		$addressid = array();

		foreach($address['addressid'] as $k=>$val){
			$addressid[] =  $val;
		}

		$addrfname = array();
		foreach($address['addrfname'] as $k=>$val){
			$addrfname[] =  $val;
		}

		$addrlname = array();
		foreach($address['addrlname'] as $k=>$val){
			$addrlname[] =  $val;
		}

		$addrcompany = array();
		foreach($address['addrcompany'] as $k=>$val){
			$addrcompany[] =  $val;
		}

		$addrstreet = array();
		foreach($address['addrstreet'] as $k=>$val){
			$addrstreet[] =  $val;
		}

		$addrcity = array();
		foreach($address['addrcity'] as $k=>$val){
			$addrcity[] =  $val;
		}

		$country_id = array();
		foreach($address['country_id'] as $k=>$val){
			$country_id[] =  $val;
		}

		$address_region_id = array();
		foreach($address['address_region_id'] as $k=>$val){
			$address_region_id[] =  $val;
		}

		$addrpostcode = array();
		foreach($address['addrpostcode'] as $k=>$val){
			$addrpostcode[] =  $val;
		}

		$address_telephone = array();
		foreach($address['address_telephone'] as $k=>$val){
			$address_telephone[] =  $val;
		}

		$addrfax = array();
		foreach($address['addrfax'] as $k=>$val){
			$addrfax[] =  $val;
		}

		// $caddrenidArray = array();
		$parent_id = $this->Customermod->get_customer_address_entity_id($parent_ids);	//get parent 		
		// echo '<pre>';
		// print_r($parent_id);

		foreach($addressid as $k=>$val){
		
			
			$post_address = [
				'parent_id' => $parent_ids,
				// 'prefix' 	=> $this->input->post('address_prefix'),
			 	'firstname' => $addrfname[$k],
			 	'lastname'	=> $addrlname[$k],
			 	// 'suffix' 	=> $this->input->post('address_suffix'),
			    'company' 	=> $addrcompany[$k],
			    'street' 	=> $addrstreet[$k],
			    'city' 		=> $addrcity[$k],
			    // 'id' 		=> $country_id,
			    // 'region_id' => $address_region_id,
			    'postcode' 	=> $addrpostcode[$k],
			    'telephone' => $addrpostcode[$k]
			    // 'fax' 		=> $address_fax
			];   	


			//checks if parent id is !blank
			if( $parent_id[$k]->parent_id >0 ){


				$entity_id= $this->Customermod->all_customer_address_entity_varchar($parent_id[$k]->entity_id);			
				$getbyValueId 	= $this->Customermod->getbyValueId($parent_id[$k]->entity_id);

				//print_r($entity_id);

			
				//from user
				$post_entity_id 		= array();
				$pos_entity_type_id 	= array();
				$pos_backend_type		= array();
				$post_attribute_id 		= array();
				$post_addressvalue 		= array();

				//from database
				$push_value_id 			= array();
				$push_attribute_id 		= array();
				$push_value 			= array();
				$push_entity_type_id 	= array();

				foreach($getbyValueId as $m=>$n){
					$push_value_id[] 		= $getbyValueId[$m]->value_id; 
					$push_attribute_id[] 	= $getbyValueId[$m]->attribute_id; 
					$push_value[] 			= $getbyValueId[$m]->value; 
					$push_entity_id[] 		= $getbyValueId[$m]->entity_id; 
					$push_entity_type_id[] 	= $getbyValueId[$m]->entity_type_id; 
				}
			
				//pass the enity_type_id
				$attribute_ids 	= $this->Customermod->get_all_eav_attribute($entity_id[$k]->entity_type_id);

				foreach($post_address as $addresskey => $addressvalue)
				{	

					// echo $addresskey."<br/>";
					foreach($attribute_ids as $attribute_idskey=>$attribute_idsvalue)
					{

						if($attribute_idsvalue->attribute_code ==$addresskey){		


							$type = $attribute_idsvalue->backend_type; 
						

							if($type =='varchar'){

								$this->db->query("UPDATE customer_address_entity_varchar SET value = '".$addressvalue."' WHERE entity_id = '".$parent_id[$k]->entity_id."' AND attribute_id = '".$attribute_idsvalue->attribute_id."' ");
							
							}elseif($type == 'text'){


								$this->db->query("UPDATE customer_address_entity_text SET value = '".$addressvalue."' WHERE entity_id = '".$parent_id[$k]->entity_id."' AND attribute_id = '".$attribute_idsvalue->attribute_id."' ");

							}

						}
					}
				}	
			}						
		
		}				
		
	}


	public function delete(){
		
		

		$empId =  $this->input->post('entity_id');		
		$this->Customermod->delete($empId);					

		try{					

			$response['status']  = "true";	
			$response['message'] = $this->db->affected_rows();	
		
		
		}catch(Exception $e) {		

				$response['status']  = "false";	
				$response['message'] = $e->getMessage();

		}			
		
	}


	public  function exportCus()
	{
		$store_id  		= store_id(isstore_id());
		$getAllcustomer	= $this->Customermod->getExportCust($store_id);
	
		$queMe = $this->db->last_query();
        $this->load->dbutil();
        $this->load->helper('file');
        $this->load->helper('download');
        $delimiter = ",";
        $newline = "\r\n";
        $filename = "customers.csv";

       	$result = $this->db->query($queMe);
        $data = $this->dbutil->csv_from_result($result, $delimiter, $newline);
        force_download($filename, $data);
     
	}
	
	public function sendAutoGeneratedPassword($to,$firstname, $lastname, $get_random_password){
		$config = array(
		    'mailtype' => 'html'		    
		);

		$this->load->library('email', $config); //Load the library

		$this->email->from('phpdeveloper11@unilab.com.ph', ucfirst(web_sitename()));  //set sender email
		$this->email->to($to);  //set recipient email
		//$this->email->to('junsay.john4@gmail.com');
		$this->email->cc('johnjunsay1@yahoo.com.com'); //optional
		//$this->email->bcc('them@their-example.com'); //optional

		$this->email->subject('New password for ' .$firstname. ' ' .$lastname); //Set email subject
		$this->email->message('Your new password is: ' .$get_random_password); //Set email message			

		if($this->email->send())
		    {
		    	return true;
		    }
		    else
		    {
		     	return false;
		    }	
		
	}
	
  
}