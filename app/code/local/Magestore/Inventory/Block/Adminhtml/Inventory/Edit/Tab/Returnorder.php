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
 * Inventory Inventory Grid Block
 * 
 * @category    Magestore
 * @package     Magestore_Inventory
 * @author      Magestore Developer
 */
class Magestore_Inventory_Block_Adminhtml_Inventory_Edit_Tab_Returnorder extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('returnorderGrid');
        $this->setDefaultSort('returned_order_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setVarNameFilter('returnorder_filter');
    }
    
    /**
     * prepare collection for block to display
     *
     * @return Magestore_Inventory_Block_Adminhtml_Inventory_Edit_Tab_Returnorder
     */
    protected function _prepareCollection()
    {
        $product_id = Mage::app()->getRequest()->getParam('id');
        $returnOrderProducts = Mage::getModel('inventory/returnorderproduct')
                                        ->getCollection()
                                        ->addFieldToFilter('product_id',$product_id);
        $returnOrderIds = array();
        foreach($returnOrderProducts as $returnOrderProduct)
            $returnOrderIds[] = $returnOrderProduct->getReturnedOrderId();
        $collection = Mage::getModel('inventory/returnorder')
                            ->getCollection()
                            ->addFieldToFilter('returned_order_id',array('in'=>$returnOrderIds));
        
        $filter   = $this->getParam($this->getVarNameFilter(), null);
        $condorder = '';
        if($filter){
            $data = $this->helper('adminhtml')->prepareFilterString($filter);
            foreach($data as $value=>$key){
                if($value == 'returned_on'){
                    $condorder = $key;
                }
            }
        }
        if($condorder){
            $condorder = Mage::helper('inventory')->filterDates($condorder,array('from','to'));
            $from = $condorder['from'];
            $to = $condorder['to'];
            if($from){
                $from = date('Y-m-d',strtotime($from));
                $collection->addFieldToFilter('returned_on',array('gteq'=>$from));
            }
            if($to){
                $to = date('Y-m-d',strtotime($to));
                $to .= ' 23:59:59';
                $collection->addFieldToFilter('returned_on',array('lteq'=>$to));
            }
        }        
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    /**
     * prepare columns for this grid
     *
     * @return Magestore_Inventory_Block_Adminhtml_Inventory_Edit_Tab_Returnorder
     */
    protected function _prepareColumns()
    {
        $this->addColumn('returned_order_id', array(
            'header'    => Mage::helper('inventory')->__('Return Order Id'),
            'align'     =>'center',
            'width'     => '50px',
            'index'     => 'returned_order_id',
        ));

        $this->addColumn('returned_on', array(
            'header'    => Mage::helper('inventory')->__('Returned On'),
            'align'     =>'right',
            'type'      => 'date',
            'index'     => 'returned_on',
            'filter_condition_callback' => array($this, 'filterCreatedOn')
        ));

        $this->addColumn('grand_total_excl_tax', array(
            'header'    => Mage::helper('inventory')->__('Grand Total Excl .TAX'),
            'width'     => '150px',
            'currency_code' => (string)Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'type'	=> 'price',
            'index'     => 'total_amount',
            'align'     =>'right',
            'renderer'  => 'Magestore_Inventory_Block_Adminhtml_Supplier_Renderertotalexcl',
        ));
		
	$this->addColumn('grand_total_incl_tax', array(
            'header'    => Mage::helper('inventory')->__('Grand Total Incl.TAX'),
            'width'     => '150px',
            'align'     =>'right',
            'currency_code' => (string)Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'type'		=> 'price',
            'index'     => 'total_amount',
        ));
        
	$this->addColumn('total_products', array(
            'header'    => Mage::helper('inventory')->__('Quantity Return'),
            'name'      => 'total_products',
            'type'	=> 'number',
            'index'     => 'total_products',
        ));

        return parent::_prepareColumns();
    }
    
    public function getRowUrl($row)
    {
    }
    
    public function filterCreatedOn($collection, $column)
    {
        return $this;
    }
}