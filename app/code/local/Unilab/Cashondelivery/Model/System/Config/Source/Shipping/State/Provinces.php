<?php
class Unilab_Cashondelivery_Model_System_Config_Source_Shipping_State_Provinces
{

    public function toOptionArray() {

        $results = $this->_getConnection()->fetchAll("SELECT * 
            FROM `payment_method_cities`");     

        $n = count($results);

        for ($i=0; $i < $n; $i++) { 
            $options[]= array(
                'value'=>$results[$i]['region_id'],
                'label'=>$results[$i]['category']
                );
        }

        return $options;

    }


    public function _getConnection(){

        return Mage::getSingleton('core/resource')->getConnection('core_read'); 
    }

}