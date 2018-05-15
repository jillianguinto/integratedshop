<?php 

class Unilab_Xend_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getConfigSettings()
	{
		return Mage::getModel("xend/config")->getConfig();
	}
}
