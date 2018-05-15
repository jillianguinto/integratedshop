<?php

class RRA_Oneshop_UnauthorizedController extends Mage_Core_Controller_Front_Action

{

    public function indexAction()

    {



        //Get current layout state

        $this->loadLayout();          

 

        $block = $this->getLayout()->createBlock(

            'Mage_Core_Block_Template',

            'oneshop',

            array('template' => 'webservice/unauthorized.phtml')

        );

        

        $this->getLayout()->getBlock('root')->setTemplate('page/1column.phtml');

        $this->getLayout()->getBlock('content')->append($block);

        $this->_initLayoutMessages('core/session'); 

        $this->renderLayout();



        //$this->extendcreateuser();

    }


}

?>