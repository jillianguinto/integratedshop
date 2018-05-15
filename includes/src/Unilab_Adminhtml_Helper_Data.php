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

class Unilab_Adminhtml_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getDateForFilename()
    {
        return Mage::getSingleton('core/date')->date('Y-m-d_H-i-s');
    }
}