/*
 * jQuery City Updater v1.0
 *
 * This plugin will dynamically replace the city input field to dropdown
 * Currently work for magento ecommerce 
 * 
 * Copyright 2014, Diszo Sasil (diszo.sasil@gmail.com)
 * Free to use under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 * 
 */

(function ($) {
	/*
	 * @parameters: 
	 * 		country - 	Element
	 * 		region	-	Element
	 * 		city	- 	Element
	 * 		cities 	- 	Array for dropdown options
	 * 		showOnCountry - country code
	 */	 
    $.CityUpdater = function (country, region, city, cities, defaultCity, showOnCountry ) { 
        this.country = (country instanceof $) ? country : $('#'+country);
        this.region = (region instanceof $) ? region : $('#'+region);
        this.city = (city instanceof $) ? city : $('#'+city);
        this.cities = eval(cities);
        this.defaultCity = defaultCity;
        this.showOnCountry = (showOnCountry == "") ? "PH" : showOnCountry;
        this.observe();
    };
		   
    $.CityUpdater.prototype = {
    	
        observe: function(){
        	var that = this;
        	$([this.country,this.region]).each(function(idx){
        		$(this).bind('change',function(){
        			that.refresh();
	        	});
        	});
        	this.refresh();       	
        },
        
        getCity: function(){
	    	if(this.defaultCity != this.city.val() && this.city.val() != ""){
	    		return this.city.val();
	    	}
	    	return this.defaultCity;
	    },
        
        refresh: function(){
        	var that = this;
			sourceId = this.city.attr("id");
			sourceName = this.city.attr("name");
			if(this.country.val() != this.showOnCountry){
				$('#'+sourceId).replaceWith('<input type="text" class=" input-text required-entry absolute-advice " title="'+sourceId+'" value="' + 
           			this.getCity() + '" id="'+sourceId+'" name="'+sourceName+'" autocomplete="off">');
			}else{
				if(typeof(this.cities) == "object"){
        			options="";
	        		for(var i in this.cities){
						if(this.cities.hasOwnProperty(i)){	
							options += that.prepareSelectOption(this.cities[i]);	
						}
					}
					
					oldSelVal = this.getCity();
					$('#'+sourceId).replaceWith('<select id="'+sourceId+'" name="'+sourceName+'" class="validate-select required-entry">' +
			                  '<option value="">Please select city</option>' +options+
			                '</select>');
			                
			        $('#'+sourceId).val(oldSelVal);
					
	        	}		
			}
    			
        },
        
        prepareSelectOption: function(data){
        	if(typeof(data) != "undefined"){		        		
        		if(data.country == this.country.val() && this.region.val() != "" && data.region_id == this.region.val()){
					return '<option value="'+data.name+'">'+data.name+'</option>';
				}else if(data.country == this.country.val() && this.region.val() == ""){
					//return '<option value="'+data.name+'">'+data.name+'</option>';
				}
        	}
        	return '';
        }
        
    };
}(jQuery));