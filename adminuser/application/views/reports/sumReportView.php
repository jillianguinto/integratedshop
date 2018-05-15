<!-- view -->
<style>
#loading {
   width: 100%;
   height: 100%;
   top: 0px;
   left: 0px;
   position: fixed;
   display: block;
   opacity: 0.9;
   background-color: #fff;
   z-index: 99;
   text-align: center;
}

#loading-image {
  position: absolute;
  top: 200px;
  left: 700px;
  z-index: 100;
}
.btn {
  font-size: 11px !important;
  padding: 1px 4px;
}
.pagination>li>a, .pagination>li>span {
    padding: 1px 11px !important;
}
div.dataTables_wrapper div.dataTables_length label {
    font-size: 11px !important;

}
</style>
			<div id="page-wrapper">
	            <div class="container-fluid">

	                <!--<div class="row">
	                    <div class="col-lg-12">
	                        <h1 class="page-header">
	                             <i class="fa fa-life-ring"></i>Order Summary Report
	                        </h1>
	                     <!--    <ol class="breadcrumb">
	                            <li class="active">
									<a href="<?php echo base_url();?>">
										<i class="fa fa-dashboard"></i> Dashboard
									</a>
	                            </li>
	                            <li class="active">
										<strong><i class="fa fa-life-ring"></i> 
											<?php echo $this->uri->segment(1) . " [ ".count($getDefault) ." ]"; ?>
										</strong>
	                           </li>
	                        </ol> -->
	                    <!-- </div>
	                </div>-->
	                <div></div>
	                <div class="row">
						<div class="col-lg-12">
						
							<p><span style="font-size: 21px;">Order Summary</span><br/> as of <?php if(isset($_GET['and'])) { echo $_GET['and']; } else { echo date("j-M-Y",strtotime(date('Y-m-d H:i:s'))); }  ?></p>
							
	<!-- 
							<p id="renderingEngineFilter"></p>
							<p id="browserFilter"></p>
							<p id="platformsFilter"></p>
							<p id="engineVersionFilter"></p>
							<p id="cssGradeFilter"></p> -->


							<!-- <table cellspacing="0" cellpadding="0" border="0" class="display" ID="Table1">
				                <thead>
				                <tr>
				                        <th>Column</th>
				                        <th>Filter</th>
				                </tr>
				                </thead>
				                <tbody>
				                        <tr id="filter_global">
				                                <td align="center">Rendering engine</td>
				                                <td align="center" id="renderingEngineFilter"></td>
				                        </tr>
				                        <tr id="filter_col1">
				                                <td align="center">Browser</td>
				                                <td align="center" id="browserFilter"></td>
				                        </tr>
				                        <tr id="filter_col2">
				                                <td align="center">Platform(s)</td>
				                                <td align="center" id="platformsFilter"></td>
				                        </tr>
				                        <tr id="filter_col3">
				                                <td align="center">Engine version</td>
				                                <td align="center" id="engineVersionFilter"></td>
				                        </tr>
				                        <tr id="filter_col4">
				                                <td align="center">CSS grade</td>
				                                <td align="center" id="cssGradeFilter"></td>
				                        </tr>
				                </tbody>
							</table>
	 -->
							<div class="multiselect">
									<div id="loading">
  <img id="loading-image" src="../../template/images/loader.gif" alt="Loading..." />
</div>
								<!-- test -->

								<!-- <table cellspacing="0" cellpadding="0" border="0" class="display" ID="Table1">
					             
					                <tbody>
					                        <tr id="filter_global">
					                                <td align="center">Store</td>
					                                <td align="center" id="storeFilter"></td>
					                        </tr>
					                       
					                </tbody>
								</table> -->


								<!-- // test -->

								
								
								<?php 

									//year
								
										$GtYear = $yearPost;

									



									//month
								
										$GtMonth = $monthPost;

									


									//status
										$GtStatus = $statusPost;



									//gender
								
										$GtGender = $genderPost;



									//city
									
										$GtCity = $cityPost;
										// echo $GtCity;
										// die();


									//beiween
									
										$GtBetween = $betweenPost;


									//and
										$GtAnd = $andPost;
										
										$GtPromo = $promoPost;
										$GtSort = $sortPost;
										$GtSortBy = $sortByPost;
									
										


								
								

								 ?>

										<script type="text/javascript">
										$(function() {
    
										    var date1 = $('#date1');
										    var date2 = $('#date2');
										    date1.datepicker({
										      	dateFormat: 'dd-MM-yy',
										        yearRange:  '1901:2020',
										        changeYear: true,
										        changeMonth: true,
										        numberOfMonths: 1, 
										        showButtonPanel: true,
										        closeText: 'Clear',
										        // onClose: function( selectedDate ) {
										            // var date = $(this).datepicker("getDate");
										            
										            // date2.datepicker("setDate", date);
										            // date2.datepicker( "show" );
										            
										        // }
										    });
										    date2.datepicker({
										        dateFormat: 'dd-MM-yy',
										        yearRange:  '1901:2020',
										        changeYear: true,
										        changeMonth: true,
										        numberOfMonths: 1, 
										        showButtonPanel: true,
										        closeText: 'Clear',
										        onClose: function( selectedDate ) {
										            var date = $(this).datepicker("getDate");
										            var formattedDate = $.datepicker.formatDate('dd-MM-yy', date);
										            
										            // date1.val(date1.val() + " - " + formattedDate);

										            // console.log( date1.val() );
										            // console.log( '?between='+date1.val()+'&and='+formattedDate );
										            
										           // alert( '?between='+date1.val()+'&and='+formattedDate );
												
													
										        }
										    });
										});
										</script>
<table  id="dvData" style="border:none;width:100%">
<form id="postForm" action="<?php echo base_url()?>index.php/sumReport/Viewsumm" method="post">

							<tr>
							<td width="25%" style="border:none;">
							Year :
							<select id='year' name='year' class="year" style="width: 150px;margin-left: 15px;">
												<?php if($GtYear == 0) { ?><option value="0">-- select --</option> <?php } ?>
												<?php

													$i = 0; 
													foreach($getYear as $kyear=>$year): 
														$x = $i++;

													if($getYear[$x]->Year == $GtYear){


														echo "<option selected value='".$getYear[$x]->Year ."'>".$getYear[$x]->Year."</option>";
													}else{

														echo "<option value='".$getYear[$x]->Year ."'>".$getYear[$x]->Year."</option>";

													}

													endforeach; ?>
											</select>
							</td>
							<td width="25%" style="border:none;">
										Order Date </td>
							<td width="25%" style="border:none;">
							Gender :
								<select id='gender' name='gender' class="gender" style="width: 150px;">
											<?php if($GtGender == 0) { ?><option value="0">-- select --</option> <?php } ?>
												<?php foreach(array(1=>'Male', 2=>'Female') as $gk=>$gval):?>

														<?php if($gk == $GtGender): ?>
															<option selected value='<?php echo $gk; ?>'><?php echo $gval; ?></option>	
														<?php else: ?>
															<option value='<?php echo $gk; ?>'><?php echo $gval; ?></option>	

														<?php endif; ?>	

												<?php endforeach; ?>													
											</select>
							</td>
							
							<td width="25%" style="border:none;">
							Sort By :
							<select id='sort' name='sort' class="sort" style="width: 154px;margin-left:20px;">
							
												<option value="0">-- select --</option>
												<option <?php if(isset($GtSort)){  if($GtSort == 'YEAR(created_at)') { echo 'selected'; }} ?> value="YEAR(created_at)">Year</option>
												<option <?php if(isset($GtSort)){  if($GtSort == 'MONTH(created_at)') { echo 'selected'; }} ?> value="MONTH(created_at)">Month</option>
												<option <?php if(isset($GtSort)){  if($GtSort == 'sales_flat_order.status') { echo 'selected'; }} ?> value="sales_flat_order.status">Payment Status</option>
												<option <?php if(isset($GtSort)){  if($GtSort == 'sales_flat_order.customer_gender') { echo 'selected'; }} ?> value="sales_flat_order.customer_gender">Gender</option>
												<option <?php if(isset($GtSort)){  if($GtSort == 'sales_flat_order_address.city') { echo 'selected'; }} ?> value="sales_flat_order_address.city">City</option>
											</select>
							</td>
							</tr>
							
							
							
							
							
							
							<tr>
							<td width="25%" style="border:none;">
							Month :
						<select id='month' name='month' class="month" style="width: 150px;  margin-left: 8px;">
											<?php if($GtMonth == 0) { ?><option value="0">-- select --</option> <?php } ?>	
												<?php $i = 0; 
												
													// sort($getMonth);
													// print_r($getMonth);
													// die();
													foreach($getMonth as $kmonth=>$month): 
														$x = $i++;

														$getmo =  $getMonth[$x]->month;

																		
														if($getmo == 'January'){
															$month = 1;
														}elseif($getmo == 'February'){
															$month = 2;	
														}elseif($getmo == 'March'){
															$month = 3;
														}elseif($getmo == 'April'){
															$month = 4;
														}elseif($getmo == 'May'){
															$month = 5;
														}elseif($getmo == 'June'){
															$month = 6;
														}elseif($getmo == 'July'){
															$month = 7;
														}elseif($getmo == 'August'){
															$month = 8;
														}elseif($getmo == 'September'){
															$month = 9;
														}elseif($getmo == 'October'){
															$month = 10;
														}elseif($getmo == 'November'){
															$month = 11;
														}elseif($getmo == 'December'){
															$month = 12;
														}



														if($GtMonth == $month){													
													
															echo "<option selected value='".$month."'>".$getmo."</option>";
														}else{												
													

															echo "<option value='".$month."'>".$getmo."</option>";

														}


													 endforeach; ?>

											</select>
							</td>
							
							<td width="25%" style="border:none;">
						From :
											<input name="date1" id="date1" readonly="readonly" value="<?php if(isset($GtBetween)) { echo $GtBetween; }?>" style="margin-left:20px;height:25px;width: 150px;"/>
											<img src="../../template/images/calendar.gif" id="date1Image" style="width:19px;position: relative; left: -24px;">
										
							</td>
							
							<td width="25%" style="border:none;">
							City :
								<select id='city' name='city' class="city" style="margin-left:20px;width: 150px;">
												<?php if($GtCity == 0) { ?><option value="0">-- select --</option> <?php } ?>	
												<?php 
															echo $GtCity;
													$i = 0; 
													foreach($getCity as $kCity=>$City): 
														$x = $i++;
														// if($GtCity == 0)
														// {
															// echo "<option value='".$getCity[$x]->city."'>".$getCity[$x]->city."</option>";
														// }
														// else{
														if(trim($getCity[$x]->city) == trim($GtCity)){
															echo "<option selected value='".$getCity[$x]->city."'>".$getCity[$x]->city."</option>";
														}else{

															echo "<option value='".$getCity[$x]->city."'>".$getCity[$x]->city."</option>";

														}
														// }

													endforeach; 

												?>
											</select>
							</td>
							
							<td width="25%" style="border:none;">
							<?php 
							if(isset($_GET['sortBy']) == 'desc' )
							{
							?>
							<input type="radio" name="sortby" value="asc"  style="margin-left: 70px;">Ascending</input>
							<input type="radio" name="sortby" value="desc" checked="checked">Descending</input>
							<?php }
							else
							{
							?>
							<input type="radio" name="sortby" value="asc" checked="checked" style="margin-left: 70px;">Ascending</input>
							<input type="radio" name="sortby" value="desc" >Descending</input>
							<?php
							}
							?>
							</td>
							</tr>
							
							<tr>
							
							<td width="25%" style="border:none;">
							Status :
						<select id='status' name='status' class="status" style="width: 150px;margin-left:7px">
												<?php if($GtStatus == 0) { ?><option value="0">-- select --</option> <?php } ?>
												
													<?php

													$i = 0; 
													foreach($getStatus as $kStatus=>$status): 
														$x = $i++;
														if($GtStatus){
														if($getStatus[$x]->status == $GtStatus){


															echo "<option selected value='".$getStatus[$x]->status ."'>".$getStatus[$x]->status."</option>";
														}else{

															echo "<option value='".$getStatus[$x]->status ."'>".$getStatus[$x]->status."</option>";

														}
														}
														else
														{
														echo "<option value='".$getStatus[$x]->status ."'>".$getStatus[$x]->status."</option>";

														}

													endforeach; 
													?>


											</select>
							</td>
							
									
										<td width="25%" style="border:none;">
											 To :
											<!-- <input id="and" class="and" name="and" value="<?php echo (isset($GtBetween))? $GtBetween : NULL; ?>"  /> -->
											<input name="date2" id="date2" readonly="readonly" value="<?php if(isset($GtAnd)) { echo $GtAnd; }?>" style=" margin-left: 35px;height:25px;width: 150px;" />
											<img src="../../template/images/calendar.gif" id="date2Image" style="width: 19px;position: relative; left: -24px;">
										</td>
										<td width="25%" style="border:none;">
										Promo : 
							<select id='promo' name='promo' class="promo" style="width: 150px;margin-left: 6px;">
											<?php if($GtPromo == 0) { ?><option value="0">-- select --</option> <?php } ?>
												<?php 

													$i = 0; 
													foreach($getPromo as $kPromo=>$Promo): 
														$x = $i++;

														if($getPromo[$x]->promo <> ""){
															if($getPromo[$x]->promo == $GtPromo){


															echo "<option selected value='".$getPromo[$x]->promo."'>".$getPromo[$x]->promo."</option>";
														}else{

															echo "<option value='".$getPromo[$x]->promo."'>".$getPromo[$x]->promo."</option>";
																}
														}

													endforeach; 

												?>
											</select>
							
										</td>
										
										<td width="25%" style="border:none;">
										Search Box : <input type="text" id="searchbox">
							
										</td>
									</tr>
									<input type="submit" name="submit" id="buttonForm" style="display:none;" />
									</form>
							</table>

							</div>	



							<div class="btun">
							<!--<button type="button" class="btn btn-danger" onclick="createpdf()"><i class="fa fa-file-pdf-o"></i> GENERATE PDF</button>!-->
							<!--<a id="xx" href="<?php echo base_url()?>index.php/sumReport/exportDet" type="button" class="btn btn-success"><i class="fa fa-file-excel-o"></i> EXPORT TO EXCEL</a>!-->
							<a id="aaxx" onclick="download_excel();"class="btn btn-success"><i class="fa fa-file-excel-o"></i> EXPORT TO EXCEL</a>
							<a href="<?php echo base_url() ?>index.php/sumReport/Viewsumm" id="resetFilter" type="button" class="btn btn-info"><i class=""></i> RESET FILTER</a>
							<!-- <form id="sumRepp" action="<?php echo base_url()?>index.php/sumReport/sumbyDate" method="post">
							<input type="text" id="datepicker" name="datepicker1"/> TO <input type="text" id="datepicker2" name="datepicker2"/>
							<button type="submit" class="_view btn btn-info" title="" data-id="" ><i class="fa fa-file-excel-o"></i> Apply Changes</button>
							
							</form> -->
						</div>	
								
							<!-- <form id="sumRepp" action="<?php echo base_url()?>index.php/sumReport/detbyDate" method="post">
							<input type="text" id="datepicker" name="datepicker1"/> TO <input type="text" id="datepicker2" name="datepicker2"/>
							<button type="submit" class="_view btn btn-info" title="" data-id="" ><i class="fa fa-file-excel-o"></i> Apply Changes</button>
							</form> -->
							
						
						</div>		
					<?php
					$quantity = 0;
					$orderAmount = 0;
					$orderTotal = 0;
					$discount = 0;
					$shippingRate = 0;		
					if (count($getDefault)>0){
						foreach($getDefault as $key => $value)
						{
						$orderAmount = $orderAmount + $value->{'Order Amount'};
						$orderTotal = $orderTotal + $value->{'Order Total'};
						$discount = $discount + $value->{'Discount Amount'};
						$shippingRate = $shippingRate + $value->{'Order Shipping'};
						}
						}
						 					
					?>							
	                    <div class="col-lg-12 table-responsive">
						
							<table id="sumreport" width="100%" data-tableName="sumreport" style="line-height:0.5 !important;">
							 <thead>
										<tr bgcolor="#330000" style="color:#fff">	
											<th>Order No.</th>
											<th>Shoppers Name</th>
											<th>Total</th>
											<th>Amt</th>
											<th>Shipping</th>
											<th>Promo Amt</th>
											<th>Promo</th>
											<th>Status</th>
											<th>Order Date</th>
											<th>Waybill No.</th>
											<th>Gender</th>
											<th>Country</th>
											<th>City</th>
										</tr>
									</thead>
									 <tfoot>
										<tr bgcolor="#FF6699">
											<td colspan="2" align="center" ><b>Grand Total</b></td>
											<td align="right"><b><?php echo number_format($orderTotal,2); ?></b></td>
											<td align="right"><b><?php echo number_format($orderAmount,2); ?></b></td>
											<td align="right"><b><?php echo number_format($shippingRate,2); ?></b></td>
											<td align="right"><b><?php echo number_format($discount,2); ?></b></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
										</tr>
									</tfoot>
						 			<tbody>
						 					<?php
						 					if (count($getDefault)>0){
						 					foreach($getDefault as $_list):
							 					//$pormID = $_list->{'Store Name'};
												echo '<tr>';
												echo '<td>'.$_list->{'Order Number'}.'</td>';
												echo '<td>'.$_list->{"Shopper's Name"}.'</td>';	
												echo '<td align="right">'.number_format($_list->{'Order Total'},2).'</td>';
												echo '<td align="right">'.number_format($_list->{'Order Amount'},2).'</td>';		
												echo '<td align="right">'.number_format($_list->{'Order Shipping'},2).'</td>';
												echo '<td align="right">'.number_format($_list->{'Discount Amount'},2).'</td>';
												echo '<td>'.$_list->{'Promo Name'}.'</td>';
												echo '<td>'.$_list->{'Payment Status'}.'</td>';
												echo '<td>'.date("d-m-Y", strtotime($_list->{'Order Date'})).'</td>';
												echo '<td>'.$_list->{'Waybill Number'}.'</td>';
												echo '<td>'.$_list->{'Gender'}.'</td>';
												echo '<td>'.$_list->{'Country'}.'</td>';
												echo '<td>'.$_list->{'City'}.'</td>';
						 					endforeach;
						 				}else{echo "there is no record to show!";}
						 					?>
						 			</tbody>

									<tbody>


									</tbody>
								</table>                    
						</div>   
						
						
						
						
						<div  id="tableExport" style="display:none;">
						
							<table>
							 <thead>
						
										<tr bgcolor="#330000" style="color:#fff">	
											<th>Order No.</th>
											<th>Shoppers Name</th>
											<th>Total</th>
											<th>Amt</th>
											<th>Shipping</th>
											<th>Promo Amt</th>
											<th>Promo</th>
											<th>Status</th>
											<th>Order Date</th>
											<th>Waybill No.</th>
											<th>Gender</th>
											<th>Country</th>
											<th>City</th>
										</tr>
									</thead>
									
						 			<tbody>
						 					<?php
						 					if (count($getDefault)>0){
						 					foreach($getDefault as $_list):
							 					//$pormID = $_list->{'Store Name'};
												echo '<tr>';
												echo '<td>'.$_list->{'Order Number'}.'</td>';
												echo '<td>'.$_list->{"Shopper's Name"}.'</td>';	
												echo '<td align="right">'.number_format($_list->{'Order Total'},2).'</td>';
												echo '<td align="right">'.number_format($_list->{'Order Amount'},2).'</td>';		
												echo '<td align="right">'.number_format($_list->{'Order Shipping'},2).'</td>';
												echo '<td align="right">'.number_format($_list->{'Discount Amount'},2).'</td>';
												echo '<td>'.$_list->{'Promo Name'}.'</td>';
												echo '<td>'.$_list->{'Payment Status'}.'</td>';
												echo '<td>'.date("d-m-Y", strtotime($_list->{'Order Date'})).'</td>';
												echo '<td>'.$_list->{'Waybill Number'}.'</td>';
												echo '<td>'.$_list->{'Gender'}.'</td>';
												echo '<td>'.$_list->{'Country'}.'</td>';
												echo '<td>'.$_list->{'City'}.'</td>';
						 					endforeach;
						 				}else{echo "there is no record to show!";}
						 					?>
											
										<tr bgcolor="#FF6699">
											<td></td>
											<td align="center" ><b>Grand Total</b></td>
											<td align="right"><b><?php echo number_format($orderTotal,2); ?></b></td>
											<td align="right"><b><?php echo number_format($orderAmount,2); ?></b></td>
											<td align="right"><b><?php echo number_format($shippingRate,2); ?></b></td>
											<td align="right"><b><?php echo number_format($discount,2); ?></b></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
										</tr>
									
						 			</tbody>

									<tbody>


									</tbody>
								</table>                    
						</div>   
					   <form id="project_form_word" method="POST" action="<?php echo base_url()?>index.php/Export/exportToExcel">
							<input type="hidden" name="data" value=""/>
							</form>
	                </div>

	            </div>

	        </div>
	        </body>
			
			
<script>

$(document).ready(function(){
   $('.year').change(function(){
       $('#buttonForm').click();
    });
	$('.gender').change(function(){
       $('#buttonForm').click();
    });
	$('.month').change(function(){
       $('#buttonForm').click();
    });
	$('.status').change(function(){
       $('#buttonForm').click();
    });
	$('.promo').change(function(){
       $('#buttonForm').click();
    });
	$('.city').change(function(){
       $('#buttonForm').click();
    });
	$('.sort').change(function(){
       $('#buttonForm').click();
    });
	$('#date2').change(function(){
       $('#buttonForm').click();
    });
	$('#date2Image').click(function(){
	$('#date2').datepicker('show');
    });
	$('#date1Image').click(function(){
	$('#date1').datepicker('show');
    });

});
</script>
<script type="text/javascript">
function download_excel()
{
     $("input[name='data']").val($("#tableExport").html());
    $("form#project_form_word").submit();
	// var strCopy = document.getElementById("tableExport").innerHTML;
// window.clipboardData.setData("Text", strCopy);
// var objExcel = new ActiveXObject ("Excel.Application");
// objExcel.visible = true;

// var objWorkbook = objExcel.Workbooks.Add;
// var objWorksheet = objWorkbook.Worksheets(1);
// objWorksheet.Paste;
}

</script>
<script>						

// DATEPICKER RANGE FROM - TO
$(function(){

    var dateToday = new Date();
    var yrRange = (dateToday.getFullYear()-5) + ":" + (dateToday.getFullYear() + 2);

    var fromDate = $("#between").datepicker({
        dateFormat: 'dd-MM-yy', 
        yearRange:  '1901:2020',
        changeYear: true,
        showOn: 'focus',
        showButtonPanel: true,
        closeText: 'Clear',
        changeMonth: true,
        numberOfMonths: 1,
        onSelect: function(selectedDate) {
            var instance = $(this).data("datepicker");
            var date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);
            date.setDate(date.getDate()+1);
            toDate.datepicker("option", "minDate", date);

       		if(document.location.search.length)
			{
			        var href = window.location.href.substring(0, window.location.href.indexOf('?'));
			        var qs = window.location.href.substring(window.location.href.indexOf('?') + 1);     

			     
			        var newParam = $(this).attr("id") + '=' + $(this).val();

			        if (qs.indexOf($(this).attr("id") + '=') == -1) {
			            if (qs == '') {
			                qs = '?'
			            }
			            else {
			                qs = qs + '&'
			            }
			            qs = qs + newParam;

			        }
			        else {
			            var start = qs.indexOf($(this).attr("id") + "=");
			            var end = qs.indexOf("&", start);
			            if (end == -1) {
			                end = qs.length;
			            }
			            var curParam = qs.substring(start, end);
			            qs = qs.replace(curParam, newParam);
			        }
			        // window.location.replace('?' + qs);
			    // });

			}else{		
		        	var baseUrl = '<?php echo base_url()?>index.php/sumReport/Viewsumm?between=' + $(this).val();
					// location.href = baseUrl;
			}	
        },
        // onClose: function () {
            // var event = arguments.callee.caller.caller.arguments[0];       
            // if ($(event.delegateTarget).hasClass('ui-datepicker-close')) {
               // $(this).val('');
            // }
        // }
    });
    
    var toDate = $("#and").datepicker({
        dateFormat: 'dd-MM-yy',
        yearRange:  '1901:2020',
        changeYear: true,
        showOn: 'focus',
        showButtonPanel: true,
        closeText: 'Clear',
        changeMonth: true,
        numberOfMonths: 1,
        onSelect: function(selectedDate) {

        	if(document.location.search.length)
			{
			        var href = window.location.href.substring(0, window.location.href.indexOf('?'));
			       		       
			        var qs = window.location.href.substring(window.location.href.indexOf('?') + 1);     

			     
			        var newParam = $(this).attr("id") + '=' + $(this).val();

			        if (qs.indexOf($(this).attr("id") + '=') == -1) {
			            if (qs == '') {
			                qs = '?'
			            }
			            else {
			                qs = qs + '&'
			            }
			            qs = qs + newParam;

			        }
			        else {
			            var start = qs.indexOf($(this).attr("id") + "=");
			            var end = qs.indexOf("&", start);
			            if (end == -1) {
			                end = qs.length;
			            }
			            var curParam = qs.substring(start, end);
			            qs = qs.replace(curParam, newParam);
			        }
			        // window.location.replace('?' + qs);
			}else{	
		        	var baseUrl = '<?php echo base_url()?>index.php/sumReport/Viewsumm?and=' + $(this).val();
					// location.href = baseUrl;
				
			}	
        },
        onClose: function () {
            var event = arguments.callee.caller.caller.arguments[0];       
            if ($(event.delegateTarget).hasClass('ui-datepicker-close')) {
                $(this).val('');
            }
        }
    });
    var _gotoToday = jQuery.datepicker._gotoToday;
    jQuery.datepicker._gotoToday = function(a){
        var target = jQuery(a);
        var inst = this._getInst(target[0]);
        _gotoToday.call(this, a);
        jQuery.datepicker._selectDate(a, jQuery.datepicker._formatDate(inst,inst.selectedDay, inst.selectedMonth, inst.selectedYear));
    };


});




	
$(document).ready(function () {

	function exportTableToCSV($table, filename) {
    
        var $rows = $table.find('tr:has(td),tr:has(th)'),
    
            // Temporary delimiter characters unlikely to be typed by keyboard
            // This is to avoid accidentally splitting the actual contents
            tmpColDelim = String.fromCharCode(11), // vertical tab character
            tmpRowDelim = String.fromCharCode(0), // null character
    
            // actual delimiter characters for CSV format
            colDelim = '","',
            rowDelim = '"\r\n"',
    
            // Grab text from table into CSV formatted string
            csv = '"' + $rows.map(function (i, row) {
                var $row = $(row), $cols = $row.find('td,th');
    
                return $cols.map(function (j, col) {
                    var $col = $(col), text = $col.text();
    
                    return text.replace(/"/g, '""'); // escape double quotes
    
                }).get().join(tmpColDelim);
    
            }).get().join(tmpRowDelim)
                .split(tmpRowDelim).join(rowDelim)
                .split(tmpColDelim).join(colDelim) + '"',
    
            
    
            // Data URI
            csvData = 'data:application/csv;charset=utf-8,' + encodeURIComponent(csv);
            
            console.log(csv);
            
        	if (window.navigator.msSaveBlob) { // IE 10+
        		//alert('IE' + csv);
        		window.navigator.msSaveOrOpenBlob(new Blob([csv], {type: "text/plain;charset=utf-8;"}), "csvname.csv")
        	} 
        	else {
        		$(this).attr({ 'download': filename, 'href': csvData, 'target': '_blank' }); 
        	}
    }
    
    // This must be a hyperlink
    $("#xx").on('click', function (event) {
    	
        exportTableToCSV.apply(this, [$('#tableExport'), 'export.csv']);
        
        // IF CSV, don't do event.preventDefault() or return false
        // We actually need this to be a typical hyperlink
    });

});
	</script>    

	<script type="text/javascript">
		$(document).ready(function() {	




			//$("div#sumreport_length .dataTables_length").find("label").hide();//append("<p>test</p>");
			
			//$("#createpdf").before($(".dataTables_length").clone().html());
			$("body").on('click', '._view', function (){
				$("[name=sumRepp]").attr("action","<?php echo base_url()?>index.php/sumReport/sumbyDate");
				$("[name=sumRepp]").submit();		
			});	
		});
	$(document).ready(function(){

	$.datepicker.regional[""].dateFormat = 'dd/mm/yy';
	$.datepicker.setDefaults($.datepicker.regional['']);
	$('#sumreport').dataTable({
		 	"bSort" : true, 
		 	"aLengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]],
	        "iDisplayLength": 25,
	        "dom": 'T<"clear">lfrtip',
	        "tableTools": {
	            "sSwfPath": "/swf/copy_csv_xls_pdf.swf"
	        }

		})
	});
	


</script>
<script type="text/javascript">
						$(document).ready(function() {
						var dataTable = $('#sumreport').dataTable();
						$("#searchbox").keyup(function() {
							dataTable.fnFilter(this.value);
						});    
					});
							</script>
<script src="<?php echo base_url()?>template/media/js/jquery.dataTables.columnFilter.js"></script> 

<script language="javascript" type="text/javascript">
     $(window).load(function() {
     $('#loading').hide();
  });
</script>
<style type="text/css">
table, 
th,
td {
   border: 1px dotted black;
 
}

th,td{
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  /*border: 1px dotted solid #000;*/
  width: 80px;
}

th{
	text-align: center;
	font-size: 12px;
	padding: 5px !important;
}

td {
	font-size: 12px;
	padding: 3px;
}
.btun {
	margin: 9px 0 0 0px;
	position: absolute;
	z-index: 1000;
}

.dataTables_length{
	float:right;
}
.dataTables_filter {
     display: none;
}

.multiselect > table, 
.multiselect > table >  tr,
.multiselect > table > tr > td{
	/*border: none;*/
}  

.multiselect {
	padding-bottom: 10px;
padding-top: 10px;
}

.multiselect > table {
	width: 79%;
}

#ui-datepicker-div {
top: 117px !important;
}
#sumreport_info {
margin: -2px 0 0 0px;
    position: absolute;
    z-index: 1000;
}



</style>
