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
 * Supplier Adminhtml Block
 * 
 * @category    Magestore
 * @package     Magestore_Inventory
 * @author      Magestore Developer
 */
class Magestore_Inventory_Block_Adminhtml_Supplyneeds extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_supplyneeds';
        $this->_blockGroup = 'inventory';
        $this->_headerText = Mage::helper('inventory')->__('Supply Needs Manager');
        $this->_addButtonLabel = Mage::helper('inventory')->__('Create New Purchase Order ');
        parent::__construct();
        $this->_addButton('stock_issuing',array(
            'label'=>'Create New Purchase Order',
            'onclick'    => 'createPurchaseOrder()',
            'class'        => 'add',
        ), 0);
        $this->_addButton('fill_min',array(
            'label'=>'Min All',
            'onclick'    => 'fillAllMin()',
            'class'        => 'save',
        ), 0);
        $this->_addButton('fill_max',array(
            'label'=>'Max All',
            'onclick'    => 'fillAllMax()',
            'class'        => 'save',
        ), 0);
        $this->setTemplate('inventory/supplyneeds/content-header.phtml');
        $this->_removeButton('add');
    }
}