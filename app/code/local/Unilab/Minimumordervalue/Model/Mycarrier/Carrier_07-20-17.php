<?php
class Unilab_Minimumordervalue_Model_MyCarrier_Carrier extends Mage_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{
    protected $_code = 'minimumordervalue';
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        $storeId = Mage::app()->getStore()->getStoreId();
        $carriers = Mage::getStoreConfig('carriers', $storeId);
        $result = Mage::getModel('shipping/rate_result');
        /* @var $result Mage_Shipping_Model_Rate_Result */
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
            $groups = Mage::getStoreConfig('carriers/minimumordervalue/destinations');
            $groupie = explode(",",$groups);
               foreach ($groupie as $key) {
                    $groupid = $key;
                    $query_          = "SELECT * FROM `unilab_mov_shipping` WHERE `group` = '$groupid'";
                    $rows_           = $connection->fetchRow($query_);
                    $citylist = $rows_['listofcities'];
                    $citie = explode(",",$citylist);
                    if(in_array($cityid, $citie)):
                        $mov_group_id           =  $rows_['id'];
                        $mov_group_name         =  $rows_['group'];
                        $mov_query              = "SELECT * FROM `unilab_mov_shipping` WHERE id = '$mov_group_id'";
                        $fetchdata              = $connection->fetchRow($mov_query);
                        $connection->commit();
                        $greater_equal          = $fetchdata['greaterequal_mov']; 
                        $lessthan_equal         = $fetchdata['lessthan_mov'];
                        $minimumorder_value     = Mage::getStoreConfig('carriers/minimumordervalue/minorderval');
                        $carriertitle           = Mage::getStoreConfig('carriers/minimumordervalue/title');

                        
						
                            if($subtotal >= $minimumorder_value):
                           
                                $shippingFee = $this->getAddedWeight($greater_equal);
								$shippingFee = $this->freeshipping($shippingFee, $regionid);

								 //free shipping title if (0.0)
								if($greater_equal == 0){
                                    $carriertitle = "Free Shipping";
                                }

                                $rate->setCarrier($this->_code);
                                $rate->setCarrierTitle($carriertitle);
                                $rate->setMethod($mov_group_name);
                                $rate->setMethodTitle($mov_group_name);
                                $rate->setPrice($shippingFee);
                                $rate->setCost($shippingFee);
                                $result->append($rate);
                            else:
                                $shippingFee = $this->getAddedWeight($lessthan_equal);
								$shippingFee = $this->freeshipping($shippingFee, $regionid);
								
								 //free shipping title if (0.0)
								if($greater_equal == 0){
                                    $carriertitle = "Free Shipping";
                                }
								
                                $rate->setCarrier($this->_code);
                                $rate->setCarrierTitle($this->getConfigData('title'));
                                $rate->setMethod($mov_group_name);
                                $rate->setMethodTitle($mov_group_name);
                                $rate->setPrice($shippingFee);
                                $rate->setCost($shippingFee);
                                $result->append($rate); 
                            endif;
                        break;
                        else:
                        //     echo "Not";
                                $rate->setCarrier($this->_code);
                                $rate->setCarrierTitle('Not Serviceable');
                                $rate->setMethod('notserviceable');
                                // $rate->setMethodTitle('Not Serviceable');
                                $rate->setPrice('0.0');
                                $rate->setCost('0.0');
                                $result->append($rate);
                    endif;
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
            $groups = Mage::getStoreConfig('carriers/minimumordervalue/destinations');
            $groupie = explode(",",$groups);

            foreach ($groupie as $key) 			{
				# code...
                $groupid = $key;				
                $query_         = "SELECT * FROM  `unilab_mov_shipping` WHERE id = '$groupid'";
                $rows_          = $connection->fetchRow($query_);
                $citylist = $rows_['listofcities'];
                $citie = explode(",",$citylist);
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

    public function getAddedWeight($greater_equal){

        $helper = Mage::helper('checkout/cart');
        $Cartitems = $helper->getCart()->getItems();    
        foreach ($Cartitems as $item){
            $_prodcut = "";
            $itemProductId = $item->getProductId();
            $_product = Mage::getModel('catalog/product')->loadByAttribute('entity_id', $itemProductId); 

            $final_Weight = $_product->getWeight();
            
            $getFinalWeight = $final_Weight / 1000;

            $dimensionval = $getFinalWeight * $item->getQty();

            $dimensiontotal = $dimensiontotal +  $dimensionval;
          
        }

        $minimumWeight = Mage::getStoreConfig('carriers/minimumordervalue/minweight');
		$priceperkilo = Mage::getStoreConfig('carriers/minimumordervalue/priceperkilo');

        $addedWeight = $dimensiontotal - $minimumWeight;
		
				
		//Mage::log($dimensiontotal, null, 'jillian1.log');
		//Mage::log($addedWeight, null, 'jillian1.log');
		
		$excessweight = explode('.', $addedWeight);
		
		$excess = intval($excessweight[0]);
		$decimal = intval($excessweight[1]);
		
		$additionalshippingfee = $priceperkilo * $excess;
		
		if($decimal > 0){
			$additionalshippingfee = $additionalshippingfee + $priceperkilo;
		}
		
		//Mage::log($additionalshippingfee, null, 'jillian1.log');

        if($greater_equal == 0){
            $totalShippingFee = 0;
        }else{

            if($dimensiontotal > $minimumWeight){
            $totalShippingFee = $greater_equal + $additionalshippingfee;
            }else{
                $totalShippingFee = $greater_equal;
            }
        }

        return $totalShippingFee;

      //Mage::log($totalShippingFee, null, 'getAddedWeight.log');  

    }
	
	
	public function freeshipping($shippingFee, $regionid)
	{
		//$skuArr = array ("141001SP,20170501MD, 20170502MD, 20170503MD, 20170504MD");
	
		$skus	= Mage::getStoreConfig('carriers/freeshipping/skufree');
		$skuArr = explode(",",$skus);
		
	
		$cartItems = Mage::getSingleton('checkout/session')->getQuote()->getAllItems();
		
		foreach ($cartItems as $item)
		{
			$product = Mage::getModel('catalog/product')->load($item->getProduct_id());
			
			$sku =  $product->getSku();
			
			if (in_array($sku, $skuArr))
			{
				$validitity_fromdate 	= strtotime(Mage::getStoreConfig('carriers/freeshipping/fromdate'));
				$validitity_todate 		= strtotime(Mage::getStoreConfig('carriers/freeshipping/todate'));
				
				$current_date           = strtotime(date("m/j/Y"));
				
				if($current_date>=$validitity_fromdate and $current_date<=$validitity_todate)
				{
				
					if($regionid == 532 && $shippingFee >= 90)
					{
						$shippingFee = $shippingFee-90;
					}
				}
	
			}
		} 
		
		return $shippingFee; 
		
	}


}