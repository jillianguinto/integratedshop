<?php
class Unilab_Standardshipping_Model_MyCarrier_Carrier extends Mage_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{
    protected $_code = 'standardshipping';
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        $storeId = Mage::app()->getStore()->getStoreId();
        $carriers = Mage::getStoreConfig('carriers', $storeId);
        $result = Mage::getModel('shipping/rate_result');

        $rate = Mage::getModel('shipping/rate_result_method');
   
		$checkout           = Mage::getSingleton('checkout/session')->getQuote();
		
		//get subtotal
		$totals = Mage::getSingleton('checkout/cart')->getQuote()->getTotals(); 
		$subtotal = $totals["subtotal"]->getValue();
		
		//shipping information
		$shippingAddress    = $checkout->getShippingAddress();
		$regionid           = $shippingAddress->getRegionId();
		$cityname           = $shippingAddress->getCity();
		$connection     = Mage::getSingleton('core/resource')->getConnection('core_read');
		$query          = "SELECT * FROM  `unilab_cities` WHERE region_id = '$regionid' AND name = '$cityname'";
		$rows           = $connection->fetchRow($query);
		$cityid         = $rows['city_id'];
		
		//Determine equivalent Matrix
		$groups = Mage::getStoreConfig('carriers/standardshipping/destinations', 45);
		$groupie = explode(",",$groups);
	
		foreach ($groupie as $key) 
		{
			$groupid = $key;
			$query_          = "SELECT * FROM `unilab_mov_shipping` WHERE `group` = '$groupid'";
			$rows_           = $connection->fetchRow($query_);
			$citylist = $rows_['listofcities'];
			$citie = explode(",",$citylist);

			
			if(in_array($cityid, $citie))
			{
				$minimumorder_value     = Mage::getStoreConfig('carriers/standardshipping/minorderval');
				$carriertitle           = Mage::getStoreConfig('carriers/standardshipping/title');
				$methodname           = Mage::getStoreConfig('carriers/standardshipping/methodname');
				
				$shippingFee     = Mage::getStoreConfig('carriers/standardshipping/shippingfee');  
				
				// if($subtotal >= $minimumorder_value)
				// {
					$rate->setCarrier($this->_code);
					$rate->setCarrierTitle($carriertitle);
					$rate->setMethod($methodname);
					$rate->setMethodTitle($methodname);
					$rate->setPrice($shippingFee);
					$rate->setCost($shippingFee);
					$result->append($rate);
					
				// }else{
					
				// 	$rate->setCarrier($this->_code);
				// 	$rate->setCarrierTitle('Not Serviceable');
				// 	$rate->setMethod('notserviceable');
				// 	$rate->setMethodTitle('Not Serviceable');
				// 	$rate->setPrice('0.0');
				// 	$rate->setCost('0.0');
				// 	$result->append($rate);
				// }
			break;
			}else{

				$rate->setCarrier($this->_code);
				$rate->setCarrierTitle('Not Serviceable');
				$rate->setMethod('notserviceable');
				$rate->setMethodTitle('Not Serviceable');
				$rate->setPrice('0.0');
				$rate->setCost('0.0');
				$result->append($rate);
			}
		}
        
		return $result;
    }

    public function isavailable($result)
    {
            $Datadata   = $result;			
            
			//Shipping Information
            $checkout           = Mage::getSingleton('checkout/session')->getQuote();
            $shippingAddress    = $checkout->getShippingAddress();
            $regionid           = $shippingAddress->getRegionId();
            $cityname           = $result;
            $connection     = Mage::getSingleton('core/resource')->getConnection('core_read');
            $query          = "SELECT * FROM  `unilab_cities` WHERE region_id = '$regionid' AND name = '$cityname'";
            $rows           = $connection->fetchRow($query);
            $cityid         = $rows['city_id'];
            
			//Determine equivalent Matrix
            $groups = Mage::getStoreConfig('carriers/standardshipping/destinations');
            $groupie = explode(",",$groups);

            foreach ($groupie as $key) 			
			{
				# code...
                $groupid	= $key;				
                $query_     = "SELECT * FROM  `unilab_mov_shipping` WHERE id = '$groupid'";
                $rows_      = $connection->fetchRow($query_);
                $citylist 	= $rows_['listofcities'];
                $citie		= explode(",",$citylist);
				
                if(in_array($cityid, $citie)):
                    $response = true;
                    Mage::getSingleton('core/session')->setenablemov(1);
                    break;
                else:
                    $response = false;
                    Mage::getSingleton('core/session')->setenablemov(0);
                endif;
            }

        return $response;
    }
    public function getAllowedMethods()
    {
        return array($this->_code =>$this->getConfigData('name'));
    }

}