<?php

class Magestore_Inventory_Block_Adminhtml_Stocktransfering_Edit_Tabs extends
Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('stocktransfering_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('inventory')->__('Stock Transfer Information'));
    }

    public function _beforeToHtml() {
            $id = $this->getRequest()->getParam('id');
            $type = $this->getRequest()->getParam('type');
            $source = $this->getRequest()->getParam('source');
            $target = $this->getRequest()->getParam('target');
            $unwarehouseId = Mage::getModel('inventory/warehouse')->getCollection()
                        ->addFieldToFilter('is_unwarehouse',1)
                        ->getFirstItem()
                        ->getWarehouseId();
            $this->addTab('form_section', array(
                'label' => Mage::helper('inventory')->__('General Information'),
                'title' => Mage::helper('inventory')->__('General Information'),
                'content' => $this->getLayout()
                    ->createBlock('inventory/adminhtml_stocktransfering_edit_tab_form')
                    ->toHtml(),
            ));
            $active = false;
            if($type == 1){
                if($target){
                    if($source != $unwarehouseId){
                        $active = false;
                    }else{
                        $active = true;
                    }
                }
            }
            if($type == 2 && $source && $target){
                $active = true;
            }
            if($id || ($type && $source && $target)){
                $this->addTab('product_section', array(
                    'label' => Mage::helper('inventory')->__('Products'),
                    'title' => Mage::helper('inventory')->__('Products'),
                    'active'    => $active,
                    'url' => $this->getUrl('*/*/products', array('_current' => true, 'id' => $this->getRequest()->getParam('id'))),
                    'class' => 'ajax',
                ));
            }
            if($id)
                $this->addTab('history_section', array(
                    'label' => Mage::helper('inventory')->__('Change History'),
                    'title' => Mage::helper('inventory')->__('Change History'),
                    'content' => $this->getLayout()
                                      ->createBlock('inventory/adminhtml_stocktransfering_edit_tab_history')
                                      ->toHtml(),
                ));
        return parent::_beforeToHtml();
    }

}

?>
