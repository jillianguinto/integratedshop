<?php
class Magestore_Inventory_Block_Adminhtml_Stockreceiving_View extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        
        $this->_objectId = 'id';
        $this->_blockGroup = 'inventory';
        $this->_controller = 'adminhtml_stockreceiving';
        
        $this->_removeButton('delete');
        $this->_removeButton('reset');
        
        $warehouse_id = $this->getRequest()->getParam('warehouse_id');
        $this->_updateButton('back', 'onclick', 'setLocation(\''.$this->getUrl("inventoryadmin/adminhtml_warehouse/edit",array("id"=>$warehouse_id)).'\')');
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('inventory_content') == null)
                    tinyMCE.execCommand('mceAddControl', false, 'inventory_content');
                else
                    tinyMCE.execCommand('mceRemoveControl', false, 'inventory_content');
            }
            
        ";
    }
    
    /**
     * get text to show in header when edit an item
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('inventory_data')
            && Mage::registry('inventory_data')->getId()
        ) {
            return Mage::helper('inventory')->__("View Stock Receving '%s'",
                                                $this->htmlEscape(Mage::registry('inventory_data')->getTitle())
            );
        }
        return Mage::helper('inventory')->__('View Stock Receving');
    }
}
?>
