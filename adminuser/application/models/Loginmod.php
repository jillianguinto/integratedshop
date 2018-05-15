<?php
class Loginmod extends CI_Model {


    function __construct()
    {
		parent::__construct();
		$this->load->database();
		$this->load->helper('cookie');
    }
    
	function loginaccount()
	{
		$dataACcount 	= null;

		if(isset($_POST['username'])){
			$username = $_POST['username'];
		
		}
		
		if(isset($_POST['stid'])){
			
			$store_id       = $_POST['stid'];
		}
		else{
			$store_id = '1';
		}		


		$sql 			= "SELECT * FROM admin_user WHERE username='$username' AND is_active ='1'";
		
		$sqlResult 		= $this->db->query($sql); 

		if($sqlResult->num_rows() > 0):
		
			foreach ($sqlResult->result() as $row):
			
				if($row->website_ids):

					$dataACcount =  $row;
					$datainfo = array();
					$datainfo[] = $row->firstname;
					$datainfo[] = $row->username;
					$datainfo[] = $row->website_ids;
					$datainfo[] =  $store_id;
					$datainfo[] = $row->user_id;
					$datainfo[] = $store_id;
					$tosave = implode(",",$datainfo);

					
					// print_r($dataACcount);
					  // print_r($cookie);
					
            // set cookie 
              $cookie= array(
						  'name'   => 'useraccount',
						  'value'  => $tosave,
						  'expire' => '3600',
					  );
					   $this->input->set_cookie($cookie);	

				$cookie2= array(
						  'name'   => 'websiteID', 
						  'value'  => $row->website_ids,
						  'expire' => '3600',
					  );
					   $this->input->set_cookie($cookie2);						   
													  
				endif;
	
			endforeach;		
			
		endif;
		
		return $dataACcount;


	}

	function islogged($sessionid)
	{
			$cookie= array(
		  'name'   => 'useraccount',
		  'value'  => '',
		   'expire' => '3600',
		);
		$sql 			= "SELECT * FROM user_session WHERE session_id ='$sessionid' AND is_active ='1'";
		$sqlResult 		= $this->db->query($sql); 
		$dataACcount 	= null;
		
		if($sqlResult->num_rows() > 0):
			foreach ($sqlResult->result() as $row):
				if($row->id):	
					$dataACcount =  $row;		
				endif;
				
			endforeach;		
			
		endif;
		 $this->input->set_cookie($cookie);	
		$this->db->close();
		
		return $dataACcount;
	}	
	
	
	function websiteidchecker()
	{

		$response = false;
		if ($this->db->field_exists('website_ids', 'admin_user'))
		{
		   $response = true;
		}			
		$this->db->close();
		return $response;
		
	}
	
	function addcolumnadmin()
	{
		$this->load->dbforge();
		$fields = array(
				'website_ids' => array(
						'type' => 'VARCHAR',
						'constraint' => '100',
				),
		);		
		$this->dbforge->add_column('admin_user', $fields);
		$this->db->close();
		//echo $fields;
		return true;
		
	}
	


}
?>