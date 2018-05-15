<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	if ( ! function_exists('is_logged'))
	{
		function is_logged()
		{
			
			$CI 		= & get_instance();
			$sessionid 	= $CI->input->cookie('useraccount',true);
		
			if(!empty($sessionid)):
				$datainfo 		= explode(",",$sessionid);
				$isActive 		= true;
				  $cookie= array(
					  'name'   => 'useraccount',
					  'value'  => $sessionid,
					  //'expire' => '3600'
					  'expire' => '7200'
				  );
				  
				   
				//$CI->input->set_cookie($cookie);
				// comment for a while muna error e
				 
			else:
				$isActive 		= false;
			endif;
			//print_r($_COOKIE);
			return  $isActive;
		}
		
	}
	
	if ( ! function_exists('isstore_id')) 
	{
		function isstore_id()
		{	
			$CI 		= & get_instance();
			$sessionid 	= $CI->input->cookie('useraccount',true);
			// print_r($sessionid);
				//echo $sessionid;
			if(isset($sessionid)):
			$datainfo 		= explode(",",$sessionid);
			
			//$store_id 		= $datainfo[4]; //index as: website_id 
			// $store_id = "'";
			
			$store_id = null;
			
		
			$sessionstore_id = $CI->input->cookie('websiteID',true);
		
			$sessionstore_id = explode(",",$sessionstore_id);
			
			foreach($sessionstore_id as $_value){
			
				if(!empty($_value)){
					$store_id .= "'$_value',";
				}
			} 

			$store_id = substr($store_id, 0 , -1);

			else:

			$store_id 		= 0;

			endif;
			
		return $store_id;

		}
		
	}


//userid
	if ( ! function_exists('isuser_id'))
	{
		function isuser_id()
		{	
			$CI 		= & get_instance();
			$sessionid 	= $CI->input->cookie('useraccount',true);
			//print_r($sessionid);
			if(!empty($sessionid)):
			$datainfo 		= explode(",",$sessionid);
			//$store_id 		= $datainfo[4]; //index as: website_id 
			$use_rid 		= $datainfo[3]; //index as: website_id 
			//$store_id 	= $datainfo[4]; //index as: website_id 
			else:
			$use_rid 		= 0;
			//$sstore_id       =0;
			endif;
			return $use_rid;
			//return $datainfo[2];
		}
		
	}
//check access
	
	if (! function_exists('ccheck_form')){
		function ccheck_form($userid,$cform){
			$ci =& get_instance(); 
			$ci ->load->database();

		$sql = "SELECT permission
				FROM admin_role, admin_rule, admin_user
				where admin_role.parent_id = admin_rule.role_id
				and admin_role.user_id = admin_user.user_id
				and admin_user.user_id = '".$userid."'
				and resource_id LIKE '%".$cform."%'";

        $query = $ci->db->query($sql);
        $data = $query->result_array();

        if($query->num_rows() > 0)
			{
			//print_r($data[0]["permission"]);
			//$query->permission;
			  if($data[0]["permission"]=="allow"){
			   	return true;
			   }else
			   {
			   	//false by default
			   	//return true;
			   	return false;
			   }
			}else{
				//print_r($sql);
			//print_r("hahahah");	
			return true;
			}
		}
	}
	
	if ( ! function_exists('is_usersession'))
	{
		function is_usersession()
		{	
			$CI 		= & get_instance();
			$sessionid 	= $CI->input->cookie('useraccount',true);
			if(!empty($sessionid)):
				$datainfo 		= explode(",",$sessionid);
				$usersession		= $datainfo[0];
			else:
				$usersession	= '';
			endif;
			
			return $usersession;
		}
		
	}
	
	if ( ! function_exists('web_sitename'))
	{
		function web_sitename()
		{	
			$CI 			= & get_instance();
			$sessionid 		= $CI->input->cookie('useraccount',true);
		
			$store_id		= isstore_id();
			if(empty($store_id)):
				$store_id = 0;
			endif;
			
			$CI->load->model('Customermod');	
			$getadminuserwebsite 	= $CI->Customermod->getadminuserwebsite($store_id);
		
			foreach($getadminuserwebsite  as $_value){
				$sitename = $_value->name;
			}
		
			return $sitename;
		}
		
	}
	
	if ( ! function_exists('store_id'))
	{
		function store_id($store_id=null)
		{	

			$ci =& get_instance(); 

			$ci ->load->database();


			$sql = "SELECT store_id FROM core_store WHERE store_id in ($store_id)";
			
			$query = $ci->db->query($sql);
							
			
			return $query->result();
        		
        		
       		 }	
		
	}
	
	
	if ( ! function_exists('astore_id'))
	{
		function astore_id($sid)
		{	
			
			$ci =& get_instance(); 
			$ci ->load->database();

			$sql = "SELECT store_id FROM core_store WHERE website_id in ($sid) ";
			$query = $ci->db->query($sql);
        	$data = $query->result_array();
        	
			
        	$store_id = null;
			
			if(count($query->result()) > 0){
        	foreach($query->result() as $_value){
		
				if(!empty($_value)){
					$store_id .= "'$_value->store_id',";
				}
			} 

			$store_id = substr($store_id, 0 , -1);
		
        	return $store_id;  
			}
			else {
			return '0';  
			}

        	// echo '<pre>';
        	// print_r($data);     
       		}
	
		
	}
	
	
	if ( ! function_exists('get_random_password'))
	{
	   	         
	    function get_random_password($chars_min=6, $chars_max=8, $use_upper_case=true, $include_numbers=true, $include_special_chars=true)
	    {
	        $length = rand($chars_min, $chars_max);	    
	        $selection = 'aeuoyibcdfghjklmnpqrstvwxz';
	        if($include_numbers) {
	            $selection .= "1234567890";
	        }
	        if($include_special_chars) {	           
	            $selection .= "_-.";
	        }

	        $password = "";
	        for($i=0; $i<$length; $i++) {
	            $current_letter = $use_upper_case ? (rand(0,1) ? strtoupper($selection[(rand() % strlen($selection))]) : $selection[(rand() % strlen($selection))]) : $selection[(rand() % strlen($selection))];            
	            $password .=  $current_letter;
	        }                

	      return $password;
	    }

	}
		
		
		
	if( ! function_exists('sendAutoGeneratedPassword') ){
	
		
		function sendAutoGeneratedPassword($to,$firstname, $lastname, $get_random_password){

			$config = array(
			    'mailtype' => 'html'		    
			);
			//Load the library
			$this->load->library('email', $config); 
			//set sender email
			$this->email->from('phpdeveloper11@unilab.com.ph', ucfirst(web_sitename()));  
			//set recipient email
			$this->email->to($to);  
			//optional
			$this->email->cc('johnjunsay1@yahoo.com.com');
			//optional 
			//$this->email->bcc('them@their-example.com'); 
			
			//Set email subject
			$this->email->subject('New password for ' .$firstname. ' ' .$lastname);
			//Set email message 
			$this->email->message('Your new password is: ' .$get_random_password); 
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