 <?php
class Unilab_Webservice_Block_Adminhtml_Customer_Edit_Tab_Sendtons
    extends Mage_Adminhtml_Block_Template
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
 
    protected function _construct()

    {

        parent::_construct();

        

        $this->setTemplate('webservice/customer/edit/tab/senttons.phtml');

        $this->setId('webservice_sendtons');

        $this->setUseAjax(true);

    }

    

   public function getTabLabel() {

        return $this->__('Sending Customer History');

    }



    public function getTabTitle() {

        return $this->__('Sending Customer History');

    }



    public function canShowTab() {

        return true;

    }



    public function isHidden() {

        return false;

    }



    public function getCustomer(){

        return Mage::registry('current_customer');

    }

    



} 

?> 