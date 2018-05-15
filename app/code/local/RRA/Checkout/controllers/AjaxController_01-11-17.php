<?php


class RRA_Checkout_AjaxController extends Mage_Core_Controller_Front_Action
{
    public function onepageAction()
    {  
    	if(isset($_POST['region_id'])){

			$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
			$connection->beginTransaction();

	    	$provinceid = $_POST['region_id'];
			$cityid = $_POST['city_id'];

	    	$qrycities = $connection->select()->from('unilab_cities', array('*')) 
						->where('region_id=?',$provinceid)	
						->order(array('name ASC'));
			$cities = $connection->fetchAll($qrycities);	
				
			$opt = '';

			$opt .= "<option value=''>--Select City--</option>";
			foreach ($cities as $value) 
			{ 
				$cities_id = $value['city_id'];
				$cities_name = $value['name'];
				
				if($cities_id == $cityid){
					$opt .= "<option value='$cities_id' selected>$cities_name</option>";
				}
				
				$opt .= "<option value='$cities_id'>$cities_name</option>";
				

			}	

	    	echo $opt;
	    }	
    }	

    public function webuiltthiscityAction()
    {  
    	if(isset($_POST['region_id'])){

			$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
			$connection->beginTransaction();

	    	$provinceid = $_POST['region_id'];

	    	$qrycities = $connection->select()->from('unilab_cities', array('*')) 
						->where('region_id=?',$provinceid)	
						->order(array('name ASC'));
			$cities = $connection->fetchAll($qrycities);	
				
			$opt = '';

			$opt .= "<option value=''>--Select City--</option>";
			foreach ($cities as $value) 
			{ 
				$cities_id = $value['city_id'];
				$cities_name = $value['name'];
			if($_POST['cityval'] == $cities_name):
				$opt .= "<option value='{$cities_id}' selected>{$cities_name}</option>";
			else:
				$opt .= "<option value='{$cities_id}'>{$cities_name}</option>";
			endif;

			}	

	    	echo $opt;
	    }	
    }

}    