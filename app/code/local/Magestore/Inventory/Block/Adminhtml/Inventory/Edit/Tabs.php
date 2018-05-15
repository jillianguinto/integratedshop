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
 * Inventory Edit Tabs Block
 * 
 * @category    Magestore
 * @package     Magestore_Inventory
 * @author      Magestore Developer
 */
class Magestore_Inventory_Block_Adminhtml_Inventory_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('inventory_tabs');
        $this->setDestElementId('edit_form');
    }
    
    /**
     * prepare before render block to html
     *
     * @return Magestore_Inventory_Block_Adminhtml_Inventory_Edit_Tabs
     */
    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => Mage::helper('inventory')->__('General Information'),
            'title'     => Mage::helper('inventory')->__('General Information'),
            'content'   => $this->getLayout()
                                ->createBlock('inventory/adminhtml_inventory_edit_tab_form')
                                ->toHtml(),
        ));
        
        $this->addTab('purchaseorder_section', array(
            'label'     => Mage::helper('inventory')->__('Purchase Orders'),
            'title'     => Mage::helper('inventory')->__('Purchase Orders'),
            'url'       => $this->getUrl('*/*/purchaseorder',array(
                '_current'	=> true,
                'id'			=> $this->getRequest()->getParam('id'),
                'store'		=> $this->getRequest()->getParam('store')
            )),
            'class'     => 'ajax',
        ));
        
        $this->addTab('returnorder_section', array(
            'label'     => Mage::helper('inventory')->__('Return Orders'),
            'title'     => Mage::helper('inventory')->__('Return Orders'),
             'url'       => $this->getUrl('*/*/returnorder',array(
                '_current'	=> true,
                'id'			=> $this->getRequest()->getParam('id'),
                'store'		=> $this->getRequest()->getParam('store')
            )),
            'class'     => 'ajax',
        ));
        
        return parent::_beforeToHtml();
    }
}