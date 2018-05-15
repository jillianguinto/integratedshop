<?php
/**
 * Movent
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Movent EULA that is bundled with
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
 * @category   Movent
 * @package    Unilab_Inquiry
 * @copyright  Copyright (c) 2009 Movent (http://www.unilab.com/)
 * @license    http://www.unilab.com/LICENSE-1.0.html
 */

/**
 * Extended Contacts extension
 *
 * @category   Movent
 * @package    Unilab_Inquiry
 * @author     Movent Dev Team <dev@unilab.com>
 */

class Unilab_Inquiry_Block_Form extends Mage_Core_Block_Template
{
    const XML_PATH_FORM_BLOCK = 'unilab_customers/inquiry/form_block';
    const XML_PATH_FORM_ENABLED = 'unilab_customers/inquiry/form_enabled';

    public function getCmsBlockHtml()
    {
        if (!$this->getData('cms_block_html')) {
            $html = $this->getLayout()->createBlock('cms/block')
                ->setBlockId(Mage::getStoreConfig(self::XML_PATH_FORM_BLOCK))
                ->toHtml();
            $this->setData('cms_block_html', $html);
        }
        return $this->getData('cms_block_html');
    }

    public function getDepartmentHtmlSelect($value = '', $class = '')
    {
        $helper = Mage::helper('inquiry');
        if ($options = $helper->getDepartmentOptions()){
            try{
                //$layout = $this->getLayout();
                //print_r($layout);exit;
                $select = $this->getLayout()->createBlock('core/html_select')
                    ->setName('department')
                    ->setId('department')
                    ->setTitle($helper->__('Department'))
                    ->setClass($class)
                    ->setValue($value)
                    ->setOptions($options);
            } catch (Exception $e){ print_r($e); exit;}
            return $select->toHtml();
        }
        return null;
    }

    public function isFormEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_FORM_ENABLED);
    }
}