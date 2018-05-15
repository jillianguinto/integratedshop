<?php
class Unilab_Minimumordervalue_Block_Adminhtml_Minimumordervalue_Edit_Renderer_Cities 
extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 
	public function render(Varien_Object $row)
	{
		$cities =  $row->getData($this->getColumn()->getIndex());
		
		
		$connection 	= Mage::getSingleton('core/resource')->getConnection('core_read');
		$connection->beginTransaction();
		
		
		$cityArray = explode(',', $cities);
		
		foreach($cityArray as $cityArr)
		{
			
			$qryCity 	=	$connection->select()->from('unilab_cities', array('name'))
								->where('city_id=?',$cityArr); 
						
			$rsCity 	= $connection->fetchRow($qryCity);
			$city		= $rsCity['name'] . ', ';
			
			$listofcities = $listofcities . $city;
			
		}
		
		return $listofcities; 
	 
	}
 
}
?>  