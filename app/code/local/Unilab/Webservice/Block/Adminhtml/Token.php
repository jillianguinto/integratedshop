<?php

class Unilab_Webservice_Block_Adminhtml_Token extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {

        $this->_controller = 'adminhtml_token';

        $this->_blockGroup = 'webservice';

        $this->_headerText = Mage::helper('webservice')->__('Manage Token');

        $this->_addButtonLabel = Mage::helper('webservice')->__('Add Token');

        parent::__construct();

    }


}