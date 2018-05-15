<?php
class Productsmod extends CI_Model {

    function __construct()
    {
		parent::__construct();
		$this->load->database();		

    }
	
	function getAllProducts($store_id)
	{	
		// $sql = "SELECT website_id FROM core_store WHERE store_id in ($store_id)";
		// $sqlResult 		= $this->db->query($sql); 	
		// $webID= null;
	// if($sqlResult->num_rows() > 0):
		// foreach($sqlResult->result() as $_item){

			// foreach($_item as $_value){

			// $webID .= "'$_value',";

			// }

		// }  
		// $webID = substr($webID, 0 , -1);
		// endif;
		
		// $this->db->select('catalog_product_website.product_id,catalog_product_entity.*,catalog_product_entity_varchar.value');
		// $this->db->from('catalog_product_website');
		// $this->db->join('catalog_product_entity', 'catalog_product_entity.entity_id = catalog_product_website.product_id');
		// $this->db->join('catalog_product_entity_varchar', 'catalog_product_entity_varchar.entity_id = catalog_product_entity.entity_id AND catalog_product_entity_varchar.attribute_id=71');
		// $this->db->where("catalog_product_website.website_id in($webID)");
		// $this->db->group('sku,');
		// $sqlResult = $this->db->get();			
		

		// return $sqlResult->num_rows();
	
	
		$sql = "select catalog_product_website.product_id,catalog_product_entity.*,
catalog_product_entity_varchar.value,cs.store_id
from catalog_product_website
inner join catalog_product_entity on catalog_product_entity.entity_id = catalog_product_website.product_id 
inner join catalog_product_entity_varchar on catalog_product_entity_varchar.entity_id = catalog_product_entity.entity_id 
inner join core_store cs on catalog_product_website.website_id = cs.website_id
AND catalog_product_entity_varchar.attribute_id in(71)
where cs.store_id in ($store_id)
group by sku,cs.store_id";
		$sqlResult 	= $this->db->query($sql);
		return count($sqlResult->result());

	}	
	
	function getAllProductslist($store_id)
	{	
	

		// $sqlWeb 		= "SELECT website_id FROM core_store WHERE store_id in ($store_id)";
		
		// $sqlwebsiteid 	= $this->db->query($sqlWeb);
			// $webID = null;
		// if($sqlwebsiteid->num_rows() > 0):
		// foreach($sqlwebsiteid->result() as $_value){
		
				// if(!empty($_value)){
					// $webID .= "'$_value->website_id',";
				// }
			// }		
			
		// endif;
		// $webID = substr($webID, 0 , -1);
		
		// $this->db->select('catalog_product_website.product_id,catalog_product_entity.*,catalog_product_entity_varchar.value');
		// $this->db->from('catalog_product_website');
		// $this->db->join('catalog_product_entity', 'catalog_product_entity.entity_id = catalog_product_website.product_id');
		// $this->db->join('catalog_product_entity_varchar', 'catalog_product_entity_varchar.entity_id = catalog_product_entity.entity_id AND catalog_product_entity_varchar.attribute_id=71');
		// $this->db->where("catalog_product_website.website_id in($store_id)"); 

		// $sqlResult = $this->db->get();			
		// echo count($sqlResult->result());
		// die();
		
		$sql = "select catalog_product_website.product_id,catalog_product_entity.*,
catalog_product_entity_varchar.value,cs.store_id
from catalog_product_website
inner join catalog_product_entity on catalog_product_entity.entity_id = catalog_product_website.product_id 
inner join catalog_product_entity_varchar on catalog_product_entity_varchar.entity_id = catalog_product_entity.entity_id 
inner join core_store cs on catalog_product_website.website_id = cs.website_id
AND catalog_product_entity_varchar.attribute_id in(71)
where cs.store_id in ($store_id)
group by sku,cs.store_id";
		$sqlResult 	= $this->db->query($sql);
		return $sqlResult->result();
		

	}	

	
	
		function getAllProductslistExport($store_id)
	{	
		$sql = "select catalog_product_website.product_id,catalog_product_entity.*,
catalog_product_entity_varchar.value,cs.store_id
from catalog_product_website
inner join catalog_product_entity on catalog_product_entity.entity_id = catalog_product_website.product_id 
inner join catalog_product_entity_varchar on catalog_product_entity_varchar.entity_id = catalog_product_entity.entity_id 
inner join core_store cs on catalog_product_website.website_id = cs.website_id
AND catalog_product_entity_varchar.attribute_id in(71)
where cs.store_id in ($store_id)
group by sku,cs.store_id";
		$sqlResult 	= $this->db->query($sql);
		return $sqlResult;
		

	}	

	public function getProductInformationById($id){

		//note remove: at_generic_name.value AS generic_name, 
		// note this -- sign indicate that the syntax is error donot remove it will use in the future

		$this->db->select('c.sku, 
			c.entity_id, 
			at_name.value AS name,
			at_url_key.value AS url_key, 
			at_unilab_moq.value AS unilab_moq,
			at_unilab_benefit.value AS unilab_benefit, 
			at_image.value AS image, 
			at_small_image.value AS small_image, 
			at_thumbnail.value AS thumbnail,
			at_description.value AS description, 

			at_description.value AS description, 
			at_short_description.value AS short_description, 	

			at_price.value AS price, 
		
			at_weight.value AS weight, 
			at_unilab_unit_price.value AS unilab_unit_price,

			at_status.value AS status,
			at_visibility.value AS visibility,
			at_unilab_rx.value AS unilab_rx,
			at_tax_class_id.value AS tax_class_id

			');

		$this->db->from('catalog_product_entity AS c');

		// -- varchar

		$this->db->join('catalog_product_entity_varchar AS at_name', 'at_name.entity_id = c.entity_id AND at_name.attribute_id=71');
		$this->db->join('catalog_product_entity_varchar AS at_url_key', 'at_url_key.entity_id = c.entity_id AND at_url_key.attribute_id=97');
		$this->db->join('catalog_product_entity_varchar AS at_unilab_moq', 'at_unilab_moq.entity_id = c.entity_id AND at_unilab_moq.attribute_id=181');
		// -- $this->db->join('catalog_product_entity_varchar AS at_generic_name', 'at_generic_name.entity_id = c.entity_id AND at_generic_name.attribute_id=174');
		$this->db->join('catalog_product_entity_varchar AS at_unilab_benefit', 'at_unilab_benefit.entity_id = c.entity_id AND at_unilab_benefit.attribute_id=171');
		$this->db->join('catalog_product_entity_varchar AS at_image', 'at_image.entity_id = c.entity_id AND at_image.attribute_id=85');
		$this->db->join('catalog_product_entity_varchar AS at_small_image', 'at_small_image.entity_id = c.entity_id AND at_small_image.attribute_id=85');
		$this->db->join('catalog_product_entity_varchar AS at_thumbnail', 'at_thumbnail.entity_id = c.entity_id AND at_thumbnail.attribute_id=87');
		
		
		// -- text
		$this->db->join('catalog_product_entity_text AS at_description', 'at_description.entity_id = c.entity_id AND at_description.attribute_id=72');
		$this->db->join('catalog_product_entity_text AS at_short_description', 'at_short_description.entity_id = c.entity_id AND at_short_description.attribute_id=73');

		//decimal
		$this->db->join('catalog_product_entity_decimal AS at_price', 'at_price.entity_id = c.entity_id AND at_price.attribute_id=75');
		
		// -- $this->db->join('catalog_product_entity_decimal AS at_special_price', 'at_special_price.entity_id = c.entity_id AND at_special_price.attribute_id=76');
		
		$this->db->join('catalog_product_entity_decimal AS at_weight', 'at_weight.entity_id = c.entity_id AND at_weight.attribute_id=80');
		$this->db->join('catalog_product_entity_decimal AS at_unilab_unit_price', 'at_unilab_unit_price.entity_id = c.entity_id AND at_unilab_unit_price.attribute_id=184');
		
		// -- datetime
		//-- $this->db->join('catalog_product_entity_datetime AS at_special_from_date', 'at_special_from_date.entity_id = c.entity_id AND at_special_from_date.attribute_id=77');
		//-- $this->db->join('catalog_product_entity_datetime AS at_special_to_date', 'at_special_to_date.entity_id = c.entity_id AND at_special_to_date.attribute_id=78');

		//-- int
		$this->db->join('catalog_product_entity_int AS at_status', 'at_status.entity_id = c.entity_id AND at_status.attribute_id=96');
		$this->db->join('catalog_product_entity_int AS at_visibility', 'at_visibility.entity_id = c.entity_id AND at_visibility.attribute_id=102');
		$this->db->join('catalog_product_entity_int AS at_unilab_rx', 'at_unilab_rx.entity_id = c.entity_id AND at_unilab_rx.attribute_id=167');
		$this->db->join('catalog_product_entity_int AS at_tax_class_id', 'at_tax_class_id.entity_id = c.entity_id AND at_tax_class_id.attribute_id=122');

		$this->db->where("c.entity_id='$id'");

		$sqlResult = $this->db->get();		
		return $sqlResult->result();		

	}	


	public function get_website_name($store_id){	
		$query=$this->db->query("SELECT * FROM core_store WHERE website_id in ($store_id)"); 
		$website_name = $query->result_array();		
		return $website_name;
	}					


	public function create_product_settings($entity_type_id){

		$query=$this->db->query("SELECT * FROM eav_attribute_set WHERE entity_type_id = $entity_type_id"); 
		$eav_attribute_set = $query->result_array();	
		return $eav_attribute_set;	

	}


	public function getRootcategory($storeid){	

		$query=$this->db->query("SELECT e.*, f.* 
			FROM catalog_category_entity_varchar AS e 
			INNER JOIN catalog_category_entity AS f 
			ON f.entity_id = e.entity_id
			WHERE store_id in ($storeid)");
			
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

		$query=$this->db->query("SELECT DISTINCT(e.entity_id), e.path, e.parent_id, f.value AS value, i.value AS is_active 
			FROM catalog_category_entity AS e  
			
			INNER JOIN catalog_category_entity_varchar AS f 
			ON f.entity_id = e.entity_id 
			AND f.attribute_id = '{$code_id}'

			INNER JOIN catalog_category_entity_int AS i 
			ON i.entity_id = e.entity_id 
			AND i.attribute_id = '{$is_active_id}'

			WHERE e.entity_type_id = '{$eid}'  
			AND e.path LIKE '1/{$path_id}/%' 
			-- OR path LIKE '1/{$path_id}'		 

			");
		$rootid = $query->result_array();
		
		return $rootid;

		
	}
	
	public function getsubmaincategory($storeid){
		$query=$this->db->query("SELECT e.*, f.* 
			FROM catalog_category_entity_varchar AS e 
			INNER JOIN catalog_category_entity AS f 
			ON f.entity_id = e.entity_id
			WHERE store_id in ($store_id)");
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


		$query=$this->db->query("SELECT e.*, f.*, i.value AS is_active
			FROM catalog_category_entity AS e  
			
			INNER JOIN catalog_category_entity_varchar AS f 
			ON f.entity_id = e.entity_id 
			AND f.attribute_id = '{$code_id}'

			INNER JOIN catalog_category_entity_int AS i 
			ON i.entity_id = e.entity_id 
			AND i.attribute_id = '{$is_active_id}'

			WHERE e.entity_type_id = '{$eid}'  
			AND e.path LIKE '1/{$path_id}'

			");
		$rootid = $query->result_array();
		return $rootid;		
	}


	public function prodCatId($id) 
	{
		$query=$this->db->query("select * from catalog_category_product WHERE product_id ='{$id}'");
		return $product_id = $query->result_array();

	}	

	public function ExportCSV() 
	{
        $this->load->dbutil();
        $this->load->helper('file');
        $this->load->helper('download');
        $delimiter = ",";
        $newline = "\r\n";
        $filename = "manage_products.csv";
        $query = "SELECT * FROM eav_attribute WHERE entity_type_id = 4";
       	$result = $this->db->query($query);
        $data = $this->dbutil->csv_from_result($result, $delimiter, $newline);
        force_download($filename, $data);

	}

	
	
}
?>