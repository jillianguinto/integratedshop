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
 * Inventory Edit Form Content Tab Block
 * 
 * @category    Magestore
 * @package     Magestore_Inventory
 * @author      Magestore Developer
 */
class Magestore_Inventory_Block_Adminhtml_Inventory_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    
    public function _prepareLayout()
    {
        $this->setTemplate('inventory/inventory/product_information.phtml');
    }
    
    /**
     * @return a product
     */
    public function getProductInformation()
    {
        $id = $this->getRequest()->getParam('id');
        $product = Mage::getModel('catalog/product')->load($id);
        if($product->getId())
            return $product;
        return '';
    }
    
    
    /**
     * get all warehouses have product by product id
     */
    public function getWarehouseByProductId($productId)
    {
        $warehouseProducts = Mage::getModel('inventory/warehouseproduct')
                                    ->getCollection()
                                    ->addFieldToFilter('product_id',$productId);
        if(count($warehouseProducts)){
            return $warehouseProducts;
        }else{
            return null;
        }
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