<?php if(ccheck_form(isuser_id(),"admin/promo")== true ): ?>
		<div id="page-wrapper">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">
                             <i class="fa fa-life-ring"></i> Manage Promotions
                        </h1>
                        <ol class="breadcrumb">
                            <li class="active">
								<a href="<?php echo base_url();?>">
									<i class="fa fa-dashboard"></i> Dashboard
								</a>
                            </li>
                            <li class="active">
									<strong><i class="fa fa-life-ring"></i> 
										<?php echo $this->uri->segment(1) . " [ ".count($getAllPromo) ." ]"; ?>
									</strong>
                           </li>
                        </ol>
                    </div>
                </div>
                <div class="row">
					<div class="col-lg-12">
						<p>
						<button type="button" class="btn btn-danger" onclick="createpdf()"><i class="fa fa-file-pdf-o"></i> GENERATE PDF</button>
						<button type="button" class="btn btn-success"><i class="fa fa-file-excel-o"></i> EXPORT TO EXCEL</button>
						<?php if(ccheck_form(isuser_id(),"admin/promo/catalog")== true ): ?>
						<a id="" href="#create_promo_settings" data-toggle="modal"  type="button" class="btn btn-info" ><i class="fa fa-file-excel-o"></i> ADD PROMO</a>
						<?php else:?>
						<a id="" href="" data-toggle=""  type="button" class="btn btn-info disabled" ><i class="fa fa-file-excel-o"></i> ADD PROMO</a>
						<?php endif; ?>	
						<!--<button type="button" class="btn btn-info"><i class="fa fa-file-excel-o"></i>ADD PROMO</button>-->
						</p>
					</div>				
                    <div class="col-lg-12">
						<table id="promotionlists" class="table table-striped table-bordered" cellspacing="0" width="100%" data-tableName="Promo List">
								<!--<thead>
									<tr>
										<th>#</th>
										<th width="15%">Promo Name</th>
										<th>Description</th>
										<th>Discount</th>
										<th>Type</th>
										<th>Used</th>
										<th width="18%"><center>Action</center></th>
									</tr>
								</thead>
						 
								<tfoot>
									<tr>
										<th>#</th>
										<th width="15%">Promo Name</th>
										<th>Description</th>
										<th>Discount</th>
										<th>Type</th>
										<th>Used</th>
										<th width="18%"><center>Action</center></th>
									</tr>
								</tfoot>-->
						 <thead>
									<tr>
										<th>ID#</th>
										<th width="15%">Rule Name</th>
										<th>Description</th>
										<th>Date Start</th>
										<th>Date Expire</th>
										<th>Status</th>
										<th>Discount</th>
										<!--<th>Website</th>-->
										<th width="18%"><center>Action</center></th>
									</tr>
								</thead>
						 
								<tfoot>
									<tr>
										<th>ID#</th>
										<th width="15%">Rule Name</th>
										<th>Description</th>
										<th>Date Start</th>
										<th>Date Expire</th>
										<th>Status</th>
										<th>Discount</th>
										<!--<th>Website</th>-->
										<th width="18%"><center>Action</center></th>									
									</tr>
								</tfoot>
								<tbody>
									<?php 
									
										$count = 1;
										foreach($getAllPromo as $_list):
											
											$pormID = $_list->rule_id;

											echo '<tr>';
											//echo "<td>$count</td>";
											echo '<td>'.$_list->rule_id.'</td>';
											echo '<td>'.$_list->name.'</td>';
											echo '<td>'.$_list->description.'</td>';
											echo '<td>';
													echo ($_list->from_date=="" OR $_list->from_date=="0000-00-00") ? "NOT-DATED" : $_list->from_date .'</td>';
											echo '<td>';
													echo ($_list->to_date=="" OR $_list->from_date=="0000-00-00") ? "NOT-DATED" : $_list->to_date .'</td>';
											echo '<td>';
													echo ($_list->is_active=="1") ?  "ACTIVE" :  "INACTIVE" .'</td>';
											echo '<td>'. number_format($_list->discount_amount,2,'.','').'</td>';
											//echo '<td><center>'.$_list->website_id.'</center></td>';
											echo '
											<td><center>
											<button type="button" class="_view btn btn-primary" title="View" data-id="'.$pormID.'"><i class="fa fa-eye"></i> </button>';
											if(ccheck_form(isuser_id(),"admin/promo/catalog")== true ){
											echo '&nbsp<button type="button" class="_view btn btn-success" title="Edit" data-id="'.$pormID.'"><i class="fa fa-edit"></i> </button>';
											}else{}
											echo '</center></td>';
											echo '</tr>';
											$count++;
										endforeach;
									//echo count($getAllPromo);
									?>

								</tbody>
							</table>                   
					</div>   
				   
                </div>

            </div>

        </div>
<?php else: echo "This module is not allowed for viewing!"?>
<!--<a id="" href="#addcustomermodal" type="button" class="btn btn-xs btn-success" data-toggle="modal" ><i class="fa fa-file-excel-o"></i> ADD NEW CUSTOMER</a>-->

<?php endif; ?>	

<!--modal promo -->
<div class="" id="">				    
		  
		    <div id="create_promo_settings" class="modal fade">
		        <div class="modal-dialog">
		            <div class="modal-content">
		                <div class="modal-header">
		                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		                    <h4 class="modal-title">New Promo</h4>
		                </div>

						<form id="add_promo" class="form-horizontal" action="<?php echo get_baseurl() ?>/clickhealthdev/index.php/default/connectmage/index" method="post">	
			                <div class="modal-body">					
			                	<div id="validation-error" class="alert alert-success"></div>
			                		<div class="form-group"><label class="col-sm-3 control-label">Promo Details</label>
		                            </div>

		                            <div class="form-group"><label class="col-sm-2 control-label">Rule Name</label> 
	                            	<div class="col-sm-10">
	                            		<input class="form-control required" name="cmdEvent" type="hidden" value="newCatRuleCmd"> 
	                            		<input class="form-control required" name="rule_website_ids" type="hidden" value="<?php echo isstore_id(); ?>"> 
	                            		<input class="form-control required" name="rule_name" type="text" value="">
	                            		<label style="float:right;">active
	                            		<input type="checkbox" style="float:left;" name="rule_is_active" />
	                            		</label>
	                            	</div>
	                            	</div>

		                            <div class="form-group"><label class="col-sm-2 control-label">Rule Description</label>
	                            	<div class="col-sm-10">
	                            		<input class="form-control required" name="rule_description" type="text" value="">
	                            	</div>
	                           		</div>

	                           		<div class="form-group"><label class="col-sm-2 control-label">SKU</label>
	                            	<div class="col-sm-10">
	                            		<input class="form-control required" id="sku" name="skus" type="hidden" value="" autocomplete="off">

	                            	<select id="chkveg" name="sku[]" class="form-control" style="" multiple="multiple">
	                            	<?php 
	                            	//print_r($getProductslist);
	                            	foreach($getProductslist as $plist): ?>
	                            		<option value="<?php echo $plist->sku;?>"><?php echo $plist->value; ?></option>
	                            		<?php endforeach;?>
	                            	</select>
	                            	</div>
	                            	<!--<input type="button" id="btnget" value="apply" />-->
	                           		</div>

		                           <div class="form-group"><label class="col-sm-2 control-label">Customer Group</label>				                  
									<div class="col-sm-10">
										<select id="_accountgroup_id" name="rule_customer_group_ids[]" class="form-control" multiple>

										<?php 
										//print_r($customer_group);
										foreach($customer_group as $customer_group_id_key=>$customer_group_value): ?>												
											<option value="<?php echo $customer_group[$customer_group_id_key]['customer_group_id'];?>"><?php echo $customer_group[$customer_group_id_key]['customer_group_code']; ?></option>											
										<?php endforeach;?>
										</select>
									</div>
	                            </div>		

	                            <div class="form-group"><label class="col-sm-2 control-label">Apply To</label>				                  
									<div class="col-sm-10">
										<select id="_simple_id" name="rule_simple_action" class="form-control">	
											<option value="by_fixed">By Fixed Amount</option>
											<option value="to_percent">To Percentage of the Original Price</option>
											<option value="by_percent">By Percentage of the Original Price</option>
											<option value="to_fixed">To Fixed Amount</option>
										</select>
									</div>
	                            </div>	

	                            <div class="form-group"><label class="col-sm-2 control-label">Discount Amount</label>
	                            	<div class="col-sm-10">
	                            		<input class="form-control required" name="rule_discount_amount" type="text" value="">
	                            	</div>
	                           	</div>	                	
				               	  
				               	  	<div class="form-group"><label class="col-sm-2 control-label">From Date</label>	                            	
	                            	<div class="col-sm-10">
		                            	<div class='input-group date' id='datetimepicker1'>
						                    <input id="datepicker" type='text' class="form-control" name="rule_from_date" value="" autocomplete="off"/>
						                    <span class="input-group-addon">
						                        <span class="glyphicon glyphicon-calendar"></span>
						                    </span>
						                </div>
						            </div>
	                            </div>

	                            <div class="form-group"><label class="col-sm-2 control-label">To Date</label>	                            	
	                            	<div class="col-sm-10">
		                            	<div class='input-group date' id='datetimepicker1'>
						                    <input id="datepicker2" type='text' class="form-control" name="rule_to_date" value="" autocomplete="off"/>
						                    <span class="input-group-addon">
						                        <span class="glyphicon glyphicon-calendar"></span>
						                    </span>
						                </div>
						            </div>
	                            </div>
							</div>  						
							<div class="modal-footer">
			                  	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			                   	<button type="submit" class="btn btn-primary pull-right">continue</button> <p class="help-block pull-left text-danger hide" id="form-error">&nbsp; The form is not valid. </p>
								
						  	</div>	



						   <script type="text/javascript">
$('#create_promo_settings').on('shown.bs.modal', function() {
     $('input:text:visible:first').focus();
     // prepare datepicker
     $('#datepicker').datepicker({
                singleDatePicker: true,
                showDropdowns: true,
                parentEl: '#create_promo_settings'

            });
    });


						   $(function() {

$('#chkveg').multiselect({
	maxHeight: 200,
            enableFiltering: true,
            includeSelectAllOption: true,
            selectAllJustVisible: false,
            dropUp: true,
            numberDisplayed: 2,
            enableCaseInsensitiveFiltering: true,
//includeSelectAllOption: true
onChange: function(option, checked, select) {
var valuess = $('#chkveg option:selected').map(function(a, item){return item.value;}).get();
$('#sku').val(valuess);
},
});

$('#btnget').click(function() {

//alert($('#chkveg').val());

})

});
						   </script>

			            </form>		  	
		              	
		            </div>
		        </div>
		    </div>
		</div>  

	<form name="promoForm" action="?" method="post">
		<input type="hidden" value="" name="promoid" />
	</form>


</body>

<script type="text/javascript">

	$(document).ready(function() {
		$('#promotionlists').DataTable();

		$("body").on('click', '._view', function (){		
		var promid = $(this).attr('data-id');
		$("[name=promoid]").val(promid);
		$("[name=promoForm]").attr("action","<?php echo base_url()?>index.php/promotions/view");
		$("[name=promoForm]").submit();		
	});	
	});

</script>

<script type="text/javascript">
/*
$(":checkbox").change(function(e){
  $(this).val( $(":checked").length > 0 ? "1" : "0");
});

$('#add_promo--').submit(function(event) {
    event.preventDefault();
    
    $.ajax({
        url: $(this).attr('action'),
        type: "post",
        data: $(this).serialize(),
        success: function (response) {

            if(response.success == true){
                alert('you will get response from your php page (what you echo or print)'+ response.msgHndlr);  
            }
            else
            {
                alert(response.msgHndlr);  
            }                       

        },
        error: function(jqXHR, textStatus, errorThrown) {
           console.log(textStatus, errorThrown);
        }


    });

});*/
</script>

<script type="text/javascript">
$(":checkbox").change(function(e){
  $(this).val( $(":checked").length > 0 ? "1" : "0");
});

$(document).ready(function() {

    $('#create_promo_settings').on('hidden.bs.modal', function () {
        setTimeout(function(){
            window.location="<?php echo base_url(); ?>index.php/Promotions";         
        }, 1000);      

    });

    $('#add_promo').formValidation({
        message: 'This value is not valid',
        icon: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            rule_description: {
                message: 'The field is not valid',
                validators: {
                    notEmpty: {
                        message: 'The field is required and can\'t be empty'
                    }
                }
            }
        }
    })
    .on('success.form.fv', function(e) {
        // Prevent form submission
        e.preventDefault();

        var $form = $(e.target),
            fv    = $form.data('formValidation');

        // Use Ajax to submit form data
        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: $form.serialize(),
            success: function(result) {
                // ... Process the result ...

                // alert($form.attr('action'));
                jQuery("#create_promo_settings").modal('show');
                $('#myModalLabel').text('promo Information');
                $('.modal-body').text('added successfully')
            }
        });
    });
    

});

</script>