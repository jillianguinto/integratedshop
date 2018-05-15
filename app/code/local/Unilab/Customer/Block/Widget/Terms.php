<?php 

class Unilab_Customer_Block_Widget_Terms extends Mage_Customer_Block_Widget_Abstract
{
    public function _construct()
    {
        parent::_construct();
 
        $this->setTemplate('customer/widget/terms.phtml');
    }

    public function isEnabled()
    { 
        return (bool)$this->_getAttribute('agree_on_terms')->getIsVisible();
    }
    /**
     * Can show config value
     *
     * @param string $key
     * @return bool
     */
    protected function _showConfig($key)
    {
        return (bool)$this->getConfig($key);
    }

    /**
     * Can show Agree on Terms
     *
     * @return bool
     */
    public function showAgreeOnTerms()
    {
        return (bool)$this->_getAttribute('agree_on_terms')->getIsVisible();
    }

    /**
     * Define if Agree on Terms  attribute is required
     *
     * @return bool
     */
    public function isAgreeOnTermsRequired()
    {
        return (bool)$this->_getAttribute('agree_on_terms')->getIsRequired();
    }
 
	
	public function getStoreLabel($attributeCode)
    {
        $attribute = $this->_getAttribute($attributeCode);
        return $attribute ? $this->__($attribute->getStoreLabel()) : '';
    }
}
