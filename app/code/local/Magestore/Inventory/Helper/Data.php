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
class Magestore_Inventory_Helper_Data extends Mage_Core_Helper_Abstract {

    /**
     * get list of country
     * @return type
     */
    public function getCountryList() {
        $result = array();
        $collection = Mage::getModel('directory/country')->getCollection();
        foreach ($collection as $country) {
            $cid = $country->getId();
            $cname = $country->getName();
            $result[$cid] = $cname;
        }
        return $result;
    }

    public function getCountryListHash() {
        $options = array();
        foreach ($this->getCountryList() as $value => $label) {
            if ($label)
                $options[] = array(
                    'value' => $value,
                    'label' => $label
                );
        }
        return $options;
    }

    public function getIncrementId($object) {
        $id = $object->getId();
        $len = strlen($id);
        $zeros = 10 - $len;

        $incrementId = '';
        for ($i = 1; $i < $zeros; $i++){
            if($i==1){
                $incrementId .= '1';
            }else{
                $incrementId.='0';
            }
        }
        $incrementId .= $id;
        if ($object instanceof Magestore_Inventory_Model_Stockissuing) {
            $incrementId = 'SI' . $incrementId;
        } elseif ($object instanceof Magestore_Inventory_Model_Stockreceiving) {
            $incrementId = 'SR' . $incrementId;
        } elseif ($object instanceof Magestore_Inventory_Model_Stocktransfering) {
            $incrementId = 'ST' . $incrementId;
        }
        return $incrementId;
    }

    public function getIncrementRequeststockId($id) {
        $len = strlen($id);
        $zeros = 10 - $len;

        $incrementId = '';
        for ($i = 1; $i < $zeros; $i++)
            $incrementId.='0';
        $incrementId .= $id;
        return $incrementId;
    }

    public function getStockissuingType() {
        return array(
            Magestore_Inventory_Model_Stockissuing::STOCK_TRANSFERRING => 'Stock Transferring',
            Magestore_Inventory_Model_Stockissuing::CUSTOMER_ORDER => 'Customer Order',
            Magestore_Inventory_Model_Stockissuing::ORDER_RETURNED_TO_SUPPLIER => 'Order Returned to Supplier',
            Magestore_Inventory_Model_Stockissuing::CUSTOM => 'Custom'
        );
    }

    public function getTransactionType() {
        return array(
            1 => $this->__('Send stock to another Warehouse or other destination'),
            2 => $this->__('Receive stock from another Warehouse or other destination'),
            3 => $this->__('Receive stock from Purchase Order Delivery'),
            4 => $this->__('Send stock to Supplier for Return Order'),
            5 => $this->__('Send stock to Customer for Shipment'),
            6 => $this->__('Receive stock from Customer Refund'),
        );
    }

    /*
     * get product name by product id on model catalog/product
     */

    public function getProductNameByProductId($productId) {
        $productModel = Mage::getModel('catalog/product');
        $product = $productModel->load($productId);
        $productName = $product->getName();
        return $productName;
    }

    /*
     * get product sku by product id on model catalog/product
     */

    public function getProductSkuByProductId($productId) {
        $productModel = Mage::getModel('catalog/product');
        $product = $productModel->load($productId);
        $productName = $product->getSku();
        return $productName;
    }

    public function getPeriodOptions() {
        $options = array();
        $options = array(
            '1' => $this->__('Day'),
            '2' => $this->__('Month'),
            '3' => $this->__('Year'),
        );
        return $options;
    }

    public function getTotalValue($item) {
        $cost = $item->getCostPrice();
        $qty = $item->getQty();
        $total = $cost * $qty;
        return $total;
    }

    public function getAvailQty($item) {
        $product_id = $item->getEntityId();
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $installer = Mage::getModel('core/resource_setup');
        $sql = "SELECT `qty_available` FROM " . $installer->getTable('inventory/warehouseproduct') .
                ' WHERE `product_id` = ' . $product_id;
        $result = $readConnection->fetchAll($sql);
        $avail = 0;
        foreach ($result as $r) {
            $avail += $r['qty_available'];
        }
        return $avail;
    }

    public function _tempCollection() {
        $collection = new Varien_Data_Collection();
        return $collection;
    }

    public function renderNotice($id) {
        $model = Mage::getModel('inventory/notice')->load($id);
        return $model->getDescription();
    }

    public function filterDates($array, $dateFields) {
        if (empty($dateFields)) {
            return $array;
        }
        $filterInput = new Zend_Filter_LocalizedToNormalized(array(
                    'date_format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
                ));
        $filterInternal = new Zend_Filter_NormalizedToLocalized(array(
                    'date_format' => Varien_Date::DATE_INTERNAL_FORMAT
                ));

        foreach ($dateFields as $dateField) {
            if (array_key_exists($dateField, $array) && !empty($dateField)) {
                $array[$dateField] = $filterInput->filter($array[$dateField]);
                $array[$dateField] = $filterInternal->filter($array[$dateField]);
            }
        }
        return $array;
    }

    public function getQtyProductWarehouse($ptoductId, $selectWarehouse, $ShippingAddress) {
        $productQty = 0;
        $warehouseId = 0;
        $unWarehouseId = 0;
        $distance = -1;
        $d = 0;
        $warehouses = Mage::getModel('inventory/warehouse')->getCollection();
        foreach ($warehouses as $warehouse) {
            if (!$warehouse->getIsUnwarehouse()) {
                $warehouseProduct = Mage::getModel('inventory/warehouseproduct')->getCollection()
                        ->addFieldToFilter('warehouse_id', $warehouse->getId())
                        ->addFieldToFilter('product_id', $ptoductId)
                        ->getFirstItem();
                if ($warehouseProduct && $warehouseProduct->getId()) {
                    if ($selectWarehouse != 3) {
                        if ($d == 0) {
                            $warehouseId = $warehouse->getId();
                            $productQty = $warehouseProduct->getQtyAvailable();
                            $d++;
                        } elseif ($selectWarehouse == 1 && $productQty < $warehouseProduct->getQtyAvailable()) {
                            $warehouseId = $warehouse->getId();
                            $productQty = $warehouseProduct->getQtyAvailable();
                        } elseif ($selectWarehouse == 2 && $productQty > $warehouseProduct->getQtyAvailable() && $warehouseProduct->getQtyAvailable() > 0) {
                            $warehouseId = $warehouse->getId();
                            $productQty = $warehouseProduct->getQtyAvailable();
                        }
                    } else {
                        $source_address = $warehouse->getStreet() . " " . $warehouse->getCity() . " " . $warehouse->getCountryId(); //." ".$warehouse->getPostcode();
                        $street = $ShippingAddress->getStreet();
                        $destination_address = $street[0] . " " . $ShippingAddress->getCity() . " " . $ShippingAddress->getCountryId(); //." ".$ShippingAddress->getPostcode();
                        $newDistance = $this->calculateDistance($source_address, $destination_address);
                        if ($distance == -1) {
                            $warehouseId = $warehouse->getId();
                            $distance = $newDistance;
                        } else {
                            if ($newDistance && ($distance > $newDistance)) {
                                $warehouseId = $warehouse->getId();
                                $distance = $newDistance;
                            }
                        }
                    }
                }
            } else {
                $unWarehouseId = $warehouse->getId();
            }
        }
        if ($warehouseId == 0)
            $warehouseId = $unWarehouseId;
        return $warehouseId;
    }

    public function calculateDistance($source_address, $destination_address) {
        $url = "http://maps.googleapis.com/maps/api/directions/json?origin=" . str_replace(' ', '+', $source_address) . "&destination=" . str_replace(' ', '+', $destination_address) . "&sensor=false";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        $response_all = json_decode($response);
        $distance = $response_all->routes[0]->legs[0]->distance->value;
        return $distance;
    }

}