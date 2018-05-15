<?php 

class Unilab_Customer_Block_Widget_Civilstatus extends Mage_Customer_Block_Widget_Abstract
{
    public function _construct()
    {
        parent::_construct();
 
        $this->setTemplate('customer/widget/civilstatus.phtml');
    }

    public function isEnabled()
    {
        return (bool)$this->_getAttribute('civil_status')->getIsVisible();
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
     * Can show Civil Status
     *
     * @return bool
     */
    public function showCivilStatus()
    {
        return (bool)$this->_getAttribute('civil_status')->getIsVisible();
    }

    /**
     * Define if Civil Status attribute is required
     *
     * @return bool
     */
    public function isCivilStatusRequired()
    {
        return (bool)$this->_getAttribute('civil_status')->getIsRequired();
    }

    /**
     * Retrieve name Civil Status drop-down options
     *
     * @return array|bool
     */
    public function getCivilStatusOptions()
    {
        $prefixOptions = $this->helper('customer')->getCivilStatusOptions();

        if ($this->getObject() && !empty($prefixOptions)) {
            $oldPrefix = $this->escapeHtml(trim($this->getObject()->getCivilStatus()));
            $prefixOptions[$oldPrefix] = $oldPrefix;
        }
        return $prefixOptions;
    } 
	
	public function getStoreLabel($attributeCode)
    {
        $attribute = $this->_getAttribute($attributeCode);
        return $attribute ? $this->__($attribute->getStoreLabel()) : '';
    }
}
