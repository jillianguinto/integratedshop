<?php

class Unilab_Inquiry_Model_Inquiry extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('inquiry/inquiry');
    }

    protected function _beforeDelete()
    {
    	/*	
        $path = Mage::getBaseDir('media') . DS;
        $images_fields = array('logo','image','small_image'); // These are the images fields
        $promos = Mage::getModel('inquiry/inquiry')->load($this->getId());
        foreach($images_fields as $field){
            if(file_exists($path.$promos->getData($field))){
                @unlink($path.$promos->getData($field));
            }
        }
		 */
    }
}
