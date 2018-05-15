
<div id="page-wrapper">
    <div class="container-fluid">

    	<div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">
                    <i class="fa fa-qrcode"></i> Customer Information 
                </h1>
                <ol class="breadcrumb">
                    <li class="active">
						<a href="<?php echo base_url();?>">
							<i class="fa fa-dashboard"></i> Dashboard
						</a>
                    </li>
                    <li>
						<a href="<?php echo base_url();?>index.php/customers">
							<i class="fa fa-qrcode"></i>  <?php echo $this->uri->segment(1) ?>
						</a>
                    </li>
                    <li><strong><?php echo $this->uri->segment(2); ?></strong></li>
                </ol>

                
            </div>
        </div>

        <div class="row">
        	<div class="col-lg-12">
		
				<?php foreach($customer_information as $custinfo): ?>  
				
					<div class="tab-pane active" id="ai">
						
						<fieldset class="scheduler-border">
							<legend class="scheduler-border">Account Information</legend>	
							


							<form id="update_info" class="form-horizontal col-sm-12" action="<?php echo base_url(); ?>index.php/customers/update_customer_account" method="post">
						  		<!-- <form id="update_info" class="form-horizontal col-sm-12" action="" method="post"> -->
						  						
						  			<!-- customer info -->	
						        	<div class="col-md-9 customer-form">                     
			                        	<!-- <div id="validation-error" class="alert alert-success"></div>	 -->
			                        	<input name="entity_id" class="form-control" type="hidden" value="<?php echo $custinfo->entity_id; ?>">

			                            <div class="form-group">
			                            	<label class="col-sm-3 control-label">Associate to Website</label>		                            	
			                            	<div class="col-sm-9">
												<select id="_accountwebsite_id" name="website_id" class="form-control _accountwebsite_id">
													<option value="">-- Please Select --</option>								
													<?php foreach($website_id as $website_id_key=>$name): ?>	
														<?php if($website_id[$website_id_key]['website_id'] == $custinfo->website_id):?>												
																<option selected="selected"value="<?php echo $website_id[$website_id_key]['website_id'];?>"><?php echo $website_id[$website_id_key]['name']; ?></option>																							
														<?php else: ?>	
																<option value="<?php echo $website_id[$website_id_key]['website_id'];?>"><?php echo $website_id[$website_id_key]['name']; ?></option>											
														<?php endif; ?>	
													<?php endforeach;?>											
												</select>
											</div>
			                            </div> 
			                           	<div class="form-group"><label class="col-sm-3 control-label">Customer Group</label>				                  
											<div class="col-sm-9">
												<select id="_accountgroup_id" name="group_id" class="form-control">									
												<?php foreach($customer_group as $customer_group_id_key=>$customer_group_value): ?>												
													<?php if($customer_group[$customer_group_id_key]['customer_group_id'] == $custinfo->group_id):?>	
														<option selected="selected" value="<?php echo $customer_group[$customer_group_id_key]['customer_group_id'];?>"><?php echo $customer_group[$customer_group_id_key]['customer_group_code']; ?></option>											
													<?php else: ?>
														<option value="<?php echo $customer_group[$customer_group_id_key]['customer_group_id'];?>"><?php echo $customer_group[$customer_group_id_key]['customer_group_code']; ?></option>											
													<?php endif; ?>
												<?php endforeach;?>	
												</select>
											</div>
											<!-- <input name="disable_auto_group_change" class="required" type="checkbox" id="checkboxSuccess" value="1">Disable Automatic Group Change Based on VAT ID -->
			                            	
			                            </div>
			                           
										<div class="form-group"><label class="col-sm-3 control-label">Prefix</label>
											<div class="col-sm-9">
												<input name="prefix" class="form-control required" type="text" value="<?php echo $custinfo->prefix; ?>">
											</div>
				                        </div>	
				                        <div class="form-group"><label class="col-sm-3 control-label">First Name</label>
				                        	<div class="col-sm-9">
				                        		<input name="firstname" class="form-control required" type="text" value="<?php echo $custinfo->firstname; ?>">
				                        	</div>
				                        </div>	
				                       	
				                        <div class="form-group"><label class="col-sm-3 control-label">Middle Name/Initial</label>
				                        	<div class="col-sm-9">
				                        		<input name="middlename" class="form-control required" type="text" value="<?php echo $custinfo->middlename; ?>"></div>
			                        		</div>
				                    
										
				                        <div class="form-group"><label class="col-sm-3 control-label">Last Name</label>
				                        	<div class="col-sm-9">
				                        		<input name="lastname" class="form-control required" type="text" value="<?php echo $custinfo->lastname; ?>"></div>
			                        		</div>
			                        	<div class="form-group"><label class="col-sm-3 control-label">Suffix</label>
			                        		<div class="col-sm-9">
			                        			<input name="suffix" value="" class="form-control required" type="text" value="<?php echo $custinfo->suffix; ?>"></div>
				                    		</div>
				                    	<div class="form-group"><label class="col-sm-3 control-label">Email</label>
				                    		<div class="col-sm-9">
				                    			<input name="email" value="<?php echo $custinfo->email; ?>" class="form-control required" type="text"></div>
			                           		</div>
			                            <div class="form-group"><label class="col-sm-3 control-label">Date of Birth (YYYY-MM-DD)</label>	                            	
			                            	<div class="col-sm-9">
				                            	<div class='input-group date' id='datetimepicker1'>
								                    <input type='text' class="form-control" />
								                    <span class="input-group-addon">
								                        <span class="glyphicon glyphicon-calendar"></span>
								                    </span>
								                </div>
								            </div>
			                            </div>

			            
			                            <div class="form-group"><label class="col-sm-3 control-label">Tax/VAT Number</label>
			                            	<div class="col-sm-9">
			                            		<input name="taxvat" class="form-control email" type="text" value="<?php echo $custinfo->taxvat; ?>"></div>
			                            	</div>

			                            <div class="form-group"><label class="col-sm-3 control-label">Gender</label>
			                            	<div class="col-sm-9">
				                            	<select id="_accountgender" name="gender" class="form-control phone">
													<option value=""></option>											
													<?php $gender = [1=>'Male', 2=>'Female'];?>
													<?php foreach($gender as $gender_key=>$gender_value):?>
														<?php if($gender_key == $custinfo->gender):?>												
															<option selected="selected" value="<?php echo $gender_key;?>"><?php echo $gender_value;?></option>
														<?php else: ?>
															<option value="<?php echo $gender_key; ?>"><?php echo $gender_value;?></option>
														<?php endif;?>
													<?php endforeach;?>
												</select>
											</div>
			                            </div>
				                    	<div class="form-group"><label class="col-sm-3 control-label">Civil Status</label>
				                    		<div class="col-sm-9">
				                    			<input id="_accountcivil_status" name="status" value="<?php echo $custinfo->status; ?>" class="form-control email" type="text">
				                    		</div>
				                    	</div>           
				                    
				                    
			                            <div class="form-group"><label class="col-sm-3 control-label">Agree on Terms </label>
			                               	<div class="col-sm-9">
				                               	<select id="_accountagree_on_terms" name="agree_on_terms" class="form-control required">
													<option value="0" selected="selected">No</option>
													<option value="1">Yes</option>
												</select>
											</div>
			                            </div>


			                            <!-- division -->

			                            <div class="form-group" style="display:none-;">
			                            	<label class="col-sm-3 control-label">Division </label>   
			                            	<div class="col-sm-9">     

			                            		<?php
			                            		if (count($divisiongroup) < 1){
			                            		
			                            		}else{
			                            			$unilabdivision = $divisiongroup[0]->value;
			                            			$divisions = explode(',',$unilabdivision);

			                            	

								}//end else
			                            $unilabdivision = $customer_information[0]->unilabdivision;                       			
			                            			

			                            			$option_id = array();	
			                            			foreach($unilabdivision['option_id'] as $k=>$n){
			                            				$option_id[] = $n;
			                            			}

			                            			$unilabdivision_name = array();	
			                            			foreach($unilabdivision['unilabdivision_name'] as $l=>$m){
			                            				$unilabdivision_name[] = $m;
			                            			}

			                            			foreach ($unilabdivision_name as $num => $value): 																			
			                            					$a3[] = $unilabdivision_name[$num];			                            					
									endforeach; 

													foreach ($unilabdivision_name as $num => $value): 																			
			                            					$a1[] = $option_id[$num];			                            			
													endforeach; 	
			                            			if (count($divisiongroup)<1){
			                            		
			                            			}else{
			                            			
			                            			foreach($divisions as $division):		
			                            					$a2[] = $division;             			
			                            			endforeach; 
			                            			}

			                            		?>        	
												<select multiple="multiple" id="unilabdivision" name="unilabdivision[]" class="unilabdivision form-control col-lg-12 col-md-12 col-sm-4 col-xs-12 select">
													
			                            				<?php 

														foreach ($a1 as $a1key => $va1alue) {
				                            				if (in_array($va1alue, $a2)) { ?>

															     <option selected value="<?php echo $va1alue; ?>"><?php echo $a3[$a1key]; ?></option>

															    <?php 
															}else{

																?>
																 <option value="<?php echo $va1alue; ?>"><?php echo $a3[$a1key]; ?></option>
																<?php
															}	
				                            			}



													?>
															
													
												</select>
											</div>	
										</div>

										<div class="form-group" style="display:none-;">
			                            	<label class="col-sm-3 control-label">Group / Specialty</label>  
			                            	<div class="col-sm-9">     

			                            		<?php
								if (count($divisiongroup)<1){
								
								}else{

			                            			$unilabgroup = $divisiongroup[1]->value;
			                            			$groups = explode(',',$unilabgroup);
								}//end else
			                            			$unilabgroup = $customer_information[0]->unilabgroup;                    			


			                            			$option_id = array();	
			                            			foreach($unilabgroup['option_id'] as $k=>$n){
			                            				$option_id[] = $n;
			                            			}

			                            			$unilabgroup_name = array();	
			                            			foreach($unilabgroup['unilabgroup_name'] as $l=>$m){
			                            				$unilabgroup_name[] = $m;
			                            			}
			                            			
			                            			foreach ($unilabgroup_name as $num => $value): 																			
			                            					$a3b[] = $unilabgroup_name[$num];			                            					
													endforeach; 

													foreach ($unilabgroup_name as $num => $value): 																			
			                            					$a1b[] = $option_id[$num];			                            			
													endforeach; 	
			                            		if (count($divisiongroup)<1){
			                            		
			                            		}else{
			                            			foreach($groups as $group):		
			                            					$a2b[] = $group;             			
			                            			endforeach; 
			                            		}
			                            			


			                            			?>

												<select multiple="multiple" id="unilabgroup" name="unilabgroup[]" class="unilabgroup form-control col-lg-12 col-md-12 col-sm-4 col-xs-12 select">
													<?php 

														foreach ($a1b as $a1key => $va1alues) {
				                            				if (in_array($va1alues, $a2b)) { ?>

															     <option selected value="<?php echo $va1alues; ?>"><?php echo $a3b[$a1key]; ?></option>

															    <?php 
															}else{

																?>
																 <option value="<?php echo $va1alues; ?>"><?php echo $a3b[$a1key]; ?></option>
																<?php
															}	
				                            			}



													?>
														
													
												</select>
											</div>	
										</div>

			               
				                    	<div class="form-group">
				                    		<label class="col-sm-3 control-label">Password</label>
				                    		<div class="col-sm-9">
				                    			<input name="password" class="form-control required" type="password">
				                    		</div>                            	
				                 		</div>


										<div class="form-group">
											<label class="col-sm-3 control-label">Send Auto-Generated Password</label>
											<div class="col-sm-9"> 	
												<input name="password_hash" type="checkbox" id="checkboxSuccess" value="1">
											</div>
										</div>	
				                      	

			                      	</div>
			                      	<!-- // customer info -->		        
			                    

							
			                		<!-- customer address -->
						        	<div class="col-md-9 customer-form address-div">                     
			               
			                        	
			                        
			                        	<?php 

			                        	$address_information = $custinfo->address_information; 
		                   

			                        	foreach ($address_information as $ikey => $ivalue) {



			                        		// echo 'sdfgsd ' .$address_information[$ikey]->addrlname;			                        	
			                        	?>
			                        		<input name="addressid[<?php echo $ikey; ?>]" class="form-control" type="hidden" value="<?php echo (!empty($address_information[$ikey]->addressid))? $address_information[$ikey]->addressid:'';  ?>" >
			                       			   

			                        		<div class="form-group">
				                            	<label class="col-sm-3 control-label">First Name (<?php echo $ikey; ?>)</label>
				                            	<div class="col-sm-9">
				                            		<input class="form-control required" type="text" name="addrfname[<?php echo $ikey; ?>]" type="text" value="<?php echo (!empty($address_information[$ikey]->addrfname))? $address_information[$ikey]->addrfname:'';  ?>">
				                            	</div>	
				                           	</div>

			                        		<div class="form-group">
				                            	<label class="col-sm-3 control-label">Last Name (<?php echo $ikey; ?>)</label>
				                            	<div class="col-sm-9">
				                            		<input class="form-control required" type="text" name="addrlname[<?php echo $ikey; ?>]" type="text" value="<?php echo (!empty($address_information[$ikey]->addrlname))? $address_information[$ikey]->addrlname:'';  ?>">
				                            	</div>	
				                           	</div>

				                           	<div class="form-group">
				                            	<label class="col-sm-3 control-label">Company (<?php echo $ikey; ?>)</label>
				                            	<div class="col-sm-9">
				                            		<input class="form-control required" type="text" name="addrcompany[<?php echo $ikey; ?>]" type="text" value="<?php echo (!empty($address_information[$ikey]->addrcompany))? $address_information[$ikey]->addrcompany:'';  ?>">
				                            	</div>	
				                           	</div>
				                           	 <div class="form-group">
				                            	<label class="col-sm-3 control-label">Street Address</label>
				                            	<div class="col-sm-9">
				                            		<input class="form-control required" name="addrstreet[<?php echo $ikey; ?>]" type="text" value="<?php echo (!empty($address_information[$ikey]->addrstreet))? $address_information[$ikey]->addrstreet:'';  ?>">
				                            	</div>
				                           	</div>
				                           	 <div class="form-group">
				                            	<label class="col-sm-3 control-label">City</label>
				                            	<div class="col-sm-9">
				                            		<input class="form-control required" name="addrcity[<?php echo $ikey; ?>]" type="text" value="<?php echo (!empty($address_information[$ikey]->addrcity))? $address_information[$ikey]->addrcity:'';  ?>">
				                            	</div>
				                           	</div>
				                           	<div class="form-group">
											 	<label class="col-sm-3 control-label">Country </label>		                            	
												<div class="col-sm-9">
													<select name="country_id[<?php echo $ikey; ?>]" class="form-control">
														<option value=""> </option>
														<option value="AF">Afghanistan</option>
														<option value="PH" selected="selected">Philippines</option>
														<option value="US">United States</option>
													</select>
												</div>
											</div>
											<div class="form-group"><label class="col-sm-3 control-label">State/Province</label>		                            	
												<div class="col-sm-9">
													<select id="_item1region_id" name="address_region_id[<?php echo $ikey; ?>]" class="form-control">
														<option value="0" selected="selected">-- Please select --</option>
														<option value="485">Abra</option>
														<option value="486">Agusan del Norte</option>
														<option value="487">Agusan del Sur</option>
														<option value="488">Aklan</option>
														<option value="489">Albay</option>
														<option value="490">Antique</option>
														<option value="491">Apayao</option>
														<option value="492">Aurora</option>
														<option value="493">Basilan</option>
														<option value="494">Bataan</option>
														<option value="495">Batanes</option>
														<option value="496">Batangas</option>
														<option value="497">Benguet</option>
														<option value="498">Biliran</option>
														<option value="499">Bohol</option>
														<option value="500">Bukidnon</option>
														<option value="501">Bulacan</option>
														<option value="502">Cagayan</option>
														<option value="503">Camarines Norte</option>
														<option value="504">Camarines Sur</option>
														<option value="505">Camiguin</option>
														<option value="506">Capiz</option>
														<option value="507">Catanduanes</option>
														<option value="508">Cavite</option>
														<option value="509">Cebu</option>
														<option value="510">Compostela Valley</option>
														<option value="511">Cotabato</option>
														<option value="512">Davao del Norte</option>
														<option value="513">Davao del Sur</option>
														<option value="514">Davao Oriental</option>
														<option value="515">Dinagat Islands</option>
														<option value="516">Eastern Samar</option>
														<option value="517">Guimaras</option>
														<option value="518">Ifugao</option>
														<option value="519">Ilocos Norte</option>
														<option value="520">Ilocos Sur</option>
														<option value="521">Iloilo</option>
														<option value="522">Isabela</option>
														<option value="523">Kalinga</option>
														<option value="524">La Union</option>
														<option value="525">Laguna</option>
														<option value="526">Lanao del Norte</option>
														<option value="527">Lanao del Sur</option>
														<option value="528">Leyte</option>
														<option value="529">Maguindanao</option>
														<option value="530">Marinduque</option>
														<option value="531">Masbate</option>
														<option value="532">Metro Manila</option>
														<option value="533">Misamis Occidental</option>
														<option value="534">Misamis Oriental</option>
														<option value="535">Mountain Province</option>
														<option value="536">Negros Occidental</option>
														<option value="537">Negros Oriental</option>
														<option value="538">Northern Samar</option>
														<option value="539">Nueva Ecija</option>
														<option value="540">Nueva Vizcaya</option>
														<option value="541">Occidental Mindoro</option>
														<option value="542">Oriental Mindoro</option>
														<option value="543">Palawan</option>
														<option value="544">Pampanga</option>
														<option value="545">Pangasinan</option>
														<option value="546">Quezon</option>
														<option value="547">Quirino</option>
														<option value="548">Rizal</option>
														<option value="549">Romblon</option>
														<option value="550">Samar</option>
														<option value="551">Sarangani</option>
														<option value="552">Siquijor</option>
														<option value="553">Sorsogon</option>
														<option value="554">South Cotabato</option>
														<option value="555">Southern Leyte</option>
														<option value="556">Sultan Kudarat</option>
														<option value="557">Sulu</option>
														<option value="558">Surigao Del Norte</option>
														<option value="559">Surigao Del Sur</option>
														<option value="560">Tarlac</option>
														<option value="561">Tawi-Tawi</option>
														<option value="562">Zambales</option>
														<option value="563">Zamboanga del Norte</option>
														<option value="564">Zamboanga del Sur</option>
														<option value="565">Zamboanga Sibugay</option>
													</select>
												</div>
											</div>
											<div class="form-group">
												<label class="col-sm-3 control-label">Zip/Postal Code</label>
												<div class="col-sm-9">
													<input type="text" name="addrpostcode[<?php echo $ikey; ?>]" class="form-control" value="<?php echo (!empty($address_information[$ikey]->addrpostcode))? $address_information[$ikey]->addrpostcode:'';  ?>">
												</div>
											</div>
											<div class="form-group">
												<label class="col-sm-3 control-label">Telephone </label>
												<div class="col-sm-9">
													<input type="text" name="address_telephone[<?php echo $ikey; ?>]" class="form-control" value="<?php echo (!empty($address_information[$ikey]->addrfname))? $address_information[$ikey]->addrtelephone:'';  ?>">
												</div>
											</div>
											<div class="form-group">
												<label class="col-sm-3 control-label">Fax </label>
												<div class="col-sm-9">
													<input type="text" name="addrfax[<?php echo $ikey; ?>]" class="form-control" value="<?php echo (!empty($address_information[$ikey]->addrfname))? $address_information[$ikey]->addrfax:'';  ?>">
												</div>
											</div>
											<!-- <div class="form-group">
												<label class="col-sm-3 control-label">VAT number </label>
												<div class="col-sm-9">
													<input type="text" name="" class="form-control" value="<?php //echo (!empty($custinfo->addrfname))? $custinfo->addrvat:'';  ?>">
												</div>	
											</div>
											<div class="form-group">
												<label class="col-sm-3 control-label">Mobile </label>
												<div class="col-sm-9">
													<input type="text" name="" class="form-control" value="<?php //echo (!empty($custinfo->addrfname))? $custinfo->addrmobile:'';  ?>">
												</div>
											</div> -->

										<?php } ?>	


										<div style="clear:both;"></div>

				                      	<div class="col-md-12">
				                      	<?php if(ccheck_form(isuser_id(),"admin/customer/manage")== true ): ?>
				                      		
				                      		<button type="submit" class="btn btn-primary pull-right">Update Information</button> <p class="help-block pull-left text-danger hide" id="form-error">&nbsp; The form is not valid. </p>
											<input class="btn btn-danger pull-right" type="reset" value="Reset">
										<?php else: ?>
										
											<button type="submit" class="btn btn-primary pull-right disabled">Update Information</button> <p class="help-block pull-left text-danger hide" id="form-error">&nbsp; The form is not valid. </p>
											<input class="btn btn-danger pull-right" type="reset" value="Reset">
										<?php endif; ?>	
										</div>			

			                      	</div> 
			                      	<!-- // customer address -->

			                     	
									
							</form> 

						</fieldset>	                     	
              		
			
					</div>     
				

				<?php endforeach; ?> 


			</div>	
		</div>

		
	</div>
</div>			

<script type="text/javascript">

	$('input[name="password"]').removeAttr('disabled');
	$('input[name="password_hash"]').click(function() {
	     $('input[name="password"]').val('');	
	    $('input[name="password"]').attr('disabled',this.checked);
	});
// $(document).ready(function(){
//   $('button#update-customer').trigger('click');
// });

$('form#update_info, form#update_address').each(function() {
    var that = $(this)
  
    $.post(that.attr('action'), that.serialize());
});


$(function () {
   $("#unilabdivision").attr("size",$(".unilabdivision option").length);
   $("#unilabgroup").attr("size",$(".unilabgroup option").length);
});


$(function() {


	$('#_accountwebsite_id').mouseenter(function(){

		//$(this).attr('disabled','disabled');
	});

	

    $('.new-addr-btn').on('click', '.remove', function() {	
        $(this).closest('div[class^=div]').remove();
    });    

	$(".add-new-customer").on("click",function(){

		var firstname = $('input[name=firstname]').val();
		var lastname  = $('input[name=lastname]').val();
		var i =0;
		$(".new-addr-btn").append('');
	});


});
</script>

<script type="text/javascript">
$(document).ready(function() {
	//reload modal when closed
	$('#getCodeModal').on('hidden.bs.modal', function () {
	  	setTimeout(function(){
		   window.location.reload(1);			
		}, 500);

		// window.location="<?php echo base_url(); ?>index.php/products/add";

	});


	// Use Ajax to submit form data
    function doAJAX(urls, data){

        $.ajax({
            url: urls,
            type: 'post',
            data: data,
            success: function(result) {

            	alert( result );
            
                // jQuery("#getCodeModal").modal('show');
                // $('#myModalLabel').text('Account Information');
                // $('.modal-body').text('updated successfully')
               
            }
        });
    }


    $('#update_info').formValidation({
        message: 'This value is not valid',
        icon: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
         fields: {
            firstname: {
                message: 'The firstname is not valid',
                validators: {
                    notEmpty: {
                        message: 'The First Name is required and can\'t be empty'
                    }
                }
            },
            lastname: {
                message: 'The lastname is not valid',
                validators: {
                    notEmpty: {
                        message: 'The Last Name is required and can\'t be empty'
                    }
                }
            },
            email: {
                message: 'The Email is not valid',
                validators: {
                    notEmpty: {
                        message: 'The Email is required and can\'t be empty'
                    }
                }
            },
            dob: {
                message: 'The Birthdate is not valid',
                validators: {
                    notEmpty: {
                        message: 'The Birthdate is required and can\'t be empty'
                    }
                }
            },
            // gender: {
            //     message: 'The Gender is not valid',
            //     validators: {
            //         notEmpty: {
            //             message: 'The Gender is required and can\'t be empty'
            //         }
            //     }
            // },
            civil_status: {
                message: 'The Civil status is not valid',
                validators: {
                    notEmpty: {
                        message: 'The Civil status is required and can\'t be empty'
                    }
                }
            },
            // password: {
            //     message: 'The Password is not valid',
            //     validators: {
            //         notEmpty: {
            //             message: 'The Password is required and can\'t be empty'
            //         }
            //     }
            // },
            address_street: {
                message: 'The Street is not valid',
                validators: {
                    notEmpty: {
                        message: 'The street is required and can\'t be empty'
                    }
                }
            },
            address_city: {
                message: 'The City is not valid',
                validators: {
                    notEmpty: {
                        message: 'The City is required and can\'t be empty'
                    }
                }
            },
            address_postcode: {
                message: 'The Postcode is not valid',
                validators: {
                    notEmpty: {
                        message: 'The Postcode is required and can\'t be empty'
                    }
                }
            }
	        }
    })
    .on('success.form.fv',function(e) {
        // Prevent form submission
        e.preventDefault();

    	var $form = $(e.target),
            fv    = $form.data('formValidation');

        // Use Ajax to submit form data
        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: $form.serialize(),
            success: function( result ) {
                // ... Process the result ...

                // console.log( result );

                //alert($form.attr('action'));
                
                jQuery("#getCodeModal").modal('show');
                $('#myModalLabel').text('Account Information');
                $('.modal-body').text('updated successfully');
                //window.location="<?php echo base_url(); ?>index.php/products/add";
            }
        });
    });


    //Address
    // $('#update_address').formValidation({
    //     message: 'This value is not valid',
    //     icon: {
    //         valid: 'glyphicon glyphicon-ok',
    //         invalid: 'glyphicon glyphicon-remove',
    //         validating: 'glyphicon glyphicon-refresh'
    //     },
    //     fields: {
    //         address_street: {
    //             message: 'The Street is not valid',
    //             validators: {
    //                 notEmpty: {
    //                     message: 'The street is required and can\'t be empty'
    //                 }
    //             }
    //         },
    //         address_city: {
    //             message: 'The City is not valid',
    //             validators: {
    //                 notEmpty: {
    //                     message: 'The City is required and can\'t be empty'
    //                 }
    //             }
    //         },
    //         address_postcode: {
    //             message: 'The Postcode is not valid',
    //             validators: {
    //                 notEmpty: {
    //                     message: 'The Postcode is required and can\'t be empty'
    //                 }
    //             }
    //         }
    //     }
    // })
    // .on('success.form.fv', function(e) {
    //     // Prevent form submission
    //     e.preventDefault();

    // 	var $form = $(e.target),
    //         fv    = $form.data('formValidation');

    //     // Use Ajax to submit form data
    //     $.ajax({
    //         url: $form.attr('action'),
    //         type: 'POST',
    //         data: $form.serialize(),
    //         success: function(result) {
    //             // ... Process the result ...

    //             jQuery("#getCodeModal").modal('show');
    //             $('#myModalLabel').text('Address');
    //             $('.modal-body').text('updated successfully')

    //             // window.location="<?php echo base_url(); ?>index.php/products/add";
    //         }
    //     });
    // });



});
</script>


<style type="text/css">
.none{
	display: none;
}

.customer-form .form-group{
	padding: 0px !important;
}


.address-div{
	padding-bottom: 30px;
}

.form-control{
	border: 1px solid #dcdcdc;
	border-radius: 0;
}

</style>