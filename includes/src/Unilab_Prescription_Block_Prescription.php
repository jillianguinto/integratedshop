<?php
class Unilab_Prescription_Block_Prescription extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }

     public function getPrescription()
     {
        if (!$this->hasData('prescription')) {
            $this->setData('prescription', Mage::registry('prescription'));
        }
        return $this->getData('prescription');

    }
}