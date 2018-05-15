 <?php
class Unilab_Webservice_Block_Adminhtml_Order_View_Tab_Sendtons
    extends Mage_Adminhtml_Block_Template
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
 
    protected function _construct()

    {

        parent::_construct();

        
        $this->setTemplate('webservice/order/view/tab/senttons.phtml');

        $this->setId('webservice_sendtons');

        $this->setUseAjax(true);

    }

    

   public function getTabLabel() {

        return $this->__('Sending Order History');

    }



    public function getTabTitle() {

        return $this->__('Sending Order History');

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