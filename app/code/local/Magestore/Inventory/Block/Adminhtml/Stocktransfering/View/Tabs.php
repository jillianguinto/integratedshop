<?php
    class Magestore_Inventory_Block_Adminhtml_Stocktransfering_View_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('warehouse_stocktransfering_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('inventory')->__('Stock Transfering Information'));
    }
    
    /**
     * prepare before render block to html
     *
     * @return Magestore_Inventory_Block_Adminhtml_Inventory_Edit_Tabs
     */
    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => Mage::helper('inventory')->__('Stock Transfering Information'),
            'title'     => Mage::helper('inventory')->__('Stock Transfering Information'),
            'content'   => $this->getLayout()
                                ->createBlock('inventory/adminhtml_stocktransfering_view_tab_form')
                                ->toHtml(),
        ));
		
        $this->addTab('products_section',array(
                'label'     => Mage::helper('inventory')->__('Products'),
                'title'     => Mage::helper('inventory')->__('Products'),
                'content'   => $this->getLayout()
                                ->createBlock('inventory/adminhtml_stocktransfering_view_tab_products')
                                ->toHtml(),
        ));
        return parent::_beforeToHtml();
    }
}
?>