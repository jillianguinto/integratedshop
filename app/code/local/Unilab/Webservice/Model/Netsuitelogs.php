<?php
 
class Unilab_Webservice_Model_Netsuitelogs extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('webservice/netsuitelogs');
    }
}