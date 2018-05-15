<?php
class Unilab_Webservice_Block_Adminhtml_Order_View_Tab_Sendtoahc
    extends Mage_Adminhtml_Block_Template
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected $_chat = null;
 
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('webservice/sendtoahc.phtml');
    }
 
    public function getTabLabel() {
        return $this->__('API Logs');
    }
 
    public function getTabTitle() {
        return $this->__('API Logs');
    }
 
    public function canShowTab() {
        return true;
    }
 
    public function isHidden() {
        return false;
    }
 
    public function getOrder(){
        return Mage::registry('current_order');
    }
} 
?>