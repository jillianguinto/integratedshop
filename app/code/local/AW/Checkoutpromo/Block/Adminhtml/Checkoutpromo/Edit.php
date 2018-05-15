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
 * @package    AW_Checkoutpromo
 * @version    1.2.3
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Checkoutpromo_Block_Adminhtml_Checkoutpromo_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        $this->_objectId = 'id';
        $this->_blockGroup = 'checkoutpromo';
        $this->_controller = 'adminhtml_checkoutpromo';

        parent::__construct();

        $this->_updateButton('save', 'label', $this->__('Save Rule'));
        $this->_updateButton('delete', 'label', $this->__('Delete Rule'));
    }

    public function getHeaderText() {
        $rule = Mage::registry('checkoutpromo_rule');
        if ($rule->getRuleId()) {
            return $this->__("Edit Rule '%s'", $this->htmlEscape($rule->getName()));
        } else {
            return $this->__('New Rule');
        }
    }

    public function getProductsJson() {
        return '{}';
    }

}
