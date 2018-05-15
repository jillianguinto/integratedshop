<?php
class Categoriesmod extends CI_Model {

    function __construct()
    {
		parent::__construct();
		$this->load->database();		
    }    
	
	//static
	public function post_categoy($data)
	{	
		$this->db->insert('catalog_category_entity', $data);	
		$insert_id = $this->db->insert_id();
   		return  $insert_id;			
	}
	
	//varchar
	public function post_varchar($data)
	{	
		$this->db->insert('catalog_category_entity_varchar', $data);	
		$insert_id = $this->db->insert_id();
   		return  $insert_id;			
	}
	
	//text
	public function post_text($data)
	{	
		$this->db->insert('catalog_category_entity_text', $data);	
		$insert_id = $this->db->insert_id();
   		return  $insert_id;			
	}
	
	//int
	public function post_int($data)
	{	
		$this->db->insert('catalog_category_entity_int', $data);	
		$insert_id = $this->db->insert_id();
   		return  $insert_id;			
	}

	public function put_categoy_path($id, $data) {

	    $this->db->where('entity_id', $id);
	    $this->db->update('catalog_category_entity', $data);

   		return $id;	
	}
	
	
	public function get_entity_type_id()
	{	
		$sql 		= "SELECT * FROM catalog_category_entity";
		$sqlResult 	= $this->db->query($sql); 
		return $sqlResult->result();
	}
	
	public function get_attribute_code($code){

		$query=$this->db->query("SELECT * FROM eav_attribute WHERE attribute_code = '".$code."' "); 
		$attribute_code = $query->result_array();	
		return $attribute_code;	

	}

	public function get_parent($entity_id){

		$query=$this->db->query("SELECT e.*, info.* 
				FROM catalog_category_entity_varchar AS e

				INNER JOIN catalog_category_entity AS info
				ON info.entity_id = e.entity_id

				WHERE e.entity_id = '{$entity_id}'
				AND e.attribute_id = 41 "); 
		$get_parent = $query->result_array();	
		return $get_parent;	

	}
	
	//categories
	public function multipleCategories($catid)
	{	
		$sql = "SELECT DISTINCT(e.entity_id) AS entity_id, e.parent_id AS parent_id, e.path AS path, name.value AS name, status.value AS is_active 
			FROM catalog_category_entity AS e
			INNER JOIN catalog_category_entity_varchar AS name
			ON name.entity_id = e.entity_id
			AND name.attribute_id IN (SELECT attribute_id FROM eav_attribute WHERE attribute_code = 'name')
			
			INNER JOIN catalog_category_entity_int AS status
			ON status.entity_id = e.entity_id
			AND status.attribute_id IN (SELECT attribute_id FROM eav_attribute WHERE attribute_code = 'is_active')		
			
			WHERE e.parent_id ='".$catid."' ";		

		$sqlResult 	= $this->db->query($sql); //WHERE e.parent_id ='".$catid."' ";		
		return $sqlResult->result();	
	}

	public function mainmultipleCategories($catid)
	{	
		$sql = "SELECT e.*, f.*, i.value AS is_active FROM catalog_category_entity AS e INNER JOIN catalog_category_entity_varchar AS f ON f.entity_id = e.entity_id AND f.attribute_id = '41' INNER JOIN catalog_category_entity_int AS i ON i.entity_id = e.entity_id AND i.attribute_id = '42' WHERE e.entity_type_id = '3' AND e.path LIKE '1/174'";		

		$sqlResult 	= $this->db->query($sql); //WHERE e.parent_id ='".$catid."' ";		
		return $sqlResult->result();	
	}


	public function getRootcategory($storeid, $entity_id){	

		$query=$this->db->query("SELECT e.*, f.* 
			FROM catalog_category_entity_varchar AS e 
			INNER JOIN catalog_category_entity AS f 
			ON f.entity_id = e.entity_id
			WHERE store_id = '{$storeid}'");
		$path = $query->result_array();	
		$path_id = $path[0]['parent_id'];

		$query=$this->db->query("SELECT * FROM catalog_category_entity WHERE entity_id = '{$path_id}'");
		$entity_type_id = $query->result_array();	
		$eid = $entity_type_id[0]['entity_type_id']; 		

		//get name
		$query=$this->db->query("SELECT * FROM eav_attribute WHERE attribute_code = 'name' AND entity_type_id = '{$eid}'");
		$attribute_code = $query->result_array();	
		$code_id = $attribute_code[0]['attribute_id']; 

		//get is_active
		$query=$this->db->query("SELECT * FROM eav_attribute WHERE attribute_code = 'is_active' AND entity_type_id = '{$eid}'");
		$is_active = $query->result_array();	
		$is_active_id = $is_active[0]['attribute_id']; 
		
		//get description
		$query=$this->db->query("SELECT * FROM eav_attribute WHERE attribute_code = 'description' AND entity_type_id = '{$eid}'");
		$description = $query->result_array();	
		$description_id = $description[0]['attribute_id']; 		

		$query=$this->db->query("SELECT e.*, f.*, i.value AS is_active, j.value AS description
			FROM catalog_category_entity AS e  
			
			INNER JOIN catalog_category_entity_varchar AS f 
			ON f.entity_id = e.entity_id 
			AND f.attribute_id = '{$code_id}'

			INNER JOIN catalog_category_entity_int AS i 
			ON i.entity_id = e.entity_id 
			AND i.attribute_id = '{$is_active_id}'
			
			INNER JOIN catalog_category_entity_text AS j 
			ON j.entity_id = e.entity_id 
			AND j.attribute_id = '{$description_id}'

			WHERE e.entity_type_id = '{$eid}'  
			AND e.path LIKE '1/{$path_id}/%' 
			AND e.entity_id = '{$entity_id}'
			 

			");
		$rootid = $query->result_array();
		
		return $rootid;
		
	}
	

	//mainsite

	
	//update
	public function getMainsiteRootcategoryupdate($storeid, $entity_id){	

		// $query=$this->db->query("SELECT e.*, f.*, i.value AS is_active 
		// 		FROM catalog_category_entity AS e 
		// 		INNER JOIN catalog_category_entity_varchar AS f 
		// 		ON f.entity_id = e.entity_id 
		// 		AND f.attribute_id = '41' 
		// 		INNER JOIN catalog_category_entity_int AS i 
		// 		ON i.entity_id = e.entity_id 
		// 		AND i.attribute_id = '42' 
		// 		WHERE e.entity_type_id = '3' 
		// 		AND e.path 
		// 		LIKE '1/%' -- OR path LIKE '1/0'
		// 		AND e.entity_id = '{$entity_id}'			 

		// 	");
		// $rootid = $query->result_array();
		
		// return $rootid;


		$query=$this->db->query("SELECT e.*, f.* 
			FROM catalog_category_entity_varchar AS e 
			INNER JOIN catalog_category_entity AS f 
			ON f.entity_id = e.entity_id
			WHERE store_id = '{$storeid}'");
		$path = $query->result_array();	
		$path_id = 1; //$path[0]['parent_id'];

		$query=$this->db->query("SELECT * FROM catalog_category_entity WHERE entity_id = '{$path_id}'");
		$entity_type_id = $query->result_array();	
		$eid = $entity_type_id[0]['entity_type_id']; 		

		//get name
		$query=$this->db->query("SELECT * FROM eav_attribute WHERE attribute_code = 'name' AND entity_type_id = '{$eid}'");
		$attribute_code = $query->result_array();	
		$code_id = $attribute_code[0]['attribute_id']; 

		//get is_active
		$query=$this->db->query("SELECT * FROM eav_attribute WHERE attribute_code = 'is_active' AND entity_type_id = '{$eid}'");
		$is_active = $query->result_array();	
		$is_active_id = $is_active[0]['attribute_id']; 
		
		//get description
		$query=$this->db->query("SELECT * FROM eav_attribute WHERE attribute_code = 'description' AND entity_type_id = '{$eid}'");
		$description = $query->result_array();	
		$description_id = $description[0]['attribute_id']; 		

		$query=$this->db->query("SELECT e.*, f.*, i.value AS is_active, j.value AS description
			FROM catalog_category_entity AS e  
			
			INNER JOIN catalog_category_entity_varchar AS f 
			ON f.entity_id = e.entity_id 
			AND f.attribute_id = '{$code_id}'

			INNER JOIN catalog_category_entity_int AS i 
			ON i.entity_id = e.entity_id 
			AND i.attribute_id = '{$is_active_id}'
			
			INNER JOIN catalog_category_entity_text AS j 
			ON j.entity_id = e.entity_id 
			AND j.attribute_id = '{$description_id}'

			WHERE e.entity_type_id = '{$eid}'  
			AND e.path LIKE '{$path_id}/%' 
			AND e.entity_id = '{$entity_id}'
			 

			");
		$rootid = $query->result_array();
		
		return $rootid;
		
		
	}

	//add
	public function getMainsiteRootcategory($storeid){	

		// $query=$this->db->query("SELECT e.*, f.* 
		// 	FROM catalog_category_entity_varchar AS e 
		// 	INNER JOIN catalog_category_entity AS f 
		// 	ON f.entity_id = e.entity_id
		// 	WHERE store_id = '{$storeid}'");
		// $path = $query->result_array();	
		// $path_id = $path[0]['parent_id'];
		// //echo $this->db->last_query();
		// //too many values


		// $query=$this->db->query("SELECT * FROM catalog_category_entity WHERE entity_id = '{$path_id}'");
		// $entity_type_id = $query->result_array();	
		// $eid = $entity_type_id[0]['entity_type_id']; 		
		// //value not found

		// //get name
		// $query=$this->db->query("SELECT * FROM eav_attribute WHERE attribute_code = 'name' AND entity_type_id = '{$eid}'");
		// $attribute_code = $query->result_array();	
		// $code_id = $attribute_code[0]['attribute_id']; 
		// //no value for eid


		// //get is_active
		// $query=$this->db->query("SELECT * FROM eav_attribute WHERE attribute_code = 'is_active' AND entity_type_id = '{$eid}'");
		// $is_active = $query->result_array();	
		// $is_active_id = $is_active[0]['attribute_id']; 
		// //no value for eid



		// $query=$this->db->query("SELECT e.*, f.*, i.value AS is_active
		// 	FROM catalog_category_entity AS e  
			
		// 	INNER JOIN catalog_category_entity_varchar AS f 
		// 	ON f.entity_id = e.entity_id 
		// 	AND f.attribute_id = '{$code_id}'

		// 	INNER JOIN catalog_category_entity_int AS i 
		// 	ON i.entity_id = e.entity_id 
		// 	AND i.attribute_id = '{$is_active_id}'

		// 	WHERE e.entity_type_id = '{$eid}'  
		// 	AND e.path LIKE '1/%' 
		// 	-- OR path LIKE '1/{$path_id}'		 

		// 	");
		//missing valie for attribyuteid
		

		$query = $this->db->query("SELECT DISTINCT(e.entity_id), e.path, e.parent_id, f.value AS value, i.value AS is_active 
			FROM catalog_category_entity AS e 
			INNER JOIN catalog_category_entity_varchar AS f 
			ON f.entity_id = e.entity_id 
			AND f.attribute_id = '41' 
			INNER JOIN catalog_category_entity_int AS i 
			ON i.entity_id = e.entity_id 
			AND i.attribute_id = '42' 
			WHERE e.entity_type_id = '3' 
			AND e.path 
			LIKE '1/%'");

		$rootid = $query->result_array();
		

		return $rootid;

		

		

		
	}

	##### update

	 
	//static
	public function put_categoy($id, $value, $attr_id)
	{			
		$this->db->query("UPDATE catalog_category_entity SET value = '".$value."' WHERE entity_id = '".$id."' AND attribute_id = '".$attr_id."' ");
	}
	
	//varchar
	public function put_varchar($id, $data)
	{			
		$this->db->query("UPDATE catalog_category_entity_varchar SET value = '".$value."' WHERE entity_id = '".$id."' AND attribute_id = '".$attr_id."' ");
	}
	
	//text
	public function put_text($id, $data)
	{	   		
		$this->db->query("UPDATE catalog_category_entity_text SET value = '".$value."' WHERE entity_id = '".$id."' AND attribute_id = '".$attr_id."' ");
	}
	
	//int
	public function put_int($id, $data)
	{   			
		$this->db->query("UPDATE catalog_category_entity_int SET value = '".$value."' WHERE entity_id = '".$id."' AND attribute_id = '".$attr_id."' ");
	
	}


	public function getPathId($sid){
		//$query="SELECT e.*, f.* FROM catalog_category_entity_varchar AS e INNER JOIN catalog_category_entity AS f ON f.entity_id = e.entity_id	//WHERE e.store_id = in($sid)";
		//$this->db->close();
	//	print_r($query->result());
		//die();
		//return $query->result_array();
		
		$sql 	= "SELECT e.*, f.* FROM catalog_category_entity_varchar AS e INNER JOIN catalog_category_entity AS f ON
				 f.entity_id =	e.entity_id	WHERE e.store_id  in ($sid)";
		$sqlResult = $this->db->query($sql); 
		
		return $sqlResult->result();
			
	}

    
}

?>