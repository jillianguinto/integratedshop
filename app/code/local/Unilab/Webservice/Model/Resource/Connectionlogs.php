<?php
 
class Unilab_Webservice_Model_Resource_Connectionlogs extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('webservice/connectionlogs', 'id');
    }
}