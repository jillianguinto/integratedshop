<?php
class Unilab_Cashondelivery_Model_System_Config_Source_Shipping_City
{


    public function toOptionArray() {
   
        //$file = file_get_contents('stateprovince.txt', true);      
        
        $regionids = Mage::getStoreConfig("payment/cashondelivery/stateprovince");     
            
        // $regionids = 505;

        try{
            if($regionids != null):
                $results =  $this->_getConnection()->fetchAll("SELECT * FROM  unilab_cities WHERE  region_id IN ($regionids) order by region_id, name");  

                $n = count($results);

                for ($i=0; $i < $n; $i++) { 
                    $options[]= array(
                        'value'=>$results[$i]['city_id'],
                        'label'=>$results[$i]['name']
                        );
                }
            endif;
            
        }catch (Exception $e) {
                    
            Mage::log($e->getMessage(), null, 'youhavefailedthe_city.log');
                    
        }


        return $options;
    }


    public function _getConnection(){

        return Mage::getSingleton('core/resource')->getConnection('core_read'); 
    }


 
}