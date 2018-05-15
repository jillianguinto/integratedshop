<?php 

/* @package Unilab System Template Renderer
 * @Author Unilab, Inc. - Jerick Y. Duguran @ jerick.duguran@gmail.com
 * @description Custom Department (Email Template) renderer 
 * @date November 21, 2013 
 */
 
class Unilab_Adminhtml_Block_System_Config_Inquiry_Form_Renderer_Template extends Mage_Core_Block_Abstract
{ 	
	
    const XML_PATH_TEMPLATE_EMAIL = 'global/template/email/';
	
	public function _toHtml()
    {	 
		$column = $this->getColumn(); 
		 
		return  '<select id="email_template#{_elm_id}" name="' . $this->getInputName() . '"'.
						($column['size'] 		 ? 'size="' . $column['size'] . '"'  : '') . ' class="' .
						(isset($column['class']) ? $column['class'] 				 : 'input-select') . '"'.
						(isset($column['style']) ? ' style="'.$column['style'] . '"' : '').'>' .
					$this->getEmailTemplates() .
				'</select>';		  
    } 
	
	protected function getEmailTemplates()
	{ 		 
		if(!$collection = Mage::registry('config_system_email_template')){
            $collection = Mage::getResourceModel('core/email_template_collection')
                ->load(); 
            Mage::register('config_system_email_template', $collection);
        }
		
        $templates		= array();
		$options   		= "";
		
		$email_template_paths = array('unilab_customers_inquiry_email_template',
		                              'unilab_customers_inquiry_doctor_email_template',
									  'unilab_customers_inquiry_pharmacist_email_template'
									 );
		 
		foreach($email_template_paths as $path){ 
			 
			$nodeName = str_replace('/', '_', $path);
			$templateLabelNode = Mage::app()->getConfig()->getNode(self::XML_PATH_TEMPLATE_EMAIL . $nodeName . '/label');
			if ($templateLabelNode) {
				$templateName = Mage::helper('adminhtml')->__((string)$templateLabelNode);
				$templateName = Mage::helper('adminhtml')->__('%s (Default Template from Locale)', $templateName);
			}
			array_push(
				$templates,
				array(
					'value' => $nodeName,
					'label' => $templateName
				)
			);
		}
		$templates = array_merge_recursive(
				$templates,
				$collection->toOptionArray()
		);   		
		
		foreach($templates as $template){
			$options .= '<option value="'.$template['value'].'">'.$template['label'].'</option>';
		}
		
		return $options;
	}
}
