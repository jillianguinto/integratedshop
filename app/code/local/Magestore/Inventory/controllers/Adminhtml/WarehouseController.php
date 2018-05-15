<?php

class Magestore_Inventory_Adminhtml_WarehouseController extends Mage_Adminhtml_Controller_Action {

    public function indexAction() {
        $this->loadLayout()
                ->_setActiveMenu('inventory/warehouse');
        $this->renderLayout();
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function transactionAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function transactiongridAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function gridAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function viewTransactionAction() {
        $transactionId = $this->getRequest()->getParam('transaction_id');
        $model = Mage::getModel('inventory/transaction')->load($transactionId);

        if ($model->getId() || $transactionId == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('transaction_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('inventory/warehouse');

            $this->_addBreadcrumb(
                    Mage::helper('adminhtml')->__('View Transaction'), Mage::helper('adminhtml')->__('View Transaction')
            );
            $this->_addBreadcrumb(
                    Mage::helper('adminhtml')->__('Transaction'), Mage::helper('adminhtml')->__('Transaction')
            );

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true)
                    ->removeItem('js', 'mage/adminhtml/grid.js')
                    ->addItem('js', 'magestore/adminhtml/inventory/grid.js');
            $this->_addContent($this->getLayout()->createBlock('inventory/adminhtml_transaction_view'))
                    ->_addLeft($this->getLayout()->createBlock('inventory/adminhtml_transaction_view_tabs'));
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('inventory')->__('Warehouse does not exist!')
            );
            $this->_redirect('*/*/');
        }
    }

    public function editAction() {
        $warehouseId = $this->getRequest()->getParam('id');
        $model = Mage::getModel('inventory/warehouse')->load($warehouseId);

        if ($model->getId() || $warehouseId == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('warehouse_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('inventory/warehouse');

            $this->_addBreadcrumb(
                    Mage::helper('adminhtml')->__('Warehouse Manager'), Mage::helper('adminhtml')->__('Warehouse Manager')
            );
            $this->_addBreadcrumb(
                    Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News')
            );

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true)
                    ->removeItem('js', 'mage/adminhtml/grid.js')
                    ->addItem('js', 'magestore/adminhtml/inventory/grid.js');
            $this->_addContent($this->getLayout()->createBlock('inventory/adminhtml_warehouse_edit'))
                    ->_addLeft($this->getLayout()->createBlock('inventory/adminhtml_warehouse_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('inventory')->__('Warehouse does not exist!')
            );
            $this->_redirect('*/*/');
        }
    }

    public function saveAction() {
        $admin = Mage::getSingleton('admin/session')->getUser();
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('inventory/warehouse')->load($this->getRequest()->getParam('id'));
            $model->addData($data);

            //check field changed
            if ($this->getRequest()->getParam('id')) {
                $oldData = Mage::getModel('inventory/warehouse')->load($this->getRequest()->getParam('id'));
                $changeArray = array();
                $changeData = 0;
                foreach ($data as $key => $value) {
                    if (!in_array($key, $this->getFiledSaveHistory()))
                        continue;
                    if ($oldData->getData($key) != $value) {
                        $changeArray[$key]['old'] = $oldData->getData($key);
                        $changeArray[$key]['new'] = $value;
                        $changeData = 1;
                    }
                }
            }
            if (!$this->getRequest()->getParam('id'))
                $model->setCreatedBy($admin->getUsername());
        }
        try {
            $model->save();
            $resource = Mage::getSingleton('core/resource');
            $writeConnection = $resource->getConnection('core_write');
            $installer = Mage::getModel('core/resource_setup');
            $sqlNews = array();
            $sqlOlds = '';
            $countSqlOlds = 0;
            $productsHistory = array();
            $warehouseProductDeleteds = '';
            $changeProductQtys = array();
            //save products
            if (isset($data['warehouse_products'])) {
                $warehouseProducts = array();
                $warehouseProductsExplodes = explode('&', urldecode($data['warehouse_products']));
                if (count($warehouseProductsExplodes) <= 900) {
                    parse_str(urldecode($data['warehouse_products']), $warehouseProducts);
                } else {
                    foreach ($warehouseProductsExplodes as $warehouseProductsExplode) {
                        $warehouseProduct = '';
                        parse_str($warehouseProductsExplode, $warehouseProduct);
                        $warehouseProducts = $warehouseProducts + $warehouseProduct;
                    }
                }
                if (count($warehouseProducts)) {
                    $deletes = array_keys($warehouseProducts);
                    $warehouseProductDeleteds = Mage::helper('inventory/warehouse')->deleteWarehouseProducts($model, $deletes);
                    $productIds = '';
                    foreach ($warehouseProducts as $pId => $enCoded) {
                        $codeArr = array();
                        parse_str(base64_decode($enCoded), $codeArr);
                        $warehouseProductsItem = Mage::getModel('inventory/warehouseproduct')
                                ->getCollection()
                                ->addFieldToFilter('warehouse_id', $model->getId())
                                ->addFieldToFilter('product_id', $pId)
                                ->getFirstItem();
                        if ($warehouseProductsItem->getId()) {
                            $countSqlOlds++;
                            if ($codeArr['qty'] == $warehouseProductsItem->getQty())
                                continue;
                            if (!is_numeric($codeArr['qty']))
                                continue;
                            $current_warehouse_qty = $warehouseProductsItem->getQty();
                            $changeProductQtys[$pId]['old_qty'] = $current_warehouse_qty;
                            $changeProductQtys[$pId]['new_qty'] = $codeArr['qty'];
                            $oldQtyAvailable = $warehouseProductsItem->getQtyAvailable();
                            $newQtyAvailable = $oldQtyAvailable + ($codeArr['qty'] - $warehouseProductsItem->getQty());
                            $warehouseProductsItem
                                    ->setWarehouseId($model->getId())
                                    ->setQty($codeArr['qty'])
                                    ->setQtyAvailable($newQtyAvailable)
                                    ->save();
                            $productsHistory[$pId] = array('old' => $current_warehouse_qty, 'new' => $codeArr['qty']);
                            $stock_item = Mage::getModel('cataloginventory/stock_item')
                                    ->getCollection()
                                    ->addFieldToFilter('product_id', $pId)
                                    ->getFirstItem();
                            $stock_item_qty = $stock_item->getQty();
                            $new_qty = (int) $stock_item_qty + (int) $codeArr['qty'] - $current_warehouse_qty;
                            try {
                                $stock_item->setQty($new_qty);
                                $minToChangeStatus = Mage::getStoreConfig('cataloginventory/item_options/min_qty');
                                if (Mage::getStoreConfig('inventory/general/updatestock')) {
                                    if ($new_qty > $minToChangeStatus) {
                                        $stock_item->setData('is_in_stock', 1);
                                    } else {
                                        $stock_item->setData('is_in_stock', 0);
                                    }
                                }

                                //                            $stock_item->setQty($new_qty)->save();
                                $stock_item->save();
                            } catch (Exception $e) {
                                
                            }
                        }
                        $productIds[] = $pId;
                    }
                }
            }
            if ($this->getRequest()->getParam('id')) {
                if (count($productsHistory)) {
                    Mage::helper('inventory/warehouse')->createNewAdjustStock($model, $productsHistory);
                }
            }
            //save assignment
            $edits = array();
            if (isset($data['edit']) && is_array($data['edit'])) {
                $edits = $data['edit'];
            }
            $transfers = array();
            if (isset($data['transfer']) && is_array($data['transfer'])) {
                $transfers = $data['transfer'];
            }

            $adjusts = array();
            if (isset($data['adjust']) && is_array($data['adjust'])) {
                $adjusts = $data['adjust'];
            }
            $admins = Mage::getModel('admin/user')->getCollection()->getAllIds();
            $changePermissions = array();
            foreach ($admins as $adminId) {
                $assignment = Mage::getModel('inventory/assignment')->loadByWarehouseAndAdmin($model->getId(), $adminId);
                if ($assignment->getId()) {
                    $oldEditWarehouse = $assignment->getCanEditWarehouse();
                    $oldTransfer = $assignment->getCanTransfer();
                    $oldAdjust = $assignment->getCanAdjust();
                }
                $assignment->setWarehouseId($model->getId());
                $assignment->setAdminId($adminId);
                if (in_array($adminId, $edits)) {
                    if ($assignment->getId()) {
                        if ($oldEditWarehouse != 1) {
                            $changePermissions[$adminId]['old_edit'] = Mage::helper('inventory')->__('Cannot edit Warehouse');
                            $changePermissions[$adminId]['new_edit'] = Mage::helper('inventory')->__('Can edit Warehouse');
                        }
                    } else {
                        $changePermissions[$adminId]['old_edit'] = '';
                        $changePermissions[$adminId]['new_edit'] = Mage::helper('inventory')->__('Can edit Warehouse');
                    }
                    $assignment->setData('can_edit_warehouse', 1);
                } else {
                    if ($assignment->getId()) {
                        if ($oldEditWarehouse != 0) {
                            $changePermissions[$adminId]['old_edit'] = Mage::helper('inventory')->__('Can edit Warehouse');
                            $changePermissions[$adminId]['new_edit'] = Mage::helper('inventory')->__('Cannot edit Warehouse');
                        }
                    } else {
                        $changePermissions[$adminId]['old_edit'] = '';
                        $changePermissions[$adminId]['new_edit'] = Mage::helper('inventory')->__('Cannot edit Warehouse');
                    }
                    $assignment->setData('can_edit_warehouse', 0);
                }
                if (in_array($adminId, $transfers)) {
                    if ($assignment->getId()) {
                        if ($oldTransfer != 1) {
                            $changePermissions[$adminId]['old_transfer'] = Mage::helper('inventory')->__('Cannot transfer Warehouse');
                            $changePermissions[$adminId]['new_transfer'] = Mage::helper('inventory')->__('Can transfer Warehouse');
                        }
                    } else {
                        $changePermissions[$adminId]['old_transfer'] = '';
                        $changePermissions[$adminId]['new_transfer'] = Mage::helper('inventory')->__('Can transfer Warehouse');
                    }
                    $assignment->setData('can_transfer', 1);
                } else {
                    if ($assignment->getId()) {
                        if ($oldTransfer != 0) {
                            $changePermissions[$adminId]['old_transfer'] = Mage::helper('inventory')->__('Can transfer Warehouse');
                            $changePermissions[$adminId]['new_transfer'] = Mage::helper('inventory')->__('Cannot transfer Warehouse');
                        }
                    } else {
                        $changePermissions[$adminId]['old_transfer'] = '';
                        $changePermissions[$adminId]['new_transfer'] = Mage::helper('inventory')->__('Cannot transfer Warehouse');
                    }
                    $assignment->setData('can_transfer', 0);
                }
                if (in_array($adminId, $adjusts)) {
                    if ($assignment->getId()) {
                        if ($oldAdjust != 1) {
                            $changePermissions[$adminId]['old_adjust'] = Mage::helper('inventory')->__('Cannot adjust Warehouse');
                            $changePermissions[$adminId]['new_adjust'] = Mage::helper('inventory')->__('Can adjust Warehouse');
                        }
                    } else {
                        $changePermissions[$adminId]['old_adjust'] = '';
                        $changePermissions[$adminId]['new_adjust'] = Mage::helper('inventory')->__('Can adjust Warehouse');
                    }
                    $assignment->setData('can_adjust', 1);
                } else {
                    if ($assignment->getId()) {
                        if ($oldAdjust != 0) {
                            $changePermissions[$adminId]['old_adjust'] = Mage::helper('inventory')->__('Can adjust Warehouse');
                            $changePermissions[$adminId]['new_adjust'] = Mage::helper('inventory')->__('Cannot adjust Warehouse');
                        }
                    } else {
                        $changePermissions[$adminId]['old_adjust'] = '';
                        $changePermissions[$adminId]['new_adjust'] = Mage::helper('inventory')->__('Cannot adjust Warehouse');
                    }
                    $assignment->setData('can_adjust', 0);
                }

                try {
                    $assignment->save();
                } catch (Exception $e) {
                    
                }
            }

            //save histoty change
            $admin = Mage::getModel('admin/session')->getUser()->getUsername();
            if (!$this->getRequest()->getParam('id')) {
                $warehouseHistory = Mage::getModel('inventory/warehousehistory');
                $warehouseHistoryContent = Mage::getModel('inventory/warehousehistorycontent');
                $warehouseHistory->setData('warehouse_id', $model->getId())
                        ->setData('time_stamp', now())
                        ->setData('create_by', $admin)
                        ->save();
                $warehouseHistoryContent->setData('warehouse_history_id', $warehouseHistory->getId())
                        ->setData('field_name', $admin . ' created this warehouse!')
                        ->save();
            } else {
                if ($changeData == 1 || $warehouseProductDeleteds || count($changeProductQtys) || count($changePermissions)) {
                    $warehouseHistory = Mage::getModel('inventory/warehousehistory');
                    $warehouseHistory->setData('warehouse_id', $model->getId())
                            ->setData('time_stamp', now())
                            ->setData('create_by', $admin)
                            ->save();
                    $warehouseHistoryId = $warehouseHistory->getId();

                    if (count($changePermissions)) {
                        foreach ($changePermissions as $key => $value) {
                            $admin = Mage::getModel('admin/user')->load($key)->getUsername();
                            $newValue = '';
                            $oldValue = '';
                            if ($value['new_edit'])
                                $newValue .= '| ' . $value['new_edit'] . ' |';
                            if ($value['new_transfer'])
                                $newValue .= '| ' . $value['new_transfer'] . ' |';
                            if ($value['new_adjust'])
                                $newValue .= '| ' . $value['new_adjust'] . ' |';
                            if ($value['old_edit'])
                                $oldValue .= '| ' . $value['old_edit'] . ' |';
                            if ($value['old_transfer'])
                                $oldValue .= '| ' . $value['old_transfer'] . ' |';
                            if ($value['old_adjust'])
                                $oldValue .= '| ' . $value['old_adjust'] . ' |';
                            $warehouseHistoryContent = Mage::getModel('inventory/warehousehistorycontent');
                            $warehouseHistoryContent->setData('warehouse_history_id', $warehouseHistoryId)
                                    ->setData('field_name', $this->__('Change permission of ' . $admin))
                                    ->setData('old_value', $oldValue)
                                    ->setData('new_value', $newValue)
                                    ->save();
                        }
                    }
                    if (count($changeProductQtys)) {
                        foreach ($changeProductQtys as $key => $value) {
                            $productSku = Mage::helper('inventory/warehouse')->getProductSkuByProductId($key);
                            $warehouseHistoryContent = Mage::getModel('inventory/warehousehistorycontent');
                            $warehouseHistoryContent->setData('warehouse_history_id', $warehouseHistoryId)
                                    ->setData('field_name', $this->__('Change quantity of product sku: ' . $productSku))
                                    ->setData('old_value', $value['old_qty'])
                                    ->setData('new_value', $value['new_qty'])
                                    ->save();
                        }
                    }
                    if ($warehouseProductDeleteds) {
                        $warehouseHistoryContent = Mage::getModel('inventory/warehousehistorycontent');
                        $warehouseHistoryContent->setData('warehouse_history_id', $warehouseHistoryId)
                                ->setData('field_name', $this->__('Remove product(s) from warehouse'))
                                ->setData('new_value', $this->__('Remove product sku(s): ' . $warehouseProductDeleteds))
                                ->save();
                    }
                    if ($changeData == 1) {
                        foreach ($changeArray as $field => $filedValue) {
                            $fileTitle = $this->getTitleByField($field);
                            if ($field == 'status') {
                                $statusArray = Mage::getSingleton('inventory/status')->getOptionHash();
                                $filedValue['old'] = $statusArray[$filedValue['old']];
                                $filedValue['new'] = $statusArray[$filedValue['new']];
                            } elseif ($field == 'country_id') {
                                $countryArray = array();
                                $countryArrays = Mage::helper('inventory')->getCountryListHash();
                                foreach ($countryArrays as $country) {
                                    $countryArray[$country['value']] = $country['label'];
                                }
                                $filedValue['old'] = $countryArray[$filedValue['old']];
                                $filedValue['new'] = $countryArray[$filedValue['new']];
                            } elseif ($field == 'state_id') {
                                $oldRegion = Mage::getModel('directory/region')->load($filedValue['old']);
                                $oldRegionName = $oldRegion->getName();
                                $newRegion = Mage::getModel('directory/region')->load($filedValue['new']);
                                $newRegionName = $newRegion->getName();
                                $filedValue['old'] = $oldRegionName;
                                $filedValue['new'] = $newRegionName;
                            }
                            $warehouseHistoryContent = Mage::getModel('inventory/warehousehistorycontent');
                            $warehouseHistoryContent->setData('warehouse_history_id', $warehouseHistoryId)
                                    ->setData('field_name', $fileTitle)
                                    ->setData('old_value', $filedValue['old'])
                                    ->setData('new_value', $filedValue['new'])
                                    ->save();
                        }
                    }
                }
            }

            Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('inventory')->__('Warehouse was successfully saved!')
            );
            if (!$this->getRequest()->getParam('id'))
                Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('inventory')->__('Warehouse is empty. You can add products by requesting stock for warehouse.'));
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

        Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('inventory')->__('Unable to find warehouse to save!')
        );
        $this->_redirect('*/*/');
    }

    public function exportCsvAction() {
        $fileName = 'warehouse.csv';
        $content = $this->getLayout()->createBlock('inventory/adminhtml_warehouse_grid')
                ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function exportXmlAction() {
        $fileName = 'warehouse.xml';
        $content = $this->getLayout()->createBlock('inventory/adminhtml_warehouse_grid')
                ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function productsAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('warehouse.edit.tab.products')
                ->setProducts($this->getRequest()->getPost('warehouse_products', null));
        $this->renderLayout();
    }

    public function productsGridAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('warehouse.edit.tab.products')
                ->setProducts($this->getRequest()->getPost('warehouse_products', null));
        $this->renderLayout();
    }

    public function transactionproductViewAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function stockissuingAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function stockissuingGridAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function stockreceivingAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function stockreceivingGridAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function stocktransferingAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('warehouse.edit.tab.stocktransfering');
        $this->renderLayout();
    }

    public function stocktransferingGridAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('warehouse.edit.tab.stocktransfering');
        $this->renderLayout();
    }

    public function customerOrdersAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('warehouse.edit.tab.customerorders');
        $this->renderLayout();
    }

    public function customerOrdersGridAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('warehouse.edit.tab.customerorders');
        $this->renderLayout();
    }

    public function deleteAction() {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('inventory/warehouse');
                $canDelete = Mage::helper('inventory/warehouse')->canDelete($this->getRequest()->getParam('id'));
                if ($canDelete) {
                    $model->setId($this->getRequest()->getParam('id'))
                            ->delete();

                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Warehouse was successfully deleted'));
                    $this->_redirect('*/*/');
                } else {
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Can\'t delete warehouse because it still contains some products.'));
                    $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                }
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $warehouseIds = $this->getRequest()->getParam('warehouse');
        if (!is_array($warehouseIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select warehouse(s)!'));
        } else {
            try {
                $total = count($warehouseIds);
                $i = 0;
                foreach ($warehouseIds as $warehouseId) {
                    $canDelete = Mage::helper('inventory/warehouse')->canDelete($warehouseId);
                    $warehouse = Mage::getModel('inventory/warehouse')->load($warehouseId);
                    if ($warehouseId == 1) {
                        Mage::getSingleton('adminhtml/session')->addError(
                                Mage::helper('adminhtml')->__('The system need ' . $warehouse->getName() . ' to work properly <br/>' . 'Please do not try to delete it')
                        );
                        $total = $total - 1;
                    } else {
                        if ($canDelete) {
                            $warehouse->delete();
                            $i++;
                        } else {
                            $total = $total - 1;
                            Mage::getSingleton('adminhtml/session')->addError(
                                    Mage::helper('adminhtml')->__('Can\'t delete warehouse %s because it still contains some products.<br/>', $warehouse->getName())
                            );
                        }
                    }
                }
                if ($total > 0) {
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                            Mage::helper('adminhtml')->__('Total %d warehouse(s) were successfully deleted.', $i)
                    );
                }
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass change status for item(s) action
     */
    public function massStatusAction() {
        $warehouseIds = $this->getRequest()->getParam('warehouse');
        if (!is_array($warehouseIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select warehouse(s)!'));
        } else {
            try {
                foreach ($warehouseIds as $warehouseId) {
                    Mage::getSingleton('inventory/warehouse')
                            ->load($warehouseId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                        $this->__('Total %d warehouse(s) were successfully updated.', count($warehouseIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function assignmentAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('warehouse.edit.tab.assignment')
                ->setAssignments($this->getRequest()->getPost('rassignments', null));
        $this->renderLayout();
    }

    public function assignmentGridAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('warehouse.edit.tab.assignment')
                ->setAssignments($this->getRequest()->getPost('rassignments', null));
        $this->renderLayout();
    }

    public function adjuststockgridAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('warehouse.edit.tab.adjuststock')
                ->setAssignments($this->getRequest()->getPost('radjuststock', null));
        $this->renderLayout();
    }

    public function getFiledSaveHistory() {
        return array('name', 'manager_name', 'manager_email', 'telephone', 'street', 'city', 'country_id', 'state', 'stateEl', 'state_id', 'postcode', 'status');
    }

    public function getTitleByField($field) {
        $fieldArray = array(
            'name' => Mage::helper('inventory')->__('Warehouse Name'),
            'manager_name' => Mage::helper('inventory')->__('Manager\'s Name'),
            'manager_email' => Mage::helper('inventory')->__('Manager\'s Email'),
            'telephone' => Mage::helper('inventory')->__('Telephone'),
            'street' => Mage::helper('inventory')->__('Street'),
            'city' => Mage::helper('inventory')->__('City'),
            'country_id' => Mage::helper('inventory')->__('Country'),
            'stateEl' => Mage::helper('inventory')->__('State/Province'),
            'state' => Mage::helper('inventory')->__('State/Province'),
            'state_id' => Mage::helper('inventory')->__('State/Province'),
            'postcode' => Mage::helper('inventory')->__('Zip/Postal Code'),
            'status' => Mage::helper('inventory')->__('Status')
        );
        if (!$fieldArray[$field])
            return $field;
        return $fieldArray[$field];
    }

    public function historyAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('inventory.warehouse.edit.tab.history');
        $this->renderLayout();
    }

    public function historyGridAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('inventory.warehouse.edit.tab.history');
        $this->renderLayout();
    }

    public function showhistoryAction() {
        $form_html = $this->getLayout()
                ->createBlock('inventory/adminhtml_warehouse')
                ->setTemplate('inventory/warehouse/showhistory.phtml')
                ->toHtml();
        $this->getResponse()->setBody($form_html);
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('inventory');
    }

}

?>
