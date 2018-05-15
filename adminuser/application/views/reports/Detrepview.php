<?php ?>
<style>
.multiselect > table {
    width: 100% !important;
}
.dataTables_filter {
     display: none;
}
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
<?php 


							
								//year
								
									$GtYear = $yearPost;




									$GtMonth = $monthPost;								
								
									
										$GtPromo = $promoPost;
										$GtSort = $sortPost;
										$GtSortBy = $sortByPost;
									


								//status
									$GtStatus =$statusPost;



								//gender
									$GtGender = $genderPost;


								//country


								//city
									$GtCity = $cityPost;


								//beiween
									$GtBetween = $betweenPost;


								//and
									$GtAnd = $andPost;
									
										$GtName = $itemNamePost;


								
							

							?>
               <!--  <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">
                             <i class="fa fa-life-ring"></i> Detailed Report
                        </h1>
                        <ol class="breadcrumb">
                            <li class="active">
								<a href="<?php echo base_url();?>">
									<i class="fa fa-dashboard"></i> Dashboard
								</a>
                            </li>
                            <li class="active">
									<strong><i class="fa fa-life-ring"></i> 
										<?php echo $this->uri->segment(1) . " [ ".count($getdet) ." ]"; ?>
									</strong>
                           </li>
                        </ol>
                    </div>
                </div> -->
                <div class="row">
					<div class="col-lg-12">



						<p><span style="font-size: 21px;">Order Details</span><br/> as of <?php if(isset($GtAnd)) { echo $GtAnd; } else { echo date("j-M-Y",strtotime(date('Y-m-d H:i:s'))); }  ?></p>
						


						<div class="multiselect">
								<div id="loading">
  <img id="loading-image" src="../../template/images/loader.gif" alt="Loading..." />
</div>
							
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
							<form id="postForm" action="<?php echo base_url()?>index.php/sumReport/Viewdetails" method="post">
							<tr>
							<td width="25%" style="border:none;">
							Year :
							<select id='year' name='year' class="year" style="width: 150px;margin-left: 65px;">
												<?php if($GtYear == 0) { ?><option value="0">-- select --</option> <?php } ?>
												<?php

													$i = 0; 
													foreach($getdYear as $kyear=>$year): 
														$x = $i++;

													if($getdYear[$x]->Year == $GtYear){


														echo "<option selected value='".$getdYear[$x]->Year ."'>".$getdYear[$x]->Year."</option>";
													}else{

														echo "<option value='".$getdYear[$x]->Year ."'>".$getdYear[$x]->Year."</option>";

													}

													endforeach; ?>
											</select>
							</td>
							
							<td width="25%" style="border:none;">
							Payment Status :
						<select id='status' name='status' class="status" style="width: 150px;">
												<?php if($GtStatus == 0) { ?><option value="0">-- select --</option> <?php } ?>
												
													<?php

													$i = 0; 
													foreach($getdStatus as $kStatus=>$status): 
														$x = $i++;
														if($GtStatus){
														if($getdStatus[$x]->status == $GtStatus){


															echo "<option selected value='".$getdStatus[$x]->status ."'>".$getdStatus[$x]->status."</option>";
														}else{

															echo "<option value='".$getdStatus[$x]->status ."'>".$getdStatus[$x]->status."</option>";

														}
														}
														else
														{
														echo "<option value='".$getdStatus[$x]->status ."'>".$getdStatus[$x]->status."</option>";

														}

													endforeach; 
													?>


											</select>
							</td>
							
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
							<select id='sort' name='sort' class="sort" style="width: 150px;margin-left:20px;">
							
												<option value="0">-- select --</option>
												<option <?php if(isset($GtSort)){  if($GtSort == 'YEAR(sales_flat_order_item.created_at)') { echo 'selected'; }} ?> value="YEAR(sales_flat_order_item.created_at)">Year</option>
												<option <?php if(isset($GtSort)){  if($GtSort == 'MONTH(sales_flat_order_item.created_at)') { echo 'selected'; }} ?> value="MONTH(sales_flat_order_item.created_at)">Month</option>
												<option <?php if(isset($GtSort)){  if($GtSort == 'sales_flat_order.status') { echo 'selected'; }} ?> value="sales_flat_order.status">Payment Status</option>
												<option <?php if(isset($GtSort)){  if($GtSort == 'sales_flat_order_item.name') { echo 'selected'; }} ?> value="sales_flat_order_item.name">Item Name</option>
												<option <?php if(isset($GtSort)){  if($GtSort == 'sales_flat_order_item.sku') { echo 'selected'; }} ?> value="sales_flat_order_item.sku">Item Code</option>
												<option <?php if(isset($GtSort)){  if($GtSort == 'sales_flat_order.customer_gender') { echo 'selected'; }} ?> value="sales_flat_order.customer_gender">Gender</option>
												<option <?php if(isset($GtSort)){  if($GtSort == 'sales_flat_order_address.city') { echo 'selected'; }} ?> value="sales_flat_order_address.city">City</option>
											</select>
							</td>
							</tr>
							
							
							
							
							
							
							<tr>
							<td width="25%" style="border:none;">
							Month :
						<select id='month' name='month' class="month" style="width: 150px;  margin-left: 57px;">
												<?php if($GtMonth == 0) { ?><option value="0">-- select --</option> <?php } ?>	
												<?php $i = 0; 
												
													// sort($getdMonth);
													foreach($getdMonth as $kmonth=>$month): 
														$x = $i++;

														$getmo =  $getdMonth[$x]->Month;

																		
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
							Item Name :
						<select id='itemName' name='itemName' class="itemName" style="width: 150px;margin-left: 26px;">
												<?php if($GtName == 0) { ?><option value="0">-- select --</option> <?php } ?>
												
												<?php

													$i = 0; 
													foreach($getdItemName as $kname=>$name): 
														$x = $i++;

													// if(isset($GtName)){
														// if($getdItemName[$x]->name == $GtName){


															// echo "<option selected value='".$getdItemName[$x]->name  ."'>".$getdItemName[$x]->name ."</option>";
														// }else{

															// echo "<option value='".$getdItemName[$x]->name  ."'>".$getdItemName[$x]->name ."</option>";

														// }
														// }
														// else
														// {
														echo "<option value='".$getdItemName[$x]->name  ."'>".$getdItemName[$x]->name ."</option>";

														// }

													endforeach; ?>


											</select>
							</td>
							
							<td width="25%" style="border:none;">
							City :
								<select id='city' name='city' class="city" style="margin-left:20px;width: 150px;">
												<?php if($GtCity == 0) { ?><option value="0">-- select --</option> <?php } ?>	
												<?php 
															echo $GtCity;
													$i = 0; 
													foreach($getdCity as $kCity=>$City): 
														$x = $i++;
														// if($GtCity == 0)
														// {
															// echo "<option value='".$getdCity[$x]->city."'>".$getdCity[$x]->city."</option>";
														// }
														// else{
														if(trim($getdCity[$x]->city) == trim($GtCity)){
															echo "<option selected value='".$getdCity[$x]->city."'>".$getdCity[$x]->city."</option>";
														}else{

															echo "<option value='".$getdCity[$x]->city."'>".$getdCity[$x]->city."</option>";

														}
														// }

													endforeach; 

												?>
											</select>
							</td>
							
							<td width="25%" style="border:none;">
							<input type="radio" name="sortby" value="asc" <?php if(isset($_GET['sortBy']) <> 'desc' ){ echo 'checked="checked"';} ?> style="margin-left: 70px;">Ascending</input><input type="radio" <?php if(isset($_GET['sortBy']) == 'desc' ){ echo 'checked="checked"';} ?> name="sortby" value="desc">Descending</input>
							</td>
							</tr>
							
							<tr>
									<td width="25%" style="border:none;">
										Order Date From :
											<input name="date1" id="date1" readonly="readonly" value="<?php if(isset($GtBetween)) { echo $GtBetween; }?>" style="height:25px;width: 150px;" />
												<img src="../../template/images/calendar.gif" id="date1Image" style="width:19px;position: relative; left: -24px;">
										</td>
										<td width="25%" style="border:none;">
											 To :
											<!-- <input id="and" class="and" name="and" value="<?php echo (isset($_GET['and']))? $_GET['and']: NULL; ?>"  /> -->
											<input name="date2" id="date2" readonly="readonly" value="<?php if(isset($GtAnd)) { echo $GtAnd; }?>" style=" margin-left: 72px;height:25px;width: 150px;" />
											<img src="../../template/images/calendar.gif" id="date2Image" style="width: 19px;position: relative; left: -24px;">
										</td>
										<td width="25%" style="border:none;">
										Promo : 
							<select id='promo' name='promo' class="promo" style="width: 150px;margin-left: 6px;">
												<?php if($GtPromo == 0) { ?><option value="0">-- select --</option> <?php } ?>
												<?php 

													$i = 0; 
													foreach($getdPromo as $kPromo=>$Promo): 
														$x = $i++;

														if($getdPromo[$x]->promo <> ""){
															if($getdPromo[$x]->promo == $GtPromo){


															echo "<option selected value='".$getdPromo[$x]->promo."'>".$getdPromo[$x]->promo."</option>";
														}else{

															echo "<option value='".$getdPromo[$x]->promo."'>".$getdPromo[$x]->promo."</option>";
																}
														}

													endforeach; 

												?>
											</select>
							
										</td>
										<input type="submit" name="submit" id="buttonForm" style="display:none;" />
										<td width="25%" style="border:none;">
										Search Box : <input type="text" id="searchbox">
							
										</td>
									</tr>
									</form>
							</table>


						</div>	

					
						<div class="btun">
							<!--<button type="button" class="btn btn-danger" onclick="createpdf()"><i class="fa fa-file-pdf-o"></i> GENERATE PDF</button>!-->
							<!--<a id="xx" href="javascript:void(0);" type="button" class="btn btn-success"><i class="fa fa-file-excel-o"></i> EXPORT TO EXCEL</a>!-->
							<a id="aaxx" onclick="download_excel();"class="btn btn-success"><i class="fa fa-file-excel-o"></i> EXPORT TO EXCEL</a>
							<a href="<?php echo base_url() ?>index.php/sumReport/Viewdetails" id="resetFilter" type="button" class="btn btn-info"><i class=""></i> RESET FILTER</a>
							<!-- <form id="sumRepp" action="<?php echo base_url()?>index.php/sumReport/sumbyDate" method="post">
							<input type="text" id="datepicker" name="datepicker1"/> TO <input type="text" id="datepicker2" name="datepicker2"/>
							<button type="submit" class="_view btn btn-info" title="" data-id="" ><i class="fa fa-file-excel-o"></i> Apply Changes</button>
							
							</form> -->
						</div>	
				</div>		
					<?php
					$quantity = 0;
					$orderAmount = 0;
					$discount = 0;
					$shippingRate = 0;
						foreach($getdet as $_list1):
							$quantity = $quantity + $_list1->{'Quantity'};
							$orderAmount = $orderAmount + str_replace("?","",$_list1->{'Order Amount'});
							$discount = $discount + $_list1->{'Discount Amount'};
							$shippingRate = $shippingRate + str_replace("?","",$_list1->{'Shipping Rate'});
						endforeach;
					?>
                    <div class="col-lg-12 table-responsive" id="texport">
						<table id="sumreport" class="datatable_ table-striped- table-bordered-" cellspacing="0" width="100%" data-tableName="sumreport" data-show-export="true" style="line-height:0.5 !important;">
						 		<thead>
						 			<tr bgcolor="#330000" style="color:#fff">	
										<th>Order No.</th>
										
										<th>Item Name</th>
										<th>Item Code</th>
										<th>Qty</th>
										<th>Amt</th>
																				
										
										<th>Discount</th>
										<th>Shipping</th>
										<th>Status</th>
										<th>Promo</th>
										<th>Order Date</th>
										<!-- <th>Year</th>-->
									</tr>
								</thead>
							 <tfoot>
         	<tr bgcolor="#FF6699">
			
										<td colspan="3" align="center" ><b>Grand Total</b></td>
										
										<td ><b><?php echo number_format($quantity,2); ?></b></td>
										<td align="right"><b><?php echo number_format($orderAmount,2); ?></b></td>
										
										
										<td align="right"><b><?php echo number_format($discount,2); ?></b></td>
										<td align="right"><b><?php echo number_format($shippingRate,2); ?></b></td>
										<td></td>
										<td></td>
										<td></td>
										</tr>
        </tfoot>
					 			<tbody >
					 					<?php
					 					foreach($getdet as $_list):
					 						echo '<tr>';
											echo '<td>'.$_list->{'Order Number'}.'</td>';
											
											echo '<td>'.$_list->{'Item Name'}.'</td>';
											echo '<td>'.$_list->{'Item Code'}.'</td>';
											echo '<td>'.number_format($_list->{'Quantity'},2).'</td>';
											echo '<td align="right">'.number_format($_list->{'Order Amount'},2).'</td>';
											
											
											echo '<td align="right">'.number_format($_list->{'Discount Amount'},2).'</td>';
											echo '<td align="right">'.number_format($_list->{'Shipping Rate'},2).'</td>';
											echo '<td>'.$_list->{'Payment Status'}.'</td>';
											echo '<td>'.$_list->{'Promo Name'}.'</td>';
											echo '<td>'.date("d-m-Y", strtotime($_list->{'Order Date'})).'</td>';
											// echo '<td>'.$_list->{'Year'}.'</td></tr>';

										
					 					endforeach;
					 					?>
									
					 			</tbody>							
							
							</table>         
<table>

</table>
							<div   id="tableExport" style="display:none">
							<table>
							
						 		<thead>
						 			<tr bgcolor="#330000" style="color:#fff">	
										<th>Order No.</th>
										<th>Item Name</th>
										<th>Item Code</th>
										<th>Qty</th>
										<th>Amount</th>
										<th>Discount</th>
										<th>Shipping</th>
										<th>Status</th>
										<th>Promo</th>
										<th>Order Date</th>
										<th>Month</th>
										<th>Shoppers Name</th>
										<th>Email</th>
										<th>Gender</th>	
										<th>Country</th>
										<th>Address</th>
										<th>City</th>
										<th>Telephone</th>
										<th>Mobile</th>
										<!-- <th>Year</th>-->
									</tr>
								</thead>
							
					 			<tbody >
					 					<?php
					 					foreach($getdet as $_list):
					 						echo '<tr>';
											echo '<td>'.$_list->{'Order Number'}.'</td>';
											echo '<td>'.$_list->{'Item Name'}.'</td>';
											echo '<td>'.$_list->{'Item Code'}.'</td>';
											echo '<td>'.number_format($_list->{'Quantity'},2).'</td>';
											echo '<td align="right">'.number_format($_list->{'Order Amount'},2).'</td>';
											echo '<td align="right">'.number_format($_list->{'Discount Amount'},2).'</td>';
											echo '<td align="right">'.number_format($_list->{'Shipping Rate'},2).'</td>';
											echo '<td>'.$_list->{'Payment Status'}.'</td>';
											echo '<td>'.$_list->{'Promo Name'}.'</td>';
											echo '<td>'.date("d-m-Y", strtotime($_list->{'Order Date'})).'</td>';
											echo '<td>'.$_list->{'Month'}.'</td>';	
											echo '<td>'.$_list->{"Shopper's Name"}.'</td>';	
											echo '<td>'.$_list->{'Email'}.'</td>';
											echo '<td>'.($_list->{'Gender'}=="Female" ? "F" : "M").'</td>';
											echo '<td>'.$_list->{'Country'}.'</td>';
											echo '<td>'.$_list->{'Address'}.'</td>';
											echo '<td>'.$_list->{'City'}.'</td>';
											echo '<td>'.$_list->{'Telephone'}.'</td>';
											echo '<td>'.$_list->{'Mobile'}.'</td>';

										
					 					endforeach;
					 					?>
         	<tr bgcolor="#FF6699">
										<td></td>
										<td></td>
										
										<td align="center" ><b>Grand Total</b></td>
										
										<td ><b><?php echo number_format($quantity); ?></b></td>
										<td align="right"><b><?php echo number_format($orderAmount,2); ?></b></td>
										
										
										<td align="right"><b><?php echo number_format($discount,2); ?></b></td>
										<td align="right"><b><?php echo number_format($shippingRate,2); ?></b></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										</tr>

					 			</tbody>	
								
							
							</table>  
							
							
							</div>
							<form id="project_form_word" method="POST" action="<?php echo base_url()?>index.php/Export/exportToExcel">
							<input type="hidden" name="data" value=""/>
							</form>
							
					</div>   
                </div>
            </div>
        </div>
        </body>

<script type="text/javascript">
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
        numberOfMonths: 1
        minDate: new Date(),

        onClose: function () {
            var event = arguments.callee.caller.caller.arguments[0];       
            if ($(event.delegateTarget).hasClass('ui-datepicker-close')) {
               $(this).val('');
            }
        }
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



</script>
<script>

$(document).ready(function(){
   $('.year').change(function(){
       $('#buttonForm').click();
    });
	 $('.itemName').change(function(){
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
$(document).ready(function () {


	// $('input[name="daterange"]').daterangepicker();
	

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
    
            
    
            // Data URI ISO-8859-1
            //csvData = 'data:application/csv;charset=utf-8,' + encodeURIComponent(csv);
            csvData = 'data:application/csv;ISO-8859-1,' + encodeURIComponent(csv);
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
	// alert('as');
        exportTableToCSV.apply(this, [$('#tableExport'), 'detailsreport.csv']);
        
        // IF CSV, don't do event.preventDefault() or return false
        // We actually need this to be a typical hyperlink
    });

});

//export end
$(document).ready(function() {
	$("body").on('click', '._view', function (){
		$("[name=sumRepp]").attr("action","<?php echo base_url()?>index.php/");
		$("[name=sumRepp]").submit();		
	});	

	$('#sumreport').DataTable({
	 	"bSort" : true,
		"aLengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]],
	    "iDisplayLength": 25
		 // "dom" : Bfrtip,
        // "buttons" : [copyHtml5,excelHtml5,csvHtml5,pdfHtml5]
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


.dataTables_length{
	float:right;
}
.btun {
	margin: 14px 0 0 0px;
	position: absolute;
	z-index: 1000;
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