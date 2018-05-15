<?php
/**
 * Created by JetBrains PhpStorm.
 * User: diszo.sasil
 * Date: 2/1/13
 * Time: 3:24 PM
 * To change this template use File | Settings | File Templates.
 */

$installer = $this;
$installer->startSetup();
$installer->addAttribute('order', 'dragonpay_refno', array('type'=>Varien_Db_Ddl_Table::TYPE_TEXT, 'comment'=>'Dragonpay RefNo' ));
$installer->endSetup();