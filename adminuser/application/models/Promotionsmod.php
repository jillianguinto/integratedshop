<?php
class Promotionsmod extends CI_Model {

    function __construct()
    {
		parent::__construct();
		$this->load->database();
		/*
		$this->load->helper('cookie');	
	
		$sessionid 	= $this->input->cookie('useraccount',true);
		$store_id 	 = 0;
		$this->webID = 0;
		
		if(!empty($sessionid)):
		
			$datainfo 	= explode(",",$sessionid);
			$store_id 	= $datainfo[2];


			$sqlWeb 		= "SELECT website_id FROM core_store WHERE store_id='$store_id'";
			$sqlwebsiteid 	= $this->db->query($sqlWeb); 	

			if($sqlwebsiteid->num_rows() > 0):
				//$webID = 0;
				foreach ($sqlwebsiteid->result() as $webResult):
					if($webResult->website_id):
						$this->webID = $webResult->website_id;
					endif;
				endforeach;		
				
			endif;		
			
		endif;*/

    }
    
	
    function getAllPromotions($store_id)
    {
		
		//$webID = $this->webID;
		//
		//$this->db->select('salesrule_website.*,salesrule.*');
	//	$this->db->from('salesrule_website');
	//	$this->db->join('salesrule', 'salesrule.rule_id = salesrule_website.rule_id');
	//	$this->db->where("salesrule_website.website_id='$webID'");
		

		$sqlResult = $this->db->get();	
		
        return 2; //$sqlResult->num_rows();
		
    }
	
    function getAllPromo($store_id)
    {

		$webID = $store_id;

		$this->db->select('catalogrule_website.*,catalogrule.*,catalogrule_customer_group.*,customer_group.*');

		$this->db->from('catalogrule_website');

		$this->db->join('catalogrule', 'catalogrule.rule_id = catalogrule_website.rule_id');

		$this->db->join('catalogrule_customer_group', 'catalogrule.rule_id=catalogrule_customer_group.rule_id');

		$this->db->join('customer_group', 'customer_group.customer_group_id=catalogrule_customer_group.customer_group_id');

		$this->db->where("catalogrule_website.website_id in ($webID)");

		$this->db->group_by('catalogrule.name');

		$sqlResult = $this->db->get();	

        return $sqlResult->result();

    }

    function getcgbyID($iid){
    	$this->db->select('*');
    	$this->db->from('catalogrule_customer_group , customer_group');
    	$this->db->where("catalogrule_customer_group.customer_group_id = customer_group.customer_group_id AND rule_id='$iid'");
    	$sqlResult = $this->db->get();
    	//echo $this->db->last_query();
    	return $sqlResult->result();

    }
	function getWebsiteIds2($website_ids){
		$sql = "SELECT website_id FROM core_store WHERE store_id in ($website_ids)";
		// print_r($sql);
		// die();
		$sqlResult 		= $this->db->query($sql); 
			foreach($sqlResult->result() as $_value){
			
				if(!empty($_value)){
					$store_id .= "'$_value',"; 
				}
			} 

			$store_id = substr($store_id, 0 , -1);		
			// echo $store_id;
			// die();
		return $store_id;
	}

    function getPromobyID($idd,$store_id)
    {
		$sql = "SELECT website_id FROM core_store WHERE store_id in ($store_id)";
		
		$sqlResult 		= $this->db->query($sql);
		$website_id = null;
		// print_r($sqlResult->result());
		// die();
			foreach($sqlResult->result() as $_value){
			
				if(!empty($_value)){
					$website_id .= "'$_value->website_id',"; 
				}
			} 

			$website_id = substr($website_id, 0 , -1);	
			// echo $website_id;
			// die(); 
    	/*
		$this->db->select('catalogrule_website.*,catalogrule.*');
		$this->db->from('catalogrule_website');
		$this->db->join('catalogrule', 'catalogrule.rule_id = catalogrule_website.rule_id');
		$this->db->where("catalogrule_website.website_id='$store_id' and catalogrule_website.rule_id='$idd'");
*/
		$this->db->select('catalogrule_website.*,catalogrule.*,catalogrule_customer_group.*,customer_group.*');
		$this->db->from('catalogrule_website');
		$this->db->join('catalogrule', 'catalogrule.rule_id = catalogrule_website.rule_id');
		$this->db->join('catalogrule_customer_group', 'catalogrule.rule_id=catalogrule_customer_group.rule_id');
		$this->db->join('customer_group', 'customer_group.customer_group_id=catalogrule_customer_group.customer_group_id');
		$this->db->where("catalogrule_website.rule_id='$idd'");
		$this->db->where_in('catalogrule_website.website_id',$website_id);
		$this->db->group_by('catalogrule.name');
		$sqlResult = $this->db->get();	
		//echo "                                                                                                                                 ";
		//echo "heheh".$webID;
		//print_r($store_id);
		//echo $this->db->last_query();
        return $sqlResult->result();
    }


    	function core_store()
	{	
		$sql 			= "SELECT website_id, name FROM core_store WHERE is_active = 1 ORDER BY name ASC";
		$sqlResult 		= $this->db->query($sql); 		
		return $sqlResult->result();

	}


	public function update_promo($id,$data)
	{
		//print_r($data);
		//print_r($data2);
		//die();

		$n=$data['name'];
		$d=$data['description'];
		$fd=$data['from_date'];
		$td=$data['to_date'];
		$iac=$data['is_active'];
		$sac=$data['simple_action'];
		$gid=$data['group_id'];
		$dis=$data['discount_amount'];
		if ($sac=="To Percentage of the Original Price"){
			$sac="to_percent";
		}elseif ($sac=="By Fixed Amount") {
			$sac="by_fixed";
		}elseif ($sac=="By Percentage of the Original Price") {
			$sac="by_percent";
		}elseif ($sac=="To Fixed Amount") {
			$sac="to_fixed";
		}
		$sql 	= "update catalogrule set name='$n',
		description='$d',
		from_date='$fd',
		to_date='$td',
		is_active='$iac',
		simple_action='$sac',
		discount_amount='$dis'
		where rule_id='$id';
		-- update catalogrule_customer_group set customer_group_id ='$gid' where rule_id='$id'
		";
		$this->db->trans_start();
		$sql2 = "insert into catalogrule_customer_group values('$id','$gid')";
		$sqlResult3 = $this->db->query("delete from catalogrule_customer_group where rule_id='$id'");
		$sqlResult2 = $this->db->query($sql2); 	
		$sqlResult = $this->db->query($sql);
		$this->db->trans_complete(); 
		//echo $this->db->last_query();
		//return $sqlResult->result();
	}

		public function update_promo2($id,$data,$data2)
	{
		$n=$data['name'];
		$d=$data['description'];
		$fd=$data['from_date'];
		$td=$data['to_date'];
		$iac=$data['is_active'];
		$sac=$data['simple_action'];
		//$gid=$data['group_id'];
		$dis=$data['discount_amount'];
		if ($sac=="To Percentage of the Original Price"){
			$sac="to_percent";
		}elseif ($sac=="By Fixed Amount") {
			$sac="by_fixed";
		}elseif ($sac=="By Percentage of the Original Price") {
			$sac="by_percent";
		}elseif ($sac=="To Fixed Amount") {
			$sac="to_fixed";
		}
		$sql 	= "update catalogrule set name='$n',
		description='$d',
		from_date='$fd',
		to_date='$td',
		is_active='$iac',
		simple_action='$sac',
		discount_amount='$dis'
		where rule_id='$id';";

		$sqlResult = $this->db->query($sql);
		//$sqlResult3 = $this->db->query("delete from catalogrule_customer_group where rule_id='$id'");
		$sdelete ="delete from catalogrule_customer_group where rule_id='$id'";
		$queque="";

		$this->db->trans_start();
		$this->db->query($sdelete);
		foreach ($data2 as $key => $value) {
			$queque = "insert into catalogrule_customer_group values('$id','$value');";
		$this->db->query($queque);
		}
		$this->db->trans_complete(); 

	}
}
?>