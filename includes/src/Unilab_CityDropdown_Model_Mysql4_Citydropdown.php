<?php

class Unilab_CityDropdown_Model_Mysql4_Citydropdown extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('citydropdown/citydropdown', 'city_id');
    }
}
