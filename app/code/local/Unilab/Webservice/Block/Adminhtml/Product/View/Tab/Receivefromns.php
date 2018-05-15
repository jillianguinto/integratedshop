 <?php
class Unilab_Webservice_Block_Adminhtml_Product_View_Tab_Receivefromns
    extends Mage_Adminhtml_Block_Template
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
 
    protected function _construct()

    {

        parent::_construct();

        
        $this->setTemplate('webservice/product/view/tab/receivefromns.phtml');

        $this->setId('webservice_receivens');

        $this->setUseAjax(true);

    }

    

   public function getTabLabel() {

        return $this->__('Receiving Order History');

    }



    public function getTabTitle() {

        return $this->__('Receiving Order History');

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