<?php
class Unilab_Minimumordervalue_Model_System_Config_Source_Shipping_Group
{


    public function toOptionArray() {
        

        $results =  $this->_getConnection()->fetchAll("SELECT * FROM  `unilab_mov_shipping`"); 


        $n = count($results);

        for ($i=0; $i < $n; $i++) { 
            $options[]= array(
                'value'=>$results[$i]['group'],
                'label'=>$results[$i]['group']
                );
        }

        return $options;
    }


    public function _getConnection(){

        return Mage::getSingleton('core/resource')->getConnection('core_read'); 
    }


 
}