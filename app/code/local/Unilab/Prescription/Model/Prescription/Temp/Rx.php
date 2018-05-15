<?php

class Unilab_Prescription_Model_Prescription_Temp_Rx extends Mage_Core_Model_Abstract
{ 
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('prescription/prescription_temp_rx');
    } 
}