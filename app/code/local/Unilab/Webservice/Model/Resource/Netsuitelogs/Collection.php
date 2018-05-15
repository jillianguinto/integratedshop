<?php
class Unilab_Webservice_Model_Resource_Netsuitelogs_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('webservice/netsuitelogs');     
    }
}