<?php
class Logoutmod extends CI_Model {


    function __construct()
    {
		parent::__construct();
		$this->load->database();
		$this->load->helper('cookie');
    }
    
	function logoutccount()
	{
		
		$cookie= array(
		  'name'   => 'useraccount',
		  'value'  => '',
		  'expire' => '0',
		);
		
		$this->input->set_cookie($cookie);	
		$dataACcount 	= null;
		 
		// //$password = $_POST['password'];
		// //$username = $_POST['username'];
		
		// $sql 			= "SELECT * FROM user_account WHERE sessionid ='$sessionid' AND isactive ='1'";
		// $sqlResult 		= $this->db->query($sql); 
		// $dataACcount 	= null;
		
		// if($sqlResult->num_rows() > 0):
			// foreach ($sqlResult->result() as $row):
				// if($row->id):
		
					// $setsessionid = array('sessionid' => '','isactive' => 0);
					// $this->db->where('id', $row->id);
					// $this->db->update('user_account',$setsessionid);			
					// $dataACcount =  $row;	
					// $dataACcount 	= null;					
								
				// endif;
				
			// endforeach;		
			
		// endif;

		
		return $dataACcount;
	}

	}
?>