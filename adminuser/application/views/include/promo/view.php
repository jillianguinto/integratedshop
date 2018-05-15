<div id="page-wrapper">
    <div class="container-fluid">

    	<div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">
                    <i class="fa fa-qrcode"></i> Promo Information 
                </h1>
                <ol class="breadcrumb">
                    <li class="active">
						<a href="<?php echo base_url();?>">
							<i class="fa fa-dashboard"></i> Dashboard
						</a>
                    </li>
                    <li>
						<a href="<?php echo base_url();?>index.php/promotions">
							<i class="fa fa-qrcode"></i>  <?php echo $this->uri->segment(1) ?>
						</a>
                    </li>
                    <li><strong><?php echo $this->uri->segment(2); ?></strong></li>
                </ol>

                
            </div>
        </div>

		<!-- tab pane -->
        <div class="row">
        	<div class="col-lg-12">
				<ul class="nav nav-tabs" id="tabContent">
				    <li class="active"><a href="#ai" data-toggle="tab">Rule Information</a></li>	       
				</ul>
				<?php foreach($promo_info as $pinfo): ?>  
				<div class="tab-content"> 
					<div class="tab-pane active" id="ai">

						<form id="update_promo" class="form-horizontal col-sm-12" action="<?php echo base_url(); ?>index.php/promotions/update_promo" method="post">
				        	<div class="col-md-9 update-form">                     
	                        	<div id="validation-error" class="alert alert-success"></div>	
	                        	<br>
	                            <div class="form-group"><label class="col-sm-2 control-label">Rule Name</label> 
	                            	<div class="col-sm-10">
	                            		<input name="rid" class="form-control" type="hidden" value="<?php echo $pinfo->rule_id; ?>">
	                            		<input name="rrname" class="form-control" type="hidden" value="<?php echo $pinfo->name; ?>">
	                            		<input class="form-control required" name="rname" type="text" value="<?php echo $pinfo->name; ?>">
	                            		<label style="float:right;">active
	                            		<?php 
	                            		if ($pinfo->is_active=="1"){
	                            			echo '<input type="checkbox" style="float:left;" name="cb" checked/>';
	                            		}else{
	                            			echo '<input type="checkbox" style="float:left;" name="cb"/>';
	                            		}
	                            		?>
	                            		
	                            		</label>
	                            	</div>
	                            </div>

	                            <div class="form-group"><label class="col-sm-2 control-label">Rule Description</label>
	                            	<div class="col-sm-10">
	                            		<input class="form-control required" name="rdesc" type="text" value="<?php echo $pinfo->description; ?>">
	                            	</div>
	                           	</div>

	                           	<div class="form-group"><label class="col-sm-2 control-label">Customer Group</label>				                  
									<div class="col-sm-10">
										<?php //print_r($customer_group); ?>
										<select id="_accountgroup_id" name="group_id[]" class="form-control" multiple>									
										<?php 
										$ugroup = array();
										foreach ($groupsumm as $key) {
										$ugroup[]= $key->customer_group_id;
										//echo $key->customer_group_id;
										}
										//print_r($ugroup);
										
										foreach($customer_group as $customer_group_id_key=>$customer_group_value): ?>												
											<?php 
											//echo $customer_group[$customer_group_id_key]['customer_group_id'];
											
											//if($customer_group[$customer_group_id_key]['customer_group_id'] == $pinfo->customer_group_id):
											if (in_array($customer_group[$customer_group_id_key]['customer_group_id'], $ugroup)):
											?>	
												<!--<option selected="selected" value="<?php echo $customer_group[$customer_group_id_key]['customer_group_id'];?>"><input type="checkbox" name="cb[]" checked/><?php echo $customer_group[$customer_group_id_key]['customer_group_code']; ?></option>-->
												<option selected="selected" value="<?php echo $customer_group[$customer_group_id_key]['customer_group_id'];?>"><?php echo $customer_group[$customer_group_id_key]['customer_group_code']; ?></option>											
												
											<?php else: ?>
												<option value="<?php echo $customer_group[$customer_group_id_key]['customer_group_id'];?>"><!--<input type="checkbox" name="cb[]"/>--><?php echo $customer_group[$customer_group_id_key]['customer_group_code']; ?></option>										<!--<option value="<?php //echo $customer_group[$customer_group_id_key]['customer_group_id'];?>"><?php //echo $customer_group[$customer_group_id_key]['customer_group_code']; ?></option>-->											
											<?php endif; ?>
										<?php endforeach;
											
										?>	
										</select>
									</div>
	                            </div>

	                            <div class="form-group"><label class="col-sm-2 control-label">From Date</label>	                            	
	                            	<div class="col-sm-10">
		                            	<div class='input-group date' id='datetimepicker1'>
						                    <input id="datepicker" type='text' class="form-control" name="datepicker1" value="<?php echo $pinfo->from_date; ?>"/>
						                    <span class="input-group-addon">
						                        <span class="glyphicon glyphicon-calendar"></span>
						                    </span>
						                </div>
						            </div>
	                            </div>

	                            <div class="form-group"><label class="col-sm-2 control-label">To Date</label>	                            	
	                            	<div class="col-sm-10">
		                            	<div class='input-group date' id='datetimepicker1'>
						                    <input id="datepicker2" type='text' class="form-control" name="datepicker2" value="<?php echo $pinfo->to_date; ?>"/>
						                    <span class="input-group-addon">
						                        <span class="glyphicon glyphicon-calendar"></span>
						                    </span>
						                </div>
						            </div>
	                            </div>

	                            <!-- datepicker --> 				                         	
	                             <div class="form-group"><label class="col-sm-2 control-label">Apply To</label>				                  
									<div class="col-sm-10">
										<select id="_simple_id" name="simple_id" class="form-control">	
										<?php
										if($pinfo->simple_action=="to_percent"){
											echo '
											<option>To Percentage of the Original Price</option>
											<option>By Fixed Amount</option>
											<option>By Percentage of the Original Price</option>
											<option>To Fixed Amount</option>
											';
										}elseif ($pinfo->simple_action=="by_percent") {
											echo '
											<option>By Percentage of the Original Price</option>
											<option>To Percentage of the Original Price</option>
											<option>By Fixed Amount</option>
											<option>To Fixed Amount</option>
											';
										}elseif ($pinfo->simple_action=="to_fixed") {
											echo '
											<option>To Fixed Amount</option>
											<option>To Percentage of the Original Price</option>
											<option>By Fixed Amount</option>
											<option>By Percentage of the Original Price</option>
											';
										}elseif ($pinfo->simple_action=="by_fixed") {
											echo '
											<option>By Fixed Amount</option>
											<option>To Percentage of the Original Price</option>
											<option>By Percentage of the Original Price</option>
											<option>To Fixed Amount</option>
											';
										}else{
											echo '
											<option>By Fixed Amount</option>
											<option>To Percentage of the Original Price</option>
											<option>By Percentage of the Original Price</option>
											<option>To Fixed Amount</option>';
										}
										?>								
											
										</select>
									</div>
	                            </div>

	                            <div class="form-group"><label class="col-sm-2 control-label">Discount Amount</label>
	                            	<div class="col-sm-10">
	                            		<input class="form-control required" name="rdisc" type="text" value="<?php echo number_format($pinfo->discount_amount); ?>">
	                            	</div>
	                           	</div>
	                        </div>
	                            <!-- //-->
	                      	<div style="clear:both;"></div>

	                      	<div class="col-md-9">
	                      	<?php if(ccheck_form(isuser_id(),"admin/promo/catalog")== true ){
	                   echo '
	                      	&nbsp<button style=" margin-left: 20px;" ruleid="<?php echo $pinfo->rule_id; ?>" id="deleteShopRuleCmd" title="delete" cmdEvent="deleteShopRuleCmd" type="button" class="btn btn-danger pull-right">&nbspRemove Promo &nbsp</button>&nbsp<p class="help-block pull-left text-danger hide" id="form-error">&nbsp; The form is not valid. </p>&nbsp
	                      	&nbsp<button type="submit" class="btn btn-primary pull-right">&nbspUpdate Promo</button> <p class="help-block pull-left text-danger hide" id="form-error">&nbsp; The form is not valid. </p>';
							}else{
					   echo '
	                      	&nbsp<button style=" margin-left: 20px;" ruleid="<?php echo $pinfo->rule_id; ?>" id="deleteShopRuleCmd" title="delete" cmdEvent="deleteShopRuleCmd" type="button" class="btn btn-danger pull-right disabled">&nbsp Remove Promo &nbsp</button>&nbsp<p class="help-block pull-left text-danger hide" id="form-error">&nbsp; The form is not valid. </p>&nbsp
	                      	&nbsp<button type="submit" class="btn btn-primary pull-right disabled">&nbsp Update Promo</button> <p class="help-block pull-left text-danger hide" id="form-error">&nbsp; The form is not valid. </p>';	
							}
							?>
							<input type="hidden" id="rid" value="<?php echo $pinfo->rule_id; ?>"/>
							</div>				

						</form>  	
					</div> 
				</div> 
				<?php endforeach; ?> 
			</div> 
		</div> 
	</div> 
</div>
<script type="text/javascript">
	$('button#deleteShopRuleCmd').click(function(e){	
	e.preventDefault();

	var dataObject = {
		'id': 		$('#rid').val(),
		'cmdEvent':	$(this).attr('cmdEvent'),
		'ruleid': $(this).attr('ruleid')
	}

	$.ajax({
		url: "<?php echo get_baseurl() ?>/clickhealthdev/index.php/default/connectmage/index",   
		type: 'post',  
		asynchronous: true,
        cashe: false,
		data: dataObject, 
		beforeSend: function() {
			var ruleid = dataObject['ruleid'];
			var x = confirm("Are you sure you want to delete this promo?\n");
			if (x){
			    return true;
			}
			else{

				return false;
			}	
		},
		success: function(data) 
		{     
			
			window.location="<?php echo base_url(); ?>index.php/Promotions"; 
		}
	});



});

</script>
</script>>