<?php
 
class RRA_Oneshop_Model_Resource_Oneshopurl extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('oneshop/oneshopurl', 'id');
    }
}