$(function(){
            
        // Setup - add a text input to each footer cell
        $('#customerlist thead tr#filterrow td').each( function () {
            var title = $('#customerlist thead td').eq( $(this).index() ).text();
            $(this).html( '<input type="text" style="width:90%; border:0;" onclick="stopPropagation(event)" class="searh-node" placeholder="Search '+title+'" />' );
        });
     
        // DataTable
        var table = $('#customerlist').DataTable({
        "bSort" : false
        // "bSearchable": false     
       
        });
         
        // Apply the filter
        $("#customerlist thead input").on( 'keyup change', function () {
            table
                .column( $(this).parent().index()+':visible' )
                .search( this.value ) 
                .draw();
        });

        function stopPropagation(evt) {
            if (evt.stopPropagation !== undefined) {
                evt.stopPropagation();
            } else {
                evt.cancelBubble = true;
            }
        }    


        // user for FROM TO DATE
        $('tr#filterrow').find('td').each(function(i, n){    

            var main = $(this);

            if(i == 0){

                var input =  $(this).find('input[type="text"]');   
               
                input.after("<a id='reset_0' style='color:blue;' href='javaxcript:void(0);'><i class='fa fa-angle-double-left'></i><span id='errmsg_0'></span>"); 

                //main.find('i').remove();  
                $('#reset_0').on('click change ' ,function(e){

                    e.preventDefault();
                    input.select();
                    input.val('');                

                    $('#customerlist').dataTable().fnDestroy();
                    $('#customerlist').dataTable({"bSort": false});

                  
                });

                input.keypress(function (e) {
                   //if the letter is not digit then display error and don't type anything
                   if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                      //display error message
                      $("#errmsg_0").html("&uarr; Digits Only").show().fadeOut("slow").css({'color': 'red', 'font-size': '11px'});
                          return false;
                  }
                });

            }else if(i == 1){

                var input =  $(this).find('input[type="text"]');

                input.after("<a id='reset_1' style='color:blue;' href='javaxcript:void(0);'><i class='fa fa-angle-double-left'></i></a><span id='errmsg_1'></span>");   
               
                //main.find('i').remove();
                $('#reset_1').click(function(e){
                    e.preventDefault();
                    input.select();
                    input.val('');                

                    $('#customerlist').dataTable().fnDestroy();
                    $('#customerlist').dataTable({"bSort": false});
                });

                input.on('keypress', function() {
                    var re = /^[A-Za-z _.-]+$/.test(this.value);
                    if(!re) {
                        $("#errmsg_1").html("&uarr; Letters Only").show().fadeOut("slow").css({'color': 'red', 'font-size': '11px'});
                    } 
                });    

            }else if(i == 2){   

                var input =  $(this).find('input[type="text"]');

                input.after("<a id='reset_2' style='color:blue;' href='javaxcript:void(0);'><i class='fa fa-angle-double-left'></i></i></a>&nbsp;&nbsp;<span id='errmsg_2'></span>");   
                $('#reset_2').click(function(e){
                    e.preventDefault();
                    input.select();
                    input.val('');                

                    $('#customerlist').dataTable().fnDestroy();
                    $('#customerlist').dataTable({"bSort": false});
                });
                 

                input.on('keypress', function() {
                    var re = /([A-Z0-9a-z_-][^@])+?@[^$#<>?]+?\.[\w]{2,4}/.test(this.value);
                    if(!re) {
                        $("#errmsg_2").html("&uarr; Valid Emailaddress Only").show().fadeOut("slow").css({'color': 'red', 'font-size': '11px'});
                    } 
                });    
                              

            }else if(i == 3){             
              
              $(this).find('input[type="text"]').attr( 'readOnly' , 'false');
              $(this).find('input[type="text"]').attr( 'id' , 'from' );             
           
           }else if(i == 4){             
           
              $(this).find('input[type="text"]').attr( 'readOnly' , 'false');
              $(this).find('input[type="text"]').attr( 'id' , 'to' );  
                
           
           }else if(i == 5){

              $(this).html("<a id='reset_5' href='javaxcript:void(0);'><i class='fa fa-refresh'></a>").css({'text-align':'center'});
              $('#reset_5').click(function(e){
                    e.preventDefault();
                
                    $("#from, #to").val('');

                    $('#customerlist').dataTable().fnDestroy();
                    $('#customerlist').dataTable({"bSort": false});
              });

           }

        });          
     
});

// DATEPICKER RANGE FROM - TO
$(function(){

    var dateToday = new Date();
    var yrRange = (dateToday.getFullYear()-5) + ":" + (dateToday.getFullYear() + 2);

    var fromDate = $("#from").datepicker({
        dateFormat: 'MM dd, yy', 
        yearRange:  '1901:2020',
        changeYear: true,
        showOn: 'focus',
        showButtonPanel: true,
        closeText: 'Clear',
        changeMonth: true,
        numberOfMonths: 1,
        //minDate: new Date(),
        onSelect: function(selectedDate) {
            var instance = $(this).data("datepicker");
            var date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);
            date.setDate(date.getDate()+1);
            toDate.datepicker("option", "minDate", date);
        },
        onClose: function () {
            var event = arguments.callee.caller.caller.arguments[0];       
            if ($(event.delegateTarget).hasClass('ui-datepicker-close')) {
                $(this).val('');
            }
        }
    });
    
    var toDate = $("#to").datepicker({
        dateFormat: 'MM dd, yy',
        yearRange:  '1901:2020',
        changeYear: true,
        showOn: 'focus',
        showButtonPanel: true,
        closeText: 'Clear',
        changeMonth: true,
        numberOfMonths: 1,
        onClose: function () {
            var event = arguments.callee.caller.caller.arguments[0];       
            if ($(event.delegateTarget).hasClass('ui-datepicker-close')) {
                $(this).val('');
            }
        }
    });

    // user to ebnable today button, add 'showButtonPanel: true' to datepicker before to add this
    var _gotoToday = jQuery.datepicker._gotoToday;
    jQuery.datepicker._gotoToday = function(a){
        var target = jQuery(a);
        var inst = this._getInst(target[0]);
        _gotoToday.call(this, a);
        jQuery.datepicker._selectDate(a, jQuery.datepicker._formatDate(inst,inst.selectedDay, inst.selectedMonth, inst.selectedYear));
    };


});






