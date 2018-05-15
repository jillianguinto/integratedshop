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
 * Warehouse Grid Block
 * 
 * @category    Magestore
 * @package     Magestore_Inventory
 * @author      Magestore Developer
 */
class Magestore_Inventory_Block_Adminhtml_Warehouse_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('warehouseGrid');
        $this->setDefaultSort('warehouse_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * prepare collection for block to display
     *
     * @return Magestore_Inventory_Block_Adminhtml_Inventory_Grid
     */
    protected function _prepareCollection() {
        $collection = Mage::getModel('inventory/warehouse')->getCollection()
            ->joinAdminUser()
            ;
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare columns for this grid
     *
     * @return Magestore_Inventory_Block_Adminhtml_Inventory_Grid
     */
    protected function _prepareColumns() {
        $this->addColumn('warehouse_id', array(
            'header' => Mage::helper('inventory')->__('ID'),
            'align' => 'right',
            'type'  => 'number',
            'width' => '50px',
            'index' => 'warehouse_id',
            'filter_index' => 'warehouse_id'
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('inventory')->__('Warehouse Name'),
            'align' => 'left',
            'index' => 'name',
        ));
        
        $this->addColumn('created_by', array(
            'header' => Mage::helper('inventory')->__('Created By'),
            'align' => 'left',
            'index' => 'created_by',
//            'filter_index' => 'admin_user.username'
        ));
        
        $this->addColumn('manager_email', array(
            'header' => Mage::helper('inventory')->__('Manager\'s Email'),
            'align' => 'left',
            'index' => 'manager_email',
        ));

        $this->addColumn('telephone', array(
            'header' => Mage::helper('inventory')->__('Telephone'),
            'align' => 'left',
            'index' => 'telephone',
        ));

        $this->addColumn('street', array(
            'header' => Mage::helper('inventory')->__('Street'),
            'align' => 'left',
            'index' => 'street',
        ));

        $this->addColumn('city', array(
            'header' => Mage::helper('inventory')->__('City'),
            'align' => 'left',
            'index' => 'city',
        ));

        $this->addColumn('country_id', array(
            'header' => Mage::helper('inventory')->__('Country'),
            'align' => 'left',
            'index' => 'country_id',
            'type' => 'country',
//            'options' => Mage::helper('inventory')->getCountryList()
        ));
        $store = Mage::app()->getStore((int) $this->getRequest()->getParam('store', 0));

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
            'header' => Mage::helper('sales')->__('Action'),
            'width' => '80px',
            'filter' => false,
            'align' => 'left',
            'sortable' => false,
            'index'     => 'stores',
            'is_system' => true,
            'renderer' => 'inventory/adminhtml_warehouse_renderer_action'
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('inventory')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('inventory')->__('XML'));

        return parent::_prepareColumns();
    }
    
    protected function _filterTotalProductsCallback($collection, $column) {
        $filter = $column->getFilter()->getValue();
        if (isset($filter['from']) && $filter['from']) {
            $collection->getSelect()->having('SUM(warehouse_product.qty) >= ?', $filter['from']);
        }
        if (isset($filter['to']) && $filter['to']) {
            $collection->getSelect()->having('SUM(warehouse_product.qty) <= ?', $filter['to']);
        }
    }

    /**
     * prepare mass action for this grid
     *
     * @return Magestore_Inventory_Block_Adminhtml_Inventory_Grid
     */
    protected function _prepareMassaction() {
        $this->setMassactionIdField('warehouse_id');
        $this->getMassactionBlock()->setFormFieldName('warehouse');

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

    public function getGridUrl() {
        return $this->getUrl('*/*/grid');
    }

}