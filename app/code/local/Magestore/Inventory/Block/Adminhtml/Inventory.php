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
 * Inventory Adminhtml Block
 * 
 * @category    Magestore
 * @package     Magestore_Inventory
 * @author      Magestore Developer
 */
class Magestore_Inventory_Block_Adminhtml_Inventory extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_inventory';
        $this->_blockGroup = 'inventory';
        $this->_headerText = Mage::helper('inventory')->__('Inventory Product Manager');
        $this->_addButtonLabel = Mage::helper('inventory')->__('Update Cost Price');
        parent::__construct();
        $this->updateButton('add','onclick','setLocation(\''.$this->getUrl("inventoryadmin/adminhtml_inventory/update").'\')');
        $this->setTemplate('inventory/inventory/content-header.phtml');
    }
}