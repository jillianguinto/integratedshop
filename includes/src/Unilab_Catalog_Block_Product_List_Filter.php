<?php

/*
 * @package Unilab_Catalog
 * @author Jerick Y. Duguran - Movent, Inc.
 * @date November 14, 2013
 * @action Handle Attribute Filter
 */ 
 
class Unilab_Catalog_Block_Product_List_Filter extends Mage_Catalog_Block_Layer_View
{ 
    const OPTIONS_ONLY_WITH_RESULTS = 1;
	
	public function _construct()
	{
		parent::_construct();
		
		$this->setTemplate("catalog/product/list/filter.phtml");
	}
	
	protected function _getEnabledAttributeFilters()
	{
		$attribute_ids = explode(",",$this->getEnabledAttributeFilter());
		
		if(!$this->getData('attribute_filter_ids')){
			$this->setData('attribute_filter_ids',$attribute_ids);
		}				
		return $this->getData('attribute_filter_ids'); 
	} 
	
	public function getAttributeFilters()
	{ 
		$request = $this->getRequest();
		
		if(!$this->getData('attribute_filters'))
		{
			$filterableAttributes 	 = $this->_getFilterableAttributes();  
			$enabledfilterattributes = $this->_getEnabledAttributeFilters(); 
			$attributes  			 = array();			 
			
			foreach ($filterableAttributes as $attribute){ 
				if(in_array($attribute->getAttributeId(),$enabledfilterattributes)){		
					$options 	 	 = $attribute->getFrontend()->getSelectOptions();  
					$data_options 	 = array(array('label'		=> '---',
												   'value'		=> $this->getPagerUrl(array(),$attribute->getAttributeCode()),
												   'selected' 	=> ''));
					
					foreach ($options as $option){
						if (is_array($option['value'])){
							continue;
						}
						if (Mage::helper('core/string')->strlen($option['value'])){
							//if ($this->_getIsFilterableAttribute($attribute) == self::OPTIONS_ONLY_WITH_RESULTS) {							
								$attribute_filter_id = $request->getParam($attribute->getAttributeCode());								
								if (!empty($option['value'])) {
									$data_options[] = array(
										'label' 	=> $option['label'],
										'value' 	=> $this->getPagerUrl(array($attribute->getAttributeCode() => $option['value'])),
										'selected'	=> ($attribute_filter_id == $option['value'] ? ' selected="selected" ':'')
									);
								}
							//} 							
						}
					} 
					$attribute->setOptions($data_options);									
					$attributes[$attribute->getAttributeId()] = $attribute;
				}
			}
			$this->setData('attribute_filters',$attributes);
		}				
		return $this->getData('attribute_filters'); 
	} 
	
	protected function _getIsFilterableAttribute($attribute)
    {
        return $attribute->getIsFilterable();
    }
	
	public function getPagerUrl($params=array(), $exclude = null)
    {
        $urlParams 					= array();
        $urlParams['_current']  	= true;
        $urlParams['_escape']   	= true;
        $urlParams['_use_rewrite']  = true;
        $urlParams['_query']   		= $params; 
		
        return $this->getCustomUrl('*/*/*', $urlParams, $exclude);
    } 

	public function getLayer()
    { 
        return Mage::getSingleton('catalog/layer');		 
    }
	
	protected function _getFilterableAttributes()
    {
        $attributes = $this->getData('_filterable_attributes');
        if (is_null($attributes)) {
            $attributes = $this->getLayer()->getUnilabFilterableAttributes();
            $this->setData('_filterable_attributes', $attributes);
        }
        return $attributes;
    } 
	
	public function getResetFilterUrl()
	{ 
		return $this->getLayer()->getCurrentCategory()->getUrl();
	}
	
	protected function getCustomUrl($route = '', $params = array(), $exclude = null)
	{
		return $this->_getUrlModel()->getCustomUrl($route, $params,$exclude);
	} 
	
    protected function _getUrlModelClass()
    {
        return 'catalog/customurl';
    }
}
?>