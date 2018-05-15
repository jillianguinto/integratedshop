<?php

class Gurutheme_Advancedquickorder_Model_Adminhtml_Config_Block
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
            $collection = Mage::getModel('cms/block')->getCollection();
            $array = array(
                array(
                    'value' => '',
                    'label' => 'Select'
                )
            );

            foreach($collection as $block)
            {
                $array[] = array(

                    'value' => $block->getIdentifier(),
                    'label' => $block->getTitle()
                );
            }

            return $array;
    }
}
