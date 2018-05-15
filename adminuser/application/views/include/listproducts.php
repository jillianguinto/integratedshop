<?php if(ccheck_form(isuser_id(),"admin/catalog/products")== true ): ?>
		<div id="page-wrapper">
            <div class="container-fluid">

                <div class="row">
								
                    <div class="col-lg-12">
                        <h1 class="page-header">
                           <i class="fa fa-leaf"></i> Manage Products 
                        </h1>
                        <ol class="breadcrumb">
                            <li class="active">
								<a href="<?php echo base_url();?>">
									<i class="fa fa-dashboard"></i> Dashboard
								</a>
                            </li>
                            <li>
								<strong>
                                <i class="fa fa-leaf"></i>
								<?php echo $this->uri->segment(1) . " [ ".count($getProductslist) ." ]"; ?>
								</strong>
                            </li>
                        </ol>
                    </div>
                </div>
                <div class="row">
					<div class="col-lg-12">
						<p>
							<button type="button" class="btn btn-danger" onclick="createpdf()"><i class="fa fa-file-pdf-o"></i> GENERATE PDF</button>
							<!--<button type="button" class="btn btn-success"><i class="fa fa-file-excel-o"></i> EXPORT TO EXCEL</button>-->
							<a href="<?php echo base_url()?>index.php/products/exportProd" class="btn btn-success"><i class="fa fa-file-excel-o"></i> EXPORT TO EXCEL</a></p>
						<?php if(ccheck_form(isuser_id(),"admin/catalog/update_attributes")== true ): ?>
							<a id="" href="#create_product_settings" data-toggle="modal"  type="button" class="btn btn-info" ><i class="fa fa-file-excel-o"></i> ADD PRODUCT</a>		
                    	<?php else:?>
							<a id="" href="" data-toggle=""  type="button" class="btn btn-info disabled" ><i class="fa fa-file-excel-o"></i> ADD PRODUCT</a>	
						<?php endif; ?>	
                    	</p>
					</div>		

					
						
                    <div class="col-lg-12">
						<table id="productlist" class="table table-striped table-bordered" cellspacing="0" width="100%">
								<thead>
									<tr>
										<th>Product ID</th>
										<th>Product Name</th>
										<th>SKU</th>
										<th><center>Action</center></th>

									</tr>
								</thead>
						 
								<tfoot>
									<tr>
										<th>Product ID</th>
										<th>Product Name</th>
										<th>SKU</th>
										<th><center>Action</center></th>
									</tr>
								</tfoot>
						 
								<tbody>
									<?php foreach($getProductslist as $_list):

											$productId = $_list->entity_id;
											$sku = $_list->sku;
											
											echo '<tr>';
											echo '<td>'.$_list->entity_id.'</td>';
											echo '<td>'.$_list->value.'</td>';
											echo '<td>'.$_list->sku.'</td>';
											echo '<td>
											<center>
											<button type="button" class="_view  btn btn-primary" title="View" onclick="" data-id="'.$productId.'"><i class="fa fa-eye"></i></button>&nbsp';
											if(ccheck_form(isuser_id(),"admin/catalog/update_attributes")== true ){
												// echo '&nbsp<button type="button" class="_view  btn btn-success" title="Edit" onclick="" data-id="'.$productId.'"><i class="fa fa-edit"></i></button>
												 	echo '<button id="deleteProdCmd" type="button" sku="' .$sku. '" cmdEvent="deleteProdCmd" class="btn btn-danger" title="delete"><i class="fa fa-remove"></i></button>';
											}else{}
											echo '	
												</center>
												 </td>';
											echo '</tr>';
									
										endforeach;
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

	   <div class="" id="">				    
		  
		    <div id="create_product_settings" class="modal fade">
		        <div class="modal-dialog">
		            <div class="modal-content">
		                <div class="modal-header">
		                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		                    <h4 class="modal-title">New Product</h4>
		                </div>

						<form id="add_product_set" class="form-horizontal" action="<?php echo base_url(); ?>index.php/products/add" method="post">	
			                <div class="modal-body">					
			                	
			                		<div class="form-group"><label class="col-sm-3 control-label">Attribute Set</label>
		                            	<div class="col-sm-8">
		                            		<select required id="attribute_set_id" name="set" class="form-control">	
		                            		<option value="">-- Select Option --</option>	
					                		<?php $i = 0; ?>			                										
											<?php foreach($settings as $setkey=>$setval): $count = $i++; ?>									
													<option value="<?php echo $settings[$count]['attribute_set_id']; ?>"><?php echo $settings[$count]['attribute_set_name']; ?></option>									
											<?php endforeach; ?>												
											
											</select>
		                            	</div>
		                            </div>

		                            <div class="form-group"><label class="col-sm-3 control-label">Product Type</label>
		                            	<div class="col-sm-8">
		                            		<select id="product_type" name="type" title="Product Type" class="form-control select">
												<!-- <option value="">-- Select Option --</option> -->
												<option value="simple">Simple Product</option>
												<!-- <option value="grouped">Grouped Product</option>
												<option value="configurable">Configurable Product</option>
												<option value="virtual">Virtual Product</option>
												<option value="bundle">Bundle Product</option>
												<option value="downloadable">Downloadable Product</option> -->
											</select>
		                            	</div>
		                            </div>				                	
				               	  
							</div>  						

							<div class="modal-footer">
			                  	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			                   	<button type="submit" class="btn btn-primary pull-right">continue</button> <p class="help-block pull-left text-danger hide" id="form-error">&nbsp; The form is not valid. </p>
								
						  	</div>	
						   
			            </form>		  	
		              	
		            </div>
		        </div>
		    </div>
		</div>  



    </div>

    <form name="productform" action="?" method="post">
		<input type="hidden" value="" name="productId" />
	</form>

</body>

	
<script>
$(document).ready(function() {
	$('#productlist').DataTable();

	$("body").on('click', '._view', function (){			

		var productId = $(this).attr('data-id');
		$("[name=productId]").val(productId);
		$("[name=productform]").attr("action","<?php echo base_url()?>index.php/products/view");
		$("[name=productform]").submit();		
	});	
});

function createpdf()
{
	window.location.href = "<?php echo base_url();?>index.php/pdf/orders";
}	


$('button#deleteProdCmd').click(function(e){	
	e.preventDefault();

	var dataObject = {
		'cmdEvent':	$(this).attr('cmdEvent'),
		'sku': $(this).attr('sku')
	}

	$.ajax({
		url: "<?php echo get_baseurl() ?>/clickhealthdev/index.php/default/connectmage/index",   
		type: 'post',  
		asynchronous: true,
        cashe: false,
		data: dataObject, 
		beforeSend: function() {
			var sku = dataObject['sku'];
			var x = confirm("Are you sure you want to delete this product sku?\n" +sku);
			if (x){
			    return true;
			}
			else{

				return false;
			}	
		},
		success: function(data) 
		{     
			
			window.location.reload(true);
		}
	});



});

</script>


<script type="text/javascript">
$(document).ready(function() {

    $('#add_product_set-').formValidation({
            message: 'This value is not valid',
            icon: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                set: {
                    message: 'The set is not valid',
                    validators: {
                        notEmpty: {
                            message: 'The set is required and can\'t be empty'
                        }
                    }
                }
            }
        })
        .on('success.form.fv', function(e) {
            // Prevent form submission
            e.preventDefault();

         //    // Get the form instance
         //    var $form = $(e.target);

         //    // Get the FormValidation instance
         //    var bv = $form.data('formValidation');
         //    // alert($form.attr('action'));

          	
         // 	// 	var objectData = {
        	// //  		'attributeid' : $("[name='set']").val()	
        	// // }
         
         //    $.post($form.attr('action'), $form.serialize(), function(result) {
         //      //window.location="<?php echo base_url(); ?>index.php/products/add"; 
         //      console.log(result);
              
         //    }, 'json');


        	var $form = $(e.target),
                fv    = $form.data('formValidation');

            // Use Ajax to submit form data
            $.ajax({
                url: $form.attr('action'),
                type: 'POST',
                data: $form.serialize(),
                success: function(result) {
                    // ... Process the result ...

                    window.location="<?php echo base_url(); ?>index.php/products/add";
                }
            });
        });


});
</script>