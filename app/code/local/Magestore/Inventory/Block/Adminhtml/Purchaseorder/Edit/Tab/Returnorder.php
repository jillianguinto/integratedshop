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
 * Inventory Supplier Grid Block
 * 
 * @category    Magestore
 * @package     Magestore_Inventory
 * @author      Magestore Developer
 */
class Magestore_Inventory_Block_Adminhtml_Purchaseorder_Edit_Tab_Returnorder extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('returnorderGrid');
        $this->setDefaultSort('return_product_warehouse_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setVarNameFilter('returnorder_filter');

    }
    
    protected function  _prepareLayout()
    {
        $totalProductRecieved = Mage::helper('inventory/purchaseorder')->getDataByPurchaseOrderId($this->getRequest()->getParam('id'),'total_products_recieved');
        if(($totalProductRecieved >0) && $this->checkCreateReturn()){
            $this->setChild('return_order_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                  ->setData(array(
                    'label'     => Mage::helper('inventory')->__('Return Order'),
                    'onclick'   => 'setLocation(\''.$this->getUrl('*/*/newreturnorder', array('purchaseorder_id'=>$this->getRequest()->getParam('id'),'action'=>'newreturnorder','_current'=>false)).'\')',
                    'class' => 'add'
                  ))
            );
        }
        return parent::_prepareLayout();
    }
    
    /**
     * prepare collection for block to display
     *
     * @return Magestore_Inventory_Block_Adminhtml_Supplier_Grid
     */
    protected function _prepareCollection()
    {
        $purchase_order_id = Mage::app()->getRequest()->getParam('id');
        $collection = Mage::getModel('inventory/returnwarehouse')->getCollection()->addFieldToFilter('purchase_order_id ',$purchase_order_id);    
        $this->setCollection($collection);
        return parent::_prepareCollection();    
    }
    
    /**
     * prepare columns for this grid
     *
     * @return Magestore_Inventory_Block_Adminhtml_Supplier_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('return_product_warehouse_id', array(
            'header'    => Mage::helper('inventory')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'return_product_warehouse_id',
        ));
        
        $this->addColumn('returned_on', array(
            'header'    => Mage::helper('inventory')->__('Return Date'),
            'width'     => '150px',
            'type'	=> 'datetime',
            'index'     => 'returned_on',
        ));

        $this->addColumn('product_name', array(
            'header'    => Mage::helper('inventory')->__('Name'),
            'align'     =>'left',
            'index'     => 'product_name',
        ));
        
        $this->addColumn('product_image', array(
                'header' => Mage::helper('catalog')->__('Image'),
                'width' => '90px',
                'renderer' => 'inventory/adminhtml_renderer_productimage'   ,
                'filter' => false,
        ));
        
         $this->addColumn('warehouse_name', array(
            'header'    => Mage::helper('inventory')->__('Warehouse'),
            'width'     => '80px',
            'index'     => 'warehouse_name'
        ));
        
        $this->addColumn('qty_return', array(
            'header'    => Mage::helper('inventory')->__('Qty Returned'),
            'width'     => '150px',
            'name'     => 'qty_return',
            'type'      => 'number',
            'index'     => 'qty_return'
        ));
        
        $this->addColumn('create_by', array(
            'header'    => Mage::helper('inventory')->__('Create by'),
            'name'	=> 'create_by',
            'width'     => '80px',
            'index'     => 'create_by'
        ));
        
        $this->addColumn('reason', array(
            'header'    => Mage::helper('inventory')->__('Reason(s)'),
            'name'	=> 'reason',
            'width'     => '150px',
            'index'     => 'reason'
        ));
        
        return parent::_prepareColumns();
    }
    
    /**
     * prepare mass action for this grid
     *
     * @return Magestore_Inventory_Block_Adminhtml_Supplier_Grid
     */
   
    
    /**
     * get url for each row in grid
     *
     * @return string
     */
    public function getRowUrl($row)
    {
    }
    
    public function  getSearchButtonHtml()
    {
        return parent::getSearchButtonHtml() . $this->getChildHtml('return_order_button'). $this->getChildHtml('cancel_order_button');
    }
    
    public function checkCreateReturn()
    {
        $canReturn = false;
        $adminId = Mage::getModel('admin/session')->getUser()->getId();
        if(!$adminId) return null;
        $purchaseOrderId = $this->getRequest()->getParam('id');
        $purchaseOrder = Mage::getModel('inventory/purchaseorder')->load($purchaseOrderId);
        $warehouseIds = $purchaseOrder->getWarehouseId();
        $warehouseIds = explode(',', $warehouseIds);
        $warehouseAssigneds = Mage::getModel('inventory/assignment')->getCollection()
                                ->addFieldToFilter('admin_id',$adminId)
                                ;
        $warehouseAvailableIds = array();
        foreach($warehouseAssigneds as $warehouseAssigned){
            if($warehouseAssigned->getData('can_edit_warehouse')
                    || $warehouseAssigned->getData('can_transfer')
                    || $warehouseAssigned->getData('can_adjust'))
                $warehouseAvailableIds[] = $warehouseAssigned->getWarehouseId();
        }
        foreach($warehouseIds as $warehouseId){
            if(in_array($warehouseId,$warehouseAvailableIds)){
                $canReturn = true;
                break;
            }                            
        }
        return $canReturn;
    }
}