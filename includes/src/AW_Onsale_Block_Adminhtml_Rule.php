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
 * This package designed for Magento community edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento community edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Onsale
 * @version    2.4.0
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Onsale_Block_Adminhtml_Rule extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_addButton('apply_rules', array(
            'label' => $this->__('Apply Rules'),
            'onclick' => "location.href='" . $this->getUrl('*/*/applyRules') . "'",
            'class' => '',
        ));
        $this->_blockGroup = 'onsale';
        $this->_controller = 'adminhtml_rule';
        $this->_headerText = $this->__('On Sale Label Rules');
        $this->_addButtonLabel = $this->__('Add New Rule');
        parent::__construct();
    }

}
