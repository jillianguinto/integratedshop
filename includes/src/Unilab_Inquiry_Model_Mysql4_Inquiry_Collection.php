<?php

class Unilab_Inquiry_Model_Mysql4_Inquiry_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('inquiry/inquiry');
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
