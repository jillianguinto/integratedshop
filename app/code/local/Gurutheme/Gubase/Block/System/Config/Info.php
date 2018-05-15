<?php
class Gurutheme_Gubase_Block_System_Config_Info
    extends Mage_Adminhtml_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{

    /**
     * Render fieldset html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
         $html = '<div style="background:url(\'https://gurutheme.com/skin/frontend/guru/default/images/GuruThemeLogo.png\') no-repeat scroll 15px center #EAF0EE;border:1px solid #CCCCCC;margin-bottom:10px;padding:10px 5px 5px 325px;">
                    <h4>About Gurutheme</h4>
                    <p>A Professional Magento themes and extension provider.<br />
                    View more extensions @ <a href="http://www.magentocommerce.com/magento-connect/developer/Gurutheme" target="_blank">MagentoConnect</a><br />
                    <a href="https://gurutheme.com/contacts" target="_blank">Request a Quote / Contact Us</a><br />
					Email me @ <a href="mailto:contact@gurutheme.com">contact@gurutheme.com</a><br />
					Follow me on Twitter <a href="https://twitter.com/gurutheme" target="_blank">@gurutheme</a><br />
                    Visit my website:  <a href="https://gurutheme.com" target="_blank">www.gurutheme.com</a></p>
                </div>';

        return $html;
    }
}
