<?php

class Unilab_Webservice_Block_Adminhtml_Connectionlogs extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_connectionlogs';
        $this->_blockGroup = 'webservice';

        $this->_headerText = Mage::helper('webservice')->__('Webservice Connection Logs');

        parent::__construct();

		$this->_removeButton('add');
    }


}