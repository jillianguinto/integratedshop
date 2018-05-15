
Event.observe(document, 'dom:loaded', function() {



	['rracheckoutsection_design-head', 'rracheckoutsection_button_skin-head', 'rracheckoutsection_body_skin-head'].each(function(e){

		 if ($(e)){

			 $(e).up('div').addClassName('glc-design-child-head');

		 }

	 });

	['rracheckoutsection_design', 'rracheckoutsection_button_skin' , 'rracheckoutsection_body_skin'].each(function(e){

		 if ($(e)){

			 $(e).addClassName('glc-design-child');

		 }

	 });
	 
	glcChangeDesign();

	Event.observe($('rracheckoutsection_designmain-head'),'click', function(){

		glcChangeDesign();

	});
	

});

function glcChangeDesign(){

	if ($('rracheckoutsection_designmain')){

		if ($('rracheckoutsection_designmain').visible()){
		
			$$('.glc-design-child-head, .glc-design-child').each(function(e){

				e.show();

			});

		}else{

			$$('.glc-design-child-head, .glc-design-child').each(function(e){

				e.hide();

			});

		}

	}

}
