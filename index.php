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
 * @category   Mage
 * @package    Mage
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

if (version_compare(phpversion(), '5.2.0', '<')===true) {
    echo  '<div style="font:12px/1.35em arial, helvetica, sans-serif;">
<div style="margin:0 0 25px 0; border-bottom:1px solid #ccc;">
<h3 style="margin:0; font-size:1.7em; font-weight:normal; text-transform:none; text-align:left; color:#2f2f2f;">
Whoops, it looks like you have an invalid PHP version.</h3></div><p>Magento supports PHP 5.2.0 or newer.
<a href="http://www.magentocommerce.com/install" target="">Find out</a> how to install</a>
 Magento using PHP-CGI as a work-around.</p></div>';
    exit;
}

/**
 * Error reporting
 */
error_reporting(E_ALL);

/**
 * Compilation includes configuration file
 */
ini_set('memory_limit','2048M');

define('MAGENTO_ROOT', getcwd());


$compilerConfig = MAGENTO_ROOT . '/includes/config.php';
if (file_exists($compilerConfig)) {
    include $compilerConfig;
}

$ip = $_SERVER['REMOTE_ADDR'];
$allowed = array('124.106.58.50'); 

$mageFilename = MAGENTO_ROOT . '/app/Mage.php';
$maintenanceFile = 'maintenance.flag';

if (!file_exists($mageFilename)) {
    if (is_dir('downloader')) {
        header("Location: downloader");
    } else {
        echo $mageFilename." was not found";
    }
    exit;
}

//if (file_exists($maintenanceFile)) {
if (file_exists($maintenanceFile) && !in_array($ip, $allowed)) {
    include_once dirname(__FILE__) . '/errors/503.php';
    exit;
}

require_once $mageFilename;

#Varien_Profiler::enable();

if (isset($_SERVER['MAGE_IS_DEVELOPER_MODE'])) {
    Mage::setIsDeveloperMode(true);
}

#ini_set('display_errors', 1);

umask(0); 


/* Store or website code */
$mageRunCode = isset($_SERVER['MAGE_RUN_CODE']) ? $_SERVER['MAGE_RUN_CODE'] : '';

/* Run store or run website */
$mageRunType = isset($_SERVER['MAGE_RUN_TYPE']) ? $_SERVER['MAGE_RUN_TYPE'] : 'store';

//echo $_SERVER["HTTP_HOST"];
// $storeCode    = Mage::app()->getStore()->getCode();




// if($storeCode != "default" && $_SERVER['REQUEST_URI'] == "/"){

   
   //header("Location: http://onlinestore.ecomqa.com/index.php/");
   //header("Location: http://clickhealth.com.ph/default/?SID=7fdc1c6028684ab034b741c247ecf27a");
   //exit;
   
// }else{

//     echo 2;
// } 

// $store_id = 30;
// $mageRunCode = 'unilabactivehealth';

// $store_id = Mage::getSingleton('core/session')->getStoreid();
// $mageRunCode = Mage::getSingleton('core/session')->getMageRunCode();
// $mageRunType = 'store';


// Mage::app()->setCurrentStore(30);

// Mage::run($mageRunCode, $mageRunType);


// Mage::getSingleton('core/session')->getStoreid();

// $mageRunCode = 'celeteque_en';
// $mageRunType = 'store';
// Mage::app()->setCurrentStore(10);

Mage::run($mageRunCode, $mageRunType, array());
