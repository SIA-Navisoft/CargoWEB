<?php
error_reporting(E_ALL ^ E_NOTICE);
require('lock.php');

$page_file="report";


require('inc/s.php');
$result = mysqli_query($conn,"SELECT u_rights.p_view, u_rights.p_edit, s_pages.page_header, s_pages.page_icon, s_pages.page_table
								FROM setup_pages AS s_pages
								JOIN user_rights AS u_rights
								ON u_rights.page_name = s_pages.page_file
								WHERE u_rights.user_id = '".$myid."' AND u_rights.page_name='".$page_file."'");
if (!$result){die("Attention! Query to show fields failed.");}

if (mysqli_num_rows($result)<1){header("Location: welcome");die(0);}
$row = mysqli_fetch_assoc($result);
$p_view=$row['p_view'];
$p_edit=$row['p_edit'];

if($p_view!='on'){
		header("Location: welcome"); 
		die(0);		
}


$page_header=$row['page_header'];
$page_icon=$row['page_icon'];
$page_table=$row['page_table'];
mysqli_close($conn);

include('functions/base.php');
include('header.php');

if(!empty($_GET['page'])) {$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);if(false === $page) {$page = 1;}}else{$page = 1;}  //IEGŪSTAM LAPAS NUMURU

if($page){$glpage = '?page='.$page;}else{$glpage = null;}
?>

<style>
.col-centered{
    float: none;
    margin: 0 auto;
}
</style> 

<script>
$(document).ready(function(){
    $('.classlist').click(function(){
		$("#pleaseWait").toggle();
        $('#contenthere').load('/pages/report/report.php<?=$glpage;?>');
    });
})
</script>

<script>
function dateFilter() {
	
	$("#pleaseWait").toggle();
	
	var dFrom = $('#dFrom').val();
	var dTo = $('#dTo').val();
	
	$('#contenthere').load('/pages/report/report.php?dFrom='+dFrom+'&dTo='+dTo);
}
</script>

<script>
function selClient(val) {

	$("#pleaseWait").toggle();

	var val = val.value;
	var val = encodeURI(val);
	$('#contenthere').load('/pages/report/report.php?client='+val+'');
	
}
</script>

<script>
function selCode(val) {

	$("#pleaseWait").toggle();

	var val = val.value;
	var val = encodeURI(val);
	$('#contenthere').load('/pages/report/report.php?product='+val+'');
	
}
</script>

<script>
function selLocation(val) {

	$("#pleaseWait").toggle();

	var val = val.value;
	var val = encodeURI(val);
	$('#contenthere').load('/pages/report/report.php?location='+val+'');
	
}
</script>

<script>
function selCargoCode(val) {

	$("#pleaseWait").toggle();

	var val = val.value;
	var val = encodeURI(val);
	$('#contenthere').load('/pages/report/report.php?cargoCode='+val+'');
	
}
</script>

<script>
function selAuthor(val) {

	$("#pleaseWait").toggle();

	var val = val.value;
	var val = encodeURI(val);
	$('#contenthere').load('/pages/report/report.php?author='+val+'');
	
}
</script>

<script>
function selStatus(val) {

	$("#pleaseWait").toggle();

	var val = val.value;
	var val = encodeURI(val);
	$('#contenthere').load('/pages/report/report.php?selStatus='+val+'');
	
}
</script>

<script>
function selService(val) {

	$("#pleaseWait").toggle();

	var val = val.value;
	var val = encodeURI(val);
	$('#contenthere').load('/pages/report/report.php?selService='+val+'');
	
}
</script>

<script>
function selOwner(val) {

	$("#pleaseWait").toggle();

	var val = val.value;
	var val = encodeURI(val);
	$('#contenthere').load('/pages/report/report.php?owner='+val+'');
	
}
</script>

<script>
function selReceiver(val) {

	$("#pleaseWait").toggle();

	var val = val.value;
	var val = encodeURI(val);
	$('#contenthere').load('/pages/report/report.php?receiver='+val+'');
	
}
</script>

<div class="container-fluid">
	<div class="row">

		<div class="col-lg-10 col-centered">
			<div class="panel panel-default">
				<div class="panel-body"> 
						
					<?php
					//skats uz ierakstiem
					if (!$view){
						

						require('inc/s.php');
						$weightFormat = getSettingUOM($conn, 'period_work_done_report_uom');	
						echo '<div id="contenthere">';									
							echo '<div class="page-header" style="margin-top: -5px;">
							  
								<div class="btn-group btn-group-xs" role="group" aria-label="Small button group" style="display:inline-block;"> 
									<a class="btn btn-default active classlist" ><i class="glyphicon glyphicon-list" style="color: #00B5AD" title="pamatdati"></i></a>
									
									<a href="print_report" class="btn btn-default" ><i class="glyphicon glyphicon-print" style="color: #00B5AD" title="drukāt atskaiti"></i></a>
								</div>';
								
								echo '
								<div style="float: right;" >
										<div style="display: inline-block;">
											<input type="text" class="form-control input-xs datepicker" id="dFrom" value="'.date('01.m.Y').'">
										</div>
										<div style="display: inline-block;">
											<input type="text" class="form-control input-xs datepicker" id="dTo" value="'.date('t.m.Y').'">
										</div>	
										<a class="btn btn-default btn-xs" onclick="dateFilter();"><i class="glyphicon glyphicon-refresh" style="color: #00B5AD"  title="filtrēt"></i></a>					
								</div>
								';	
								
								
									  
							echo '</div>';
							
							echo '<p style="display:inline-block;">perioda analītiskā atskaite</p>';

							echo '<div style="display:inline-block;" class="total-title"></div>';

							echo '<div class="clearfix"></div>';
							
							//saņēmēja filtrs
							echo '
								<div class="form-group col-md-2 pull-right" style="display: inline-block; padding-right; 10px; padding-bottom: 5px;">
								  <select class="form-control selectpicker btn-group-xs" data-live-search="true" title="saņēmējs" onchange="selReceiver(this)">';
								   $selectClient = mysqli_query($conn, "SELECT receiverCode, receiverName FROM cargo_header GROUP BY receiverCode") or die(mysqli_error($conn));
								  
								  while($rowc = mysqli_fetch_array($selectClient)){
									  echo '<option value="'.$rowc['receiverCode'].'">'.$rowc['receiverCode'].' '.$rowc['receiverName'].'</option>';
								  }
								  
								  echo '
								  </select>	
								</div>';

							//īpašnieka filtrs
							echo '
								<div class="form-group col-md-2 pull-right" style="display: inline-block; padding-right; 10px; padding-bottom: 5px;">
								  <select class="form-control selectpicker btn-group-xs" data-live-search="true" title="īpašnieks" onchange="selOwner(this)">';
								   $selectClient = mysqli_query($conn, "SELECT Code, Name FROM n_customers GROUP BY Code") or die(mysqli_error($conn));
								  
								  while($rowc = mysqli_fetch_array($selectClient)){
									  echo '<option value="'.$rowc['Code'].'">'.$rowc['Code'].' '.$rowc['Name'].'</option>';
								  }
								  
								  echo '
								  </select>	
								</div>';
								
							//klienta filtrs
							echo '
								<div class="form-group col-md-2 pull-right" style="display: inline-block; padding-right; 10px; padding-bottom: 5px;">
								  <select class="form-control selectpicker btn-group-xs" data-live-search="true" title="klients" onchange="selClient(this)">';
								   $selectClient = mysqli_query($conn, "SELECT Code, Name FROM n_customers GROUP BY Code") or die(mysqli_error($conn));
								  
								  while($rowc = mysqli_fetch_array($selectClient)){
									  echo '<option value="'.$rowc['Code'].'">'.$rowc['Code'].' '.$rowc['Name'].'</option>';
								  }
								  
								  echo '
								  </select>	
								</div>';			

							//noliktavas filtrs
							echo '
								<div class="form-group col-md-2 pull-right" style="display: inline-block; padding-bottom: 5px;">
								  <select class="form-control selectpicker btn-group-xs" data-live-search="true" title="noliktava" onchange="selLocation(this)">';
								   $selectProduct = mysqli_query($conn, "SELECT id, name FROM n_location") or die(mysqli_error($conn));

								  while($rowc = mysqli_fetch_array($selectProduct)){
									  echo '<option value="'.$rowc['id'].'">'.$rowc['id'].' - '.$rowc['name'].'</option>';
								  }
								  
								  echo '
								  </select>	
								</div>';			

							//preces filtrs
							echo '
								<div class="form-group col-md-2 pull-right" style="display: inline-block; padding-bottom: 5px;">
								  <select class="form-control selectpicker btn-group-xs" data-live-search="true" title="prece" onchange="selCode(this)">';
								   $selectProduct = mysqli_query($conn, "SELECT code, name1, name2 FROM n_items") or die(mysqli_error($conn));

								  while($rowc = mysqli_fetch_array($selectProduct)){
									  echo '<option value="'.$rowc['code'].'">'.$rowc['code'].' '.$rowc['name1'].' '.$rowc['name2'].'</option>';
								  }
								  
								  echo '
								  </select>	
								</div>';

							//dokumenta numura filtrs
							echo '
								<div class="form-group col-md-2 pull-right" style="display: inline-block; padding-bottom: 5px;">
								  <select class="form-control selectpicker btn-group-xs" data-live-search="true" title="dokumenta nr." onchange="selCargoCode(this)">';
								   $selectProduct = mysqli_query($conn, "SELECT cargoCode FROM cargo_header GROUP BY cargoCode") or die(mysqli_error($conn));

								  while($rowc = mysqli_fetch_array($selectProduct)){
									  echo '<option value="'.$rowc['cargoCode'].'">'.$rowc['cargoCode'].'</option>';
								  }
								  
								  echo '
								  </select>	
                                </div>';
                             
                                $dateFrom = date('Y-m-01 00:00:00');
                                $dateTo = date('Y-m-t 23:59:59');                                
							
							//darbinieka filtrs
							echo '
								<div class="form-group col-md-2" style="display: inline-block; padding-bottom: 5px;">
								  <select class="form-control selectpicker btn-group-xs" data-live-search="true" title="darbinieks" onchange="selAuthor(this)">';
								   $selectProduct = mysqli_query($conn, "SELECT enteredBy FROM item_ledger_entry WHERE activityDate BETWEEN '".$dateFrom."' AND '".$dateTo."' GROUP BY enteredBy") or die(mysqli_error($conn));

								  while($rowc = mysqli_fetch_array($selectProduct)){
									  echo '<option value="'.$rowc['enteredBy'].'">'.returnMeWho($rowc['enteredBy']).'</option>';
								  }
								  
								  echo '
								  </select>	
                                </div>';
                                	

							//statusa filtrs
							echo '
								<div class="form-group col-md-2" style="display: inline-block; padding-bottom: 5px;">
								  <select class="form-control selectpicker btn-group-xs" data-live-search="true" title="status" onchange="selStatus(this)">';
                                  
                                  echo '<option value="20">saņemta</option>';
                                  echo '<option value="40">izsniegta</option>';
								  
								  echo '
								  </select>	
                                </div>'; 
                                
							//statupakalpojumasa filtrs
							echo '
								<div class="form-group col-md-2" style="display: inline-block; padding-bottom: 5px;">
								  <select class="form-control selectpicker btn-group-xs" data-live-search="true" title="pakalpojums" onchange="selService(this)">';
                                  
                                  echo '<option value="10">saņemšana</option>';

								  echo '<option value="20">pārvietošana</option>';

                                  echo '<option value="30">komplektēšana</option>';
								  
                                  echo '<option value="40">izsniegšana</option>';
								  
								  echo '
								  </select>	
								</div>';                                 

								
						echo '<div class="clearfix"></div>';
						echo '<div id="results">';	

                            $entrys = mysqli_query($conn, "

                            SELECT l.*, h.cargoCode AS cargoCode, h.clientCode AS clientCode, h.clientName AS clientName, h.ownerCode AS ownerCode, h.ownerName AS ownerName, h.receiverCode AS receiverCode, h.receiverName AS receiverName 
                            
                            FROM cargo_line AS l 
                            
                            LEFT JOIN cargo_header AS h
                            on l.docNr=h.docNr
                    
                            WHERE l.activityDate BETWEEN '".$dateFrom."' AND '".$dateTo."'                            

                            ") or die(mysqli_error($conn));
							
						
                            echo '<div class="table-responsive">
                            <table class="table table-hover table-responsive">
                                <thead>
                                    <tr>
                                        <th>datums</th>
                                        <th>pakalpojums</th>
                                        <th>status</th>
                                        <th>dokumenta nr.</th>
                                        <th>prece</th>
                                        <th>apjoms</th>				
                                        <th>noliktava</th>
                                        
                                        <th>klienta kods - nosaukums</th>
                                        <th>īpašnieka kods - nosaukums</th>
                                        <th>saņēmēja kods - nosaukums</th>							
                                        
                                        <th>darbinieks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                ';
                                
                                if(mysqli_num_rows($entrys)>0){
									$abAmount=null;
                                    while($eRow = mysqli_fetch_array($entrys)){
                                    echo '					
                                        <tr>';

                                            $anotherQuery = mysqli_query($conn, "SELECT * FROM item_ledger_entry WHERE activityDate BETWEEN '".$dateFrom."' AND '".$dateTo."' AND docNr='".$eRow['docNr']."' AND (cargoLine='".$eRow['id']."' OR orgLine='".$eRow['id']."') ".$tService." ".$tStatus." ");
                                            while($aqRow = mysqli_fetch_array($anotherQuery)){
                                            
                                            if($aqRow['amount'] >= 0){
                                            echo '
                                            <tr>
                                                <td>'.$aqRow['activityDate'].'</td>
            
                                                <td>'.returnAction($aqRow['action']).'</td>
            
                                                <td nowrap>';
                                                                  
                                                echo returnStatus($aqRow['status']);
                                                echo '</td>
                                                
                                                <td>'.$aqRow['cargoCode'].'</td>
                                                <td>'.$aqRow['productNr'].' - '.returnProductName($conn, $aqRow['productNr']).'</td>
                                                <td>'.$aqRow['amount'].' '.$aqRow['productUmo'].'</td>
                                                <td>'.$aqRow['location'].' - '.returnLocationName($conn, $aqRow['location']).'</td>
                                                <td>'.$aqRow['clientCode'].' - '.$aqRow['clientName'].'</td>
                                                <td>'.$aqRow['ownerCode'].' - '.$aqRow['ownerName'].'</td>
                                                <td>'.$aqRow['receiverCode'].' - '.$aqRow['receiverName'].'</td>
                                                <td>'.returnMeWho($aqRow['enteredBy']).'</td>
            
											</tr>';
											$abAmount += getTotValueUOM($conn, $aqRow['productNr'], $aqRow['amount'], $aqRow['productUmo'], $weightFormat);
                                            }
            
                                            }
            
											?>




											<script>
											$(document).ready(
												function() {
													$('div.total-title').html('&nbsp;kopā: <?php if($abAmount>0){echo $abAmount;}else{echo '0';} ?> <?=$weightFormat;?>');
												}
											);
											</script>
											<?php

                                            echo '
                                            
                                        </tr>';
                                    }
                                }else{
                                    echo '<td colspan="6"><i class="glyphicon glyphicon-ban-circle glyphicon-lg" style="color: red;"></i> nav neviena ieraksta!</td>';
                                }
            
                                    
                                echo '					
                                </tbody>
                            </table>';		
                    echo '</div>';
					}

					?>

					</div>


					<div id="pleaseWait" style="display: none;">
						<h1 align="center" style="padding-top: 300px;"><i class="glyphicon glyphicon-refresh spin" style="color: #00B5AD"></i></h1>
					</div>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>



<?php include_once("datepicker.php"); ?>
<?php include("footer.php"); ?>