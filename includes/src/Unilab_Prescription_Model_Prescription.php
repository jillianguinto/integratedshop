<?php

class Unilab_Prescription_Model_Prescription extends Mage_Core_Model_Abstract
{
	const TYPE_NEW			= 'NEW';
	const TYPE_PHOTO		= 'PHOTO';
	const TYPE_EXISTING		= 'EXISTING';
	const TYPE_NONE			= 'NONE';
	
	const STATUS_PENDING    = 'PENDING_APPROVAL';
	const STATUS_VALID      = 'VALID';
	const STATUS_INVALID    = 'INVALID';
	
	const DEFAULT_RX_IMG_SIZE = 2;
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('prescription/prescription');
    } 
}