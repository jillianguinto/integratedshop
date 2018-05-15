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
 * Assignment Model
 * 
 * @category    Magestore
 * @package     Magestore_Inventory
 * @author      Magestore Developer
 */
class Magestore_Inventory_Model_Assignment extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('inventory/assignment');
    }
    /**
     * load assignment by warehouse id and admin id
     * @param type $warehouseId
     * @param type $adminId
     * @return \Magestore_Inventory_Model_Assignment
     */
    public function loadByWarehouseAndAdmin($warehouseId, $adminId){
        $collection = $this->getCollection()
                ->addFieldToFilter('warehouse_id',$warehouseId)
                ->addFieldToFilter('admin_id',$adminId);
        if($collection->getSize()){
            $this->load($collection->getFirstItem()->getId());
        }
        return $this;
    }
}