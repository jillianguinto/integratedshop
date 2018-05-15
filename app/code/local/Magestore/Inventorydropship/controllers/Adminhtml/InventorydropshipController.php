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
 * @package     Magestore_Inventorydropship
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Inventorydropship Adminhtml Controller
 * 
 * @category    Magestore
 * @package     Magestore_Inventorydropship
 * @author      Magestore Developer
 */
class Magestore_Inventorydropship_Adminhtml_InventorydropshipController extends Mage_Adminhtml_Controller_Action
{
    /**
     * init layout and set active for current menu
     *
     * @return Magestore_Inventorydropship_Adminhtml_InventorydropshipController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('inventorydropship/inventorydropship')
            ->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Items Manager'),
                Mage::helper('adminhtml')->__('Item Manager')
            );
        return $this;
    }
 
    /**
     * index action
     */
    public function indexAction()
    {
        $this->_initAction()
            ->renderLayout();
    }

    /**
     * view and edit item action
     */
    public function editAction()
    {
         $this->loadLayout()
              ->_setActiveMenu('sales/order')
              ->renderLayout();
    }
 
    public function newAction()
    {
        $this->_forward('edit');
    }
 
    /**
     * save item action
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            if (isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
                try {
                    /* Starting upload */    
                    $uploader = new Varien_File_Uploader('filename');
                    
                    // Any extention would work
                       $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                    $uploader->setAllowRenameFiles(false);
                    
                    // Set the file upload mode 
                    // false -> get the file directly in the specified folder
                    // true -> get the file in the product like folders 
                    //    (file.jpg will go in something like /media/f/i/file.jpg)
                    $uploader->setFilesDispersion(false);
                            
                    // We set media as the upload dir
                    $path = Mage::getBaseDir('media') . DS ;
                    $result = $uploader->save($path, $_FILES['filename']['name'] );
                    $data['filename'] = $result['file'];
                } catch (Exception $e) {
                    $data['filename'] = $_FILES['filename']['name'];
                }
            }
              
            $model = Mage::getModel('inventorydropship/inventorydropship');        
            $model->setData($data)
                ->setId($this->getRequest()->getParam('id'));
            
            try {
                if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
                    $model->setCreatedTime(now())
                        ->setUpdateTime(now());
                } else {
                    $model->setUpdateTime(now());
                }
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('inventorydropship')->__('Item was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('inventorydropship')->__('Unable to find item to save')
        );
        $this->_redirect('*/*/');
    }
 
    /**
     * delete item action
     */
    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('inventorydropship/inventorydropship');
                $model->setId($this->getRequest()->getParam('id'))
                    ->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Item was successfully deleted')
                );
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * mass delete item(s) action
     */
    public function massDeleteAction()
    {
        $inventorydropshipIds = $this->getRequest()->getParam('inventorydropship');
        if (!is_array($inventorydropshipIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($inventorydropshipIds as $inventorydropshipId) {
                    $inventorydropship = Mage::getModel('inventorydropship/inventorydropship')->load($inventorydropshipId);
                    $inventorydropship->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted',
                    count($inventorydropshipIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
    
    /**
     * mass change status for item(s) action
     */
    public function massStatusAction()
    {
        $inventorydropshipIds = $this->getRequest()->getParam('inventorydropship');
        if (!is_array($inventorydropshipIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($inventorydropshipIds as $inventorydropshipId) {
                    Mage::getSingleton('inventorydropship/inventorydropship')
                        ->load($inventorydropshipId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($inventorydropshipIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * export grid item to CSV type
     */
    public function exportCsvAction()
    {
        $fileName   = 'inventorydropship.csv';
        $content    = $this->getLayout()
                           ->createBlock('inventorydropship/adminhtml_inventorydropship_grid')
                           ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction()
    {
        $fileName   = 'inventorydropship.xml';
        $content    = $this->getLayout()
                           ->createBlock('inventorydropship/adminhtml_inventorydropship_grid')
                           ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    
    public  function selectsupplierAction()
    {
        $div_id = $this->getRequest()->getParam('div_id');
        $div_id1 = str_replace('show_select_warehouse_supplier_', '', $div_id);
        if($div_id1 == $div_id)
            $div_id1 = str_replace('show_select_only_warehouse_', '', $div_id);
        if($div_id1 == $div_id)
            $div_id1 = str_replace('show_select_only_supplier_', '', $div_id); 
        $div_id = $div_id1;
        $div_id = explode('_', $div_id);
        $orderItemId = $div_id[0];
        $orderItem = Mage::getModel('sales/order_item')->load($orderItemId);
        $blockSingleton = Mage::getBlockSingleton('bundle/adminhtml_sales_order_items_renderer');
        if($parentItemId = $orderItem->getParentItemId()){
            $parentItem = Mage::getModel('sales/order_item')->load($parentItemId); 
            $next = false;
            if ($options = $parentItem->getProductOptions()) {
                if (isset($options['shipment_type']) && $options['shipment_type'] == Mage_Catalog_Model_Product_Type_Abstract::SHIPMENT_SEPARATELY) {                    
                    $next = true;
                }
            }
            if(!$next){
                $parentProductId = Mage::getModel('sales/order_item')->load($parentItemId)->getProductId();
                $supplierProductModel = Mage::getModel('inventory/supplierproduct')->getCollection()
                                ->addFieldToFilter('product_id', $parentProductId);
                if(count($supplierProductModel)){
                    echo '1';
                    return;
                }else{
                    return '';
                }
            }
        }
        $productId = $div_id[1];
        $supplierProductModel = Mage::getModel('inventory/supplierproduct')->getCollection()
                ->addFieldToFilter('product_id', $productId);   
        $return = '';
        if(count($supplierProductModel)){
            $firstSupplier = '';
            $return .= "<select class='supplier-shipment' name='supplier-shipment[items][$orderItemId]' onchange='changeviewsupplier(this,$orderItemId);' id='supplier-shipment[items][$orderItemId]'>";
            foreach ($supplierProductModel as $model) {
                $supplierId = $model->getSupplierId();
                $supplierName = Mage::getModel('inventory/supplier')->load($supplierId)->getName();
                if(!$firstSupplier)
                    $firstSupplier = $supplierId;
                $return .= "<option value='$supplierId' ";
                $return .= ">$supplierName</option>";            
            }      
            $return .= "</select>";
            $return .= "<br />";
            $return .= "<div style='float:right;'><a id='view_supplier-shipment[items][$orderItemId]' target='_blank' href='".$this->getUrl('inventoryadmin/adminhtml_supplier/edit',array('id'=>$firstSupplier))."'>".$this->__('view')."</a></div>";
        }
        echo $return;
    }
    
    public  function selectwarehouseAction()
    {
        $div_id = $this->getRequest()->getParam('div_id');
        $div_id = str_replace('show_select_warehouse_supplier_', '', $div_id);
        $div_id = explode('_', $div_id);
        $orderItemId = $div_id[0];
        $productId = $div_id[1];
        $allWarehouse = Mage::helper('inventory/warehouse')->getAllWarehouseNameEnable();
        $warehouseProductModel = Mage::getModel('inventory/warehouseproduct')->getCollection()
                ->addFieldToFilter('product_id', $productId)
                ->setOrder('qty', 'DESC');
        $warehouseHaveProduct = array();
        $return = "<select class='warehouse-shipment' name='warehouse-shipment[items][$orderItemId]' onchange='changeviewwarehouse(this,$orderItemId);' id='warehouse-shipment[items][$orderItemId]'>";
        $firstWarehouse = '';
        foreach ($warehouseProductModel as $model) {
            $warehouseId = $model->getwarehouseId();
            $warehouseName = $allWarehouse[$warehouseId];            
            $productQty = $model->getQty();
            if ($warehouseName != '' && $warehouseId != Mage::helper('inventory/warehouse')->getIdOfunWarehouse()) {
                if(!$firstWarehouse)
                    $firstWarehouse = $warehouseId;
                $return .= "<option value='$warehouseId' ";
                $return .= ">$warehouseName($productQty product(s))</option>";
                $warehouseHaveProduct[] = $allWarehouse[$warehouseId];
            }
        }
        foreach ($allWarehouse as $warehouseIdKey => $warehouseNameValue) {
            if ($warehouseNameValue != '' && $warehouseIdKey != Mage::helper('inventory/warehouse')->getIdOfunWarehouse()) {
                if (in_array($allWarehouse[$warehouseIdKey], $warehouseHaveProduct) == false) {
                    if(!$firstWarehouse)
                        $firstWarehouse = $warehouseIdKey;
                    $return .= "<option value='$warehouseIdKey' ";
                    $return .= ">$warehouseNameValue(0 product(s))</option>";
                }
            }
        }
        $warehouseModel = Mage::getModel('inventory/warehouse')->getCollection();
        if(count($warehouseModel)==1){
            $qtyInUnwarehouse = Mage::helper('inventory/warehouse')->getQtyOneProductInWarehouse(Mage::helper('inventory/warehouse')->getIdOfunWarehouse(), $productId);
            $warehouseIdUnwarehouse = Mage::helper('inventory/warehouse')->getIdOfunWarehouse();
            if(!$firstWarehouse)
                $firstWarehouse = $warehouseIdUnwarehouse;
            $return .= "<option value='$warehouseIdUnwarehouse' ";
                $return .= ">unWarehouse( $qtyInUnwarehouse product(s))</option>";
        }
        $return .= "</select><br />";
        $return .= "<div style='float:right;'><a id='view_warehouse-shipment[items][$orderItemId]' target='_blank' href='".$this->getUrl('inventoryadmin/adminhtml_warehouse/edit',array('id'=>$firstWarehouse))."'>".$this->__('view')."</a></div>";
        echo $return;
    }

    public function checkparentAction()
    {
        $itemId = $this->getRequest()->getParam('itemid');
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $sql = 'SELECT parent_item_id from ' . $resource->getTableName('sales/order_item') . ' WHERE (item_id = ' . $itemId . ')';
        $results = $readConnection->fetchAll($sql);
        $parentId = $itemId;
        foreach($results as $result){
            if($result['parent_item_id']){
                $parentItem = Mage::getModel('sales/order_item')->load($result['parent_item_id']); 
                $next = false;
                if ($options = $parentItem->getProductOptions()) {
                    if (isset($options['shipment_type']) && $options['shipment_type'] == Mage_Catalog_Model_Product_Type_Abstract::SHIPMENT_SEPARATELY) {                    
                        $next = true;
                    }
                }
                if(!$next)
                    $parentId = $result['parent_item_id'];
            }
        }
        echo $parentId;
    }
    
    public function savedropshipAction()
    {        
        $session = md5(now());        
        $data = $this->getRequest()->getPost();        
        $inventoryDropshipData = array();
        $orderId = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($orderId);
        $admin = Mage::getModel('admin/session')->getUser();
        $adminName = $admin->getName();
        $adminEmail = $admin->getEmail();
        $items = $data['shipment']['items'];
        $suppliers = $data['supplier-shipment']['items'];
//        $needToConfirm = false;
//        if(Mage::getStoreConfig('inventory/dropship/confirm'))
//            $needToConfirm = true;
        $supplierNotNeedToConfirmProvide = true;
        if(Mage::getStoreConfig('inventory/dropship/supplier_confirm_provide'))
            $supplierNotNeedToConfirmProvide = false;
        $adminNotNeedToApprove = true;
        if(Mage::getStoreConfig('inventory/dropship/admin_approve'))
            $adminNotNeedToApprove = false;
        $supplierNotNeedToConfirmShipped = true;
        if(Mage::getStoreConfig('inventory/dropship/supplier_confirm_shipped'))
            $supplierNotNeedToConfirmShipped = false;
        
        
        foreach($suppliers as $itemId=>$supplierId){
            $supplier = Mage::getModel('inventory/supplier')->load($supplierId);
            $supplierName = $supplier->getName();
            $dropshipModel = Mage::getModel('inventorydropship/inventorydropship')
                                            ->getCollection()
                                            ->addFieldToFilter('order_id',$orderId)                                            
                                            ->addFieldToFilter('supplier_id',$supplierId)
                                            ->addFieldToFilter('session',$session)                                                    
                                            ->getFirstItem();
            
            if(!is_numeric($items[$itemId]) || $items[$itemId]<0)
                $items[$itemId] = 0;            
            if(!$dropshipModel->getId() && $items[$itemId]>0){
                $dropshipModel = Mage::getModel('inventorydropship/inventorydropship');      
//                $statusDropShip = 3;
                /* check status for drop shipment after create */
                if($supplierNotNeedToConfirmProvide){ //supplier does not need to confirm qty product to provide
                    $statusDropShip = 3; //qty requested = qty confirmed = qty approved; need to ship from supplier
                    if($supplierNotNeedToConfirmShipped) // supplier does not need to confirm shipped
                        $statusDropShip = 6;
                }else{
                    $statusDropShip = 1;
                    if($adminNotNeedToApprove){ // admin does not need to approve qty product to supplier ships
                        if($supplierNotNeedToConfirmShipped){
                            $statusDropShip = 3;
                        }
                    }else{
                    }
                }
                    
                    
//                if($needToConfirm)
//                    $statusDropShip = 1;
                $dropshipModel->setData('order_id',$orderId)
                         ->setData('increment_id',$order->getIncrementId())
                         ->setData('supplier_id',$supplierId)
                         ->setData('supplier_name',$supplierName)
                         ->setData('supplier_email',$supplier->getEmail())
                         ->setData('shipping_name',$order->getShippingAddress()->getName())
                         ->setData('created_on',now())
                         ->setData('admin_name',$adminName)
                         ->setData('admin_email',$adminEmail)
                         ->setStatus($statusDropShip)
                         ->setSession($session)
                         ->save();
            }
            if($items[$itemId]>0){
                $dropshipProduct = Mage::getModel('inventorydropship/inventorydropshipproduct');
                $orderItem = Mage::getModel('sales/order_item')->load($itemId);                
                
                /* set qty confirmed(offer), qty approve */
                if($supplierNotNeedToConfirmProvide){
                    $qtyOffer = $items[$itemId];
                    $qtyApprove = $items[$itemId];
                    if($supplierNotNeedToConfirmShipped)
                        $qtyShipped = $items[$itemId];
                }else{
                    $qtyApprove = 0;
                    $qtyOffer = 0;
                    $qtyShipped = 0;
                    if($adminNotNeedToApprove){ // admin does not need to approve qty product to supplier ships
                        if($supplierNotNeedToConfirmShipped){
                            $qtyApprove = $items[$itemId];
                        }
                    }
                }
                
                $dropshipProduct->setData('dropship_id',$dropshipModel->getId())
                                ->setData('item_id',$itemId)
                                ->setData('supplier_id',$supplier->getId())
                                ->setData('supplier_name',$supplierName)   
                                ->setData('product_id',$orderItem->getProductId())   
                                ->setData('product_sku',$orderItem->getSku())   
                                ->setData('product_name',$orderItem->getName())   
                                ->setData('qty_request',$items[$itemId])   
                                ->setData('qty_offer',$qtyOffer)   
                                ->setData('qty_approve',$qtyApprove)   
                                ->setData('qty_shipped',$qtyShipped)   
                                ->save();
            }
            
        }
        $allDropshipCreates = Mage::getModel('inventorydropship/inventorydropship')
                                    ->getCollection()
                                    ->addFieldToFilter('order_id',$orderId)
                                    ->addFieldToFilter('session',$session);        
        if(count($allDropshipCreates)){            
            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('inventorydropship')->__('The drop shipment(s) has been created!')
            );
            
            if($supplierNotNeedToConfirmProvide){
                if($supplierNotNeedToConfirmShipped){
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('inventorydropship')->__('The shipment(s) has been created!')
                    );
                }else{
                    Mage::getSingleton('adminhtml/session')->addNotice(
                        Mage::helper('inventorydropship')->__('Awaiting supplier\'s shipment')
                    );                    
                }
            }else{
                if($adminNotNeedToApprove){ // admin does not need to approve qty product to supplier ships
                    if($supplierNotNeedToConfirmShipped){
                        Mage::getSingleton('adminhtml/session')->addNotice(
                            Mage::helper('inventorydropship')->__('Awaiting supplier\'s confirmation and shipment')
                        );
                    }else{
                        Mage::getSingleton('adminhtml/session')->addNotice(
                            Mage::helper('inventorydropship')->__('Awaiting supplier\'s confirmation')
                        );
                    }
                }else{
                    Mage::getSingleton('adminhtml/session')->addNotice(
                        Mage::helper('inventorydropship')->__('Awaiting supplier\'s confirmation')
                    );
                }
            }
            
            try{
                foreach($allDropshipCreates as $dropshipCreate){
                    if($supplierNotNeedToConfirmProvide){
                        if($supplierNotNeedToConfirmShipped){
                            $savedQtys = array();
                            $productIds = array();
                            $dropshipProductShips = Mage::getModel('inventorydropship/inventorydropshipproduct')
                                                ->getCollection()
                                                ->addFieldToFilter('dropship_id',$dropshipCreate->getId());
                            foreach($dropshipProductShips as $dropshipProductShip){
                                $savedQtys[$dropshipProductShip->getItemId()] = $dropshipProductShip->getQtyApprove();
                                $productIds[$dropshipProductShip->getItemId()] = $dropshipProductShip->getProductId();
                            }
                            try{
                                //create shipment when supplier approved
                                if($savedQtys){                            
                                    Mage::getModel('admin/session')->setData('break_shipment_event_dropship',true);
                                    Mage::getModel('core/session')->setData('break_shipment_event_dropship',true);

                                    $order = Mage::getModel('sales/order')->load($dropshipCreate->getOrderId());
                                    $transaction = Mage::getModel('core/resource_transaction')
                                                    ->addObject($order);
                                    $shipment = Mage::getModel('sales/service_order', $order)->prepareShipment($savedQtys);
                                    $shipment->register();
                                    $shipment->getOrder()->setIsInProcess(true);
                                    $transactionSave = Mage::getModel('core/resource_transaction')
                                            ->addObject($shipment)
                                            ->addObject($shipment->getOrder())
                                            ->save();
                                    foreach($savedQtys as $itemShipId=>$qtyShip){
                                        $inventoryShipmentModel = Mage::getModel('inventory/inventoryshipment');
                                        $inventoryShipmentModel->setItemId($itemShipId)
                                                               ->setProductId($productIds[$itemShipId])
                                                               ->setOrderId($dropshipCreate->getOrderId())
                                                               ->setSupplierId($dropshipCreate->getSupplierId())
                                                               ->setSupplierName($dropshipCreate->getSupplierName())
                                                               ->setShipmentId($shipment->getId())
                                                               ->setItemShiped($qtyShip)
                                                               ->save();
                                        $children = Mage::getModel('sales/order_item')
                                                        ->getCollection()
                                                        ->addFieldToFilter('parent_item_id',$itemShipId);
                                        if(count($children))
                                            foreach($children as $child){
                                                $inventoryShipmentModel = Mage::getModel('inventory/inventoryshipment');
                                                $inventoryShipmentModel->setItemId($child->getItemId())
                                                                       ->setProductId($child->getProductId())
                                                                       ->setOrderId($dropshipCreate->getOrderId())
                                                                       ->setSupplierId($dropshipCreate->getSupplierId())
                                                                       ->setSupplierName($dropshipCreate->getSupplierName())
                                                                       ->setShipmentId($shipment->getId())
                                                                       ->setItemShiped($qtyShip)
                                                                       ->save();
                                            }
                                    }
                                }
                            }catch(Exception $e){
                                
                            }			
                        }else{
                            Mage::helper('inventorydropship')->sendEmailApproveDropShipToSupplier($dropshipCreate->getId());
                        }
                    }else{
                        if($adminNotNeedToApprove){ // admin does not need to approve qty product to supplier ships
                            if($supplierNotNeedToConfirmShipped){
                                Mage::helper('inventorydropship')->sendEmailApproveDropShipToSupplier($dropshipCreate->getId());
                            }else{
                                Mage::helper('inventorydropship')->sendEmailOfferToSupplier($dropshipCreate->getId());
                            }
                        }else{
                            Mage::helper('inventorydropship')->sendEmailOfferToSupplier($dropshipCreate->getId());
                        }
                    }
                }
            }catch(Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('adminhtml/sales_order/view',array('order_id'=>$orderId));
        return;        
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('inventory');
    }   
    
    /*Drop shipment in order tab*/
    public function dropshipAction()
    {            
        $this->loadLayout();
        $this->getLayout()->getBlock('sales.order.view.dropship')
            ->setDropships($this->getRequest()->getPost('dropships', null));
        $this->renderLayout();
    }

    public function dropshipGridAction()
    {            
        $this->loadLayout();
        $this->getLayout()->getBlock('sales.order.view.dropship')
            ->setDropships($this->getRequest()->getPost('dropships', null));
        $this->renderLayout();
    }
    
    /* cancel dropship from admin */
    public function canceldropshipAction()
    {
        $dropshipId = $this->getRequest()->getParam('dropship_id');
        $dropship = Mage::getModel('inventorydropship/inventorydropship')->load($dropshipId);
        $dropship->setStatus('5')->save();
        Mage::getSingleton('adminhtml/session')->addSuccess(
            Mage::helper('adminhtml')->__('The drop shipment has been canceled!')
        );
        Mage::helper('inventorydropship')->sendEmailCancelDropShipToSupplier($dropshipId);
        $this->_redirect('*/*/edit',array('id'=>$dropshipId));
    }
    
    /* approve dropship from admin */
    public function approvedropshipAction()
    {
        if($data = $this->getRequest()->getPost()){
            $dropshipId = $data['dropship_id'];
            $itemApproves = $data['item']['approve'];
            $dropshipProducts = Mage::getModel('inventorydropship/inventorydropshipproduct')
                                    ->getCollection()
                                    ->addFieldToFilter('dropship_id',$dropshipId);
            $success = false;
            $resource = Mage::getSingleton('core/resource');
            $writeConnection = $resource->getConnection('core_write');
            $readConnection = $resource->getConnection('core_read');
            foreach($dropshipProducts as $dropshipProduct){
                if($itemApproves[$dropshipProduct->getItemId()] 
                            && is_numeric($itemApproves[$dropshipProduct->getItemId()]) 
                                && $itemApproves[$dropshipProduct->getItemId()]>0){
                    $success = true;
                    if($itemApproves[$dropshipProduct->getItemId()] > $dropshipProduct->getQtyOffer())
                        $itemApproves[$dropshipProduct->getItemId()] = $dropshipProduct->getQtyOffer();
                    $dropshipProduct->setData('qty_approve',$itemApproves[$dropshipProduct->getItemId()])
                                    ->save();
                    $pId = $dropshipProduct->getProductId();
                    $itemOrderId = $dropshipProduct->getItemId();
                    $allChildrenIds = array();
                    $orderId = Mage::getModel('inventorydropship/inventorydropship')->load($dropshipId)->getOrderId();
                    $childrenCollection = Mage::getModel('sales/order_item')
                                            ->getCollection()
                                            ->addFieldToFilter('order_id',$orderId)
                                            ->addFieldToFilter('parent_item_id',$itemOrderId);
                    if(count($childrenCollection)){
                        foreach($childrenCollection as $child){
                            $allChildrenIds[] = $child->getProductId();
                        }
                    }                    
                    $code = $itemApproves[$dropshipProduct->getItemId()];
                    if (Mage::getStoreConfig('inventory/general/updatestock')) {
                        $product = Mage::getModel('catalog/product')->load($pId);
                        $sqlSelect = 'Select qty from ' . $resource->getTableName("cataloginventory_stock_item") . ' WHERE (product_id = ' . $pId . ')';
                        $results = $readConnection->fetchAll($sqlSelect);
                        foreach ($results as $result) {
                            $oldQtyProduct = $result['qty'];
                        }
                        $minToChangeStatus = Mage::getStoreConfig('cataloginventory/item_options/min_qty');                        
                        if (($oldQtyProduct + $code) > $minToChangeStatus) {
                            $sqlUpdateProduct = 'UPDATE ' . $resource->getTableName("cataloginventory_stock_item") . ' SET qty = qty + ' . $code . ', is_in_stock = 1 WHERE (product_id = ' . $pId . ')';
                            $sqlUpdateProductStatus = 'UPDATE ' . $resource->getTableName("cataloginventory_stock_status") . ' SET qty = qty + ' . $code . ', stock_status = 1 WHERE (product_id = ' . $pId . ')';
                        } else {
                            if ($product->getTypeId() != 'configurable') {
                                $sqlUpdateProduct = 'UPDATE ' . $resource->getTableName("cataloginventory_stock_item") . ' SET qty = qty + ' . $code . ', is_in_stock = 0 WHERE (product_id = ' . $pId . ')';
                            } else {
                                $sqlUpdateProduct = 'UPDATE ' . $resource->getTableName("cataloginventory_stock_item") . ' SET qty = qty + ' . $code . ' WHERE (product_id = ' . $pId . ')';
                            }
                            $sqlUpdateProductStatus = 'UPDATE ' . $resource->getTableName("cataloginventory_stock_status") . ' SET qty = qty + ' . $code . ', stock_status = 0 WHERE (product_id = ' . $pId . ')';
                        }
                    } else {
                        $sqlUpdateProduct = 'UPDATE ' . $resource->getTableName("cataloginventory_stock_item") . ' SET qty = qty + ' . $code . ' WHERE (product_id = ' . $pId . ')';
                        $sqlUpdateProductStatus = 'UPDATE ' . $resource->getTableName("cataloginventory_stock_status") . ' SET qty = qty + ' . $code . ' WHERE (product_id = ' . $pId . ')';
                    }
                    $writeConnection->query($sqlUpdateProduct);
                    $writeConnection->query($sqlUpdateProductStatus);
                    if(count($allChildrenIds)){
                        foreach($allChildrenIds as $children){
                            if (Mage::getStoreConfig('inventory/general/updatestock')) {
                                $product = Mage::getModel('catalog/product')->load($children);
                                $sqlSelect = 'Select qty from ' . $resource->getTableName("cataloginventory_stock_item") . ' WHERE (product_id = ' . $children . ')';
                                $results = $readConnection->fetchAll($sqlSelect);
                                foreach ($results as $result) {
                                    $oldQtyProduct = $result['qty'];
                                }
                                $minToChangeStatus = Mage::getStoreConfig('cataloginventory/item_options/min_qty');                        
                                if (($oldQtyProduct + $code) > $minToChangeStatus) {
                                    $sqlUpdateProduct = 'UPDATE ' . $resource->getTableName("cataloginventory_stock_item") . ' SET qty = qty + ' . $code . ', is_in_stock = 1 WHERE (product_id = ' . $children . ')';
                                    $sqlUpdateProductStatus = 'UPDATE ' . $resource->getTableName("cataloginventory_stock_status") . ' SET qty = qty + ' . $code . ', stock_status = 1 WHERE (product_id = ' . $children . ')';
                                } else {
                                    if ($product->getTypeId() != 'configurable') {
                                        $sqlUpdateProduct = 'UPDATE ' . $resource->getTableName("cataloginventory_stock_item") . ' SET qty = qty + ' . $code . ', is_in_stock = 0 WHERE (product_id = ' . $children . ')';
                                    } else {
                                        $sqlUpdateProduct = 'UPDATE ' . $resource->getTableName("cataloginventory_stock_item") . ' SET qty = qty + ' . $code . ' WHERE (product_id = ' . $children . ')';
                                    }
                                    $sqlUpdateProductStatus = 'UPDATE ' . $resource->getTableName("cataloginventory_stock_status") . ' SET qty = qty + ' . $code . ', stock_status = 0 WHERE (product_id = ' . $children . ')';
                                }
                            } else {
                                $sqlUpdateProduct = 'UPDATE ' . $resource->getTableName("cataloginventory_stock_item") . ' SET qty = qty + ' . $code . ' WHERE (product_id = ' . $children . ')';
                                $sqlUpdateProductStatus = 'UPDATE ' . $resource->getTableName("cataloginventory_stock_status") . ' SET qty = qty + ' . $code . ' WHERE (product_id = ' . $children . ')';
                            }
                            $writeConnection->query($sqlUpdateProduct);
                            $writeConnection->query($sqlUpdateProductStatus);
                        }
                    }                    
                }
            }
            if($success){
                $dropship = Mage::getModel('inventorydropship/inventorydropship')->load($dropshipId);
                $dropship->setStatus('3')->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Drop ship was successfully approved!')
                );
                Mage::helper('inventorydropship')->sendEmailApproveDropShipToSupplier($dropshipId);
                $this->_redirect('*/*/edit',array('id'=>$dropshipId));
            }else{
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('adminhtml')->__('Please enter Qty Approved greater than 0 to approve this dropship!!')
                );                
                $this->_redirect('*/*/edit',array('id'=>$dropshipId));
            }
        }        
    }
    
    /*Drop shipment in supplier tab*/
    public function supplierdropshipAction()
    {            
        $this->loadLayout();
        $this->getLayout()->getBlock('inventory.supplier.view.dropship');
//            ->setDropships($this->getRequest()->getPost('dropships', null));
        $this->renderLayout();
    }

    public function supplierdropshipGridAction()
    {            
        $this->loadLayout();
        $this->getLayout()->getBlock('inventory.supplier.view.dropship');
//            ->setDropships($this->getRequest()->getPost('dropships', null));
        $this->renderLayout();
    }
    
    public function dropshipordersAction()
    {
        $this->loadLayout();            
        $this->renderLayout();
    }
    
    public function dropshipordersgridAction()
    {
        $this->loadLayout();            
        $this->renderLayout();
    }
    
    public function submitshipmentAction()
    {
        if($data = $this->getRequest()->getPost()){
            $dropshipId = $data['dropship_id'];
            
            $dropship = Mage::getModel('inventorydropship/inventorydropship')->load($dropshipId);
            $success = false;
            if($dropship->getId()){
                $resource = Mage::getSingleton('core/resource');
                $writeConnection = $resource->getConnection('core_write');
                $readConnection = $resource->getConnection('core_read');
                $itemIds = $data['item']['lastship'];
//                $itemApproves = $data['item']['lastship'];                
                $shipped = array();
                $savedQtys = array();
                $productIds = array();
                try{
                    foreach ($itemIds as $itemId=>$key){
                        
                        if(!is_numeric($key) || $key<0)
                            $key = 0;
                        $dropshipProduct = Mage::getModel('inventorydropship/inventorydropshipproduct')
                                                ->getCollection()
                                                ->addFieldToFilter('dropship_id',$dropshipId)
                                                ->addFieldToFilter('item_id',$itemId)
                                                ->getFirstItem();
                        if($dropshipProduct->getId()){
                            $success = true;
                            if($key > $dropshipProduct->getQtyApprove() - $dropshipProduct->getQtyShipped())
                                $key = $dropshipProduct->getQtyApprove() - $dropshipProduct->getQtyShipped();
                            $shipped[$itemId] = $key;
                            if($key>0){
                                $savedQtys[$dropshipProduct->getItemId()] = $key;
                                $productIds[$dropshipProduct->getItemId()] = $dropshipProduct->getProductId();
                            }
                            $dropshipProduct->setData('qty_shipped',$dropshipProduct->getQtyShipped() + $key)->save();                                                    
                        }
                    }
                }catch(Exception $e){
                    $this->_getSession()->addError($exception->getMessage());
                    $this->_redirect('*/*/edit',array('id'=>$dropshipId));
                    return;
                }
                if($success){
                    $partial = false;
                    $dropshipProducts = Mage::getModel('inventorydropship/inventorydropshipproduct')
                                                ->getCollection()
                                                ->addFieldToFilter('dropship_id',$dropshipId);
                    foreach($dropshipProducts as $dropshipP){
                        if($dropshipP->getQtyShipped() != $dropshipP->getQtyApprove()){
                            $partial = true;
                            break;
                        }
                    }
                    if($partial)
                        $dropship->setStatus('4')->save();
                    else
                        $dropship->setStatus('6')->save();
                    try{
                        //create shipment when supplier approved
                        if($savedQtys){                            
                            Mage::getModel('admin/session')->setData('break_shipment_event_dropship',true);
                            Mage::getModel('core/session')->setData('break_shipment_event_dropship',true);
                            
                            $order = Mage::getModel('sales/order')->load($dropship->getOrderId());
                            $transaction = Mage::getModel('core/resource_transaction')
                                            ->addObject($order);
                            $shipment = Mage::getModel('sales/service_order', $order)->prepareShipment($savedQtys);
                            $shipment->register();
                            $shipment->getOrder()->setIsInProcess(true);
                            $transactionSave = Mage::getModel('core/resource_transaction')
                                    ->addObject($shipment)
                                    ->addObject($shipment->getOrder())
                                    ->save();
                            foreach($savedQtys as $itemShipId=>$qtyShip){
                                $inventoryShipmentModel = Mage::getModel('inventory/inventoryshipment');
                                $inventoryShipmentModel->setItemId($itemShipId)
                                                       ->setProductId($productIds[$itemShipId])
                                                       ->setOrderId($dropship->getOrderId())
                                                       ->setSupplierId($dropship->getSupplierId())
                                                       ->setSupplierName($dropship->getSupplierName())
                                                       ->setShipmentId($shipment->getId())
                                                       ->setItemShiped($qtyShip)
                                                       ->save();
                                $children = Mage::getModel('sales/order_item')
                                                ->getCollection()
                                                ->addFieldToFilter('parent_item_id',$itemShipId);
                                if(count($children))
                                    foreach($children as $child){
                                        $inventoryShipmentModel = Mage::getModel('inventory/inventoryshipment');
                                        $inventoryShipmentModel->setItemId($child->getItemId())
                                                               ->setProductId($child->getProductId())
                                                               ->setOrderId($dropship->getOrderId())
                                                               ->setSupplierId($dropship->getSupplierId())
                                                               ->setSupplierName($dropship->getSupplierName())
                                                               ->setShipmentId($shipment->getId())
                                                               ->setItemShiped($qtyShip)
                                                               ->save();
                                    }
                            }
                        }
                    }catch(Exception $e){
                    }			
                    
                    $this->_getSession()->addSuccess($this->__('Drop ship was successfully shipped!'));
//                    Mage::helper('inventorydropship')->sendEmailConfirmShippedToAdmin($dropshipId,$shipped);
                    $this->_redirect('*/*/edit',array('id'=>$dropshipId));
                    return;
                }
            }
            $this->_getSession()->addError($this->__('Please enter Qty To Ship greater than 0 to ship this dropship!'));
            $this->_redirect('*/*/edit',array('id'=>$dropshipId));
            return;
        }
    }
}