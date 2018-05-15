<?php 

class Unilab_Inquiry_Block_Quickaccess extends Unilab_Inquiry_Block_Form
{ 	 
	public function _construct()
	{
		parent::_construct();
		
		$this->setTemplate("inquiry/form/quickaccess.phtml");
	} 
	
	public function getHashDepartmentCode($code)
	{ 
		if($department_data = Mage::helper('inquiry')->getDepartmentByCode($code,false))
		{
			return md5($department_data->getCode()); 	
		}		
		return false;
	}
}