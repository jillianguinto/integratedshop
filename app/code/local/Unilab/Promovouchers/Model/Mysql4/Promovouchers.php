<?php

 

class Unilab_Promovouchers_Model_Mysql4_Promovouchers extends Mage_Core_Model_Mysql4_Abstract

{

    public function _construct()

    {   

        $this->_init('promovouchers/promovouchers', 'voucher_id');

    }

}