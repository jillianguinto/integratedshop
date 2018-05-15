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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml system config array field renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Unilab_Adminhtml_Block_System_Config_Inquiry_Form_Field_Departments extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    public function __construct()
    {
        $this->addColumn('code', array(
            'label' => Mage::helper('inquiry')->__('Code'),
            'style' => 'width:50px',
        ));
		
		$this->addColumn('name', array(
            'label' => Mage::helper('inquiry')->__('Department'),
            'style' => 'width:70px',
        ));
		
        $this->addColumn('email', array(
            'label' => Mage::helper('inquiry')->__('Email'),
            'style' => 'width:80px',
        ));
		
		$this->addColumn('subject', array(
            'label' => Mage::helper('inquiry')->__('Subject'),
            'style' => 'width:90px',
        ));
		
		$renderer = new Unilab_Adminhtml_Block_System_Config_Inquiry_Form_Renderer_Template();
		
		$this->addColumn('template', array(
            'label' => Mage::helper('inquiry')->__('Template'),
            'style' => 'width:90px',
			'renderer' => $renderer
        ));
		
		$this->addColumn('sortorder', array(
            'label' => Mage::helper('inquiry')->__('Sort Order'),
            'style' => 'width:30px', 
        ));
		
		
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add Department'); 
		$this->setTemplate('system/config/form/field/customarray.phtml');
        parent::__construct();
    }
}
