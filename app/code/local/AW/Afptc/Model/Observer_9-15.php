<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Afptc
 * @version    1.1.8
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

class AW_Afptc_Model_Observer extends Varien_Object
{
    public function checkoutCartSaveAfter($observer)
    {
        if (Mage::helper('awafptc')->extensionDisabled()) {
            return $this;
        }

        $cart = $observer->getCart();

        $store = Mage::app()->getStore();

        $rulesCollection = Mage::getResourceModel('awafptc/rule')->getActiveRulesCollection($store);


        foreach ($rulesCollection as $ruleModel) {

            $ruleModel->load($ruleModel->getId());


            if (!$ruleModel->validate($cart)) {
                continue;
            }

            if (isset($_stopFlag)) {
                break;
            }

            if ($ruleModel->getStopRulesProcessing()) {
                $_stopFlag = true;
            }

            if ($ruleModel->getShowPopup()) {
                continue;
            }

            if (
                (Mage::helper('awafptc')->getDeclineRuleCookie($ruleModel->getId())
                || Mage::registry('rule_decline') == $ruleModel->getId())
                && !Mage::registry('ignore_decline')
            ) {
                continue;
            }


            try {


                  // $ruleModel->apply($cart, null);
                $canAdd = true;
                
                foreach ($cart->getAllItems() as $item) {   

                    $product_id = $item->getProduct()->getId(); 


                    if($product_id == $ruleModel->getproduct_id()):

                        $canAdd = false;

                    endif;
                    
                }       



            // //add product
            // if($canAdd == true): 

            foreach ($ruleModel as $_key => $value) {
              
                if($_key == '_origData'):

                    foreach ($value as $key => $_value) {

                        if($key == 'conditions_serialized'):
                                $step_discount = $_value;

                        endif;
                    }

                endif;

            }


                $getsku = unserialize($step_discount);
                
                if(!empty($getsku)):
                
                   foreach($getsku as $key=>$_value):

                        if(!empty($_value)):
                           foreach($_value as $_key=>$value):

                             $sku = $value['conditions'][0]['value'];

                           endforeach;
                           
                        endif;

                   endforeach;
                   
                endif;



                foreach ($cart->getQuote()->getAllItems() as $_cartItem) {
                    //explode
                    foreach (explode(",", $sku) as $sku_value) {
                       if($sku_value ==  $_cartItem->getProduct()->getsku())
                        {
                            $y_qty  = $_cartItem->getQty();
                        }   
                    }
                }



                $finalY  =  $ruleModel->gety_qty();


                if($ruleModel->getauto_increment() == true):  

                     $countYInc  = 0;
                     $QtyCart    = $y_qty;  

                    while($QtyCart >= $ruleModel->getdiscount_step()){    

                        $QtyCart = $QtyCart - $ruleModel->getdiscount_step();

                        $countYInc++;               

                    }              

                   if ($countYInc >=1):

                        $finalY = $ruleModel->gety_qty() * $countYInc;

                    endif;  


                endif; 


                if($canAdd == true):    
                
                    $productId  = $ruleModel->getproduct_id();              
                    $product_id =   $productId ;
                    $product = Mage::getModel('catalog/product')->load($product_id);
                    $cart = Mage::getModel('checkout/cart');
                    $cart->init();              
                    $cart->addProduct($product, array('qty' => $finalY));

                endif;

            
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }

        $cart->getQuote()->unsTotalsCollectedFlag()->collectTotals()->save();

        return $this;
    }

    public function quoteAfterLoad($observer)
    {

        $quote = $observer->getEvent()->getQuote();
        foreach ($quote->getAllItems() as $_item) {
            $ruleIdOption = $_item->getOptionByCode('aw_afptc_rule');
            if (null === $ruleIdOption || !$ruleIdOption->getValue()) {
                continue;
            }
            $ruleModel = Mage::getModel('awafptc/rule')->load($ruleIdOption->getValue());

            $storeId = Mage::app()->getStore()->getId();
            $customerGroup = Mage::helper('awafptc')->getCustomerGroup();
            if (
                null === $ruleModel->getId()
                || (0 != $ruleModel->getStoreIds() && !in_array($storeId, explode(',', $ruleModel->getStoreIds())))
                || !in_array($customerGroup, explode(',', $ruleModel->getCustomerGroups()))
            ) {
                $quote->removeItem($_item->getId());
                $_needRecollectFlag = true;
            }
        }
        if (isset($_needRecollectFlag)) {
            $quote->unsTotalsCollectedFlag()->collectTotals()->save();
        }
        return $this;
    }

    public function checkoutCartProductAddAfter()
    {

        if (Mage::helper('awafptc')->extensionDisabled() || !Mage::helper('awafptc')->isAllowReAddToCart()) {
            return $this;
        }

        Mage::helper('awafptc')->removeDeclineCookies();
        Mage::register('ignore_decline', 1, true);
        return $this;
    }

    public function salesQuoteRemoveAfter($observer)
    {

        if (Mage::helper('awafptc')->extensionDisabled() || Mage::helper('awafptc')->isAllowReAddToCart()) {
            return $this;
        }

        $ruleIdOption = $observer->getQuoteItem()->getOptionByCode('aw_afptc_rule');
        if (null !== $ruleIdOption) {
            Mage::helper('awafptc')->setDeclineRuleCookie($ruleIdOption->getValue());
            Mage::register('rule_decline', $ruleIdOption->getValue(), true);
        }

    }

    public function getFinalPrice($observer)
    {

        if (Mage::helper('awafptc')->extensionDisabled()) {
            return $this;
        }

        $ruleDiscount = $observer->getProduct()->getCustomOption('aw_afptc_discount');
        if (null !== $ruleDiscount) {
            $finalPrice = $observer->getProduct()->getFinalPrice();
            $observer->getProduct()->setFinalPrice(max(0, $finalPrice - ($finalPrice * $ruleDiscount->getValue() / 100)));
            $observer->getProduct()->addCustomOption('option_ids', null);

            if (version_compare(Mage::getVersion(), '1.4.1.1', '<=')
                && $observer->getProduct()->getTypeInstance(true) instanceof Mage_Catalog_Model_Product_Type_Configurable
            ) {
                $attributes = $observer
                    ->getProduct()
                    ->getTypeInstance(true)
                    ->getConfigurableAttributes($observer->getProduct())
                ;
                /* @var $attribute Mage_Catalog_Model_Product_Type_Configurable_Attribute */
                foreach ($attributes as $attribute) {
                    $priceData = $attribute->getPrices();
                    foreach ($priceData as $key => $price) {
                        if (!$price['is_percent']) {
                            $price['pricing_value'] = max(
                                0,
                                $price['pricing_value'] - ($price['pricing_value'] * $ruleDiscount->getValue() / 100)
                            );
                        }
                        $priceData[$key] = $price;
                    }
                    $attribute->setPrices($priceData);
                }
                $observer->getProduct()->setData('_cache_instance_configurable_attributes', $attributes);
            }
        }
        return $this;
    }

    public function getConfigurablePrice(Varien_Event_Observer $observer)
    {

        if (Mage::helper('awafptc')->extensionDisabled()) {
            return $this;
        }

        $product = $observer->getEvent()->getProduct();
        $ruleDiscount = $product->getCustomOption('aw_afptc_discount');
        if (null === $ruleDiscount) {
            return $this;
        }

        $configurablePrice = $observer->getProduct()->getConfigurablePrice();
        $configurablePrice = max(0, $configurablePrice - ($configurablePrice * $ruleDiscount->getValue() / 100));
        $product->setConfigurablePrice($configurablePrice);

        return $this;
    }

    /**
     * added for compatibility with AW_ACP
     *
     * @see AFPTC-5
     *
     * @param $observer
     *
     * @throws Mage_Core_Exception
     */


    public function checkoutCartUpdateItemsAfter($observer)
    {


        $cart = $observer->getCart();

        $store = Mage::app()->getStore();

        $rulesCollection = Mage::getResourceModel('awafptc/rule')->getActiveRulesCollection($store);


        foreach ($rulesCollection as $ruleModel) 
        {


            foreach ($ruleModel as $_key => $value) {
              
                //var_dump($value);


                if($_key == '_origData'):

                    foreach ($value as $key => $_value) {

                        if($key == 'conditions_serialized'):


                                $step_discount = $_value;


                                $getsku = unserialize($step_discount);

                               foreach($getsku as $key=>$_value):

                                   foreach($_value as $_key=>$value):

                                            $sku = $value['conditions'][0]['value'];

                                                foreach ($cart->getQuote()->getAllVisibleItems() as $item) {

                                                    if( $sku == $item->getSku()):

                                                        $productId = $ruleModel->getProductId();
                                                        $this->removeFreeitem($productId);

                                                    endif;

                                                }                                   

                                   endforeach;

                               endforeach;


                        endif;
                    }

                endif;

            }


        }


        // // $cart = $observer->getCart();
        // foreach ($cart->getQuote()->getAllVisibleItems() as $item) {

        //    $ruleIdOption = $item->getOptionByCode('aw_afptc_rule');

        //     if (null !== $ruleIdOption && $item->getQty() > 0) {

        //                                 ///throw new Mage_Core_Exception(
        //                                 //     Mage::helper('awafptc')->__(
        //                                 //         "Unfortunately the quantity of %s can not be changed due to"
        //                                 //         . " current set of products in the cart", $item->getProduct()->getName().' - '. $item->getProduct()->getId()
        //                                 //     )
        //                                 // );


        //         $productId = $item->getItemId();

        //         $this->removeFreeitem($productId);

        //     }

            //echo $productId = $item->getItemId();
            //echo $productId;

      //  }


    }


    function removeFreeitem($productId)
    {

        Mage::log($productId, null, 'observer-afptc.log');
       $cartHelper = Mage::helper('checkout/cart');
        $items = $cartHelper->getCart()->getItems();
        foreach ($items as $item) {
            if ($item->getProduct()->getId() == $productId) {
                $itemId = $item->getItemId();
                $cartHelper->getCart()->removeItem($itemId)->save();
            }
        }    

    }



}

$this->removeFreeitem($productId);

        //     }

            //echo $productId = $item->getItemId();
            //echo $productId;

      //  }


    }


    function removeFreeitem($productId)
    {

        Mage::log($productId, null, 'observer-afptc.log');
       $cartHelper = Mage::helper('checkout/cart');
        $items = $cartHelper->getCart()->getItems();
        foreach ($items as $item) {
            if ($item->getProduct()->getId() == $productId) {
                $itemId = $item->getItemId();
                $cartHelper->getCart()->removeItem($itemId)->save();
            }
        }    

    }



}

