<?php
 
class RRA_Oneshop_Model_Resource_Oneshopslider extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('oneshop/oneshopslider', 'id');
    }
}