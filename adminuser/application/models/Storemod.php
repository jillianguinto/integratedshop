<?php
class Storemod extends CI_Model {

    function __construct()
    {
		parent::__construct();
		$this->load->database();		

    }
	
	function getAllStore()
	{
	$sql = "SELECT cs.store_id, cs.name,
CASE 
WHEN ccd.path LIKE  '%design/header/logo_src%'
THEN CONCAT( (

SELECT ccd2.value
FROM core_config_data ccd2
WHERE ccd2.path =  'web/secure/base_url'
AND ccd2.scope_id = ccd.scope_id
), ccd.value ) 
ELSE  '0'
END  'imagePath', ccd.value
FROM core_store cs
INNER JOIN core_config_data ccd ON cs.store_id = ccd.scope_id  and ccd.scope = 'stores'
WHERE cs.code NOT LIKE  '%admin%'
AND ccd.path LIKE  '%design/header/logo_src%'
AND cs.name NOT LIKE  '%admin%'
UNION ALL 
SELECT cs.store_id, cs.name, '0' as 'imagePath', ccd.value
FROM core_store cs
INNER JOIN core_config_data ccd ON cs.store_id = ccd.scope_id  and ccd.scope = 'stores'
WHERE cs.code NOT LIKE  '%admin%'
AND ccd.path LIKE  '%web/secure/base_url%'
AND cs.name NOT LIKE  '%admin%'
AND cs.store_id NOT 
IN (

SELECT cs.store_id
FROM core_store cs
INNER JOIN core_config_data ccd ON cs.store_id = ccd.scope_id and ccd.scope = 'stores'
WHERE cs.code NOT LIKE  '%admin%'
AND ccd.path LIKE  '%design/header/logo_src%'
AND cs.name NOT LIKE  '%admin%'
)";
 
		$sqlResult 		= $this->db->query($sql); 
	// print_r($sqlResult);
	
		
		return $sqlResult->result();

		
		// $this->db->select('*');
		// $this->db->from('core_store');
		// $this->db->where("core_store.code NOT LIKE '%admin%' -- AND core_store.code NOT LIKE '%default%'");
		// $this->db->where("core_store.is_active = 1");
		
		
		
		 
		
		//$this->db->where("core_store.code NOT LIKE '%default%'");


		// echo $this->db->get();
		// exit();



		// $sqlResult = $this->db->get();		
			
		// print_r($sqlResult->result());
		// return $sqlResult->result();
	}
	
}
?>