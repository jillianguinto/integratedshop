<?php

class Unilab_Webservice_Model_Netsuite_Maintenance extends Mage_Core_Model_Abstract
{
	protected function _getConnection()
	{
		$this->_Connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		return $this->_Connection;
	}
	
	public function createType()
	{
		$arg_attribute = 'unilab_type';
		$arg_value = $_POST['name'];

		$attr_model = Mage::getModel('catalog/resource_eav_attribute');
		$attr = $attr_model->loadByCode('catalog_product', $arg_attribute);
		$attr_id = $attr->getAttributeId();

		$option['attribute_id'] = $attr_id;
		$option['value']['any_option_name'][0] = $arg_value;

		$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
		$setup->addAttributeOption($option);		
	}

	public function createFormat()
	{
		$arg_attribute = 'unilab_type';
		$arg_value = $_POST['name'];

		$attr_model = Mage::getModel('catalog/resource_eav_attribute');
		$attr = $attr_model->loadByCode('catalog_product', $arg_attribute);
		$attr_id = $attr->getAttributeId();

		$option['attribute_id'] = $attr_id;
		$option['value']['any_option_name'][0] = $arg_value;

		$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
		$setup->addAttributeOption($option);		
	}

	public function createBenefit()
	{
		$arg_attribute = 'unilab_type';
		$arg_value = $_POST['name'];

		$attr_model = Mage::getModel('catalog/resource_eav_attribute');
		$attr = $attr_model->loadByCode('catalog_product', $arg_attribute);
		$attr_id = $attr->getAttributeId();

		$option['attribute_id'] = $attr_id;
		$option['value']['any_option_name'][0] = $arg_value;

		$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
		$setup->addAttributeOption($option);		
	}

	public function createSegment()
	{
		$arg_attribute = 'unilab_type';
		$arg_value = $_POST['name'];

		$attr_model = Mage::getModel('catalog/resource_eav_attribute');
		$attr = $attr_model->loadByCode('catalog_product', $arg_attribute);
		$attr_id = $attr->getAttributeId();

		$option['attribute_id'] = $attr_id;
		$option['value']['any_option_name'][0] = $arg_value;

		$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
		$setup->addAttributeOption($option);		
	}

	public function createDivision()
	{
		$arg_attribute = 'unilab_type';
		$arg_value = $_POST['name'];

		$attr_model = Mage::getModel('catalog/resource_eav_attribute');
		$attr = $attr_model->loadByCode('catalog_product', $arg_attribute);
		$attr_id = $attr->getAttributeId();

		$option['attribute_id'] = $attr_id;
		$option['value']['any_option_name'][0] = $arg_value;

		$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
		$setup->addAttributeOption($option);		
	}

	public function createGroup()
	{
		$arg_attribute = 'unilab_type';
		$arg_value = $_POST['name'];

		$attr_model = Mage::getModel('catalog/resource_eav_attribute');
		$attr = $attr_model->loadByCode('catalog_product', $arg_attribute);
		$attr_id = $attr->getAttributeId();

		$option['attribute_id'] = $attr_id;
		$option['value']['any_option_name'][0] = $arg_value;

		$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
		$setup->addAttributeOption($option);		
	}
}