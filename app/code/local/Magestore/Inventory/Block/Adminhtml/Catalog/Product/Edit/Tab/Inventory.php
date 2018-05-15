<?php
class  Magestore_Inventory_Block_Adminhtml_Catalog_Product_Edit_Tab_Inventory extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Inventory
{
    public function __construct()
    {
        parent::__construct();
        if (Mage::getStoreConfig('inventory/general/enable'))
        $this->setTemplate('inventory/catalog/product/tab/inventory.phtml');
    }
}
	

