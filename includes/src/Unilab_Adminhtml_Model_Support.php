<?php
/**
 * Unilab
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Unilab EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.unilab.com/LICENSE-1.0.html
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@unilab.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.unilab.com/ for more information
 * or send an email to sales@unilab.com
 *
 * @category   Unilab
 * @package    Unilab_Adminhtml
 * @copyright  Copyright (c) 2009 Unilab (http://www.unilab.com/)
 * @license    http://www.unilab.com/LICENSE-1.0.html
 */

/**
 * Unilab Adminhtml extension
 *
 * @category   Unilab
 * @package    Unilab_Adminhtml
 * @author     Unilab Dev Team <dev@unilab.com>
 */

class Unilab_Adminhtml_Model_Support extends Varien_Object
{
    const XML_PATH_SUPPORT_EMAIL = 'unilab/support/email';
    const XML_PATH_SUPPORT_NAME = 'unilab/support/name';
    const XML_PATH_SUPPORT_EMAIL_TEMPLATE = 'unilab/support/template';

    public function send()
    {
        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);

        $errors = array();

        $this->_emailModel = Mage::getModel('core/email_template');
        $subject = $this->getSubject();
        $message = $this->getMessage();
        $reason  = $this->getReason();
        $version = Mage::getVersion();
        $url     = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
        $sender  = array(
            'name' => strip_tags($this->getName()),
            'email' => strip_tags($this->getEmail())
        );

        $this->_emailModel->setDesignConfig(array('area'=>'admin'))
            ->sendTransactional(
                Mage::getStoreConfig(self::XML_PATH_SUPPORT_EMAIL_TEMPLATE),
                $sender,
                base64_decode(Mage::getStoreConfig(self::XML_PATH_SUPPORT_EMAIL)),
                Mage::getStoreConfig(self::XML_PATH_SUPPORT_NAME),
                array(
                    'reason'        => $reason,
                    'subject'		=> $subject,
                    'message'		=> $message,
                    'version'       => $version,
                    'url'           => $url,
                )
        );
        $translate->setTranslateInline(true);

        return $this;
    }
}