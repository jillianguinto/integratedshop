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

class AW_Afptc_Model_Rule extends Mage_Rule_Model_Rule
{
    const BY_PERCENT_ACTION  = 1;
    const BUY_X_GET_Y_ACTION = 2;

    public function _construct()
    {
        parent::_construct();
        $this->_init('awafptc/rule');
    }

    public function getConditionsInstance()
    {
        return Mage::getModel('awafptc/rule_condition_combine');
    }

    public function loadPost(array $rule)
    {
        $arr = $this->_convertFlatToRecursive($rule);
        if (isset($arr['conditions'])) {
            $this->getConditions()->setConditions(array())->loadArray($arr['conditions'][1]);
        }
        return $this;
    }

    public function validate(Varien_Object $object)
    {
        $_result = false;
		
        if ($object instanceof Mage_Checkout_Model_Cart) {
            $this->_prepareValidate($object);

            //check if free product by rule already in cart
            $this->setBuyXProductIds(array());
            foreach ($object->getQuote()->getAllItems() as $item) {
                $ruleIdOption = $item->getOptionByCode('aw_afptc_rule');

                //Buy X get one free
                if ($this->getSimpleAction() == self::BUY_X_GET_Y_ACTION
                    && $item->getQty() >= $this->getDiscountStep()
                    && null === $ruleIdOption
                ) {
                    $_products = $this->getBuyXProductIds();
                    $_products[$item->getId()] = $item->getProductId();
                    $this->setBuyXProductIds($_products);
                }

                //check already applied rules and remove item if shopping cart not valid for rule
                if (null !== $ruleIdOption && $ruleIdOption->getValue() == $this->getId()) {
                    if ($this->getSimpleAction() == self::BUY_X_GET_Y_ACTION) {

                        $relatedItemIdOption = $item->getOptionByCode('aw_afptc_related_item_id');

                        $_relatedItemId = null;
                        if (null !== $relatedItemIdOption) {
                            $_relatedItemId = $relatedItemIdOption->getValue();

                            $_products = $this->getBuyXProductIds();
                            unset($_products[$_relatedItemId]);
                            $this->setBuyXProductIds($_products);
                        }

                        $itemModel = null;
                        foreach ($object->getQuote()->getItemsCollection() as $_item) {
                            if ($_item->getId() == $_relatedItemId) {
                                $itemModel = $_item;
                                break;
                            }
                        }

                        if (null === $itemModel
                            || null === $itemModel->getId()
                            || $itemModel->getQty() < $this->getDiscountStep()
                            || $itemModel->isDeleted()
                        ) {
                            $needRemoveFlag = true;
                        }
                    }
                    if (!$this->getConditions()->validate($object) || isset($needRemoveFlag)) {
                        $object->removeItem($item->getId());
                    } else {
                        $this->setAlreadyApplied(true);
                    }
                    if ($this->getSimpleAction() == self::BUY_X_GET_Y_ACTION && $this->getBuyXProductIds() != 0) {
                        $this->setAlreadyApplied(false);
                    }
                }
            }
			
            $_result = $this->getConditions()->validate($object);
        }
        return $_result;
    }

    protected function _prepareValidate(Mage_Checkout_Model_Cart $cart)
    {
        $cart->setData('all_items', $cart->getQuote()->getAllItems());

        if ($cart->getQuote()->isVirtual()) {
            $address = $cart->getQuote()->getBillingAddress();
        } else {
            $address = $cart->getQuote()->getShippingAddress();
        }

        foreach ($cart->getQuote()->getAllItems() as $item) {
            $ruleIdOption = $item->getOptionByCode('aw_afptc_rule');

            if (null === $ruleIdOption) {
                continue;
            }

            $address->setTotalQty($cart->getItemsQty() - $item->getQty());
            $address->setBaseSubtotal($address->getBaseSubtotal() - $item->getBaseRowTotal());
            $address->setWeight($address->getWeight() - $item->getWeight());
        }

        if ((AW_All_Helper_Versions::getPlatform() == AW_All_Helper_Versions::CE_PLATFORM
                && version_compare(Mage::getVersion(), '1.7', '>='))
            || (AW_All_Helper_Versions::getPlatform() == AW_All_Helper_Versions::EE_PLATFORM
                && version_compare(Mage::getVersion(), '1.14', '>='))
        ) {
            $quote = $cart->getQuote();
            foreach ($quote->getAllItems() as $item) {
                $itemProduct = $item->getProduct();
                $product = Mage::getModel('catalog/product')->load($itemProduct->getId());
                foreach ($product->getData() as $key => $value) {
                    if (null === $itemProduct->getData($key)) {
                        $itemProduct->setData($key, $value);
                    }
                }
            }
        }
        return $this;
    }

    public function getDiscount()
    {
        $_discount = $this->getData('discount');
        if ($this->getSimpleAction() == self::BUY_X_GET_Y_ACTION) {
            $_discount = 100;
        }
        return $_discount;
    }

    public function apply(Mage_Checkout_Model_Cart $cart, $relatedItemId = null)
    {
        if (!$this->validate($cart) || true === $this->getAlreadyApplied()) {
            return $this;
        }
		
		
		//***** Get Cart Current Quantity ***//
		
			$cartItem = 1;		
			foreach ($cart->getQuote()->getAllItems() as $_cartItem) {
				$cartItem = $_cartItem->getQty();			
			}
		
		//***** Get Cart Current Quantity - End***//
		
		
        $itemModel = Mage::getModel('sales/quote_item');
        if ($this->getSimpleAction() == self::BUY_X_GET_Y_ACTION && count($this->getBuyXProductIds()) != 0) {

            if (null === $relatedItemId) {
                foreach ($this->getBuyXProductIds() as $itemId => $productId) {
                    $this->apply($cart, $itemId);
                }
                return $this;
            }

            if (array_key_exists($relatedItemId, $this->getBuyXProductIds())) {
                $itemModel = null;
                foreach ($cart->getQuote()->getItemsCollection() as $_item) {
                    if ($_item->getId() == $relatedItemId) {
                        $itemModel = $_item;
                        break;
                    }
                }
                if (null === $itemModel || null === $itemModel->getId()) {
                    return $this;
                }
                $itemModel = clone $itemModel;
                $_product = $itemModel->getProduct();
                $_product->addCustomOption('aw_afptc_related_item_id', $relatedItemId);
            }
        }

        if ($cartItem >= $this->getdiscount_step() && $this->getSimpleAction() == AW_Afptc_Model_Rule::BY_PERCENT_ACTION && true !== $this->getAlreadyApplied()) {
            $_product = Mage::getModel('catalog/product')->load($this->getProductId());

        }

        if (!isset($_product)) {
            return $this;
        }
        
        if($_product->getTypeId() == 'downloadable') {
            if (!$_product->getTypeInstance(true)->getProduct($_product)->getLinksPurchasedSeparately()) {
                $links = $_product->getTypeInstance(true)->getLinks($_product);
                foreach($links as $link) {
                    $preparedLinks[] = $link->getId();
                }
                $_product->addCustomOption('downloadable_link_ids', implode(',', $preparedLinks));
            }
        }

        if (!isset($_product) || null === $_product->getId() || !$_product->isSaleable()) {
            return $this;
        }

        $_product->addCustomOption('aw_afptc_discount', min(100, $this->getDiscount()));
        $_product->addCustomOption('aw_afptc_rule', $this->getId());

		
		//***** check if auto increment is enabled ***//
		
			$finalY	= $this->gety_qty();

			if($this->getauto_increment() == true):
			
				$countYInc 	= 0;
				$QtyCart	= $cartItem;
				
				while($QtyCart >= $this->getdiscount_step()){
				
					$QtyCart = $QtyCart - $this->getdiscount_step();
					$countYInc++;
				
				}
				
				if ($countYInc >=1):
					$finalY = $this->gety_qty() * $countYInc;
				endif;
				
				
			endif;
			
		//***** check if auto increment is enabled - End ***//	

		
        $itemModel
            ->setQuote($cart->getQuote())
            ->setStoreId(Mage::app()->getStore()->getId())
            ->setOptions($_product->getCustomOptions())
            ->setProduct($_product)
            ->setQty($finalY);

        $cart->getQuote()->addItem($itemModel);
        $cart->save();
        return $this;
    }
}