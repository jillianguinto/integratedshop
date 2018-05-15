<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Inventorydropship
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * create inventorydropship table
 */
$installer->run("   
    ALTER TABLE {$this->getTable('erp_inventory_dropship')} 
        ADD `increment_id` varchar(255) default '';
");

$installer->endSetup();

$dropships = Mage::getModel('inventorydropship/inventorydropship')
                        ->getCollection();
if(count($dropships)){
    foreach($dropships as $dropship){        
        $orderId = $dropship->getOrderId();
        $order = Mage::getModel('sales/order')->load($orderId);
        try{
            $dropship->setIncrementId($order->getIncrementId())->save();
        }catch(Exception $e){
            
        }
    }
}

 