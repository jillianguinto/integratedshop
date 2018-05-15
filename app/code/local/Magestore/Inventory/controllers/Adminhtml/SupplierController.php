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
 * Inventory Adminhtml Controller
 * 
 * @category    Magestore
 * @package     Magestore_Inventory
 * @author      Magestore Developer
 */
class Magestore_Inventory_Adminhtml_SupplierController extends Mage_Adminhtml_Controller_Action
{
    /**
     * init layout and set active for current menu
     *
     * @return Magestore_Inventory_Adminhtml_SupplierController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('inventory/supplier')
            ->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Supplier Manager'),
                Mage::helper('adminhtml')->__('Supplier Manager')
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
        $supplierId     = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('inventory/supplier')->load($supplierId);

        if ($model->getId() || $supplierId == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('supplier_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('inventory/supplier');

            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Supplier Manager'),
                Mage::helper('adminhtml')->__('Supplier Manager')
            );
            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Supplier News'),
                Mage::helper('adminhtml')->__('Supplier News')
            );

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true)
                                                ->removeItem('js','mage/adminhtml/grid.js')
                                                ->addItem('js','magestore/adminhtml/inventory/grid.js');
            $this->_addContent($this->getLayout()->createBlock('inventory/adminhtml_supplier_edit'))
                ->_addLeft($this->getLayout()->createBlock('inventory/adminhtml_supplier_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('inventory')->__('Supplier does not exist')
            );
            $this->_redirect('*/*/');
        }
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
            if(isset($data['state_id'])){
                $data['state'] = $data['state_id'];
            }
            $model = Mage::getModel('inventory/supplier')->load($this->getRequest()->getParam('id'));
            if($this->getRequest()->getParam('id')){
                $data['create_by'] = $model->getData('create_by');
            }
            $model->addData($data);
            try {
                
                //check field changed
                if($this->getRequest()->getParam('id')){
                    $oldData = Mage::getModel('inventory/supplier')->load($this->getRequest()->getParam('id'));
                    $changeArray = array();
                    $changeData = 0;
                    foreach($data as $key=>$value){
                        if(!in_array($key,$this->getFiledSaveHistory())) continue;
                        if($oldData->getData($key) != $value){
                            $changeArray[$key]['old'] = $oldData->getData($key);
                            $changeArray[$key]['new'] = $value;
                            $changeData = 1;
                        }
                    }
                }
                
                $admin = Mage::getModel('admin/session')->getUser()->getUsername();
                if(!$this->getRequest()->getParam('id')){
                    $model->setData('create_by',$admin);
                }
                
                /*change password for supplier*/
                if(Mage::getStoreConfig('inventory/dropship/enable')){
                    if($data['new_password']){
                        $newPassword = $data['new_password'];                  
                        $newPasswordHash = md5($newPassword);                    
                        $model->setPasswordHash($newPasswordHash);                        
                    }
                }
                
                $model->save();
                if(array_key_exists('send_mail',$data)){
                    $data['send_mail']=1;
                }                
                if(Mage::getStoreConfig('inventory/dropship/enable'))
                    if($data['new_password'])
                        if($data['send_mail'])                        
                            Mage::helper('inventorydropship')->sendPasswordResetConfirmationEmail($model,$newPassword);                                 
                
                $resource = Mage::getSingleton('core/resource');
                $writeConnection = $resource->getConnection('core_write');
                $installer = Mage::getModel('core/resource_setup');
                $sqlNews = array();
                $sqlOlds = '';
                $countSqlOlds = 0;
                $productChangeds = array();
                $productNews = array();
                $productDeleteds = '';
                if (isset($data['supplier_products'])){
                    $supplierProducts = array();
                    $supplierProductsExplodes = explode('&',urldecode($data['supplier_products']));
                    if(count($supplierProductsExplodes) <=900){
                        parse_str(urldecode($data['supplier_products']),$supplierProducts);
                    }else{
                        foreach($supplierProductsExplodes as $supplierProductsExplode){
                            $supplierProduct = '';
                            parse_str($supplierProductsExplode,$supplierProduct);
                            $supplierProducts = $supplierProducts + $supplierProduct;
                        }
                    }
                    if (count($supplierProducts)){
                        $productIds = '';
                        $qtys = '';
                        $count = 0;
                        foreach ($supplierProducts as $pId => $enCoded){
                            $codeArr = array(); 
                            parse_str(base64_decode($enCoded),$codeArr);
                            $supplierProductItem = Mage::getModel('inventory/supplierproduct')
                                ->getCollection()
                                ->addFieldToFilter('supplier_id',$model->getId())
                                ->addFieldToFilter('product_id',$pId)
                                ->getFirstItem();
                            $productIds[] = $pId;
                            if($supplierProductItem->getId()){
                                $countSqlOlds++;
                                if(($codeArr['cost']==$supplierProductItem->getCost())
                                    &&($codeArr['discount']==$supplierProductItem->getDiscount())
                                    &&($codeArr['tax']==$supplierProductItem->getTax()))
                                            continue;
                                $productChangeds[$pId]['old_cost'] = $supplierProductItem->getCost();
                                $productChangeds[$pId]['new_cost'] = $codeArr['cost'];
                                $productChangeds[$pId]['old_discount'] = $supplierProductItem->getDiscount();
                                $productChangeds[$pId]['new_discount'] = $codeArr['discount'];
                                $productChangeds[$pId]['old_tax'] = $supplierProductItem->getDiscount();
                                $productChangeds[$pId]['new_tax'] = $codeArr['tax'];
                                $sqlOlds .= 'UPDATE '.$installer->getTable('inventory/supplierproduct').' 
                                                                        SET `cost` = \''.$codeArr['cost'].'\',
                                                                                `discount` = \''.$codeArr['discount'].'\',
                                                                                `tax` = \''.$codeArr['tax'].'\'
                                                                                WHERE `supplier_product_id` ='.$supplierProductItem->getId().';';
                                if ($countSqlOlds == 900) {
                                        $writeConnection->query($sqlOlds);
                                        $countSqlOlds = 0;
                                }
                            }else{
                                $productNews[$pId]['new_cost'] = $codeArr['cost'];                                
                                $productNews[$pId]['new_discount'] = $codeArr['discount'];                                
                                $productNews[$pId]['new_tax'] = $codeArr['tax'];
                                $sqlNews[] = array(
                                                    'product_id' => $pId,
                                                    'supplier_id' => $model->getId(),
                                                    'discount' => $codeArr['discount'],
                                                    'tax' => $codeArr['tax'],
                                                    'cost' => $codeArr['cost']
                                            );
                                if (count($sqlNews) == 1000) {
                                        $writeConnection->insertMultiple($installer->getTable('inventory/supplierproduct'), $sqlNews);
                                        $sqlNews = array();
                                }
                            }
							
                        }
                        if(!empty($sqlNews)){
                            $writeConnection->insertMultiple($installer->getTable('inventory/supplierproduct'), $sqlNews);
                        }
                        if(!empty($sqlOlds)){
                            $writeConnection->query($sqlOlds);
                        }
                        $writeConnection->commit();
                        $productDeletes = Mage::getModel('inventory/supplierproduct')->getCollection()
                                                                                ->addFieldToFilter('supplier_id',$model->getId())
                                                                                ->addFieldToFilter('product_id',array('nin'=>$productIds));
                        if(count($productDeletes)>0){
                            $i = 0;
                            foreach($productDeletes as $productDelete){
                                if($i!=0)
                                    $productDeleteds .= ', ';
                                $productDeleteds .= Mage::helper('inventory/warehouse')->getProductSkuByProductId($productDelete->getProductId());
                                $productDelete->delete();
                            }
                        }							
                    }
                }
                
                //save histoty change
                $admin = Mage::getModel('admin/session')->getUser()->getUsername();
                if(!$this->getRequest()->getParam('id')){
                    $supplierHistory = Mage::getModel('inventory/supplierhistory');
                    $supplierHistoryContent = Mage::getModel('inventory/supplierhistorycontent');
                    $supplierHistory->setData('supplier_id',$model->getId())
                                    ->setData('time_stamp',now())
                                    ->setData('create_by',$admin)
                                    ->save();
                    $supplierHistoryContent->setData('supplier_history_id',$supplierHistory->getId())
                                            ->setData('field_name',$admin.' created this supplier!')
                                            ->save();
                }else{
                    if($changeData == 1 || $productDeleteds || count($productChangeds) || count($productNews)){
                        $supplierHistory = Mage::getModel('inventory/supplierhistory');
                        $supplierHistory->setData('supplier_id',$model->getId())
                                        ->setData('time_stamp',now())
                                        ->setData('create_by',$admin)
                                        ->save();
                        $supplierHistoryId = $supplierHistory->getId();

                        if(count($productChangeds)){
                            foreach($productChangeds as $key=>$value){
                                $newValue = '';
                                $oldValue = '';
                                if($value['new_cost']) $newValue .= '| '.Mage::helper('adminhtml')->__('Cost: ').round(floatval($value['new_cost']),2).' |';
                                if($value['new_discount']) $newValue .= '| '.Mage::helper('adminhtml')->__('Discount: ').round(floatval($value['new_discount']),2).' |';
                                if($value['new_tax']) $newValue .= '| '.Mage::helper('adminhtml')->__('Tax: ').round(floatval($value['new_tax']),2).' |';
                                if($value['old_cost']) $oldValue .= '| '.Mage::helper('adminhtml')->__('Cost: ').round(floatval($value['old_cost']),2).' |';
                                if($value['old_discount']) $oldValue .= '| '.Mage::helper('adminhtml')->__('Discount: ').round(floatval($value['old_discount']),2).' |';
                                if($value['old_tax']) $oldValue .= '| '.Mage::helper('adminhtml')->__('Tax: ').round(floatval($value['old_tax']),2).' |';
                                $productSku = Mage::helper('inventory/warehouse')->getProductSkuByProductId($key);
                                $supplierHistoryContent = Mage::getModel('inventory/supplierhistorycontent');
                                $supplierHistoryContent->setData('supplier_history_id',$supplierHistoryId)
                                                       ->setData('field_name',$this->__('Change product sku: '.$productSku))
                                                       ->setData('old_value',$oldValue)
                                                       ->setData('new_value',$newValue)
                                                       ->save();
                            }
                        }
                        if(count($productNews)){
                            foreach($productNews as $key=>$value){
                                $newValue = '';
                                $oldValue = '';
                                if($value['new_cost']) $newValue .= '| '.Mage::helper('adminhtml')->__('Cost: ').round(floatval($value['new_cost']),2).' |';
                                if($value['new_discount']) $newValue .= '| '.Mage::helper('adminhtml')->__('Discount: ').round(floatval($value['new_discount']),2).' |';
                                if($value['new_tax']) $newValue .= '| '.Mage::helper('adminhtml')->__('Tax: ').round(floatval($value['new_tax']),2).' |';                                
                                $productSku = Mage::helper('inventory/warehouse')->getProductSkuByProductId($key);
                                $supplierHistoryContent = Mage::getModel('inventory/supplierhistorycontent');
                                $supplierHistoryContent->setData('supplier_history_id',$supplierHistoryId)
                                                       ->setData('field_name',$this->__('Add product sku : '.$productSku.'for this supplier'))
                                                       ->setData('old_value',$oldValue)
                                                       ->setData('new_value',$newValue)
                                                       ->save();
                            }
                        }
                        if($productDeleteds){
                            $supplierHistoryContent = Mage::getModel('inventory/supplierhistorycontent');
                            $supplierHistoryContent->setData('supplier_history_id',$supplierHistoryId)
                                                   ->setData('field_name',$this->__('Remove product(s) from supplier'))
                                                   ->setData('new_value',$this->__('Remove product sku(s): '.$productDeleteds))
                                                   ->save();
                        }
                        if($changeData == 1){
                            foreach($changeArray as $field=>$filedValue){
                                $fileTitle = $this->getTitleByField($field);
                                if($field == 'status'){
                                    $statusArray = Mage::getSingleton('inventory/status')->getOptionHash();
                                    $filedValue['old'] = $statusArray[$filedValue['old']];
                                    $filedValue['new'] = $statusArray[$filedValue['new']];
                                }elseif($field == 'country_id'){
                                    $countryArray = array();
                                    $countryArrays = Mage::helper('inventory')->getCountryListHash();
                                    foreach($countryArrays as $country){
                                        $countryArray[$country['value']] = $country['label'];
                                    }
                                    $filedValue['old'] = $countryArray[$filedValue['old']];
                                    $filedValue['new'] = $countryArray[$filedValue['new']];  
                                }elseif($field =='state'){                                   
                                    $oldRegion = Mage::getModel('directory/region')->load($filedValue['old']);
                                    $oldRegionName = $oldRegion->getName();                                  
                                    if(!$oldRegionName || $oldRegionName==''){
                                        $oldRegionName = $filedValue['old'];
                                    }
                                    $newRegion = Mage::getModel('directory/region')->load($filedValue['new']);
                                    $newRegionName = $newRegion->getName();                                   
                                    if(!$newRegionName || $newRegionName==''){
                                        $newRegionName = $filedValue['new'];
                                    }
                                    $filedValue['old'] = $oldRegionName;
                                    $filedValue['new'] = $newRegionName;
                                }
                                
                                $supplierHistoryContent = Mage::getModel('inventory/supplierhistorycontent');
                                $supplierHistoryContent->setData('supplier_history_id',$supplierHistoryId)
                                                        ->setData('field_name',$fileTitle)
                                                        ->setData('old_value',$filedValue['old'])
                                                        ->setData('new_value',$filedValue['new'])
                                                        ->save();
                            }
                        }
                    }
                }
                
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('inventory')->__('Supplier was successfully saved!')
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
                $this->_redirect('*/*/edit', array('id' => $model->getId()));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('inventory')->__('Unable to find supplier to save!')
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
                $model = Mage::getModel('inventory/supplier');
                $model->setId($this->getRequest()->getParam('id'))
                    ->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Supplier was successfully deleted!')
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
        $supplierIds = $this->getRequest()->getParam('inventory');
        if (!is_array($supplierIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select supplier(s)!'));
        } else {
            try {
                foreach ($supplierIds as $supplierId) {
                    $supplier = Mage::getModel('inventory/supplier')->load($supplierId);
                    $supplier->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Total %d record(s) were successfully deleted',
                    count($supplierIds))
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
        $supplierIds = $this->getRequest()->getParam('inventory');
        if (!is_array($supplierIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select supplier(s)'));
        } else {
            try {
                foreach ($supplierIds as $supplierId) {
                    Mage::getSingleton('inventory/supplier')
                        ->load($supplierId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total %d record(s) were successfully updated', count($supplierIds))
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
        $fileName   = 'supplier.csv';
        $content    = $this->getLayout()
                           ->createBlock('inventory/adminhtml_supplier_grid')
                           ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction()
    {
        $fileName   = 'supplier.xml';
        $content    = $this->getLayout()
                           ->createBlock('inventory/adminhtml_supplier_grid')
                           ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('inventory');
    }
    
    /**
     * show grid purchase order for supplier
     */
    
    public function purchaseorderAction() {
        $this->loadLayout();
	$this->getLayout()->getBlock('inventory.supplier.edit.tab.purchaseorder');
        $this->renderLayout();
    }	
    public function purchaseorderGridAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('inventory.supplier.edit.tab.purchaseorder');
        $this->renderLayout();
    }
    
    /**
     * show grid return order for supplier
     */
    public function returnorderAction() {
        $this->loadLayout();
	$this->getLayout()->getBlock('inventory.supplier.edit.tab.returnorder');
        $this->renderLayout();
    }	
    public function returnorderGridAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('inventory.supplier.edit.tab.returnorder');
        $this->renderLayout();
    }
    public function productAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('inventory.supplier.edit.tab.products')
                ->setProducts($this->getRequest()->getPost('supplier_products',null));
        $this->renderLayout();
        if(Mage::getModel('admin/session')->getData('supplier_product_import'))
            Mage::getModel('admin/session')->setData('supplier_product_import',null);
    }

    public function productGridAction()
    {
            $this->loadLayout();
            $this->getLayout()->getBlock('inventory.supplier.edit.tab.products')
                    ->setProducts($this->getRequest()->getPost('supplier_products',null));
            $this->renderLayout();
    }
	
    public function importproductAction()
    {
        if (isset($_FILES['fileToUpload']['name']) && $_FILES['fileToUpload']['name'] != '') {
            try {
                $fileName   = $_FILES['fileToUpload']['tmp_name'];
                $Object  	= new Varien_File_Csv();
                $dataFile 	= $Object->getData($fileName);
                $supplierProduct = array();
                $supplierProducts = array();
                $fields = array();
                $count = 0;
                $supplierHelper = Mage::helper('inventory/supplier');
                if(count($dataFile))
                foreach($dataFile as $col => $row){
                        if($col == 0){
                                if(count($row))
                                foreach($row as $index=>$cell)
                                        $fields[$index] = (string)$cell;			
                        }elseif($col>0){
                                if(count($row))
                                foreach($row as $index=>$cell){
                                        if(isset($fields[$index])){
                                                $supplierProduct[$fields[$index]] = $cell;
                                        }
                                }
                                $supplierProducts[] = $supplierProduct;
                        }		
                }
                $supplierHelper->importProduct($supplierProducts);
            } catch (Exception $e) {
            }
        }
    }

    public function getFiledSaveHistory()
    {
        return array('name','contact_name','email','telephone','fax','street','city','country_id','state'/*,'stateEl','state_id'*/,'postcode','website','description','status');
    }
    
    public function getTitleByField($field)
    {
        $fieldArray = array(
                            'name' => Mage::helper('inventory')->__('Supplier Name '),
                            'contact_name' => Mage::helper('inventory')->__('Contact Person'),
                            'email' => Mage::helper('inventory')->__('Email'),
                            'telephone'  => Mage::helper('inventory')->__('Telephone'),
                            'fax' => Mage::helper('inventory')->__('Fax'),
                            'street' => Mage::helper('inventory')->__('Street'),
                            'city' => Mage::helper('inventory')->__('City'),
                            'country_id' => Mage::helper('inventory')->__('Country'),
                            'state' => Mage::helper('inventory')->__('State/Province'),
//                            'stateEl' => Mage::helper('inventory')->__('State/Province'),
//                            'state_id'  => Mage::helper('inventory')->__('State/Province'),
                            'postcode' => Mage::helper('inventory')->__('Zip/Postal Code'),
                            'website' => Mage::helper('inventory')->__('Website'),
                            'description' => Mage::helper('inventory')->__('Description'),
                            'status' => Mage::helper('inventory')->__('Status')
                        );
        if(!$fieldArray[$field]) return $field;
        return $fieldArray[$field];
    }
    
    public function getStateByCountry(){
        $country = Mage::app()->getRequest()->getParam('country');        
    }
    
    public function historyAction() {
        $this->loadLayout();
	$this->getLayout()->getBlock('inventory.supplier.edit.tab.history');
        $this->renderLayout();
    }	
    public function historyGridAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('inventory.supplier.edit.tab.history');
        $this->renderLayout();
    }
    
    public function showhistoryAction() {
        $form_html = $this->getLayout()
            ->createBlock('inventory/adminhtml_supplier')
            ->setTemplate('inventory/supplier/showhistory.phtml')
            ->toHtml();
        $this->getResponse()->setBody($form_html);
    }
    
    public function showreportAction(){
        $form_html = $this->getLayout()
            ->createBlock('inventory/adminhtml_supplier')
            ->setTemplate('inventory/supplier/showreport.phtml')
            ->toHtml();
        $this->getResponse()->setBody($form_html);
    }
}