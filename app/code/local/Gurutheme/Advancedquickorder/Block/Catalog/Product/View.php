<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Product View block
 *
 * @category Mage
 * @package  Mage_Catalog
 * @module   Catalog
 * @author   Magento Core Team <core@magentocommerce.com>
 */
class Gurutheme_Advancedquickorder_Block_Catalog_Product_View extends Mage_Catalog_Block_Product_View
{
    //advanced quick order product row id
    protected  $_rowId;
    
    public function setRowId($_rowId)
    {
        $this->_rowId = $_rowId;
        return $this;
    }
    
    public function getRowId()
    {
        if(!isset($this->_rowId)) $this->_rowId = 0;
        return $this->_rowId;
    }
    
    /**
     * Get JSON encoded configuration array which can be used for JS dynamic
     * price calculation depending on product options
     *
     * @return string
     */
    public function getJsonConfig()
    {
        $config = array();
        if (!$this->hasOptions()) {
            return Mage::helper('core')->jsonEncode($config);
        }

        $_request = Mage::getSingleton('tax/calculation')->getRateRequest(false, false, false);
        /* @var $product Mage_Catalog_Model_Product */
        $product = $this->getProduct();
        $_request->setProductClassId($product->getTaxClassId());
        $defaultTax = Mage::getSingleton('tax/calculation')->getRate($_request);

        $_request = Mage::getSingleton('tax/calculation')->getRateRequest();
        $_request->setProductClassId($product->getTaxClassId());
        $currentTax = Mage::getSingleton('tax/calculation')->getRate($_request);

        $_regularPrice = $product->getPrice();
        $_finalPrice = $product->getFinalPrice();
        $_priceInclTax = Mage::helper('tax')->getPrice($product, $_finalPrice, true);
        $_priceExclTax = Mage::helper('tax')->getPrice($product, $_finalPrice);
        $_tierPrices = array();
        $_tierPricesInclTax = array();
        foreach ($product->getTierPrice() as $tierPrice) {
            $_tierPrices[] = Mage::helper('core')->currency($tierPrice['website_price'], false, false);
            $_tierPricesInclTax[] = Mage::helper('core')->currency(
                Mage::helper('tax')->getPrice($product, (int)$tierPrice['website_price'], true),
                false, false);
        }
        $config = array(
            'productId'           => $product->getId(),
            'priceFormat'         => Mage::app()->getLocale()->getJsPriceFormat(),
            'includeTax'          => Mage::helper('tax')->priceIncludesTax() ? 'true' : 'false',
            'showIncludeTax'      => Mage::helper('tax')->displayPriceIncludingTax(),
            'showBothPrices'      => Mage::helper('tax')->displayBothPrices(),
            'productPrice'        => Mage::helper('core')->currency($_finalPrice, false, false),
            'productOldPrice'     => Mage::helper('core')->currency($_regularPrice, false, false),
            'priceInclTax'        => Mage::helper('core')->currency($_priceInclTax, false, false),
            'priceExclTax'        => Mage::helper('core')->currency($_priceExclTax, false, false),
            /**
             * @var skipCalculate
             * @deprecated after 1.5.1.0
             */
            'skipCalculate'       => ($_priceExclTax != $_priceInclTax ? 0 : 1),
            'defaultTax'          => $defaultTax,
            'currentTax'          => $currentTax,
            'idSuffix'            => '_clone',
            'oldPlusDisposition'  => 0,
            'plusDisposition'     => 0,
            'plusDispositionTax'  => 0,
            'oldMinusDisposition' => 0,
            'minusDisposition'    => 0,
            'tierPrices'          => $_tierPrices,
            'tierPricesInclTax'   => $_tierPricesInclTax,
            'rowId'               => $this->getRowId(),
        );

        $responseObject = new Varien_Object();
        Mage::dispatchEvent('catalog_product_view_config', array('response_object'=>$responseObject));
        if (is_array($responseObject->getAdditionalOptions())) {
            foreach ($responseObject->getAdditionalOptions() as $option=>$value) {
                $config[$option] = $value;
            }
        }

        return Mage::helper('core')->jsonEncode($config);
    }
}