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

class AW_Afptc_Block_Adminhtml_Rules_Edit_Tab_Action extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $rule = Mage::registry('awafptc_rule');
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('action_');

        $fieldset = $form->addFieldset('action_fieldset', array(
            'legend'=> $this->__('Action')
        ));

        if(!$rule->getId()) {
            $rule->setDiscount(100);
			$rule->setdiscount_step(1);
			$rule->sety_qty(1);
        }
		

        $_ruleAction = $fieldset->addField('simple_action', 'select', array(
            'label'      => $this->__('Apply'),
            'name'       => 'simple_action',
            'options'    => array(
                AW_Afptc_Model_Rule::BY_PERCENT_ACTION  => $this->__('Percent of product price discount'),
                AW_Afptc_Model_Rule::BUY_X_GET_Y_ACTION => $this->__('Buy X get Y free'),
            ),
        ));

        $_discountAmount = $fieldset->addField('discount', 'text', array(
            'label'    => $this->__('Discount Amount Applied to Product, %'),
            'title'    => $this->__('Discount Amount Applied to Product, %'),
            'required' => true,
            'name'     => 'discount',
            'class'    => 'validate-not-negative-number validate-percents'
        ));
				
		
        $_discountStep = $fieldset->addField('discount_step', 'text', array(
            'name'  => 'discount_step',
            'label' => $this->__('Target Qty (Buy X)'),
            'class' => 'validate-not-negative-number',
			'required' => true
        ));


        $_y_qty = $fieldset->addField('y_qty', 'text', array(
            'label'    => $this->__('Quantity of Free Product (Get Y)'),
            'title'    => $this->__('Quantity of Free Product (Get Y)'),
            'required' => true,
            'name'     => 'y_qty',
            'class'    => 'validate-not-negative-number'
        ));
		
		
		$_auto_increment = $fieldset->addField('auto_increment', 'select', array(
            'label'   => $this->__('Auto Increment Free Product'),
            'title'   => $this->__('Auto Increment Free Product'),
            'name'    => 'auto_increment',
            'options' => array(
                '1' => $this->__('Yes'),
                '0' => $this->__('No'),
            ),
			'note'		=> 'Free product will automatically increment. (Get Y) '
        ));
		
		//custom
		// $_y_qty = $fieldset->addField('coupon_code', 'text', array(
            // 'label'    => $this->__('Coupon Code'),
            // 'title'    => $this->__('Coupon Code'),
            // 'required' => false,
            // 'name'     => 'coupon_code',

        // ));

        $fieldset->addField('free_shipping', 'select', array(
            'label'   => $this->__('Free Shipping'),
            'title'   => $this->__('Free Shipping'),
            'name'    => 'free_shipping',
            'options' => array(
                '1' => $this->__('Yes'),
                '0' => $this->__('No'),
            ),
        ));

        $fieldset->addField('stop_rules_processing', 'select', array(
            'label'     => $this->__('Stop Further Rules Processing'),
            'title'     => $this->__('Stop Further Rules Processing'),
            'name'      => 'stop_rules_processing',
            'options'    => array(
                '1' => $this->__('Yes'),
                '0' => $this->__('No'),
            ),
        ));
		
		
        $productsRenderBlock = $this->getLayout()->createBlock('awafptc/adminhtml_rules_edit_renderer_products');
        $form
            ->addFieldset('awafptc_grid_fieldset',
                array(
                   'fieldset_container_id' => 'aw-afptc-grid-products',
                   'class'                 => 'aw-afptc-grid-products',
                   'legend'                => $this->__('Action Product')
                )
            )
            ->addField('awafptc_grid_product', 'select',
                array(
                    'name'     => 'awafptc_grid_product',
                    'formdata' => $rule,
                )
            )
            ->setRenderer($productsRenderBlock)
        ;

        $form->setValues($rule->getData());
        $this->setForm($form);

        // field dependencies
        $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
                ->addFieldMap($_ruleAction->getHtmlId(), $_ruleAction->getName())
//                ->addFieldMap($_discountStep->getHtmlId(), $_discountStep->getName())
                ->addFieldMap($_discountAmount->getHtmlId(), $_discountAmount->getName())
                ->addFieldDependence(
                    $_discountAmount->getName(),
                    $_ruleAction->getName(),
                    AW_Afptc_Model_Rule::BY_PERCENT_ACTION
                )
                ->addFieldDependence(
                    $_discountStep->getName(),
                    $_ruleAction->getName(),
                    AW_Afptc_Model_Rule::BUY_X_GET_Y_ACTION
                )
        );
        return parent::_prepareForm();
    }
}