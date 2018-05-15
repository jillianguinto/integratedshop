<?php
/**
 * Created by JetBrains PhpStorm.
 * User: diszo.sasil
 * Date: 2/5/13
 * Time: 3:01 PM
 * To change this template use File | Settings | File Templates.
 */

class Unilab_DragonPay_Block_Form extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('dragonpay/payment/form.phtml');
    }
}