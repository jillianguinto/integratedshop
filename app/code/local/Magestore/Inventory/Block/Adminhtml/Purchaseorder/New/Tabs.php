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
 * Inventory Supplier Edit Tabs Block
 * 
 * @category    Magestore
 * @package     Magestore_Inventory
 * @author      Magestore Developer
 */
class Magestore_Inventory_Block_Adminhtml_Purchaseorder_New_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('purchaseorder_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('inventory')->__('Select Supplier'));
    }
    
    /**
     * prepare before render block to html
     *
     * @return Magestore_Inventory_Block_Adminhtml_Inventory_Edit_Tabs
     */
    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => Mage::helper('inventory')->__('Select Supplier'),
            'title'     => Mage::helper('inventory')->__('Select Supplier'),
            'content'   => $this->getLayout()
                                ->createBlock('inventory/adminhtml_purchaseorder_new_tab_form')
                                ->toHtml(),
        ));
		
        
        return parent::_beforeToHtml();
    }
}