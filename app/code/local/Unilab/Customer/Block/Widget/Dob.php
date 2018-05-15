<?php 
class Unilab_Customer_Block_Widget_Dob extends Mage_Customer_Block_Widget_Dob
{    
	public function getNumOptions($minValue, $maxValue,$selected)
	{
		$options = ""; 
		
		for ($count = $minValue; $count <= $maxValue; $count++)
		{
			$options = $options . '<option '. ($selected == $count ? ' selected="SELECTED" ' : '').' value="' . str_pad($count,2,'0',STR_PAD_LEFT) . '">' . str_pad($count,2,'0',STR_PAD_LEFT) . '</option>';
		}		
		return $options;
	}	
	
	public function getDob()
	{ 
		if(!$this->getCustomer()->getDob()){
			return ;
		}
		
		return date("m-d-Y",strtotime($this->getCustomer()->getDob()));
	}
	
	/**
     * Get current customer from session
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }
}
