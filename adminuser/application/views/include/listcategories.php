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
                    <i class="fa fa-qrcode"></i> Categories
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
			
        		<?php //echo $parentid; ?><!-- SELECT * FROM catalog_category_entity_varchar WHERE value = 'athena' -->
				<div class="showcat"></div>
				
			</div>

			
				

				
			
			
			<div class="col-md-9" id="content-form">
				<div class="row">
					<form id="add_category" class="form-horizontal" action="<?php echo base_url() ?>index.php/categories/add_category" method="post">   

						<!-- <div class="col-md-12">New Subcategory for
							<span id="parent-name">
								<input style="border:0;" type="text" id="catname" name="catname" value="">
							</span>

						</div> -->
						<div class="col-md-8">							
								
								<input class="form-control required" id="parent_id" name="parent_id" type="hidden" value="">
						
						
								<input class="form-control required" id="path" name="path" type="hidden" value="">
								<!-- <input class="form-control required" id="is_active" name="is_active" type="hidden" value="1"> -->							
					
								<input class="form-control required" id="include_in_menu" name="include_in_menu" type="hidden" value="1">
						
								<input class="form-control required" id="is_anchor" name="is_anchor" type="hidden" value="1">					



								<div class="form-group">
									<label class="col-md-3 control-label">New Subcategory for</label>
									<div class="col-md-9">

										<select id="catname" name="catname" class="form-control select">
										<option value="">-- select name to create category --</option>
										<?php 
											$i = 0; 
											foreach($categories as $key=>$category) :
	        									$increment = $i++; 
	        									
	        									if($categories[$increment]['is_active'] == 1 && $categories[$increment]['value'] !=''){ 
        								?>	
													
													<option path="<?php echo $categories[$increment]['path']; ?>" value="<?php echo $categories[$increment]['entity_id']; ?>"><?php echo $categories[$increment]['value']; ?></option>
												                
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
										<input class="form-control required" id="name" name="name" type="text" value="">
									</div>
								</div>    

								<div class="form-group">
									<label class="col-md-3 control-label">Is Active</label>
									<div class="col-md-9">
										<select id="is_active" name="is_active" class="form-control select">
											<option value="0">No</option>
											<option value="1">Yes</option>                  
										  
										</select>
									</div>
								</div> 

								<div class="form-group">
									<label class="col-md-3 control-label">Description </label>
									<div class="col-md-9">          
										<textarea class="form-control required" rows="10"  name="description"></textarea>
									</div>
								</div>  


								<div class="form-group">
									<div class="col-md-3"></div>
									<div class="col-md-9">
										 <button id="btn-category" type="submit" class="btn btn-primary btn-small">Save Category</button>
									</div>
								</div>        
							
			 
						</div> 
					</form>   		
	            </div> 

				
			</div>
		</div>
	</div>
</div>	

<!-- test -->


<!-- <ul class="nav nav-list-main">
  	<li><label class="nav-toggle nav-header"><span class="nav-toggle-icon glyphicon glyphicon-chevron-right"></span> <a href="javascript:void(0)">Header 1</a></label>
        <ul class="nav nav-list nav-left-ml">
            <li><a href="#">Link</a></li>
            <li><a href="#">Link</a></li>
            <li><label class="nav-toggle nav-header"><span class="nav-toggle-icon glyphicon glyphicon-chevron-right"></span> <a href="http://www.google.com/">Header 1.1</a></label>
                <ul class="nav nav-list nav-left-ml">
                    <li><a href="#">Link</a></li>
                    <li><a href="#">Link</a></li>
                    <li><label class="nav-toggle nav-header"><span class="nav-toggle-icon glyphicon glyphicon-chevron-right"></span> <a href="http://www.google.com/">Header 1.1.1</a></label>
                        <ul class="nav nav-list nav-left-ml">
                            <li><a href="#">Link</a></li>
                            <li><a href="#">Link</a></li>
                        </ul>
                    </li>
                </ul>
            </li>
        </ul>
    </li> -->
 	<!--    
 	<li class="nav-divider"></li>
    <li><label class="nav-toggle nav-header"><span>Header 2</span></label>
        <ul class="nav nav-list nav-left-ml">
            <li><a href="#">Link</a></li>
            <li><a href="#">Link</a></li>
            <li><label class="nav-toggle nav-header"><span>Header 2.1</span></label>
                <ul class="nav nav-list nav-left-ml">
                    <li><a href="#">Link</a></li>
                    <li><a href="#">Link</a></li>
                  <li><label class="nav-toggle nav-header"><span>Header 2.1.1</span></label>
                        <ul class="nav nav-list nav-left-ml">
                            <li><a href="#">Link</a></li>
                            <li><a href="#">Link</a></li>
                        </ul>
                    </li>
                </ul>
            </li>
          <li><label class="nav-toggle nav-header"><span>Header 2.2</span></label>
                <ul class="nav nav-list nav-left-ml">
                    <li><a href="#">Link</a></li>
                    <li><a href="#">Link</a></li>
                  <li><label class="nav-toggle nav-header"><span>Header 2.2.1</span></label>
                        <ul class="nav nav-list nav-left-ml">
                            <li><a href="#">Link</a></li>
                            <li><a href="#">Link</a></li>
                        </ul>
                    </li>
                </ul>
            </li>
        </ul>
    </li> -->
<!-- </ul> -->

<script type="text/javascript">
$('ul.nav-left-ml').toggle();
$('label.nav-toggle span').click(function () {
  $(this).parent().parent().children('ul.nav-left-ml').toggle(300);
  var cs = $(this).attr("class");
  if(cs == 'nav-toggle-icon glyphicon glyphicon-chevron-right') {
    $(this).removeClass('glyphicon-chevron-right').addClass('glyphicon-chevron-down');
  }
  if(cs == 'nav-toggle-icon glyphicon glyphicon-chevron-down') {
    $(this).removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-right');
  }
});
</script>

<!-- // -->


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



// $('.showcat').find('ul').each(function(i, n){

// 	console.log(this.id);
// });













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

	//alert( pid );

	//2345
	$('div.showcat').each(function(x, y){		

		$(this).find('ul.categoryTree').find('li').each(function(a, b){


		});	



		$(this).css('border','1px solid #dcdcdc', 'list-style:none;');		
		$(this).find('ul.categoryTree').find('li > a').each(function(a, b){		

			// console.log(this.id);

			// if(this.id == pid){

			// 	$(this).parent().parent().each(function(){


			// 	});


			// 	//.css( {'background':'#dcdcdc'} );
			// 	//$(this).parent().parent().find('li > div > ul').css( {'background':'yellow'} ).show();
			// 	//.each(function(){
			// 	//	console.log( $(this).css( {'background':'#dcdcdc'} ) );

			// 	//});
			// }else{
			// 	$(this).parent().parent().css({'background':'lavender'}).hide();
			// }
			
			var parent_li = $(this).parent('li');					
			var checkId = parent_li.attr('id');			
			if(checkId == pid){		
				
				parent_li.find('div').removeAttr('style');
			}else{		
				
				parent_li.parent('ul.categoryTree').each(function(f,g){
				
					var style = $(this).find('div.menulevel').attr('style','display:blank;'); //
					if(style == 'display:none;'){						
						console.log($(this).hide());
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

$('#add_category').formValidation({
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
			$('.modal-body').text('add successfully')

			// window.location="<?php echo base_url(); ?>index.php/products/add";
		}
	});
});
			
</script>


<script type="text/javascript">
/* Create Menu sub menu linking */
var category_listing = $(".showcat");
if(category_listing.length > 0){

	// Prevent the linking
	category_listing.find("a").click(function(event) {
		event.preventDefault();
	});


	category_listing.find("ul li").click(multiLevelClickHandler);
}

// The multi-level menu click handler
function multiLevelClickHandler(event)
{
	event.preventDefault();
    event.stopPropagation();
	var $this = $(this);
	console.log($this);
	var $actives = $this.siblings().find(".active");

	if($actives.length > 0){
		$actives.removeClass('active');
		setTimeout(function(){
			$this.addClass('active');
		}, 250);
	} else {
		$this.addClass('active');
	}
}

</script>

