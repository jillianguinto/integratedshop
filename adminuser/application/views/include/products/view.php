<?php //echo '<pre>'; print_r($customer_information);?>
<?php //print_r($website_id);?>
<?php //print_r($customer_group);?>
<?php 	

// echo '<pre>'; 
// print_r($category_id);

// //print_r($settings);


// print_r(array_merge($root['maincat'], $settings['subcat']));


//echo '<pre>';

// //echo $category_id;

 //print_r($root);


// $i = 0; 
// foreach($root['maincat'] as $key=>$category) :
//     $inc = $i++; 

//     echo $root['maincat'][$inc]['value'];


// endforeach;


// die();

?>



<div id="page-wrapper">
    <div class="container-fluid">

    	<div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">
                    <i class="fa fa-qrcode"></i>Product Information 
                </h1>
                <ol class="breadcrumb">
                    <li class="active">
						<a href="<?php echo base_url();?>">
							<i class="fa fa-dashboard"></i> Dashboard
						</a>
                    </li>
                    <li>
						<a href="<?php echo base_url();?>index.php/products">
							<i class="fa fa-qrcode"></i>  <?php echo $this->uri->segment(1) ?>
						</a>
                    </li>
                    <li><strong><?php echo $this->uri->segment(2); ?></strong></li>
                </ol>

                
            </div>
        </div>
      <!--   <div class="row">
            <div class="col-md-12">
                <div id="validation-error" class="alert alert-success"></div>
            </div>
        </div>   -->
    	
        <div class="row">

            <!-- <form id="update_product-" class="form-horizontal" action="<?php echo base_url(); ?>index.php/products/update_products" method="post"> -->
            <form id="update_product" class="form-horizontal" action="<?php echo get_baseurl() ?>/clickhealthdev/index.php/default/connectmage/index" method="post">         
                
                <input class="form-control required" name="cmdEvent" type="hidden" value="updateProdCmd"> 
                  <input class="form-control required" name="attributeid" type="hidden" value="11">
                <input class="form-control required" name="websiteids[]" type="hidden" value="<?php echo $products_view[0]->websiteids; ?>">
                <!-- <input class="form-control required" name="categoryIDs[]" type="hidden" value="174"> -->

                <div class="row">    
                    <div class="col-md-12">
                    <?php if(ccheck_form(isuser_id(),"admin/catalog/update_attributes")== true ): ?>
                        <button type="submit" class="btn btn-primary pull-right">Save</button>  
                    <?php else: ?>
                        <button type="submit" class="btn btn-primary pull-right disabled">Save</button> 
                    <?php endif; ?> 
                    </div>
                </div>    
                              
                <br>




                <div class="panel-group" id="accordion">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                                Product Details
                                </a><i class="indicator glyphicon glyphicon-chevron-down  pull-right"></i>
                            </h4>
                        </div>
                        <div id="collapseOne" class="panel-collapse collapse in">
                            <div class="panel-body">
                               
                                <div class="col-lg-12">                                  
                                            
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Name</label>
                                        <div class="col-sm-6">          
                                            <input class="form-control required" name="entity_id" type="hidden" value="<?php echo $products_view[0]->entity_id; ?>">
                                            <input class="form-control required" name="name" type="text" value="<?php echo $products_view[0]->name; ?>">
                                        </div>
                                    </div>      

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Generic Name</label>
                                        <div class="col-sm-6">                                
                                            <input class="form-control required" name="generic_name" type="text" value="<?php echo (!empty($products_view[0]->generic_name))? $products_view[0]->generic_name: ''; ?>">
                                        </div>
                                    </div>      

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Description</label>
                                        <div class="col-sm-6">          
                                            <textarea class="form-control required" rows="10" name="description"><?php echo $products_view[0]->description; ?></textarea>
                                        </div>
                                    </div>      

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Short Description </label>
                                        <div class="col-sm-6">          
                                            <textarea class="form-control required" rows="10"  name="short_description"><?php echo $products_view[0]->short_description; ?></textarea>
                                        </div>
                                    </div>  


                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Sku</label>
                                        <div class="col-sm-6">          
                                            <input class="form-control required" name="sku" type="text" value="<?php echo $products_view[0]->sku; ?>">
                                        </div>
                                    </div>          

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Weight </label>
                                        <div class="col-sm-6">          
                                            <input class="form-control required" name="weight" type="text" value="<?php echo $products_view[0]->weight; ?>">
                                        </div>
                                    </div>                                   

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Status </label>
                                        <div class="col-sm-6">          
                                            <input class="form-control required" name="status" type="text" value="<?php echo $products_view[0]->status; ?>">
                                        </div>
                                    </div>
                                  

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Visibility  </label>
                                        <div class="col-sm-6">          
                                            <input class="form-control required" name="visibility" type="text" value="<?php echo $products_view[0]->visibility; ?>">
                                        </div>
                                    </div>                       

                           

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">MOQ </label>
                                        <div class="col-sm-6"> 
                                            <input class="form-control required" name="unilab_moq" type="text" value="<?php echo (isset($products_view[0]->unilab_moq))? $products_view[0]->unilab_moq: ''; ?>">
                                        </div>
                                    </div>     
                                            
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Price</label>
                                        <div class="col-sm-6"> 
                                            <input class="form-control required" name="unit_price" type="text" value="<?php echo $products_view[0]->price; ?>">
                                        </div>
                                    </div> 
									
									<div class="form-group">
                                        <label class="col-sm-2 control-label">Unilab Rx</label>
                                        <div class="col-sm-6"> 
                                            <input class="form-control required" name="unilab_rx" type="text" value="<?php echo $products_view[0]->unilab_rx; ?>">
                                        </div>
                                    </div> 
									
										
									<div class="form-group">
                                        <label class="col-sm-2 control-label">Unilab Benefit</label>
                                        <div class="col-sm-6"> 
                                            <input class="form-control required" name="unilab_benefit" type="text" value="<?php echo $products_view[0]->unilab_benefit; ?>">
                                        </div>
                                    </div> 
									
									<div class="form-group">
                                        <label class="col-sm-2 control-label">Unit Price</label>
                                        <div class="col-sm-6"> 
                                            <input class="form-control required" name="unilab_unit_price" type="text" value="<?php echo $products_view[0]->unilab_unit_price; ?>">
                                        </div>
                                    </div> 


                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Qty</label>
                                        <div class="col-sm-6"> 
                                            <input class="form-control required" name="qty" type="text" value="">
                                        </div>
                                    </div> 
									
									<div class="form-group">
                                        <label class="col-sm-2 control-label">Tax Class</label>
                                        <div class="col-sm-6"> 
                                            <input class="form-control required" name="tax_class_id" type="text" value="<?php echo $products_view[0]->tax_class_id; ?>">
                                        </div>
                                    </div> 


                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Images</label>
                                        <div class="col-sm-6"> 
                                            <img class="product_image" src="http://172.16.9.196/clickhealthdev/media/catalog/product<?php echo $products_view[0]->image; ?>" /> 
                                    
                                        </div>
                                    </div> 

                                   <!--  <div class="form-group fileupload fileupload-new" data-provides="fileupload">
                                        <label class="col-sm-2 control-label">Images</label>
                                        <div class="col-sm-6"> 
                                        <?php if(ccheck_form(isuser_id(),"admin/catalog/update_attributes")== true ): ?>
                                            <span class="btn btn-primary btn-file"><span class="fileupload-new">Select file</span>
                                            <span class="fileupload-exists">Change</span><input type="file" name="ImgFilename" /></span>
                                            <span class="fileupload-preview"></span>
                                            <a href="#" class="close fileupload-exists" data-dismiss="fileupload" style="float: none">×</a>
                                        <?php else: ?>
                                            
                                        <?php endif; ?> 
                                        </div> 
                                    </div> -->


                                    <div class="form-group"><label class="col-sm-2 control-label">Categories</label>
                                        <div class="col-sm-6">
                                            <select multiple="multiple" id="product_categories" name="categoryIDs[]" title="Product Type" class="form-control col-lg-12 col-md-12 col-sm-4 col-xs-12 select">
                                                <option value="">-- Select Option --</option>
                                                <?php 

                                                        $i = 0; 
                                                        foreach($root['maincat'] as $key=>$category) :
                                                            $increment = $i++; 



                                                            if($root['maincat'][$increment]['is_active'] == 1 && $root['maincat'][$increment]['value'] !=''){ 
                                                        ?>  
                                                                
                                                                <option path="<?php echo $root['maincat'][$increment]['path']; ?>" value="<?php echo $root['maincat'][$increment]['entity_id']; ?>"><?php echo $root['maincat'][$increment]['value']; ?></option>
                                                                            
                                                        <?php
                                                            } 

                                                           // echo $root['maincat'][$increment]['value'];


                                                        endforeach;


                                                    ?>
                                              
                                            </select>
                                        </div>
                                    </div> 


                                </div>          
                            </div>
                        </div>
                    </div>
                  
                </div>  

            </form>           
        </div>
    </div>
</div>   

<style type="text/css">
.panel-default>.panel-heading {
    color: #fff !important;
    font-weight: bold;
    background-color: #6f8992 !important;
 
}

.panel-heading {
    padding: 6px 15px !important;
}
</style>         

<script type="text/javascript">
$(function () {
   $("#product_categories").attr("size",$("#product_categories option").length);
});



function toggleChevron(e) {
    $(e.target)
        .prev('.panel-heading')
        .find("i.indicator")
        .toggleClass('glyphicon-chevron-down glyphicon-chevron-up');
}
$('#accordion').on('hidden.bs.collapse', toggleChevron);
$('#accordion').on('shown.bs.collapse', toggleChevron);
</script>

<script type="text/javascript">
// has selection
$('#image_has').click(function(){
    $('#image_no').prop('checked', false); 
});

$('#small_image_has').click(function(){
    $('#small_image_no').prop('checked', false); 
});

$('#thumbnail_has').click(function(){
    $('#thumbnail_no').prop('checked', false); 
});

// no selection
$('#image_no').click(function(){
    $('#image_has').prop('checked', false); 
});

$('#small_image_no').click(function(){
    $('#small_image_has').prop('checked', false); 
});

$('#thumbnail_no').click(function(){
    $('#thumbnail_has').prop('checked', false); 
});


</script>

<script type="text/javascript">

$(document).ready(function() {

    $('#getCodeModal').on('hidden.bs.modal', function () {
        setTimeout(function(){
            window.location="<?php echo base_url(); ?>index.php/products";         
        }, 1000);      

    });

    $('#update_product').formValidation({
        message: 'This value is not valid',
        icon: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            name: {
                message: 'The Name is not valid',
                validators: {
                    notEmpty: {
                        message: 'The Name is required and can\'t be empty'
                    }
                }
            },
            description: {
                message: 'The Description is not valid',
                validators: {
                    notEmpty: {
                        message: 'The Description is required and can\'t be empty'
                    }
                }
            },            
            short_description: {
                message: 'The Short Description is not valid',
                validators: {
                    notEmpty: {
                        message: 'The Short Description  is required and can\'t be empty'
                    }
                }
            },
            sku: {
                message: 'The Sku is not valid',
                validators: {
                    notEmpty: {
                        message: 'The Sku is required and can\'t be empty'
                    }
                }
            },
            weight: {
                message: 'The Weight is not valid',
                validators: {
                    notEmpty: {
                        message: 'The Weight is required and can\'t be empty'
                    }
                }
            },
            status: {
                message: 'The Status is not valid',
                validators: {
                    notEmpty: {
                        message: 'The Status is required and can\'t be empty'
                    }
                }
            },
            moq: {
                message: 'The Moq is not valid',
                validators: {
                    notEmpty: {
                        message: 'The Moq is required and can\'t be empty'
                    }
                }
            },
            unit_price: {
                message: 'The Unit Price is not valid',
                validators: {
                    notEmpty: {
                        message: 'The Unit Price is required and can\'t be empty'
                    }
                }
            },
            qty: {
                message: 'The Qty is not valid',
                validators: {
                    notEmpty: {
                        message: 'The Qty is required and can\'t be empty'
                    }
                }
            },
            categoryIDs: {
                message: 'The Category Id is not valid',
                validators: {
                    notEmpty: {
                        message: 'The Category Id is required and can\'t be empty'
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
                
                jQuery("#getCodeModal").modal('show');
                $('#myModalLabel').text('Product Information');
                $('.modal-body').text('update successfully')
            }
        });
    });
    

});

</script>