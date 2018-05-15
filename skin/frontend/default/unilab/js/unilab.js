 /*
  * @package Unilab JS Scripts 
  * @author  Jerick. Y. DUguran - Movent, INC. -  jerick.duguran@gmail.com
  * @date    November 06, 2013
  *
**/

/******************************************************************************/

/*
 * Prescription Switcher
 * @author Jerick Y. Duguran - jerick.duguran@gmail.com
 * @date November 07, 2013
 */

PrescriptionSwitcher = Class.create();
PrescriptionSwitcher.prototype = {
	initialize: function (wrapperEl,selElm)
	{ 
		try {	 		
			this.wrapper = $(wrapperEl); 	
			this.types   = $$("input."+selElm);		 
			this.types.each(function(element){
				Event.observe(element, 'change', this.update.bind(this))
			}.bind(this));			
		}catch(e){
			console.log(e);
		}
	},
	update: function(event){  		
		var _obj = this;
		var element = Event.element(event); 		 
		this.types.each(function(elm){
			var prescription_details = $(elm).up().down('div.prescription_details');
			if(element.value == elm.value){
				if(prescription_details){
					if(prescription_details.select("input").length > 0){
						prescription_details.select("input").each(function(currentElement){
								Validation.reset(currentElement);	 
								if(currentElement.hasClassName("required")){
									currentElement.addClassName('required-entry');
								}else if(currentElement.hasClassName("required-file-u")){
									currentElement.addClassName('required-file');	 
									Event.observe(currentElement,'change', _obj.upload.bind(_obj));  
								}else if(currentElement.hasClassName("required-one")){
									currentElement.addClassName('validate-one-required-by-name');							
								}else{
									console.log('..');
								}
						}); 	
						prescription_details.show();
					}
				}
			}else{
				if(prescription_details){
					if(prescription_details.select("input").length > 0){
						prescription_details.select("input").each(function(currentElement){
								Validation.reset(currentElement);	 
								if(currentElement.hasClassName("required")){
									currentElement.removeClassName('required-entry');
								}else if(currentElement.hasClassName("required-file-u")){
									currentElement.removeClassName('required-file');							
								}else if(currentElement.hasClassName("required-one")){
									currentElement.removeClassName('validate-one-required-by-name');							
								}else{
									console.log('..');
								}
						}); 	
						prescription_details.hide();		
					} 			
				}
			}
		});  
	},
	upload: function(event)
	{  
		var element  = Event.element(event); 
		Validation.validate(element); 	
	}
}


/*
 * Custom Validators
 * @author	Jerick Y. Duguran - jerick.duguran@gmail.com
 * @date 	November 07, 2013
 */
Validation.addAllThese([
		['validate-file-type', 'Please select valid file type. [jpg,png,gif]', function (v,element) {	    
					var file	  		= element.files[0];				 
					var filename 		= file.name;
					var file_extension  = filename.split('.')[filename.split('.').length - 1].toLowerCase();
					
					if(file_extension === 'jpg' || file_extension === 'png' || file_extension === 'gif'){
						return true;
					}					
					return false;
					
		}],
		['validate-file-size', 'Maximum File Size is 2MB', function (v,element) {
	
					var file	  		= element.files[0];				 
					var file_size 		= (file.size / 1024 / 1024).toFixed(2);
					
					if(file_size > 2)
					{
						return false;
					}					
					return true;
		}]
	]); 