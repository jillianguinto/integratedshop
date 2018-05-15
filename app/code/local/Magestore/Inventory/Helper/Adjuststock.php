<?php
class Magestore_Inventory_Helper_Adjuststock extends Mage_Core_Helper_Abstract
{
	public function listAdjuststockStatus(){
		return array(
                1 => 'Pending',
                2 => 'Processing',
                3 => 'Complete',
                4 => 'Cancel'
           );
	}
        
        public function importProduct($data)
	{
            if(count($data)){
                Mage::getModel('admin/session')->setData('adjuststock_product_import',$data);
            }
	}

}