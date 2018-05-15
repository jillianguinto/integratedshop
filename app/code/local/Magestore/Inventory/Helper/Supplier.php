<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Inventory
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Inventory Helper
 * 
 * @category    Magestore
 * @package     Magestore_Inventory
 * @author      Magestore Developer
 */
class Magestore_Inventory_Helper_Supplier extends Mage_Core_Helper_Abstract
{
    public function importProduct($data)
    {
        if(count($data)){
            Mage::getModel('admin/session')->setData('supplier_product_import',$data);
        }
    }
	
    public function returnArrAllRowOfcolumnOftableSupplier($column){
        $arr = array();
        $model = Mage::getModel('inventory/supplier');
        $collection = $model->getCollection();
        foreach($collection as $c){
                $arr[$c->supplier_id] = $c->$column;
        }
        return $arr;
    }
		
    public function getSupplierInfoBySupplierId($supplierId)
    {
        $supplierCollection = Mage::getResourceModel('inventory/supplier_collection')
                                                ->addFieldToFilter('supplier_id', $supplierId);
        $data = $supplierCollection->getFirstItem()->getData();
        $supplierField = $data['name'];
        $supplierField .= "<br/>".$this->__('Address: ').$data['street'];
        if(isset($data['state'])){
            $supplierField .= " - ".$data['state'];
        }
        $supplierField .= " - ".$data['city'];
        $supplierField .= "<br/>".$this->__('Telephone: ').$data['telephone'];
        $supplierField .= "<br/>".$this->__('Email: ').$data['email'];
        return  $supplierField;
    }
	
	public function getBillingAddressBySupplierId($supplierId)
    {
        $supplierCollection = Mage::getResourceModel('inventory/supplier_collection')
                                                ->addFieldToFilter('supplier_id', $supplierId);
        $data = $supplierCollection->getFirstItem()->getData();
		$countryLists = Mage::getModel('directory/country')->getResourceCollection()->loadByStore() ->toOptionArray(true);
		  $countryList=array();
		  foreach($countryLists as $county)
		  {
			$countryList[$county['value']]=$county['label'];
		  }
        $supplierField = $data['name'];
        $supplierField .= "<br/>".$data['street'];
		$supplierField .= "<br/>".$data['city'];
        if(isset($data['state'])){
            $supplierField .= ", ".$data['state'];
        }
		$supplierField .= ", ".$data['postcode'];
		$supplierField .= "<br/>".$countryList[$data['country_id']];
        $supplierField .= "<br/>".$this->__('T: ').$data['telephone'];
        return  $supplierField;
    }
    
    public function getAllSupplierName(){
        $suppliers = array();
        $model = Mage::getModel('inventory/supplier');
        $collection = $model->getCollection();
        foreach($collection as $supplier){
            $suppliers[$supplier->getId()] = $supplier->getName();
        }
        return $suppliers;
    }
    
    /**
     * get all suppliers have product by product id
     */
    public function getSupplierByProductId($productId)
    {
        $supplierProducts = Mage::getModel('inventory/supplierproduct')
                                    ->getCollection()
                                    ->addFieldToFilter('product_id',$productId);
        if(count($supplierProducts)){
            return $supplierProducts;
        }else{
            return null;
        }
    }
}