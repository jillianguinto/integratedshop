
$(document).ready(function() {
    //#form-user, #form-account_info, #form-account_addressinfo, #update_products

    $('#validation-error').css('display','none');

	$("#form-user-").on('submit',(function(e) {
        e.preventDefault();		
						
        $.ajax({
            url: $(this).attr('action'),
            type: "POST",
            data:  new FormData(this), 
            contentType: false,
            cache: true,
            processData:false,
            success: function(data){
                   
                    var data = JSON.parse(data);
					if(data.st == 0)
                    {
                        $( ".error-message" ).remove();
                        var data1   = JSON.parse(data.msg);    
                        $('form :input').each(function(){                            
                                        var elementName = $(this).attr('name');											
                                        var message = data1[elementName];
											if(message){
											var element = $('<div>' + message + '</div>')
															.attr({
																'class' : 'error-message'
															})
															.css ({
																display: 'none'
															});
											$(this).after(element);
											$(element).fadeIn();												
										
										}
                        });
                    }
                       
                  

                    if(data.st == 1)
                    {
                        $('#validation-error').show();
						$('#validation-error').html(data.msg);   
                        // setTimeout(function(){ 
                        //     location.reload(); 
                        // }, 3000);

						// $( ".error-message" ).remove();		
                    }
            },
            error: function(){}             
        });
    }));
    
});




$(document).ready(function() {
    $('#validation-error').css('display','none');
    $("#update_promo").on('submit',(function(e) {
        e.preventDefault();     
                        
        $.ajax({
            url: $(this).attr('action'),
            type: "POST",
            data:  new FormData(this), 
            contentType: false,
            cache: true,
            processData:false,
            success: function(data){
                    //console.log(data);
                    //alert(data);
                    var data = JSON.parse(data);
                    if(data.st == 0)
                    {
                        $( ".error-message" ).remove();
                        var data    = JSON.parse(data.msg);    
                        $('form :input').each(function(){                            
                                        var elementName = $(this).attr('name');                                         
                                        var message = data1[elementName];
                                            if(message){
                                            var element = $('<div>' + message + '</div>')
                                                            .attr({
                                                                'class' : 'error-message'
                                                            })
                                                            .css ({
                                                                display: 'none'
                                                            });
                                            $(this).after(element);
                                            $(element).fadeIn();                                                
                                        }
                        });
                    }
                    if(data.st == 1)
                    {
                        $('#validation-error').css({'margin-top': '10px','margin-bottom': '-6px'}).show();
                        $('#validation-error').html(data.msg);   
                        setTimeout(function(){ 
                           location.reload(); 
                        }, 3000);

                        $( ".error-message" ).remove(); 
                    }
            },
            error: function(){}             
        });
    }));
});