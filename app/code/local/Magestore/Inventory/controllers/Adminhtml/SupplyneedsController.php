<?php

class Magestore_Inventory_Adminhtml_SupplyneedsController extends Mage_Adminhtml_Controller_Action {

    /**
     * init layout and set active for current menu
     *
     * @return Magestore_Inventory_Adminhtml_SupplierController
     */
    protected function _initAction() {
        $this->loadLayout()
            ->_setActiveMenu('inventory/supplyneeds')
            ->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Supply Needs Manager'), Mage::helper('adminhtml')->__('Supply Needs Manager')
        );
        return $this;
    }

    /**
     * index action
     */
    public function indexAction() {
        $this->_initAction()
            ->renderLayout();
    }

    public function chartAction() {
        $form_html = $this->getLayout()
            ->createBlock('inventory/adminhtml_supplyneeds')
            ->setTemplate('inventory/supplyneeds/chart.phtml')
            ->toHtml();
        $this->getResponse()->setBody($form_html);
    }

    /**
     * create a purchase order from supply need
     */
    public function createPurchaseAction() {
        $data = $this->getRequest()->getPost();
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if (!isset($data['supplier_select']) || is_null($data['supplier_select']) || $data['supplier_select'] == '')
                return;
            if (!isset($data['warehouse_select']) || is_null($data['warehouse_select']) || $data['warehouse_select'] == '')
                return;
            $model = Mage::getModel('inventory/purchaseorder');

            $data['supplier_id'] = $data['supplier_select'];
            $supplier = Mage::getModel('inventory/supplier')->load($data['supplier_id']);
            $data['warehouse_id'] = $data['warehouse_select'];
            $warehouse = Mage::getModel('inventory/warehouse')->load($data['warehouse_id']);
            $model->setData($data)->setId(null);
            $model->setSupplierName($supplier->getName());
            $model->setWarehouseName($warehouse->getName());
            $model->setPurchaseOn(now());
            try {
                $model->save();
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addSuccess($e->getMessage());
            }
            if (isset($data['product_list'])) {
                $list = array();
                $list = explode(';', $data['product_list']);
                $list = Mage::helper('inventory/supplyneeds')->filterList($list);
                $totalProducts = 0;
                $totalAmounts = 0;
                foreach ($list as $productId => $qty) {
                    $product = Mage::getModel('catalog/product')->load($productId);
                    $productInfo = Mage::getModel('inventory/supplierproduct')
                        ->getCollection()
                        ->addFieldToFilter('supplier_id', $supplier->getId())
                        ->addFieldToFilter('product_id', $productId)
                        ->getFirstItem();
                    $purchaseProductItem = Mage::getModel('inventory/purchaseorderproduct')
                        ->setProductId($productId)
                        ->setProductName($product->getName())
                        ->setProductSku($product->getSku())
                        ->setPurchaseOrderId($model->getId())
                        ->setQty($qty);
                    ;
                    if ($productInfo->getId()) {
                        $purchaseProductItem->setCost($productInfo->getCost())
                            ->setDiscount($productInfo->getDiscount())
                            ->setTax($productInfo->getTax());
                        $totalAmounts += (int) $qty * $productInfo->getCost() * (1 - $productInfo->getDiscount() / 100 + $productInfo->getTax() / 100);
                    }
                    $purchaseProductItem->save();
                    $totalProducts += $qty;
                }
                try {
                    $model->setTotalProducts($totalProducts)
                        ->setTotalAmount($totalAmounts)
                        ->save();
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('inventory')->__('Purchase order was successfully saved')
                    );
                    $this->_redirect('inventoryadmin/adminhtml_purchaseorder/edit', array('id' => $model->getId()));
                    return;
                } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addSuccess($e->getMessage());
                }
            }
        }
    }

    public function gridAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function exportPostAction($data) {
        $headers = new Varien_Object(array(
                'ID' => Mage::helper('inventory')->__('ID'),
                'Name' => Mage::helper('inventory')->__('Name'),
                'SKU' => Mage::helper('inventory')->__('SKU'),
                'Cost' => Mage::helper('inventory')->__('Cost'),
                'Price' => Mage::helper('inventory')->__('Price'),
                'Warehouse' => Mage::helper('inventory')->__('Warehouse'),
                'Supplyneeds' => Mage::helper('inventory')->__('Supplyneeds'),
                'Supplier' => Mage::helper('inventory')->__('Supplier')
            ));
        $template = '"{{ID}}","{{Name}}","{{SKU}}","{{Cost}}","{{Price}}","{{Supplyneeds}}","{{Warehouse}}","{{Supplier}}"';
        $content = $headers->toString($template);
        if (($data['product_list'])) {
            $info = array();
            $list = explode(';', $data['product_list']);
            $arr = Mage::helper('inventory/supplyneeds')->filterList($list);
            foreach($arr as $productId=>$qty){
                $product = Mage::getModel('catalog/product')->getCollection()
                    ->addFieldToFilter('entity_id', $productId)
                    ->addAttributeToSelect('*')
                    ->getFirstItem();
                $warehouse = Mage::getModel('inventory/warehouse')
                    ->getCollection()
                    ->addFieldToFilter('warehouse_id', $data['warehouse_select'])
                    ->getFirstItem()
                    ->getName();
                $supplier = Mage::getModel('inventory/supplier')
                    ->getCollection()
                    ->addFieldToFilter('supplier_id', $data['supplier_select'])
                    ->getFirstItem()
                    ->getName();
                $cost = Mage::getModel('inventory/inventory')
                    ->getCollection()
                    ->addFieldToFilter('product_id',$productId)
                    ->getFirstItem()
                    ->getCostPrice();
                $info['ID'] = $productId;
                $info['Name'] = $product->getName();
                $info['SKU'] = $product->getSku();
                $info['Cost'] = $cost;
                $info['Price'] = $product->getPrice();
                $info['Supplyneeds'] = $qty;
                $info['Warehouse'] = $warehouse;
                $info['Supplier'] = $supplier;
                $csv_content = new Varien_Object($info);
                $content .= "\n";
                $content .= $csv_content->toString($template);
            }
        }
        $this->_prepareDownloadResponse('supplyneeds.csv', $content);
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('inventory');
    }
    
}
?>

