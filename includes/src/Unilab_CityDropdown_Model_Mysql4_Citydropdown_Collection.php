<?php

class Unilab_CityDropdown_Model_Mysql4_Citydropdown_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('citydropdown/citydropdown');
    }

    public function addAttributeToSort($attribute, $dir='asc')
    {
        if (!is_string($attribute)) {
            return $this;
        }
        $this->setOrder($attribute, $dir);
        return $this;
    }
}
