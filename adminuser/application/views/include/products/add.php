<?php //echo '<pre>'; print_r($customer_information);?>
<?php //print_r($website_id);?>
<?php //print_r($customer_group);?>
<?php 	//echo '<pre>'; print_r($settings); die(); ?>

<?php 

// echo '<pre>';

// print_r(array_merge($root['maincat'], $settings['subcat']));

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

    	<div class="row">



           <form id="prod_data" name="prod_data" class="form-horizontal" action="<?php echo get_baseurl() ?>/index.php/default/connectmage/index" method="post" enctype="multipart/form-data">
           <!-- <form id="prod_data-" class="form-horizontal" action="<?php echo base_url() ?>index.php/products/add_products" method="post">  -->

                    
                <div class="row">    
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary pull-right">Save Product</button>
                        <!-- <button type="submit" class="btn btn-primary pull-right">continue</button> <p class="help-block pull-left text-danger hide" id="form-error">&nbsp; The form is not valid. </p> -->
                                
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
                                    
                                    <input class="form-control required" name="attributeid" type="hidden" value="<?php echo $settings['set']; ?>">
                                    <!-- <input class="form-control required" name="type" type="hidden" value="<?php echo $settings['type']; ?>"> -->
                                    <input class="form-control required" name="cmdEvent" type="hidden" value="newProdCmd"> 
                                    <input class="form-control required" name="websiteids[]" type="hidden" value="<?php echo $settings['websiteids']; ?>">
                                    <!-- <input class="form-control required" name="categoryIDs[]" type="hidden" value="<?php echo ($settings['set'] == 11 || $settings['set'] == 15 )? 1: 0; ?>"> -->
                                       

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Name</label>
                                        <div class="col-sm-6">
                                            <input class="form-control required" id="name" name="name" type="text" value="">
                                        </div>
                                    </div>      

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Generic Name</label>
                                        <div class="col-sm-6">                                
                                            <input class="form-control required" name="generic_name" type="text" value="">
                                        </div>
                                    </div>      

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Description</label>
                                        <div class="col-sm-6">          
                                            <textarea class="form-control required" rows="10" name="description"></textarea>
                                        </div>
                                    </div>      

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Short Description </label>
                                        <div class="col-sm-6">          
                                            <textarea class="form-control required" rows="10"  name="short_description"></textarea>
                                        </div>
                                    </div>  


                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Sku</label>
                                        <div class="col-sm-6">          
                                            <input class="form-control required" name="sku" type="text" value="">
                                        </div>
                                    </div>          

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Weight </label>
                                        <div class="col-sm-6">          
                                            <input class="form-control required" name="weight" type="text" value="">
                                        </div>
                                    </div>                                   

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Status </label>
                                        <div class="col-sm-6">          
                                            <input class="form-control required" name="status" type="text" value="">
                                        </div>
                                    </div>
                                  

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Visibility  </label>
                                        <div class="col-sm-6">          
                                            <input class="form-control required" name="visibility" type="text" value="">
                                        </div>
                                    </div>                     
                                
                             

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">MOQ </label>
                                        <div class="col-sm-6"> 
                                            <input class="form-control required" name="unilab_moq" type="text" value="">
                                        </div>
                                    </div>     
                                            
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Price</label>
                                        <div class="col-sm-6"> 
                                            <input class="form-control required" name="unit_price" type="text" value="">
                                        </div>
                                    </div> 
									
									<div class="form-group unilab_rx">
                                        <label class="col-sm-2 control-label">Unilab Rx</label>
                                        <div class="col-sm-6"> 
                                            <input class="form-control required" name="unilab_rx" type="text" value="<?php echo ($settings['set'] == 11 || $settings['set'] == 15 )? 1: 0; ?>">
                                       
                                        </div>
                                    </div> 
									
										
									<div class="form-group">
                                        <label class="col-sm-2 control-label">Unilab Benefit</label>
                                        <div class="col-sm-6"> 
                                            <input class="form-control required" name="unilab_benefit" type="text" value="">
                                        </div>
                                    </div> 

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Unit Price</label>
                                        <div class="col-sm-6"> 
                                            <input class="form-control required" name="unilab_unit_price" type="text" value="">
                                        </div>
                                    </div> 
									
									<div class="form-group">
                                        <label class="col-sm-2 control-label">Unit Price</label>
                                        <div class="col-sm-6"> 
                                            <input class="form-control required" name="unit_price" type="text" value="">
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
                                            <input class="form-control required" name="tax_class_id" type="text" value="">
                                        </div>
                                    </div> 



                                  <!--   <div class="form-group">
                                        <label class="col-sm-2 control-label">Image</label>
                                        <div class="col-sm-6"> 
                                            <input class="form-control required" name="ImgFilename" type="text" value="1/0/10243-1.png">
                                        </div>
                                    </div>  -->

                                  <!--   <div class="form-group">
                                        <label class="col-sm-2 control-label">Images</label>
                                        <div class="col-sm-6"> 
                                            <img class="product_image" src="http://172.16.9.196/clickhealthdev/media/catalog/product<?php //echo $products_view[0]->image; ?>" /> 
                                    
                                        </div>
                                    </div>  -->


                                    <div class="form-group fileupload fileupload-new" data-provides="fileupload">
                                        <label class="col-sm-2 control-label">Images</label>
                                        <div class="col-sm-6"> 
                                            <span class="btn btn-primary btn-file"><span class="fileupload-new">Select file</span>
                                            <span class="fileupload-exists">Change</span><input type="file" name="ImgFilename" /></span>
                                            <span class="fileupload-preview"></span>
                                            <a href="#" class="close fileupload-exists" data-dismiss="fileupload" style="float: none">×</a>
                                        </div> 
                                    </div>


                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Categories</label>
                                        <div class="col-sm-6">
                                            <select multiple="multiple" id="product_categories" name="categoryIDs[]" title="Product Type" class="form-control select">
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

                            <!-- <div style="clear:both;"></div>

                            <div class="modal-footer">
                            
                                <button type="button" class="btn btn-primary">Save Product</button>
                                
                            </div>   -->
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
    $(e.target).prev('.panel-heading').find("i.indicator").toggleClass('glyphicon-chevron-down glyphicon-chevron-up');
}
$('#accordion').on('hidden.bs.collapse', toggleChevron);
$('#accordion').on('shown.bs.collapse', toggleChevron);
</script>

<script type="text/javascript">

$(document).ready(function() {

    $('#getCodeModal').on('hidden.bs.modal', function () {
        setTimeout(function(){
            window.location="<?php echo base_url(); ?>index.php/products";         
        }, 1000);      

    });

    $('#prod_data').formValidation({
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
            ImgFilename: {
                message: 'The Image is not valid',
                validators: {
                    notEmpty: {
                        message: 'The Image is required and can\'t be empty'
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
                $('.modal-body').text('added successfully')
            }
        });
    });
    

});

</script>

