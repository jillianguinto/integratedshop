<?php

class Unilab_Prescription_Model_Mysql4_Prescription_Temp_Rx extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        // Note that the prescription_temp_rx_id refers to the key field in your database table.
        $this->_init('prescription/prescription_temp_rx', 'prescription_temp_rx_id');
    }
    
    public function _beforeSave(Mage_Core_Model_Abstract $object)
    {
		$date = new Zend_Date(now());
		
        if ($object->getCreatedAt() == NULL || $object->getUpdatedAt() == NULL) {
            $object->setCreatedAt($date->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
            $object->setUpdatedAt($date->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
        } else {
			$object->setUpdatedAt($date->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
		}
		
        return parent::_beforeSave($object);
    }
}
