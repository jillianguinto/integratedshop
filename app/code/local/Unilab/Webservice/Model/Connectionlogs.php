<?php
 
class Unilab_Webservice_Model_Connectionlogs extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('webservice/connectionlogs');
    }
}