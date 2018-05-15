<?php ////echo '<pre>'; print_r($customer_information);?>
<?php //print_r($website_id);?>
<?php //echo '<pre>'; print_r($categories);?>
<?php //echo '<pre>'; 

//print_r($menulevel);



//$m = 0;
//foreach($menulevel as $mkey=>$mVal){
	
	//echo $menulevel[$mkey]['children']; 
	//print_r($mVal);
//}


 

?>

<?php //echo $categories[0]['entity_id']; ?>




<?php 
//category_tree(174);
//$catid = 174; 

//function category_tree($catid){
	//foreach($menulevel as $k=>$menu){
		
		//echo $menulevel[$k]->entity_id;
		
		//echo '<ul>';
			//echo '<li title="'.$menulevel[$k]->entity_id.'">' . $menulevel[$k]->name; //$row->path
				//category_tree($menulevel[$k]->entity_id);
			//echo '</li>';	
		
		//echo '</ul>';
	//}
//}	
//echo $menulevel[0]->entity_id;





?>



<script>

//alert(2345);
/*
category_tree(174);

function category_tree(catid){
	alert(catid);
	
	
	
}*/

</script>




<div id="page-wrapper">
    <div class="container-fluid">
		
		
    	<div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">
                    <i class="fa fa-qrcode"></i> Categories Update
                </h1>
                <ol class="breadcrumb">
                    <li class="active">
						<a href="<?php echo base_url();?>">
							<i class="fa fa-dashboard"></i> Dashboard
						</a>
                    </li>
                    <li>
						<a href="<?php echo base_url();?>index.php/categories">
							<i class="fa fa-qrcode"></i>  <?php echo $this->uri->segment(1) ?>
						</a>
                    </li>
                    <li><strong><?php //echo $this->uri->segment(2); ?></strong></li>
                </ol>

                
            </div>
        </div>

		<!-- tab pane -->
        <div class="row">
        	<div class="col-md-3">
			

				<div class="showcat"></div>
				
			</div>

			
				

				
			
			
			<div class="col-md-9" id="content-form">
				<div class="row">
					<form id="category_update" class="form-horizontal" action="<?php echo base_url() ?>index.php/categories/category_update" method="post">   

						<!-- <div class="col-md-12">New Subcategory for
							<span id="parent-name">
								<input style="border:0;" type="text" id="catname" name="catname" value="">
							</span>

						</div> -->
						<div class="col-md-8">							
								
								<input class="form-control required" id="parent_id" name="parent_id" type="hidden" value="<?php echo $categories_update[0]['parent_id']; ?>">
						
						
								<input class="form-control required" id="path" name="path" type="hidden" value="<?php echo $categories_update[0]['path']; ?>">
								<!-- <input class="form-control required" id="is_active" name="is_active" type="hidden" value="1"> -->							
					
								<input class="form-control required" id="include_in_menu" name="include_in_menu" type="hidden" value="1">
						
								<input class="form-control required" id="is_anchor" name="is_anchor" type="hidden" value="1">	

								<input class="form-control required" id="entity_id" name="entity_id" type="hidden" value="<?php echo $categories_update[0]['entity_id']; ?>">					

								<div class="form-group">
									<label class="col-md-3 control-label">&nbsp;</label>
									<div class="col-md-9"><?php echo $categories_update[0]['value']; ?> (ID: <?php echo $categories_update[0]['entity_id']; ?>)</div>
								</div>	

								<div class="form-group" style="display:none;">
									<label class="col-md-3 control-label">New Subcategory for</label>
									<div class="col-md-9">

										<select disabled="disabled" id="catname" name="catname" class="form-control select">
										<option value="">-- select name to create category --</option>
										<?php 
											$i = 0; 
											foreach($categories as $key=>$category) :
	        									$increment = $i++; 
	        									
	        									if($categories[$increment]['is_active'] == 1 && $categories[$increment]['value'] !==''){ 
        								?>	
													<?php if($categories[$increment]['entity_id'] == $categories_update[0]['entity_id']){ ?>
														<option selected="selected" path="<?php echo $categories[$increment]['path']; ?>" value="<?php echo $categories[$increment]['entity_id']; ?>"><?php echo $categories[$increment]['value']; ?></option>
												    
												    <?php }else{?>	      
												    	<option  path="<?php echo $categories[$increment]['path']; ?>" value="<?php echo $categories[$increment]['entity_id']; ?>"><?php echo $categories[$increment]['value']; ?></option>
												    

												    <?php } ?>      
													
											<?php
												} 

        								endforeach; ?>		  
										</select>

										<script type="text/javascript">
										$('select[name=catname]').change(function() { 
											//alert( $('option:selected', this).attr('path') ); 

											$('#parent_id').attr('value',$(this).val());
											$('#path').attr('value', $('option:selected', this).attr('path'));
										});

										</script>
									</div>
								</div> 			
								
								<div class="form-group">
									<label class="col-md-3 control-label">Name</label>
									<div class="col-md-9">
										<input class="form-control required" id="name" name="name" type="text" value="<?php echo $categories_update[0]['value']; ?>">
									</div>
								</div>    

								<div class="form-group">
									<label class="col-md-3 control-label">Is Active</label>
									<div class="col-md-9">
										<select id="is_active" name="is_active" class="form-control select">
										<?php foreach(array('No','Yes') as $optkey=>$optValue): ?>	

											<?php if($optkey == $categories_update[0]['is_active']): ?>	
										 		<option selected="selected" value="<?php echo $optkey; ?>"><?php echo $optValue; ?></option>

										 	<?php else:?>

										 		<option value="<?php echo $optkey; ?>"><?php echo $optValue; ?></option>
										 	<?php endif; ?>

									 	<?php endforeach; ?>									          
										  
										</select>
									</div>
								</div> 

								<div class="form-group">
									<label class="col-md-3 control-label">Description </label>
									<div class="col-md-9">          
										<textarea class="form-control required" rows="10"  name="description"><?php echo $categories_update[0]['description']; ?></textarea>
									</div>
								</div>  


								<div class="form-group">
									<div class="col-md-3"></div>
									<div class="col-md-9">
										<button id="btn-category-update" type="submit" class="btn btn-primary btn-small">Update Category</button>
										<a href="javascript:(void)" id="btn-category-delete" del="<?php echo $categories_update[0]['entity_id']; ?>" type="submit" class="btn btn-danger btn-small">Delete Category</a>
									
									</div>
								</div>        
							
			 
						</div> 
					</form>   		
	            </div> 

				
			</div>
		</div>
	</div>
</div>	




<style type="text/css">
.subcat{

	font-size: 9px;
	color: orange;
	text-decoration: underline;
	font-weight: bold;
	text-transform: uppercase;
	font-family: inherit;
}

.subcat:hover{color: red;}
</style>


<script type="text/javascript">
$(function() {

    $('.new-addr-btn').on('click', '.remove', function() {
        $(this).closest('div[class^=div]').remove();
    });    

	$(".add-new-customer").on("click",function(){

		var firstname = $('input[name=firstname]').val();
		var lastname  = $('input[name=lastname]').val();
		var i =0;
		$(".new-addr-btn").append('');
	});

	
	var pid = <?php echo $parentid; ?>

	//2345
	$('div.showcat').each(function(x, y){		
		$(this).css('border','1px solid #dcdcdc', 'list-style:none;');		
		$(this).find('ul.categoryTree').find('li > a').each(function(a, b){		
			
			var parent_li = $(this).parent('li');					
			var checkId = parent_li.attr('id');			
			if(checkId == pid){		
				
				parent_li.find('div').removeAttr('style');
			}else{		
				
				parent_li.parent('ul.categoryTree').each(function(f,g){
				
					var style = $(this).find('div.menulevel').attr('style', 'display:blank;');
					if(style == 'display:none;'){						
						//console.log($(this).hide());
					}
				});								
			}
			
			$(this).click(function(e){			

				//e.preventDefault;
				//alert(this.id);
				window.location.href = "<?php echo base_url(); ?>index.php/categories/category_update_view/"+this.id;
				// var categoryid = this.id;
				// $("[name=categoryid]").val(categoryid);
				// $("[name=categoryid]").attr("action","<?php echo base_url(); ?>index.php/categories/category_update");
				// $("[name=categoryid]").submit();		

				// console.log('afaf: ' +this.title);
				// var name_value = this.title;

				// $('#parent_id').val( $(this).attr('entity_id') );
				// $('#path').val( $(this).attr('path') );

				// var helper = $('#catname');
				// var catname  = $('#catname').attr('value', $('#catname').attr('title') );
				// $('#catname').parent('span').children('i').attr('class', 'form-control-feedback fv-icon-no-label glyphicon glyphicon-ok');

				
				
				// if(catname.val() ===''){
				// 	// console.log('1 ' +$('#catname').attr('value',this.title));
				// 	// $('#catname').parent('span').children('i').attr('class', 'form-control-feedback fv-icon-no-label glyphicon glyphicon-ok');

				// // }else{				

				// 	console.log('2 ');

				// 	helper.val(name_value);//.trigger('click');
				// 	helper.parent('span').children('i').attr('class', 'form-control-feedback fv-icon-no-label glyphicon glyphicon-ok');
				// 	helper.parent('span').children('i').attr('style', 'display: block;');
				// 	$('small').attr('data-fv-result', 'VALID');
				// 	$('small').attr('style', 'display: none;');
					
				// 	$('#btn-category').removeAttr('disabled');
				// 	$('#btn-category').attr('class','btn btn-primary btn-small');
				// }

				
				
			});
		});		
	});
});
			
var m = $('.showcat').html($('.menulevel').html());				
m.each(function(i, n){					
	//$(this,'ul > li').find('div').removeAttr('style');						
	
});


//validation

/*
var parent_id = $('#name').val();

if( parent_id == '' ) {
	$('#btn-category').hide();
}else{
	$('#btn-category').show();
}
*/
//$('#btn-category').hide();

/*	
$('#add_category').find('input').each(function(i, index){
	
	console.log('aaf ' + i, index);
	
	$('#parent_id, #name').keyup(function(){
		
		if( $(this).val() == '' ) {
			$('#btn-category').hide();
		}else{
			$('#btn-category').show();
		}
	});	
	
});
*/	
	
	
//reload modal when closed
$('#getCodeModal').on('hidden.bs.modal', function () {
	setTimeout(function(){
	   window.location.reload(1);			
	}, 500);
});

$('#category_update').formValidation({
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
		catname: {
			message: 'The category name is not valid',
			validators: {
				notEmpty: {
					message: 'The category name is required and can\'t be empty'
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

			jQuery("#getCodeModal").modal('show');
			$('#myModalLabel').text('Categories ');
			$('.modal-body').text('update successfully')

			// window.location="<?php echo base_url(); ?>index.php/products/add";
		}
	});
}); 

//delete 

$('#btn-category-delete').click(function(e){
	
	e.preventDefault();

	var dataObject = {
		'entity_id': $('#btn-category-delete').attr('del')	
	}	
		
	$.ajax({
		url: "<?php echo get_baseurl() ?>/adminuser/index.php/categories/delete_categroy",   
		type: 'post',  
		asynchronous: true,
        cashe: false,
		data: dataObject, 
		beforeSend: function() {
			var sku = dataObject['sku'];
			var x = confirm("Are you sure do you want to delete this (ID: "+$('#btn-category-delete').attr('del')+") ?");
			if (x){
			    return true;
			}
			else{

				return false;
			}	
		},
		success: function(data) 
		{     
			console.log(data);
			window.location="<?php echo base_url(); ?>index.php/categories";
		}
	});
	
	
});
			
</script>

