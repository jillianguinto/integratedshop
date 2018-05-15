<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Categories extends CI_Controller {
	
	public 		$type_id 			= array();
	public 		$set_id  			= array();
	public 		$i 		 			= 0;
	public 		$attribute_code		= array();
	protected 	$id;	
	protected 	$attribute_set_id;
	protected 	$entity_type_id;	
	
	private $exists_ids =array();
	private $not_exists_ids = array();
		
	public function __construct()
	{
		parent::__construct();

		$this->load->helper(array('form','url', 'islogged_helper'));

       		$this->load->library('form_validation');
		$this->load->model('Customermod');		
		$this->load->model('Productsmod');
		$this->load->model('Categoriesmod');
		$this->load->model('Promotionsmod');		
		$this->load->helper('url');		
		//$this->id =  store_id();	
		$store_id  	= isstore_id();
		//Get column [entity_type_id, attribute_set_id] from catalog_category_entity automatically
		$data	= $this->Categoriesmod->get_entity_type_id();			
		
		foreach($data as $value){
			$askey = $this->i++;
			
			$this->type_id[]  =  $data[$askey]->entity_type_id;
			$this->attribute_set_id =  $data[$askey]->attribute_set_id;
			if($this->attribute_set_id > 0){
				$set_id[] = $this->attribute_set_id;
			}					
		}
		
		foreach(array_unique($this->set_id) as $setkey=>$set){
			($setkey == 0)? $this->attribute_set_id = $set: null;
		}
		
		foreach(array_unique($this->type_id) as $typekey=>$type){
			($typekey == 0)? $this->entity_type_id = $type: null;
		}	
		$this->store_id = $store_id;	
		
	}
	 		 	 
		 
	public function index()
	{						
						
		$this->load->model('Categoriesmod');	
		$this->load->model('Productsmod');
		
		$wid 		= $this->store_id;
		
		$store_ids 	= astore_id($wid); 
		
		$path_id 	= $this->Categoriesmod->getPathId($store_ids);	
		$path_id1 = null;
		//$pathid 	= $path_id[0]['parent_id'];		
		foreach($path_id as $value):
			$path_id1 = $value->parent_id;
			break;
		endforeach;	
		
		if($path_id1 == 0){

		 	$parent_id =1;

		 	$store_ids;

			$userdata['categories'] = $this->Categoriesmod->getMainsiteRootcategory($parent_id);			

		 	$this->main_category_tree($parent_id); 		
			$userdata['parentid'] = $parent_id;	

		}else{

			$parent_id  = $path_id1;

			$store_ids;


			$userdata['categories'] 		= $this->Productsmod->getRootcategory($store_ids);		
			$userdata['root']				= $this->Productsmod->getsubmaincategory($store_ids);
			

			$get_parent = $this->Categoriesmod->get_parent($parent_id);		
			
			$categname 	= $get_parent[0]['value'];
			$entity_id 	= $get_parent[0]['entity_id'];
			$path 		= $get_parent[0]['path'];

			echo '<div class="menulevel" style="display:none;">';
				echo '<ul class="categoryTree">';
					echo '<li id="'.$entity_id.'">';
					
						echo '<a title="'.$entity_id.'" id="'.$entity_id.'" entity_id="'.$entity_id.'" path="'.$path.'" href="javascript:void(0)">'.$categname.'</a>';
							$this->category_tree($parent_id); 		
						echo '</li>';
				echo '</ul>';
			echo '</div>';
			
			
			$userdata['parentid'] = $parent_id;	

		}	
			
		$this->load->view('head/head');
		$this->load->view('sidebar/menu',$userdata);
		$this->load->view('include/listcategories');
		$this->load->view('footer/footer');	
	}		


	public function category_update_view(){

		$id = $this->uri->segment(3, 0);  

		$this->load->model('Categoriesmod');	
		$this->load->model('Productsmod');
			
		$wid 		= isstore_id();
		$store_ids 	= astore_id($wid); 	

		$path_id 	= $this->Categoriesmod->getPathId($store_ids);		
		$pathid 	= $path_id[0]['parent_id'];	

		if($pathid == 0){

		 	$parent_id =1;

		 	$store_ids;

			$userdata['categories_update'] = $this->Categoriesmod->getMainsiteRootcategoryupdate($parent_id, $id);	

		 	$this->main_category_tree($parent_id); 		
			$userdata['parentid'] = $parent_id;	

		}else{

			$parent_id  = $pathid;

			$userdata['categories_update'] 	= $this->Categoriesmod->getRootcategory($store_ids, $id);	


			//$userdata['categories'] 		= $this->Productsmod->getRootcategory($store_ids);		
			//$userdata['root']				= $this->Productsmod->getsubmaincategory($store_ids);		

			$get_parent = $this->Categoriesmod->get_parent($parent_id);		
			
			$categname 	= $get_parent[0]['value'];
			$entity_id 	= $get_parent[0]['entity_id'];
			$path 		= $get_parent[0]['path'];

			echo '<div class="menulevel" style="display:none;">';
				echo '<ul class="categoryTree">';
					echo '<li id="'.$entity_id.'">';
					
						echo '<a title="'.$entity_id.'" id="'.$entity_id.'" entity_id="'.$entity_id.'" path="'.$path.'" href="javascript:void(0)">'.$categname.'</a>';
							$this->category_tree($parent_id); 		
						echo '</li>';
				echo '</ul>';
			echo '</div>';

			//die();	
			$userdata['parentid'] = $parent_id;	

		}		

		$this->load->view('head/head');
		$this->load->view('sidebar/menu',$userdata);
		$this->load->view('include/listcategoriesupdate');
		$this->load->view('footer/footer');		

	}	

	
	
	//recursive menu function
	public function category_tree($catid){		
		
		$this->load->model('Categoriesmod');	
		$rows = $this->Categoriesmod->multipleCategories($catid);	
		//$queMe = $this->db->last_query();

		foreach ($rows as $k=> $row)
		{			
			if($rows[$k]->is_active == 1){				

				echo '<ul id="'.$rows[$k]->entity_id.'">';
					echo '<li id="'.$rows[$k]->entity_id.'"><a title="'.$rows[$k]->entity_id.'" id="'.$rows[$k]->entity_id.'" entity_id="'.$rows[$k]->entity_id.'" path="'.$rows[$k]->path.'" href="javascript:void(0)" >' . $rows[$k]->name; 
						$this->category_tree($rows[$k]->entity_id);
					echo '</a></li>';	
				
				echo '</ul>';

			}
		}	
	
	}

	public function main_category_tree($catid){		
		
	
		$this->load->model('Categoriesmod');	
		$rows = $this->Categoriesmod->multipleCategories($catid);	
		//$queMe = $this->db->last_query();

		//print_r($queMe);
		echo '<div class="menulevel" style="display:none;">';
		foreach ($rows as $k=> $row)
		{			
			if($rows[$k]->is_active == 1){				

				echo '<ul class="categoryTree" id="'.$rows[$k]->entity_id.'">';
					echo '<li id="'.$rows[$k]->entity_id.'"><a title="'.$rows[$k]->entity_id.'" id="'.$rows[$k]->entity_id.'" entity_id="'.$rows[$k]->entity_id.'" path="'.$rows[$k]->path.'" href="javascript:void(0)" >' . $rows[$k]->name; 
						$this->category_tree($rows[$k]->entity_id);
					echo '</a></li>';	
				
				echo '</ul>';

			}
		}	
		echo '</div>';	
	
	}
	
	

	public function category_update(){

		$this->load->model('Categoriesmod');
		$data = array(
			//'parent_id'=>$this->input->post("parent_id"),
			//'entity_id'=>$this->input->post("entity_id"),
			//'catname'=>$this->input->post("catname"),
			'name'=>$this->input->post("name"),
			'is_active'=>$this->input->post("is_active"),
			'description'=>$this->input->post("description")
		);


		
		$id= $this->input->post("entity_id");

		$this->load->model('Categoriesmod');
		
		foreach($data as $postkey=> $post){			
			$this->attribute_code	= $this->Categoriesmod->get_attribute_code($postkey);	
			$x = 0; 
		

			foreach($this->attribute_code as $codeindex=>$attr_code){			
				
			$inc = $x++;		
				
			$entity_type_id = $this->attribute_code[$inc]['entity_type_id'];
			$attribute_code = $this->attribute_code[$inc]['attribute_code'];
			$backend_type 	= $this->attribute_code[$inc]['backend_type'];
			$attribute_id 	= $this->attribute_code[$inc]['attribute_id'];
			
				
				if($attribute_code =='name' || $attribute_code =='is_active' || $attribute_code =='description' ){		
				

					$value = $this->input->post($attribute_code);
					$attr_id = $attribute_id;
					
					//$func_name = 'put_'.$backend_type;					
					//$this->Categoriesmod->$func_name($id, $value, $attr_id);
					$this->db->query("UPDATE catalog_category_entity_{$backend_type} SET value = '".$value."' WHERE entity_id = '".$id."' AND attribute_id = '".$attr_id."' ");
	

				}					
			}		
		}	

	}

	public function add_category(){		

			$data = array(
			'entity_id' =>'NUll',
			'entity_type_id' 	=>$this->entity_type_id,
			'attribute_set_id' 	=>$this->attribute_set_id,
			'parent_id' 		=>$this->input->post("parent_id"),
			'created_at' 		=>date("Y-m-d H:i:s", strtotime('now')),
			'updated_at' 		=>date("Y-m-d H:i:s", strtotime('now')),
			'path' 				=>$this->input->post("path"),
			//'position' 		=> '',
			//'level' 			=> '',
			//'children_count' 	=> '' 
			);		
	
		$this->load->model('Categoriesmod');
		$id = $this->Categoriesmod->post_categoy($data);
		
		$update_path = array(
		'path' => $this->input->post("path").'/'.$id		
			);

		$path_id = $this->Categoriesmod->put_categoy_path($id, $update_path);
	
		// compare key to eav_attribute table	
		foreach($_POST as $postkey=> $post){			
			$this->attribute_code	= $this->Categoriesmod->get_attribute_code($postkey);	
			$x = 0; 
			foreach($this->attribute_code as $codeindex=>$attr_code){			
				
				$inc = $x++;		
				
				$entity_type_id = $this->attribute_code[$inc]['entity_type_id'];
				$attribute_code = $this->attribute_code[$inc]['attribute_code'];
				$backend_type 	= $this->attribute_code[$inc]['backend_type'];
				$attribute_id 	= $this->attribute_code[$inc]['attribute_id'];
				
				if($attribute_code !='path'){
				
					$datas_ = array(
						'value_id' 			=>'NULL', 
						'entity_type_id' 	=>$entity_type_id , 
						'attribute_id'		=>$attribute_id , 
						'store_id' 			=>0,//$this->id,	
						'entity_id'			=>$path_id,
						'value'				=>$this->input->post($attribute_code)
					);

					$func_name = 'post_'.$backend_type; 					
					$this->Categoriesmod->$func_name($datas_);


				}					
			}		
		}		
	}		


	public function delete_categroy() 
	{
        $id =  $_POST['entity_id'];
		
		$this->db->query("DELETE FROM catalog_category_entity WHERE entity_id = '{$id}'");
		
		
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