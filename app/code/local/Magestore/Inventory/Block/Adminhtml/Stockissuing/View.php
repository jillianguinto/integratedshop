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
 * Inventory Edit Block
 * 
 * @category     Magestore
 * @package     Magestore_Inventory
 * @author      Magestore Developer
 */
class Magestore_Inventory_Block_Adminhtml_Stockissuing_View extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        
        $this->_objectId = 'id';
        $this->_blockGroup = 'inventory';
        $this->_controller = 'adminhtml_stockissuing';
         $warehouse_id = $this->getRequest()->getParam('warehouse_id');
        $this->_updateButton('back', 'onclick', 'setLocation(\''.$this->getUrl("inventoryadmin/adminhtml_warehouse/edit",array("id"=>$warehouse_id)).'\')');
        $this->_removeButton('delete');
        $this->_removeButton('reset');
    }
    
    /**
     * get text to show in header when edit an item
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('stockissuing_data')
            && Mage::registry('stockissuing_data')->getId()
        ) {
            return Mage::helper('inventory')->__("View Stock Issuing '%s'",
                                                $this->htmlEscape(Mage::helper('inventory')->getIncrementId(Mage::registry('stockissuing_data')))
            );
        }
        return Mage::helper('inventory')->__('Add Item');
    }
}