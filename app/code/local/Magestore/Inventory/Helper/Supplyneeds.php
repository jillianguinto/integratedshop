<?php

class Magestore_Inventory_Helper_Supplyneeds extends Mage_Core_Helper_Abstract {

    public function getOrderInPeriod($datefrom, $dateto) {
        //Khoang thoi gian tinh tu thoi diem hien tai den thoi gian da chon
        $range = (strtotime($dateto) - strtotime($datefrom)) / (3600 * 24);
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $installer = Mage::getModel('core/resource_setup');
        $allOrder = array();
        if (!$datefrom) {
            $date = now();
            $datefrom = date('Y-m-d', strtotime($date));
        }
        for ($i = 1; $i < 10; $i++) {
            $orderIds = array();
            $j = $i - 1;
            $x = $range * $j;
            $y = $range * $i;
            $today = strftime('%Y-%m-%d', strtotime(date("Y-m-d", strtotime($datefrom)) . " -$x day"));
            $lastperiod = strftime('%Y-%m-%d', strtotime(date("Y-m-d", strtotime($datefrom)) . " -$y day"));
            $orders = $readConnection->fetchAll("SELECT `entity_id` FROM `" . $installer->getTable('sales/order') . "` WHERE (created_at >= '$lastperiod' AND created_at <= '$today')");
            foreach ($orders as $order) {
                array_push($orderIds, $order['entity_id']);
            }
            $string_orderIds = implode("','", $orderIds);
            array_push($allOrder, $string_orderIds);
        }
        return $allOrder;
    }

    //Tinh so luong san pham can thiet min
    public function calMin($product_id, $warehouse) {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $installer = Mage::getModel('core/resource_setup');
        if (!$warehouse) {
            $stockCollection = $readConnection->fetchAll("SELECT * FROM `" . $installer->getTable('cataloginventory/stock_item') . "` WHERE (product_id = $product_id)");
            $stock = $stockCollection[0]['qty'];
            $min_needs = - (int)$stock;
        } else {
            $order_items = $readConnection->fetchAll("SELECT * FROM `" . $installer->getTable('sales/order_item') . "` WHERE (product_id = $product_id)");
            $warehouse_product = $readConnection->fetchAll("SELECT * FROM `" . $installer->getTable('inventory/warehouseproduct') . "` WHERE (product_id = $product_id) AND (warehouse_id = $warehouse)");
            $qty_warehouse = $warehouse_product[0]['qty'];
            $qty_ordered = array();
            $qty_shipped = array();
            $qty_canceled = array();
            foreach ($order_items as $order_item) {
                $shipped = $order_item['qty_shipped'];
                $ordered = $order_item['qty_ordered'];
                $canceled = $order_item['qty_canceled'];
                $qty_ordered[] = $ordered;
                $qty_shipped[] = $shipped;
                $qty_canceled[] = $canceled;
            }
            $min_needs = (int) array_sum($qty_ordered) - (int) array_sum($qty_shipped) - (int) $qty_warehouse - (int) array_sum($qty_canceled);
            
        }
        return $min_needs;
    }

    //Tinh so luong san pham can thiet max
    public function calMaxExponential($product_id, $datefrom, $dateto, $warehouse) {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $installer = Mage::getModel('core/resource_setup');
        $a = 0.1; //Bien a va bien b se duoc nhap vao tu tren form, de toi uu thi $a nen nam trong khoang 0.1 - 0.4
        $b = 0.5;
        $D = array(); //Doanh so thuc te
        $F = array(); //Du bao
        $T = array(); //Dinh huong
        $FIT = array(); //Du bao co dinh huong = date('Y-m-d');
        //Lay min
        $min_needs = $this->calMin($product_id, $warehouse);
        //Lay order
        $orders = $this->getOrderInPeriod($datefrom, $dateto);
        if (!$warehouse) {
            $canceled_qty = array();
            if ($orders) {
                ////////////////////////////////////////////////////////////
                //Xac dinh so luong san pham tuong lai
                foreach ($orders as $order) {
                    $period_qty = array();
                    $order_items = $readConnection->fetchAll("SELECT `qty_ordered`,`qty_canceled` FROM `" . $installer->getTable('sales/order_item') . "` WHERE (product_id = '$product_id') AND (order_id IN ('$order'))");
                    foreach ($order_items as $item) {
                        array_push($period_qty, $item['qty_ordered']);
                        array_push($canceled_qty, $item['qty_canceled']);
                    }
                    array_push($D, array_sum($period_qty));
                }
            } else {
                array_push($D, 0);
                array_push($canceled_qty, 0);
            }
        } else {
            if ($orders) {
                $canceled_qty = array();
                ////////////////////////////////////////////////////////////
                //Xac dinh so luong san pham tuong lai
                foreach ($orders as $order) {
                    $warehouse_order_ids = array();
                    $period_qty = array();
                    $warehouse_orders = $readConnection->fetchAll("SELECT `order_id` FROM `". $installer->getTable('inventory/inventoryshipment') ."` WHERE (product_id = '$product_id') AND (warehouse_id = '$warehouse') AND (order_id IN ('$order'))");
                    foreach($warehouse_orders as $warehouse_order){
                        $warehouse_order_ids[] = $warehouse_order['order_id'];
                    }
                    $warehouse_order_ids_unique = array_unique($warehouse_order_ids);
                    $string_warehouse_orders = implode("','", $warehouse_order_ids_unique);
                    $order_items = $readConnection->fetchAll("SELECT `qty_ordered`,`qty_canceled` FROM `" . $installer->getTable('sales/order_item') . "` WHERE (product_id = '$product_id') AND (order_id IN ('$string_warehouse_orders'))");
                    foreach ($order_items as $item) {
                        array_push($period_qty, $item['qty_ordered']);
                        array_push($canceled_qty, $item['qty_canceled']);
                    }
                    array_push($D, array_sum($period_qty));
                }
            } else {
                array_push($D, 0);
                array_push($canceled_qty, 0);
            }
        }
        ////////////////////////////////////////////////////////////
        //Lay so luong san pham canceled trong ki
        $D = array_reverse($D);
        $F[0] = $D[0];
        $T[0] = 0;
        $FIT[0] = $F[0] + $T[0];
        for ($i = 1; $i < count($D) + 1; $i++) {
            $F[$i] = $a * $D[$i - 1] + (1 - $a) * $F[$i - 1];
            $T[$i] = $T[$i - 1] + $b * ($F[$i] - $F[$i - 1]);
            $FIT[$i] = $F[$i] + $T[$i];
        }
        $future_qty = round(end($FIT), 0);
        //////////////////////////////////////////////////////////
        //Tinh nhu cau max trong tuong lai
        $max_needs = (int) $min_needs + (int) array_sum($canceled_qty) + (int) $future_qty;
        return $max_needs;
    }

    public function filterList($list) {
        $result = array();
        foreach ($list as $item) {
            $p = explode('=', $item);
            if (isset($p[0]) && isset($p[1])) {
                $qty = explode('_', $p[0]);
                $result[$qty[1]] = $p[1];
            }
        }
        return $result;
    }

    public function getWarehousesCanPurchase() {
        $collection = Mage::getModel('inventory/warehouse')
            ->getCollection()
            ->addFieldToFilter('status', 1);
        $ids = array();
        if ($collection->getSize()) {
            $ids = $collection->getAllIds();
        }
        return $ids;
    }

}

?>