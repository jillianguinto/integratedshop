<?php 

class Unilab_Inquiry_Helper_Data extends Mage_Core_Helper_Abstract
{

    const XML_PATH_ENABLED   	 = 'unilab_customers/inquiry/enabled';
    const XML_PATH_DEPARTMENTS   = 'unilab_customers/inquiry/departments';

    public function isEnabled()
    {
        return Mage::getStoreConfig( self::XML_PATH_ENABLED );
    }

    public function getUserName()
    {
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            return '';
        }
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        return trim($customer->getName());
    }

    public function getUserEmail()
    {
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            return '';
        }
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        return $customer->getEmail();
    }

    public function getDepartmentOptions()
    {
        $departments = unserialize(Mage::getStoreConfig(self::XML_PATH_DEPARTMENTS));
        $options = array();
        foreach ($departments as $department){
        	
            if (!$department['name'] || !$department['email'] || $department['code'] =='DOCTOR' ||  $department['code'] =='PHARMACIST') continue;
            
            $options[] = array(
                'label' => $department['name'],
                'value' => md5($department['code']),
                'sortorder' => $department['sortorder'],
                
            );
        } 
		$this->sortDepartments($options,'sortorder');
        if (count($options)){
            array_unshift($options, array('value' => '', 'label' => $this->__('-- Please Select --')));
        }
        return $options;
    }

    public function getDepartmentByHash($hash)
    {
        if($departments = unserialize(Mage::getStoreConfig(self::XML_PATH_DEPARTMENTS))){
			foreach ($departments as $department){
				if (!$department['name'] || !$department['email']) continue;
				if (md5($department['email']) == $hash){
					return array('name' => $department['name'], 'email' => $department['email']);
				}
			}
		}
        return null;
    }
	
	public function getDepartmentByCode($code, $hash = true)
	{
		if($departments	= unserialize(Mage::getStoreConfig(self::XML_PATH_DEPARTMENTS))){		
			$department_obj = new Varien_Object();
			foreach ($departments as $department){
			
				if (!$department['code'] || !$department['email'] || !$department['template']) continue;  
				
				if($hash){
					if($code == md5($department['code'])){
						return $department_obj->setData($department);
					}
				}else{
					if($code == $department['code']){
						return $department_obj->setData($department);
					}
				}			
			}
		}
        return null;
	}
	
	protected function sortDepartments(&$options, $col, $dir = SORT_ASC)
	{ 
		$sort_col = array();
		foreach ($options as $key=> $row) {
			$sort_col[$key] = $row[$col];
		} 
		array_multisort($sort_col, $dir, $options);
	} 
	
	
	public function getAllDepartments(){
		
		$departments = unserialize(Mage::getStoreConfig(self::XML_PATH_DEPARTMENTS));
        $options = array();
        foreach ($departments as $department){        	
            $options[$department['code']] = array(
            	'code' => $department['code'],
                'name' => $department['name'],                
                'email' => $department['email'],
                'subject' => $department['subject'],
                'template' => $department['template'],
                'sortorder' => $department['sortorder']                
            );
        }
		return $options;		
	}
	
	public function getDepartmentByCodeCstm($code,$isHash=false)
	{		
		$options = $this->getAllDepartments();
		if(!$isHash){
			if(isset($options[$code])){
				return $options[$code];
			}	
		}else{
			foreach($options as $key=>$option){
				if(md5($key) == $code){
					return $option;
				}
			}
		}
		
		return '';
	}
	
}
