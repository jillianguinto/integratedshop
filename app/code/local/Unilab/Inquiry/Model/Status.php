<?php

class Unilab_Inquiry_Model_Status extends Varien_Object
{
    const STATUS_ENABLED	= 1;
    const STATUS_DISABLED	= 0;
	
	const CUSTOMER_GUEST    = 0;
	const CUSTOMER_LOGGED   = 1;

    static public function getOptionArray()
    {
        return array(
            self::STATUS_ENABLED    => Mage::helper('inquiry')->__('Read'),
            self::STATUS_DISABLED   => Mage::helper('inquiry')->__('Pending')
        );
    }
	 

    static public function getCustomerTypes()
    {
        return array(
            self::CUSTOMER_GUEST    => Mage::helper('inquiry')->__('Guest'),
            self::CUSTOMER_LOGGED   => Mage::helper('inquiry')->__('Customer')
        );
    }
}
