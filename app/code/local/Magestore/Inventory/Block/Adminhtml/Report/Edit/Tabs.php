<?php
/**
 * Magestore
 * 
 * Online Magento Course
 * 
 */

/**
 * Lesson06 Edit Tabs Block
 * 
 * @category    Magestore
 * @package     Magestore_Lesson06
 * @author      Magestore Developer
 */
class Magestore_Inventory_Block_Adminhtml_Report_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('notice_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('inventory')->__('Backorder Request Information'));
    }
    
    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => Mage::helper('inventory')->__('Backorder Request Information'),
            'title'     => Mage::helper('inventory')->__('Backorder Request Information'),
            'content'   => $this->getLayout()
                                ->createBlock('inventory/adminhtml_report_edit_tab_form')
                                ->toHtml(),
        ));
        return parent::_beforeToHtml();
    }
}