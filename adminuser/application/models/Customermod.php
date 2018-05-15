<?php
class Customermod extends CI_Model {

    function __construct()
    {
		parent::__construct();
		$this->load->database();		

    }
		

	function getAllEav(){

		$sql1 			= "SELECT * FROM eav_attribute";
		$sqlResult1		= $this->db->query($sql1);
		return $sqlResult1->result();		
		
		 	

	}	
	
	
    function getadminuserstoreid($website_ids){

    	$sql1 			= "SELECT name FROM core_website WHERE website_id in($website_ids)";
		$sqlResult1 		= $this->db->query($sql1); 

		$webname = $sqlResult1[0]->name; 

    	$sql = "SELECT store_id FROM core_store WHERE name = '" .$webname. "' ";
    		$sqlResult 		= $this->db->query($sql); 
		
		return $sqlResult->result();
    }
    
	function getallCustomers($website_ids =null)
	{	
		 

	
		$sql 			= "SELECT * FROM customer_entity WHERE store_id in($website_ids)";
		
		$sqlResult 		= $this->db->query($sql); 
		
		return $sqlResult->num_rows();

	}
	
	function getadminuserwebsite($website_ids)
	{	
		 
		$sql 			= "SELECT name FROM core_website WHERE website_id in($website_ids)";
		$sqlResult 		= $this->db->query($sql); 

		//$webname =$sqlResult[0]->name;
		//$this->getadminuserstoreid($webname);	
	
		
		return $sqlResult->result();

	}
	
	

	function log_customer($id)
	{	
		$sql = "SELECT login_at FROM log_customer WHERE customer_id = '{$id}'";
		$sqlResult 		= $this->db->query($sql); 		
		return $sqlResult->result();

	}


	function get_division()
	{		

		$sql = "SELECT  opt.option_id, unilabdivision_name.value AS unilabdivision_name
				FROM eav_attribute AS e

				LEFT JOIN eav_attribute_option AS opt
				ON opt.attribute_id = e.attribute_id	
				
				LEFT JOIN eav_attribute_option_value AS unilabdivision_name
				ON unilabdivision_name.option_id = opt.option_id

				WHERE e.attribute_code = 'unilab_division'";
		$sqlResult 		= $this->db->query($sql); 		
		return $sqlResult->result();	

		
	}


	function get_group()
	{		

		$sql = "SELECT opt.option_id,(SELECT value FROM eav_attribute_option_value WHERE option_id = opt.option_id) AS unilabgroup_name
				FROM eav_attribute AS e

				LEFT JOIN eav_attribute_option AS opt
				ON opt.attribute_id = e.attribute_id

				WHERE e.attribute_code = 'unilab_group'";
		$sqlResult 		= $this->db->query($sql); 		
		return $sqlResult->result();		


	}


	function getWebsiteIds($website_ids){
		$sql = "SELECT name,website_id FROM core_store WHERE store_id in ($website_ids)";
		// print_r($sql);
		// die();
		$sqlResult 		= $this->db->query($sql); 		
		return $sqlResult->result();
	}
	
	function getWebsiteIdOnly($website_ids){
		$sql = "SELECT website_id FROM core_store WHERE store_id in ($website_ids)";
		// print_r($sql);
		// die();
		$sqlResult 		= $this->db->query($sql); 	
		$website_ids= null;

		foreach($sqlResult->result() as $_item){

			foreach($_item as $_value){

			$website_ids .= "'$_value',";

			}

		}  
		$website_ids = substr($website_ids, 0 , -1);
		return $website_ids;
	}


	function getExportCust($website_ids){
		$sql = "SELECT 
					customer_entity.entity_id AS 'ID',						
					CONCAT(at_name.value, ' ' ,at_lastname.value) AS 'Name',	 
					customer_entity.email AS 'Email',		
					(
						SELECT customer_group_code 
						FROM customer_group 
						WHERE customer_group_id = customer_entity.group_id
					) AS 'Group', 
					at_telephone.value AS 'Telephone',
					at_zip.value AS 'ZIP',
					at_country_id.value AS 'Country',
					at_region.value AS 'State/Province',
					customer_entity.created_at AS 'Customer Since',
					at_websitename.value AS 'Website'
			
				FROM customer_entity 
		
				LEFT JOIN customer_entity_varchar AS at_name			
				ON at_name.entity_id = customer_entity.entity_id 
				AND at_name.attribute_id IN(SELECT attribute_id FROM eav_attribute WHERE attribute_code = 'firstname') 

				LEFT JOIN customer_entity_varchar AS at_lastname			
				ON at_lastname.entity_id = customer_entity.entity_id 
				AND at_lastname.attribute_id IN(SELECT attribute_id FROM eav_attribute WHERE attribute_code = 'lastname')  

				LEFT JOIN customer_address_entity_varchar AS at_telephone
				ON at_telephone.entity_id = customer_entity.entity_id 
				AND at_telephone.attribute_id IN(SELECT attribute_id FROM eav_attribute WHERE attribute_code = 'telephone')  
				-- AND at_telephone.entity_type_id = 2

				LEFT JOIN customer_address_entity_varchar AS at_zip
				ON at_zip.entity_id = customer_entity.entity_id 
				AND at_zip.attribute_id IN(SELECT attribute_id FROM eav_attribute WHERE attribute_code = 'postcode')  
				-- AND at_telephone.entity_type_id = 2

				LEFT JOIN customer_address_entity_varchar AS at_country_id
				ON at_country_id.entity_id = customer_entity.entity_id 
				AND at_country_id.attribute_id IN(SELECT attribute_id FROM eav_attribute WHERE attribute_code = 'at_country_id')  
				-- AND at_country_id.entity_type_id = 2

				LEFT JOIN customer_address_entity_varchar AS at_region
				ON at_region.entity_id = customer_entity.entity_id 
				AND at_region.attribute_id IN(SELECT attribute_id FROM eav_attribute WHERE attribute_code = 'region')  
				-- AND at_region.entity_type_id = 2
				
				LEFT JOIN customer_entity_varchar AS at_websitename
				ON at_websitename.entity_id = customer_entity.entity_id 
				AND at_websitename.attribute_id IN(SELECT attribute_id FROM eav_attribute WHERE attribute_code = 'created_in')  
				-- AND at_websitename.entity_type_id = 1

				WHERE customer_entity.website_id  in ($website_ids) ";
		$sqlResult 		= $this->db->query($sql); 
		return $sqlResult->result();
	}
	
	
	function getcustomerinfo($website_ids)
	{	
	
		 
		// $sql 			= "SELECT * FROM customer_entity WHERE website_id in($website_ids)";			
		
		$sql = "SELECT 	customer_entity.entity_id, 
						customer_entity.entity_type_id,
						customer_entity.attribute_set_id,
						customer_entity.website_id,
						customer_entity.email,
						customer_entity.group_id,
						customer_entity.increment_id,
						customer_entity.store_id,
						customer_entity.created_at AS customer_since,
						customer_entity.updated_at,
						customer_entity.is_active,
						customer_entity.disable_auto_group_change,

						customer_entity_varchar.value AS first_name 
						-- log_customer.login_at
			
				FROM customer_entity 
		

				left JOIN customer_entity_varchar 			
				ON customer_entity_varchar.entity_id = customer_entity.entity_id 
				AND customer_entity_varchar.attribute_id = '5'
			
				WHERE customer_entity.store_id in($website_ids)";	
				
		$sqlResult 		= $this->db->query($sql); 
		
		return $sqlResult->result();

	}
	
	public function getCustomerByCustomerId($customerId, $toreid){

		$this->db->select('*');
		$this->db->from('customer_entity');
		
		$this->db->where(array('customer_entity.entity_id' => $customerId, 'customer_entity.store_id ' => $toreid));		
		
		$sqlResult = $this->db->get();		
		
		return $sqlResult->result();
	}

	//	insert 

	public function customer_address_entity($data)
	{		
		$this->db->insert('customer_address_entity', $data);	
		$insert_id = $this->db->insert_id();  
		return  $insert_id;	 
	}

	public function customer_address_entity_last_id($insert_id, $website_ids)
	{		
		$sql 		= "SELECT * FROM customer_address_entity WHERE parent_id = $insert_id";
		$sqlResult 	= $this->db->query($sql); 
		return $sqlResult->result();
	}
	

	public function customer_address_entity_varchar($data)
	{	
		$this->db->insert('customer_address_entity_varchar', $data);	
		$insert_id = $this->db->insert_id();  
		return  $insert_id;	 	
	}

	public function customer_address_entity_int($data)
	{	
		$this->db->insert('customer_address_entity_int', $data);	  
	}

	public function customer_address_entity_text($data)
	{	
		$this->db->insert('customer_address_entity_text', $data);	  
	}
	
	public function customer_entity($data)
	{	
		$this->db->insert('customer_entity', $data);	
		$insert_id = $this->db->insert_id();
   		return  $insert_id;			
	}
	
	public function customer_entity_last_id($insert_id, $website_ids)
	{		
		$sql 		= "SELECT * FROM customer_entity WHERE website_id in($website_ids) AND entity_id = $insert_id ORDER BY entity_id DESC LIMIT 1";
		$sqlResult 	= $this->db->query($sql); 
		return $sqlResult->result();
	}
	
	public function get_all_eav_attribute($entity_type_id)
	{		
		$sql = "SELECT * FROM eav_attribute WHERE entity_type_id = $entity_type_id";		
		$sqlResult 	= $this->db->query($sql); 
		return $sqlResult->result();		
	}	


	// public function customer_entity_static($data)
	// {	
	// 	$this->db->insert('customer_entity', $data);				
	// }

	public function customer_entity_varchar($data)
	{	
		$this->db->insert('customer_entity_varchar', $data);		
		return $this;
	}


	public function customer_entity_int($data)
	{	
		$this->db->insert('customer_entity_int', $data);
		return $this;		
	}
	
	public function customer_entity_datetime($data)
	{	
		$this->db->insert('customer_entity_datetime', $data);	
		return $this;	
	}

	// get account information$website_ids)
	public function get_account_information($entity_id, $website_ids)
	{			
		$sql = "SELECT e.*,  
				at_prefix.`value` AS prefix,		
				at_firstname.`value` AS firstname,
				at_middlename.`value` AS middlename,
				at_lastname.`value` AS lastname,
				at_suffix.`value` AS suffix,	
				at_password.`value` AS password,				
				at_status.`value` AS status,					
				at_gender.`value` AS gender,
				at_taxvat.`value` AS taxvat,	
				at_medprefix.`value` AS medprefix,				
			
				CONCAT(at_prefix.value) AS name
				
				FROM customer_entity AS e											

				LEFT JOIN customer_entity_varchar AS at_prefix 
				ON (at_prefix.entity_id = e.entity_id)				
				AND (at_prefix.attribute_id = '4') 
				
				LEFT JOIN customer_entity_varchar AS at_firstname 
				ON (at_firstname.entity_id = e.entity_id) 
				AND (at_firstname.attribute_id = '5')				
				
				LEFT JOIN customer_entity_varchar AS at_middlename 
				ON (at_middlename.entity_id = e.entity_id) 
				AND (at_middlename.attribute_id = '6')				
				
				LEFT JOIN customer_entity_varchar AS at_lastname 
				ON (at_lastname.entity_id = e.entity_id) 
				AND (at_lastname.attribute_id = '7')				
				
				LEFT JOIN customer_entity_varchar AS at_suffix 
				ON (at_suffix.entity_id = e.entity_id) 
				AND (at_suffix.attribute_id = '8')	
				
				LEFT JOIN customer_entity_varchar AS at_password 
				ON (at_password.entity_id = e.entity_id)				
				AND (at_password.attribute_id = '12') 			
				
				LEFT JOIN customer_entity_varchar AS at_status 
				ON (at_status.entity_id = e.entity_id)				
				AND (at_status.attribute_id = '96')				
					
				
				LEFT JOIN customer_entity_varchar AS at_gender 
				ON (at_gender.entity_id = e.entity_id)				
				AND (at_gender.attribute_id = '18')
				
				LEFT JOIN customer_entity_varchar AS at_taxvat 
				ON (at_taxvat.entity_id = e.entity_id)				
				AND (at_taxvat.attribute_id = '16')			

				LEFT JOIN customer_entity_varchar AS at_medprefix 
				ON (at_medprefix.entity_id = e.entity_id)				
				AND (at_medprefix.attribute_id = '195')					
												
				WHERE e.entity_id = $entity_id								
				AND e.website_id in($website_ids)";				
				
		$sqlResult 		= $this->db->query($sql); 		
		return $sqlResult->result();			
	}
	
	public function get_customer_address($parent_id)
	{	
		$sql = "SELECT 
				c.entity_id AS addressid, 
				at_addrprefix.value AS addrprefix,
				at_addrcountry.value AS addrcountry,
				at_address.value AS address,
				at_addrfname.value AS addrfname,
				at_addrlastname.value AS addrlname,
				at_company_addr.value AS addrcompany,
				at_city_addr.value AS addrcity,
				at_street_addr.value AS addrstreet,
				at_telephone.value AS addrtelephone,
				at_postcode.value AS addrpostcode,
				at_fax.value AS addrfax,
			

				CONCAT(at_address.value) 

				FROM customer_address_entity AS c

				LEFT JOIN customer_address_entity_varchar AS at_addrprefix
				ON at_addrprefix.entity_id = c.entity_id
				AND at_addrprefix.attribute_id = 19


				LEFT JOIN customer_address_entity_varchar AS at_addrfname
				ON at_addrfname.entity_id = c.entity_id
				AND at_addrfname.attribute_id = 20

				LEFT JOIN customer_address_entity_varchar AS at_addrlastname
				ON at_addrlastname.entity_id = c.entity_id
				AND at_addrlastname.attribute_id = 22

				LEFT JOIN customer_address_entity_varchar AS at_company_addr
				ON at_company_addr.entity_id = c.entity_id
				AND at_company_addr.attribute_id = 24

				LEFT JOIN customer_address_entity_varchar AS at_street_addr
				ON at_street_addr.entity_id = c.entity_id
				AND at_street_addr.attribute_id = 25


				LEFT JOIN customer_address_entity_varchar AS at_city_addr
				ON at_city_addr.entity_id = c.entity_id
				AND at_city_addr.attribute_id = 26
				

				LEFT JOIN customer_address_entity_varchar AS at_addrcountry
				ON at_addrcountry.entity_id = c.entity_id
				AND at_addrcountry.attribute_id = 27

				LEFT JOIN customer_address_entity_varchar AS at_address
				ON at_address.entity_id = c.entity_id
				AND at_address.attribute_id = 28

				LEFT JOIN customer_address_entity_varchar AS at_postcode
				ON at_postcode.entity_id = c.entity_id
				AND at_postcode.attribute_id = 30

				LEFT JOIN customer_address_entity_varchar AS at_telephone
				ON at_telephone.entity_id = c.entity_id
				AND at_telephone.attribute_id = 31

				LEFT JOIN customer_address_entity_varchar AS at_fax
				ON at_fax.entity_id = c.entity_id
				AND at_fax.attribute_id = 32

			

				WHERE c.parent_id = '$parent_id'";	
				
		$sqlResult 		= $this->db->query($sql); 		
		return $sqlResult->result();			
	}	
	
	public function update_customer_entity($id, $data) {
	    $this->db->where('entity_id', $id);
	    $this->db->update('customer_entity', $data);
	     
	}

	public function update_customer_entity_varchar($entity_id, $attribute_id, $data) {
		$this->db->where('entity_id', $entity_id);
	    $this->db->where('attribute_id', $attribute_id);
	    $this->db->update('customer_entity_varchar', $data);
	   
	}
	
	public function update_customer_address_entity($id, $data) {

	    $this->db->where('parent_id', $id);
	    $this->db->update('customer_address_entity', $data);
	}


	public function get_customer_address_entity_id($id){
		$sql = "SELECT * FROM customer_address_entity WHERE parent_id = $id";

		$sqlResult 		= $this->db->query($sql); 		
		return $sqlResult->result();	
	}


	public function all_customer_address_entity_varchar($id){
		$sql = "SELECT * FROM customer_address_entity_varchar WHERE entity_id = $id";

		$sqlResult 		= $this->db->query($sql); 		
		return $sqlResult->result();	
	}


	public function update_customer_address($value_id, $attribute_id, $entity_id, $entity_type_id, $data)
	{		

		return $data;

		// $this->db->where('value_id',$value_id);		
		// $this->db->where('attribute_id',$attribute_id);
		// $this->db->where('entity_id',$entity_id);
		// $this->db->where('entity_type_id',$entity_type_id);
		// $this->db->update('customer_address_entity_varchar', $data);

		// UPDATE customer_address_entity_varchar 
		// SET value = 'zzz'
		// WHERE value_id = 1601
		// AND attribute_id = 22
		// AND entity_id = 386	
				
	}

	//
	function customer_group()
	{	
		$sql 			= "SELECT * FROM customer_group";
		$sqlResult 		= $this->db->query($sql); 		
		return $sqlResult->result();
	}
	
	function core_store()
	{	
		$sql 			= "SELECT website_id, name FROM core_store WHERE is_active = 1 ORDER BY name ASC";
		
		//$sql = "SELECT website_id, name FROM core_store_group ORDER BY name ASC";
		$sqlResult 		= $this->db->query($sql); 		
		return $sqlResult->result();

	}

	public function delete($empId){

		$sql = "DELETE FROM customer_entity WHERE entity_id = '{$empId}'";
		$this->db->query($sql, array($empId));	
		return $this->db->affected_rows();	
	}

	public function getbyValueId($entity_id){
		$sql = "SELECT * FROM customer_address_entity_varchar 
		WHERE entity_id ='{$entity_id}'";
		$sqlResult 		= $this->db->query($sql); 		
		return $sqlResult->result();
	}


	public function customer_information($table, $data){
		$this->db->insert('customer_entity_varchar', $data);	
	}	



	public function getSerialize($entity_id){
		$sql = "SELECT *
				FROM customer_entity_varchar
				WHERE entity_id = '{$entity_id}'
				AND entity_type_id =1
				AND attribute_id
				IN (201,202)";
		$sqlResult 	= $this->db->query($sql); 		
		return $sqlResult->result();		


	}
	
}
