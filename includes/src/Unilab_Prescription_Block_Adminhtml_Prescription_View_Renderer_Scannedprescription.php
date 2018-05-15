<?php

class Unilab_Prescription_Block_Adminhtml_Prescription_View_Renderer_Scannedprescription extends Varien_Data_Form_Element_Abstract
{ 
	
    /**
     * Enter description here...
     *
     * @param array $data
     */
    public function __construct($data)
    {
        parent::__construct($data);
        $this->setType('file');
    }
    
    public function getElementHtml()
    {
        $html = '';

        if ($this->getValue()) {
            $url = $this->_getUrl();
			if(strpos($url, ',') === FALSE){
				$urls = array($url);
			}else{ 
				$urls = explode(",",$url);
			}
			
			$html = '<div>';
			$indx = 0;
			foreach($urls as $img_url){
				if( !preg_match("/^http\:\/\/|https\:\/\//", $img_url) ) {
					$img_url = Mage::getBaseUrl('media') .'prescriptions/'. $img_url;
					$html .= '<a style="float:left;padding:2px;" href="'.$img_url.'" onclick="imagePreview(\''.$this->getHtmlId(). $indx . '_image\'); return false;"><img src="'.$img_url.'" id="'.$this->getHtmlId().$indx .'_image" title="'.$this->getValue().'" alt="'.$this->getValue().'" height="34" class="small-image-preview v-middle" /></a> ';
				}
				$indx++;
			}
			$html .= "<div style='clear:both;'></div></div>";

	    }
        
        /*********************************************************
         * Process: Validation css 'class' for image type field
         * Modified: 2013-10-29
         * Jerick Y. Duguran - Movent, Inc
         * 
         * $this->setClass('input-file'); 
         ********************************************************/
        
       // $html.= parent::getElementHtml();
       // $html.= $this->_getDeleteCheckbox();

        return $html;
    }
    

    /**
     * Enter description here...
     *
     * @return string
     */
    protected function _getDeleteCheckbox()
    {
        $html = '';
        if ($this->getValue()) {
            $html .= '<span class="delete-image">';
            $html .= '<input type="checkbox" name="'.parent::getName().'[delete]" value="1" class="checkbox" id="'.$this->getHtmlId().'_delete"'.($this->getDisabled() ? ' disabled="disabled"': '').'/>';
            $html .= '<label for="'.$this->getHtmlId().'_delete"'.($this->getDisabled() ? ' class="disabled"' : '').'> Delete Image</label>';
            $html .= $this->_getHiddenInput();
            $html .= '</span>';
        }

        return $html;
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    protected function _getHiddenInput()
    {
        return '<input type="hidden" name="'.parent::getName().'[value]" value="'.$this->getValue().'" />';
    }

    /**
     * Get image preview url
     *
     * @return string
     */
    protected function _getUrl()
    {
        return $this->getValue();
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getName()
    {
        return  $this->getData('name');
    }
}