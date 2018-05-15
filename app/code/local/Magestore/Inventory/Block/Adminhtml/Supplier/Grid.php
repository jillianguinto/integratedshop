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
class Magestore_Inventory_Block_Adminhtml_Supplier_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('supplierGrid');
        $this->setDefaultSort('supplier_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * prepare collection for block to display
     *
     * @return Magestore_Inventory_Block_Adminhtml_Supplier_Grid
     */
    protected function _prepareCollection() {
        $collection = Mage::getModel('inventory/supplier')->getCollection();

        $filter = $this->getParam($this->getVarNameFilter(), null);
        $condorder = '';
        if ($filter) {
            $data = $this->helper('adminhtml')->prepareFilterString($filter);
            foreach ($data as $value => $key) {
                if ($value == 'last_purchase_order') {
                    $condorder = $key;
                }
            }
        }
        if ($condorder) {
            $condorder = Mage::helper('inventory')->filterDates($condorder, array('from', 'to'));
            $from = $condorder['from'];
            $to = $condorder['to'];
            if ($from) {
                $from = date('Y-m-d', strtotime($from));
                $collection->addFieldToFilter('last_purchase_order', array('gteq' => $from));
            }
            if ($to) {
                $to = date('Y-m-d', strtotime($to));
                $to .= ' 23:59:59';
                $collection->addFieldToFilter('last_purchase_order', array('lteq' => $to));
            }
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare columns for this grid
     *
     * @return Magestore_Inventory_Block_Adminhtml_Supplier_Grid
     */
    protected function _prepareColumns() {
        $currencyCode = Mage::app()->getStore()->getBaseCurrency()->getCode();
        $this->addColumn('supplier_id', array(
            'header' => Mage::helper('inventory')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'type' => 'number',
            'index' => 'supplier_id',
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('inventory')->__('Supplier Name'),
            'align' => 'left',
            'index' => 'name',
        ));

        $this->addColumn('create_by', array(
            'header' => Mage::helper('inventory')->__('Created by'),
            'width' => '80px',
            'index' => 'create_by'
        ));

        $this->addColumn('total_order', array(
            'header' => Mage::helper('inventory')->__('Purchase Order'),
            'type' => 'number',
            'width' => '150px',
            'index' => 'total_order',
        ));

        $this->addColumn('purchase_order', array(
            'header' => Mage::helper('inventory')->__('Purchase Order Value'),
            'width' => '150px',
            'type' => 'price',
            'currency_code' => $currencyCode,
            'index' => 'purchase_order',
        ));

        $this->addColumn('return_order', array(
            'header' => Mage::helper('inventory')->__('Return Order Value'),
            'width' => '150px',
            'type' => 'price',
            'currency_code' => $currencyCode,
            'index' => 'return_order',
        ));

        $this->addColumn('last_purchase_order', array(
            'header' => Mage::helper('inventory')->__('Last Purchase Order On'),
            'width' => '150px',
            'type' => 'date',
            'default' => '--',
            'index' => 'last_purchase_order',
            'filter_condition_callback' => array($this, 'filterCreatedOn')
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('inventory')->__('Status'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'status',
            'type' => 'options',
            'options' => array(
                1 => 'Enabled',
                2 => 'Disabled',
            ),
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('inventory')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('inventory')->__('Edit'),
                    'url' => array('base' => '*/*/edit'),
                    'field' => 'id'
            )),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('inventory')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('inventory')->__('XML'));

        return parent::_prepareColumns();
    }

    /**
     * prepare mass action for this grid
     *
     * @return Magestore_Inventory_Block_Adminhtml_Supplier_Grid
     */
    protected function _prepareMassaction() {
        $this->setMassactionIdField('inventory_id');
        $this->getMassactionBlock()->setFormFieldName('inventory');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('inventory')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('inventory')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('inventory/status')->getOptionArray();

        array_unshift($statuses, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('inventory')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('inventory')->__('Status'),
                    'values' => $statuses
            ))
        ));
        return $this;
    }

    /**
     * get url for each row in grid
     *
     * @return string
     */
    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    public function filterCreatedOn($collection, $column) {
        return $this;
    }

}