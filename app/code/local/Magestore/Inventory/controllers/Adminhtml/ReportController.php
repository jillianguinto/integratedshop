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
 * Report Adminhtml Controller
 * 
 * @category    Magestore
 * @package     Magestore_Inventory
 * @author      Magestore Developer
 */
class Magestore_Inventory_Adminhtml_ReportController extends Mage_Adminhtml_Controller_Action
{
    /**
     * init layout and set active for current menu
     *
     * @return Magestore_Inventory_Adminhtml_InventoryController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('inventory/report')
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
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('inventory');
    }
    
    public function customerordersAction(){
        $this->_initAction()
            ->renderLayout();
    }
    
    protected function _initFilter(){
        $filterData = $this->prepareFilterData();
        Mage::register('report_filter_data',$filterData);
        $blockName = Mage::helper('inventory/report')->getBlockName($filterData);
        $gridBlock = $this->getLayout()->getBlock($blockName);
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');
        $this->_initSalesReportAction(array($gridBlock,$filterFormBlock));
    }
    
    protected function _initProductInfoFilter(){
        $blockName = 'inventoryadmin_adminhtml_report_productinfo';
        $gridBlock = $this->getLayout()->getBlock($blockName);
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');
        $this->_initSalesReportAction(array($gridBlock,$filterFormBlock));
    }
    
    public function salesAction(){
        
        $this->_initAction()
            ->_title($this->__('Sales Report'))
            ->_setActiveMenu('inventory/report/sales')
            ->_addBreadcrumb($this->__('Sales Report'));
        $this->_initFilter();
        $this->renderLayout();
    }
    
    public function warehousesalesAction(){
        
        $this->_initAction()
            ->_title($this->__('Sales Report by Warehouse'))
            ->_setActiveMenu('inventory/report/warehousesales')
            ->_addBreadcrumb($this->__('Sales Report by Warehouse'));
        $this->_initFilter();
        $this->renderLayout();
    }
    
    public function warehouseproductsalesAction(){
        
        $this->_initAction()
            ->_title($this->__('Sales Report by Warehouse Products'))
            ->_setActiveMenu('inventory/report/warehouseproductsales')
            ->_addBreadcrumb($this->__('Sales Report by Warehouse Products'));
        $this->_initFilter();
        $this->renderLayout();
    }
    
    public function purchasesAction(){
        $this->_initAction()
            ->renderLayout();
    }
    
    public function purchasessupplierAction(){
        $this->_initAction()
            ->renderLayout();
    }
    
    /**
     * Export customer order grid to CSV format
     */
    public function exportCustomerOrdersCsvAction()
    {
        $fileName   = 'orders.csv';
        $grid       = $this->getLayout()->createBlock('inventory/adminhtml_report_customerorders_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     *  Export customer order grid to Excel XML format
     */
    public function exportCustomerOrdersExcelAction()
    {
        $fileName   = 'orders.xml';
        $grid       = $this->getLayout()->createBlock('inventory/adminhtml_report_customerorders_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
    
    /**
     * Export sales grid case1 to CSV format
     */
    public function exportCase1CsvAction()
    {
        $fileName   = 'orders.csv';
        $grid       = $this->getLayout()->createBlock('inventory/adminhtml_report_sales_case1');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     *  Export sales grid case 1 to Excel XML format
     */
    public function exportCase1XmlAction()
    {
        $fileName   = 'orders.xml';
        $grid       = $this->getLayout()->createBlock('inventory/adminhtml_report_sales_case1');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
    
    /**
     * Export sales grid case2 to CSV format
     */
    public function exportCase2CsvAction()
    {
        $this->_initFilter();
        $fileName   = 'warehousesales.csv';
        $grid       = $this->getLayout()->createBlock('inventory/adminhtml_report_sales_case2');
        $this->_initSalesReportAction($grid);
        $grid->prepareExport();
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     *  Export sales grid case 2 to Excel XML format
     */
    public function exportCase2XmlAction()
    {
        $this->_initFilter();
        $fileName   = 'warehousesales.xml';
        $grid       = $this->getLayout()->createBlock('inventory/adminhtml_report_sales_case2');
        $this->_initSalesReportAction($grid);
        $grid->prepareExport();
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile());
    }
    
    /**
     * Export sales grid case3 to CSV format
     */
    public function exportCase3CsvAction()
    {
        $this->_initFilter();
        $fileName   = 'warehousesales.csv';
        $grid       = $this->getLayout()->createBlock('inventory/adminhtml_report_sales_case3');
        $this->_initSalesReportAction($grid);
        $grid->prepareExport();
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     *  Export sales grid case 3 to Excel XML format
     */
    public function exportCase3XmlAction()
    {
        $this->_initFilter();
        $fileName   = 'warehousesales.xml';
        $grid       = $this->getLayout()->createBlock('inventory/adminhtml_report_sales_case3');
        $this->_initSalesReportAction($grid);
        $grid->prepareExport();
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile());
    }
    
    public function exportCase4CsvAction()
    {
        $this->_initFilter();
        $fileName   = 'warehouse_product_period.csv';
        $grid       = $this->getLayout()->createBlock('inventory/adminhtml_report_sales_case4');
        $this->_initSalesReportAction($grid);
        $grid->prepareExport();
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     *  Export sales grid case 3 to Excel XML format
     */
    public function exportCase4XmlAction()
    {
        $this->_initFilter();
        $fileName   = 'warehouse_product_period.xml';
        $grid       = $this->getLayout()->createBlock('inventory/adminhtml_report_sales_case4');
        $this->_initSalesReportAction($grid);
        $grid->prepareExport();
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile());
    }
    
    public function exportCase5CsvAction()
    {
        $this->_initFilter();
        $fileName   = 'warehouse_product.csv';
        $grid       = $this->getLayout()->createBlock('inventory/adminhtml_report_sales_case5');
        $this->_initSalesReportAction($grid);
        $grid->prepareExport();
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     *  Export sales grid case 3 to Excel XML format
     */
    public function exportCase5XmlAction()
    {
        $this->_initFilter();
        $fileName   = 'warehouse_product.xml';
        $grid       = $this->getLayout()->createBlock('inventory/adminhtml_report_sales_case5');
        $this->_initSalesReportAction($grid);
        $grid->prepareExport();
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile());
    }
    
    public function exportPurchaseCsvAction()
    {
        $fileName = 'purchase.csv';
        $content = $this->getLayout()
                        ->createBlock('inventory/adminhtml_report_purchases_grid')
                        ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);	
    }

    public function exportPurchaseXmlAction()
    {
        $fileName = 'purchase.xml';
        $content = $this->getLayout()
                        ->createBlock('inventory/adminhtml_report_purchases_grid')
                        ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    
    public function _initSalesReportAction($blocks){
        if (!is_array($blocks))
                $blocks = array($blocks);

        $params = $this->prepareFilterData();
        foreach ($blocks as $block)
                if ($block) {
                        $block->setPeriodType($params->getData('period_type'));
                        $block->setFilterData($params);
                }

        return $this;
    }
    
    public function prepareFilterData(){
        $requestData = Mage::helper('adminhtml')->prepareFilterString($this->getRequest()->getParam('top_filter'));
        $requestData = $this->_filterDates($requestData, array('from', 'to'));
        $requestData['store_ids'] = $this->getRequest()->getParam('store_ids');
        $shipmentCollection = Mage::getModel('sales/order_shipment')
                ->getCollection()
                ->setOrder('created_at','ASC');
        if(!isset($requestData['date_from'])){
            $requestData['date_from'] = $shipmentCollection->getFirstItem()->getCreatedAt();
        }
        if(!isset($requestData['date_to'])){
            $requestData['date_to'] = $shipmentCollection->getLastItem()->getCreatedAt();
        }
        $params = new Varien_Object();
        
        foreach ($requestData as $key => $value)
                if (!empty($value))
                        $params->setData($key, $value);
        return $params;
    }
    
    public function exportCsvCaseOneOneAction()
    {
        $fileName = 'report_purchases.csv';
        $content = $this->getLayout()
                        ->createBlock('inventory/adminhtml_report_purchases_caseoneone')
                        ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);	
    }
	
    public function exportXmlCaseOneOneAction()
    {
        $fileName = 'report_purchases.xml';
        $content = $this->getLayout()
                        ->createBlock('inventory/adminhtml_report_purchases_caseoneone')
                        ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    
    public function exportCsvCaseOneAllAction()
    {
        $fileName = 'report_purchases.csv';
        $content = $this->getLayout()
                        ->createBlock('inventory/adminhtml_report_purchases_caseoneall')
                        ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);	
    }
	
    public function exportXmlCaseOneAllAction()
    {
        $fileName = 'report_purchases.xml';
        $content = $this->getLayout()
                        ->createBlock('inventory/adminhtml_report_purchases_caseoneall')
                        ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }
        
    public function productpurchasedAction()
    {
        $this->_initAction()
            ->renderLayout();
    }      

    public function productmovedAction()
    {
        $this->_initAction()
            ->renderLayout();
    }   

    public function productinfoAction()
    {
        $this->_initAction()
            ->renderLayout();
    } 
    
    public function noticeAction()
    {
        $this->_initAction()
               ->renderLayout();
    }
    
    public function massstatusnoticeAction()
    {
       $noticeIds = $this->getRequest()->getParam('notice');
        if (!is_array($noticeIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($noticeIds as $noticeId) {
                    Mage::getModel('inventory/notice')
                        ->load($noticeId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($noticeIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/notice');
   }
   
   public function exportCsvProductReceivedAction()
    {
        $fileName   = 'productreceived.csv';
        $grid       = $this->getLayout()->createBlock('inventory/adminhtml_report_inventoryproduct_productpurchased');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }
    
    public function exportXmlProductReceivedAction()
    {
        $fileName   = 'productreceived.xml';
        $grid       = $this->getLayout()->createBlock('inventory/adminhtml_report_inventoryproduct_productpurchased');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
    
    public function exportCsvProductInfoAction()
    {
        $fileName   = 'productinfo.csv';
        $grid       = $this->getLayout()->createBlock('inventory/adminhtml_report_inventoryproduct_product');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }
    
    public function exportXmlProductInfoAction()
    {
        $fileName   = 'productinfo.xml';
        $grid       = $this->getLayout()->createBlock('inventory/adminhtml_report_inventoryproduct_product');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
    public function exportCsvProductMovedAction()
    {
        $fileName   = 'productmoved.csv';
        $grid       = $this->getLayout()->createBlock('inventory/adminhtml_report_inventoryproduct_productmoved');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }
    
    public function exportXmlProductMoveddAction()
    {
        $fileName   = 'productmoved.xml';
        $grid       = $this->getLayout()->createBlock('inventory/adminhtml_report_inventoryproduct_productmoved');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
    
    public function readnoticeAction()
    {
        $noticeId     = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('inventory/notice')->load($noticeId);

        if ($model->getId() || $noticeId == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('notice_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('inventory/notice');

            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Notice Manager'),
                Mage::helper('adminhtml')->__('Notice Manager')
            );
            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Notice News'),
                Mage::helper('adminhtml')->__('Notice News')
            );

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('inventory/adminhtml_report_edit'))
                ->_addLeft($this->getLayout()->createBlock('inventory/adminhtml_report_edit_tabs'));

            $this->renderLayout();
            
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('inventory')->__('Notice does not exist')
            );
            $this->_redirect('*/*/');
        }
    }
    
    public function savenoticeAction()
    {
        if ($data = $this->getRequest()->getParams()) {
            try {
                $model = Mage::getModel('inventory/notice');        
                $model->load($data['id']);
                $model->setStatus($data['status'])->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('inventory')->__('Backorder request was successfully sent!')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/readnotice', array('id' => $model->getInventoryReportPrId()));
                    return;
                }
                $this->_redirect('*/*/notice');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/readnotice', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('inventory')->__('Unable to find notice to save')
        );
        $this->_redirect('*/*/');
        }
    }
    public function reportsupplierproductAction(){
        $supplier = Mage::helper('inventory/supplier')->getAllSupplierName();
        if(count($supplier) >=1){
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
            //$this->_addContent($this->getLayout()->createBlock('core/text')->setText('<script></script>'));
            $this->getLayout()->getBlock('head')->addJs('magestore/inventory/tinybox.js');
            $this->getLayout()->getBlock('head')->addCss('css/magestore/inventory.css');
            $this->_addContent($this->getLayout()->createBlock('inventory/adminhtml_supplier')
                ->setTemplate('inventory/report/supplierproduct.phtml'));
            $this->_addContent($this->getLayout()->createBlock('inventory/adminhtml_report_supplierproduct'));
            $this->_addJs($this->getLayout()
                ->createBlock('inventory/adminhtml_supplier')
                ->setTemplate('inventory/functionjs.phtml'));
            $this->renderLayout();
        }else{
            echo $this->__('Not found supplier for render report!');
        }
    }
    public function reportsupplierproductgridAction()
    {
        $this->_initFilter();
        //$this->_initSort();
        $form_html = $this->getLayout()
            ->createBlock('inventory/adminhtml_report_supplierproduct')
            ->toHtml();
        $this->getResponse()->setBody($form_html);
    }
	
	public function exportSupplierProductCsvAction()
    {
        $fileName = 'report_supplier_product.csv';
        $content = $this->getLayout()
                        ->createBlock('inventory/adminhtml_report_supplierproduct')
                        ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);	
    }
	
	public function exportSupplierProductXmlAction()
    {
        $fileName = 'report_supplier_product.xml';
        $content = $this->getLayout()
                        ->createBlock('inventory/adminhtml_report_supplierproduct')
                        ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }
	
	public function exportCsvNoticeAction()
    {
        $fileName = 'report_other_notice.csv';
        $content = $this->getLayout()
                        ->createBlock('inventory/adminhtml_report_other_notice')
                        ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);	
    }
	
	public function exportXmlNoticeAction()
    {
        $fileName = 'report_other_notice.xml';
        $content = $this->getLayout()
                        ->createBlock('inventory/adminhtml_report_other_notice')
                        ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }
}