<?php

class Magestore_Inventory_Block_Adminhtml_Notifications extends Mage_Adminhtml_Block_Template {

    public function getSystemNotice() {
        $messages = array();
        $qty_notice = Mage::getStoreConfig('inventory/notice/qty_notice');
        $products = Mage::getModel('catalog/product')->getCollection();
        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $products->joinField('qty', 'cataloginventory/stock_item', 'qty', 'product_id=entity_id', '{{table}}.stock_id=1', 'left')
                    ->addFieldToFilter('qty', array('lt' => $qty_notice));
        }
        $str = 'qty%5Bto%5D=' . "$qty_notice" . '&price%5Bcurrency%5D=USD';
        $filter = base64_encode($str);
        $url = Mage::helper('adminhtml')->getUrl('adminhtml/catalog_product/index', array('product_filter' => $filter));
        if ($products->getSize()) {
            $messages[] = $this->__('Your system is running out of stock on some products. ') . '  <a href="' . $url . '">' . $this->__('(Click here)') . '</a>' . $this->__(' to view details.') . '<br/>';
        }

        return $messages;
    }

    public function getWarehouseNotice() {
        $messages = array();
        $qty_notice = Mage::getStoreConfig('inventory/notice/qty_notice');
        $warehouses = Mage::getModel('inventory/warehouse')->getCollection();
        $str = 'qty%5Bto%5D=' . "$qty_notice";
        $filter = base64_encode($str);
        foreach ($warehouses as $warehouse) {
            $url = Mage::helper('adminhtml')->getUrl("inventoryadmin/adminhtml_warehouse/edit", array('filter' => $filter, 'id' => $warehouse->getId(), 'loadptab' => true));
            if ($warehouse->getName() != 'unWarehouse') {
                $warehousename = $warehouse->getName();
                $warehouseproducts = Mage::getModel('inventory/warehouseproduct')
                        ->getCollection()
                        ->addFieldToFilter('warehouse_id', $warehouse->getId());
                foreach ($warehouseproducts as $warehouseproduct) {
                    $qty = $warehouseproduct->getQty();
                    if ($qty < $qty_notice) {                        
                        $messages[] = $this->__("Warehouse $warehousename is running out of stock on some products! ") . '  <a href="' . $url . '">' . $this->__('(Click here)') . '</a>'.$this->__('  to view details.').'<br/>';
                        break;
                    }
                }
            }
        }
        return $messages;
    }

    public function getBothNotice() {
        $messages = array();
        $warehousenotices = $this->getWarehouseNotice();
        $systemnotices = $this->getSystemNotice();
        foreach ($warehousenotices as $wn) {
            $messages[] = $wn;
        }
        foreach ($systemnotices as $sn) {
            $messages[] = $sn;
        }
        return $messages;
    }

}

?>
