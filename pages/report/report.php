<?php
require('../../lock.php');

$page_file="report";



require('../../inc/s.php');
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

$view = null;
if (isset($_GET['view'])){$view = htmlentities($_GET['view'], ENT_QUOTES, "UTF-8");}



if (isset($_GET['client'])){$client = htmlentities($_GET['client'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['owner'])){$owner = htmlentities($_GET['owner'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['receiver'])){$receiver = htmlentities($_GET['receiver'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['product'])){$product = htmlentities($_GET['product'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['location'])){$location = htmlentities($_GET['location'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['cargoCode'])){$cargoCode = htmlentities($_GET['cargoCode'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['author'])){$author = htmlentities($_GET['author'], ENT_QUOTES, "UTF-8");}

if (isset($_GET['selStatus'])){$selStatus = htmlentities($_GET['selStatus'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['selService'])){$selService = htmlentities($_GET['selService'], ENT_QUOTES, "UTF-8");}

if (isset($_GET['dFrom'])){$dFrom = htmlentities($_GET['dFrom'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['dTo'])){$dTo = htmlentities($_GET['dTo'], ENT_QUOTES, "UTF-8");}






include('../../functions/base.php');
require('../../inc/s.php');

if($client){$client = mysqli_real_escape_string($conn, $client);}
if($owner){$owner = mysqli_real_escape_string($conn, $owner);}
if($receiver){$receiver = mysqli_real_escape_string($conn, $receiver);}
if($product){$product = mysqli_real_escape_string($conn, $product);}
if($location){$location = mysqli_real_escape_string($conn, $location);}
if($cargoCode){$cargoCode = mysqli_real_escape_string($conn, $cargoCode);}
if($author){$author = mysqli_real_escape_string($conn, $author);}
if($selStatus){$selStatus = mysqli_real_escape_string($conn, $selStatus);}
if($selService){$selService = mysqli_real_escape_string($conn, $selService);}
if($dFrom){$dFrom = mysqli_real_escape_string($conn, $dFrom);}
if($dTo){$dTo = mysqli_real_escape_string($conn, $dTo);}

if($dFrom){ $lFrom = '&dFrom='.$dFrom; }else{ $lFrom = null; }	

if($dTo){ $lTo = '&dTo='.$dTo; }else{ $lTo = null; }

if($client){ $lClient = '&client='.urlencode($client); }else{ $lClient = null; }

if($owner){ $lOwner = '&owner='.urlencode($owner); }else{ $lOwner = null; }

if($receiver){ $lReceiver = '&receiver='.urlencode($receiver); }else{ $lReceiver = null; }

if($product){ $lProduct = '&product='.urlencode($product); }else{ $lProduct = null; }

if($location){ $lLocation = '&location='.urlencode($location); }else{ $lLocation = null; }

if($cargoCode){ $lCargoCode = '&cargoCode='.urlencode($cargoCode); }else{ $lCargoCode = null; }

if($author){ $lAuthor = '&author='.urlencode($author); }else{ $lAuthor = null; }

if($selStatus){ $lSelStatus = '&selStatus='.urlencode($selStatus); }else{ $lSelStatus = null; }

if($selService){ $lSelService = '&selService='.urlencode($selService); }else{ $lSelService = null; }


?>
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
	
	$('#contenthere').load('/pages/report/report.php?dFrom='+dFrom+'&dTo='+dTo+'<?=$lProduct;?><?=$lClient;?><?=$lLocation;?><?=$lCargoCode;?><?=$lOwner;?><?=$lReceiver;?><?=$lAuthor;?><?=$lSelStatus;?><?=$lSelService;?>');
}
</script>

<script>
function selClient(val) {

	$("#pleaseWait").toggle();

	var val = val.value;
	var val = encodeURI(val);
	$('#contenthere').load('/pages/report/report.php?client='+val+'<?=$lFrom;?><?=$lTo;?><?=$lProduct;?><?=$lLocation;?><?=$lCargoCode;?><?=$lOwner;?><?=$lReceiver;?><?=$lAuthor;?><?=$lSelStatus;?><?=$lSelService;?>');
	
}
</script>

<script>
function selCode(val) {

	$("#pleaseWait").toggle();

	var val = val.value;
	var val = encodeURI(val);
	$('#contenthere').load('/pages/report/report.php?product='+val+'<?=$lFrom;?><?=$lTo;?><?=$lClient;?><?=$lLocation;?><?=$lCargoCode;?><?=$lOwner;?><?=$lReceiver;?><?=$lAuthor;?><?=$lSelStatus;?><?=$lSelService;?>');
	
}
</script>

<script>
function selLocation(val) {

	$("#pleaseWait").toggle();

	var val = val.value;
	var val = encodeURI(val);
	$('#contenthere').load('/pages/report/report.php?location='+val+'<?=$lFrom;?><?=$lTo;?><?=$lProduct;?><?=$lClient;?><?=$lCargoCode;?><?=$lOwner;?><?=$lReceiver;?><?=$lAuthor;?><?=$lSelStatus;?><?=$lSelService;?>');
	
}
</script>

<script>
function selCargoCode(val) {

	$("#pleaseWait").toggle();

	var val = val.value;
	var val = encodeURI(val);
	$('#contenthere').load('/pages/report/report.php?cargoCode='+val+'<?=$lFrom;?><?=$lTo;?><?=$lProduct;?><?=$lClient;?><?=$lLocation;?><?=$lOwner;?><?=$lReceiver;?><?=$lAuthor;?><?=$lSelStatus;?><?=$lSelService;?>');
	
}
</script>

<script>
function selAuthor(val) {

	$("#pleaseWait").toggle();

	var val = val.value;
	var val = encodeURI(val);
	$('#contenthere').load('/pages/report/report.php?author='+val+'<?=$lFrom;?><?=$lTo;?><?=$lProduct;?><?=$lClient;?><?=$lLocation;?><?=$lOwner;?><?=$lReceiver;?><?=$lCargoCode;?><?=$lSelStatus;?><?=$lSelService;?>');
	
}
</script>

<script>
function selStatus(val) {

	$("#pleaseWait").toggle();

	var val = val.value;
	var val = encodeURI(val);
	$('#contenthere').load('/pages/report/report.php?selStatus='+val+'<?=$lFrom;?><?=$lTo;?><?=$lProduct;?><?=$lClient;?><?=$lLocation;?><?=$lOwner;?><?=$lReceiver;?><?=$lCargoCode;?><?=$lAuthor;?><?=$lSelService;?>');
	
}
</script>

<script>
function selService(val) {

	$("#pleaseWait").toggle();

	var val = val.value;
	var val = encodeURI(val);
	$('#contenthere').load('/pages/report/report.php?selService='+val+'<?=$lFrom;?><?=$lTo;?><?=$lProduct;?><?=$lClient;?><?=$lLocation;?><?=$lOwner;?><?=$lReceiver;?><?=$lCargoCode;?><?=$lAuthor;?><?=$lSelStatus;?>');
	
}
</script>

<script>
function selOwner(val) {

	$("#pleaseWait").toggle();

	var val = val.value;
	var val = encodeURI(val);
	$('#contenthere').load('/pages/report/report.php?owner='+val+'<?=$lFrom;?><?=$lTo;?><?=$lProduct;?><?=$lClient;?><?=$lLocation;?><?=$lCargoCode;?><?=$lReceiver;?><?=$lAuthor;?><?=$lSelStatus;?><?=$lSelService;?>');
	
}
</script>

<script>
function selReceiver(val) {

	$("#pleaseWait").toggle();

	var val = val.value;
	var val = encodeURI(val);
	$('#contenthere').load('/pages/report/report.php?receiver='+val+'<?=$lFrom;?><?=$lTo;?><?=$lProduct;?><?=$lClient;?><?=$lLocation;?><?=$lCargoCode;?><?=$lOwner;?><?=$lAuthor;?><?=$lSelStatus;?><?=$lSelService;?>');
	
}
</script>

	<script>
	$('.removeFilter').on('click', function() {
		
		var tclass = $(this).attr('id');
		
		if(tclass=='fClient'){
			$("#pleaseWait").toggle();
			$('#contenthere').load('/pages/report/report.php?client=<?=$lFrom;?><?=$lTo;?><?=$lProduct;?><?=$lLocation;?><?=$lCargoCode;?><?=$lOwner;?><?=$lReceiver;?><?=$lAuthor;?><?=$lSelStatus;?><?=$lSelService;?>');
		}
		if(tclass=='fOwner'){
			$("#pleaseWait").toggle();
			$('#contenthere').load('/pages/report/report.php?owner=<?=$lFrom;?><?=$lTo;?><?=$lProduct;?><?=$lLocation;?><?=$lCargoCode;?><?=$lClient;?><?=$lReceiver;?><?=$lAuthor;?><?=$lSelStatus;?><?=$lSelService;?>');
		}
		if(tclass=='fReceiver'){
			$("#pleaseWait").toggle();
			$('#contenthere').load('/pages/report/report.php?receiver=<?=$lFrom;?><?=$lTo;?><?=$lProduct;?><?=$lLocation;?><?=$lCargoCode;?><?=$lOwner;?><?=$lClient;?><?=$lAuthor;?><?=$lSelStatus;?><?=$lSelService;?>');
		}
		if(tclass=='fCargoCode'){
			$("#pleaseWait").toggle();
			$('#contenthere').load('/pages/report/report.php?cargoCode=<?=$lFrom;?><?=$lTo;?><?=$lProduct;?><?=$lLocation;?><?=$lReceiver;?><?=$lOwner;?><?=$lClient;?><?=$lAuthor;?><?=$lSelStatus;?><?=$lSelService;?>');
		}
		if(tclass=='fProduct'){
			$("#pleaseWait").toggle();
			$('#contenthere').load('/pages/report/report.php?product=<?=$lFrom;?><?=$lTo;?><?=$lReceiver;?><?=$lLocation;?><?=$lCargoCode;?><?=$lOwner;?><?=$lClient;?><?=$lAuthor;?><?=$lSelStatus;?><?=$lSelService;?>');
		}
		if(tclass=='fLocation'){
			$("#pleaseWait").toggle();
			$('#contenthere').load('/pages/report/report.php?location=<?=$lFrom;?><?=$lTo;?><?=$lProduct;?><?=$lReceiver;?><?=$lCargoCode;?><?=$lOwner;?><?=$lClient;?><?=$lAuthor;?><?=$lSelStatus;?><?=$lSelService;?>');
		}		
		if(tclass=='fAuthor'){
			$("#pleaseWait").toggle();
			$('#contenthere').load('/pages/report/report.php?author=<?=$lFrom;?><?=$lTo;?><?=$lProduct;?><?=$lReceiver;?><?=$lCargoCode;?><?=$lOwner;?><?=$lClient;?><?=$lLocation;?><?=$lSelStatus;?><?=$lSelService;?>');
		}	

		if(tclass=='fStatus'){
			$("#pleaseWait").toggle();
			$('#contenthere').load('/pages/report/report.php?selStatus=<?=$lFrom;?><?=$lTo;?><?=$lProduct;?><?=$lReceiver;?><?=$lCargoCode;?><?=$lOwner;?><?=$lClient;?><?=$lLocation;?><?=$lAuthor;?><?=$lSelService;?>');
		}	

		if(tclass=='fService'){
			$("#pleaseWait").toggle();
			$('#contenthere').load('/pages/report/report.php?selService=<?=$lFrom;?><?=$lTo;?><?=$lProduct;?><?=$lReceiver;?><?=$lCargoCode;?><?=$lOwner;?><?=$lClient;?><?=$lLocation;?><?=$lAuthor;?><?=$lSelStatus;?>');
		}						

	});
	</script>

<?php
	
if($dFrom){ $fDate = $dFrom; }else{ $fDate = date('01.m.Y 00:00:00'); }

if($dTo){ $tDate = $dTo; }else{ $tDate = date('t.m.Y 23:59:59'); }	

if(!$view){
	$weightFormat = getSettingUOM($conn, 'period_work_done_report_uom');	
	echo '<div id="contenthere">';									
		echo '<div class="page-header" style="margin-top: -5px;">
		  
			<div class="btn-group btn-group-xs" role="group" aria-label="Small button group" style="display:inline-block;"> 
				<a class="btn btn-default active classlist" ><i class="glyphicon glyphicon-list" style="color: #00B5AD" title="pamatdati"></i></a>
				
				<a href="print_report?view=print'.$lFrom.''.$lTo.''.$lProduct.''.$lClient.''.$lLocation.''.$lCargoCode.''.$lOwner.''.$lReceiver.''.$lAuthor.''.$lSelStatus.''.$lSelService.'" class="btn btn-default" ><i class="glyphicon glyphicon-print" style="color: #00B5AD" title="drukāt atskaiti"></i></a>				
			</div>';
			
			echo '
			<div style="float: right;" >
					<div style="display: inline-block;">
						<input type="text" class="form-control input-xs datepicker" id="dFrom" value="'.date('d.m.Y', strtotime($fDate)).'">
					</div>
					<div style="display: inline-block;">
						<input type="text" class="form-control input-xs datepicker" id="dTo" value="'.date('t.m.Y', strtotime($tDate)).'">
					</div>	
					<a class="btn btn-default btn-xs" onclick="dateFilter();"><i class="glyphicon glyphicon-refresh" style="color: #00B5AD"  title="filtrēt"></i></a>					
			</div>';	
					  	  
		echo '</div>';
		
		echo '<p style="display:inline-block;">perioda analītiskā atskaite</p>';

		echo '<div style="display:inline-block;" class="total-title"></div>';

		echo '<div class="clearfix"></div>';
	
		//saņēmēja filtrs
		echo '
			<div class="form-group col-md-2 pull-right" style="display: inline-block; padding-right; 10px; padding-bottom: 5px;">
			  <select class="form-control selectpicker btn-group-xs" data-live-search="true" title="saņēmējs" onchange="selReceiver(this)">';
			   $selectClient = mysqli_query($conn, "SELECT receiverCode, receiverName FROM cargo_header GROUP BY receiverCode") or die(mysqli_error($conn));
			  echo '<option value="">noņemt filtru</option>';
			  while($rowc = mysqli_fetch_array($selectClient)){
				  echo '<option value="'.$rowc['receiverCode'].'"';
				  if($rowc['receiverCode']==$receiver && $receiver!=''){echo ' selected';}
				  echo '>'.$rowc['receiverCode'].' '.$rowc['receiverName'].'</option>';
			  }
			  
			  echo '
			  </select>	
			</div>';
		
		//īpašnieka filtrs
		echo '
			<div class="form-group col-md-2 pull-right" style="display: inline-block; padding-right; 10px; padding-bottom: 5px;">
			  <select class="form-control selectpicker btn-group-xs" data-live-search="true" title="īpašnieks" onchange="selOwner(this)">';
			   $selectClient = mysqli_query($conn, "SELECT Code, Name FROM n_customers GROUP BY Code") or die(mysqli_error($conn));
			  echo '<option value="">noņemt filtru</option>';
			  while($rowc = mysqli_fetch_array($selectClient)){
				  echo '<option value="'.$rowc['Code'].'"';
				  if($rowc['Code']==$owner){echo ' selected';}
				  echo '>'.$rowc['Code'].' '.$rowc['Name'].'</option>';
			  }
			  
			  echo '
			  </select>	
			</div>';
		
		//klienta filtrs
		echo '
			<div class="form-group col-md-2 pull-right" style="display: inline-block; padding-right; 10px; padding-bottom: 5px;">
			  <select class="form-control selectpicker btn-group-xs" data-live-search="true" title="klients" onchange="selClient(this)">';
			   $selectClient = mysqli_query($conn, "SELECT Code, Name FROM n_customers GROUP BY Code") or die(mysqli_error($conn));
			  echo '<option value="">noņemt filtru</option>';
			  while($rowc = mysqli_fetch_array($selectClient)){
				  echo '<option value="'.$rowc['Code'].'"';
				  if($rowc['Code']==$client){echo ' selected';}
				  echo '>'.$rowc['Code'].' '.$rowc['Name'].'</option>';
			  }
			  
			  echo '
			  </select>	
			</div>';

		//noliktavas filtrs
		echo '
			<div class="form-group col-md-2 pull-right" style="display: inline-block; padding-bottom: 5px;">
			  <select class="form-control selectpicker btn-group-xs" data-live-search="true" title="noliktava" onchange="selLocation(this)">';
			  $selectProduct = mysqli_query($conn, "SELECT id, name FROM n_location") or die(mysqli_error($conn));
			  echo '<option value="">noņemt filtru</option>';
			  while($rowc = mysqli_fetch_array($selectProduct)){
				  echo '<option value="'.$rowc['id'].'"';
				  if($rowc['id']==$location){echo ' selected';}
				  echo '>'.$rowc['id'].' - '.$rowc['name'].'</option>';
			  }
			  
			  echo '
			  </select>	
			</div>';
		
		//preces filtrs
		echo '
			<div class="form-group col-md-2 pull-right" style="display: inline-block; padding-bottom: 5px;">
			  <select class="form-control selectpicker btn-group-xs" data-live-search="true" title="prece" onchange="selCode(this)">';
			  $selectProduct = mysqli_query($conn, "SELECT code, name1, name2 FROM n_items") or die(mysqli_error($conn));
			  echo '<option value="">noņemt filtru</option>';
			  while($rowc = mysqli_fetch_array($selectProduct)){
				  echo '<option value="'.$rowc['code'].'"';
				  if($rowc['code']==$product){echo ' selected';}
				  echo '>'.$rowc['code'].' '.$rowc['name1'].' '.$rowc['name2'].'</option>';
			  }
			  
			  echo '
			  </select>	
			</div>';

		//dokumenta numura filtrs
		echo '
			<div class="form-group col-md-2 pull-right" style="display: inline-block; padding-bottom: 5px;">
			  <select class="form-control selectpicker btn-group-xs" data-live-search="true" title="dokumenta nr." onchange="selCargoCode(this)">';
			  $selectProduct = mysqli_query($conn, "SELECT cargoCode FROM cargo_header GROUP BY cargoCode") or die(mysqli_error($conn));
			  echo '<option value="">noņemt filtru</option>';
			  while($rowc = mysqli_fetch_array($selectProduct)){
				  echo '<option value="'.$rowc['cargoCode'].'"';
				  if($rowc['cargoCode']==$cargoCode){echo ' selected';}
				  echo '>'.$rowc['cargoCode'].'</option>';
			  }
			  
			  echo '
			  </select>	
			</div>';

			if($dFrom){ $dateFrom = date('Y-m-d 00:00:00', strtotime(strtr($fDate, '.', '-'))); }else{ $dateFrom = date('Y-m-01 00:00:00'); }	

			if($dTo){ $dateTo = date('Y-m-d 23:59:59', strtotime(strtr($tDate, '.', '-'))); }else{ $dateTo = date('Y-m-t 23:59:59'); }			

			//autora filtrs
			echo '
				<div class="form-group col-md-2" style="display: inline-block; padding-bottom: 5px;">
				  <select class="form-control selectpicker btn-group-xs" data-live-search="true" title="darbinieks" onchange="selAuthor(this)">';
				   $selectProduct = mysqli_query($conn, "SELECT enteredBy FROM item_ledger_entry WHERE activityDate BETWEEN '".$dateFrom."' AND '".$dateTo."' GROUP BY enteredBy") or die(mysqli_error($conn));
				  echo '<option value="">noņemt filtru</option>';
				  while($rowc = mysqli_fetch_array($selectProduct)){
					  echo '<option value="'.$rowc['enteredBy'].'"';
					  if($rowc['enteredBy']==$author){echo ' selected';}
					  echo '>'.returnMeWho($rowc['enteredBy']).'</option>';
				  }
				  
				  echo '
				  </select>	
				</div>'; 
				
							//statusa filtrs
							echo '
								<div class="form-group col-md-2" style="display: inline-block; padding-bottom: 5px;">
								  <select class="form-control selectpicker btn-group-xs" data-live-search="true" title="status" onchange="selStatus(this)">';
                                  echo '<option value="">noņemt filtru</option>';
                                 
								

								  echo '<option value="20"';
								  if($selStatus==20){ echo ' selected';}
								  echo '>saņemta</option>';
                                 
                                  echo '<option value="40"';
								  if($selStatus==40){ echo ' selected';}
								  echo '>izsniegta</option>';
								  
								  echo '
								  </select>	
								</div>';  
								
								
							//pakalpojuma filtrs
							echo '
								<div class="form-group col-md-2" style="display: inline-block; padding-bottom: 5px;">
								  <select class="form-control selectpicker btn-group-xs" data-live-search="true" title="pakalpojums" onchange="selService(this)">';
                                  echo '<option value="">noņemt filtru</option>';
     
                                  echo '<option value="10"';
								  if($selService==10){ echo ' selected';}
								  echo '>saņemšana</option>';

								  echo '<option value="20"';
								  if($selService==20){ echo ' selected';}
								  echo '>pārvietošana</option>';

                                  echo '<option value="30"';
								  if($selService==30){ echo ' selected';}
								  echo '>komplektēšana</option>';
								  
                                  echo '<option value="40"';
								  if($selService==40){ echo ' selected';}
								  echo '>izsniegšana</option>';
								  
								  echo '
								  </select>	
								</div>';  								

			
	echo '<div class="clearfix"></div>';
	echo '<div id="results">';	



		if($client){ $tClient = " AND h.clientCode='".$client."'"; }else{ $tClient = null; }

		if($owner){ $tOwner = " AND h.ownerCode='".$owner."'"; }else{ $tOwner = null; }

		if($receiver){ $tReceiver = " AND h.receiverCode='".$receiver."'"; }else{ $tReceiver = null; }

		if($product){ $tProduct = " AND l.productNr='".$product."'"; }else{ $tProduct = null; }
			
		if($location){ $tLocation = " AND l.location='".$location."'"; }else{ $tLocation = null; }

		if($cargoCode){ $tCargoCode = " AND h.cargoCode='".$cargoCode."'"; }else{ $tCargoCode = null; }

		if($author){ $tAuthor = " AND l.enteredBy='".$author."'"; }else{ $tAuthor = null; }

		if($selStatus){ $tStatus = " AND status='".$selStatus."'"; }else{ $tStatus = null; }

		if($selService){ $tService = " AND action='".$selService."'"; }else{ $tService = null; }
	
		$entrys = mysqli_query($conn, "

		SELECT l.*, h.cargoCode AS cargoCode, h.clientCode AS clientCode, h.clientName AS clientName, h.ownerCode AS ownerCode, h.ownerName AS ownerName, h.receiverCode AS receiverCode, h.receiverName AS receiverName 
                            
		FROM cargo_line AS l 
		
		LEFT JOIN cargo_header AS h
		on l.docNr=h.docNr

		WHERE l.activityDate BETWEEN '".$dateFrom."' AND '".$dateTo."' ".$tClient." ".$tProduct." ".$tLocation." ".$tCargoCode." ".$tOwner." ".$tReceiver." ".$tAuthor." 

		") or die(mysqli_error($conn));


		if($cargoCode){echo '
			<div class="alert alert-info alert-dismissible" role="alert" style="display:inline-block; padding-top: 5px; padding-bottom: 5px;">
			  <button type="button" class="close removeFilter" data-dismiss="alert" id="fCargoCode"><span aria-hidden="true">&times;</span></button>
			  <strong>dokumenta nr.:</strong> '.$cargoCode.'
			</div>	
		';}	

		if($product){echo '
			<div class="alert alert-info alert-dismissible" role="alert" style="display:inline-block; padding-top: 5px; padding-bottom: 5px;">
			  <button type="button" class="close removeFilter" data-dismiss="alert" id="fProduct"><span aria-hidden="true">&times;</span></button>
			  <strong>prece:</strong> '.$product.' - '.returnProductName($conn, $product).'
			</div>	
		';}	

		if($location){echo '
			<div class="alert alert-info alert-dismissible" role="alert" style="display:inline-block; padding-top: 5px; padding-bottom: 5px;">
			  <button type="button" class="close removeFilter" data-dismiss="alert" id="fLocation"><span aria-hidden="true">&times;</span></button>
			  <strong>noliktava:</strong> '.$location.' - '.returnLocationName($conn, $location).'
			</div>	
		';}		

		if($client){echo '
			<div class="alert alert-info alert-dismissible" role="alert" style="display:inline-block; padding-top: 5px; padding-bottom: 5px;">
			  <button type="button" class="close removeFilter" data-dismiss="alert" id="fClient"><span aria-hidden="true">&times;</span></button>
			  <strong>klients:</strong> '.$client.' - '.returnClientName($conn, $client).'
			</div>	
		';}	

		if($owner){echo '
			<div class="alert alert-info alert-dismissible" role="alert" style="display:inline-block; padding-top: 5px; padding-bottom: 5px;">
			  <button type="button" class="close removeFilter" data-dismiss="alert" id="fOwner"><span aria-hidden="true">&times;</span></button>
			  <strong>īpašnieks:</strong> '.$owner.' - '.returnOwnerName($conn, $owner).'
			</div>	
		';}	

		if($receiver){echo '
			<div class="alert alert-info alert-dismissible" role="alert" style="display:inline-block; padding-top: 5px; padding-bottom: 5px;">
			  <button type="button" class="close removeFilter" data-dismiss="alert" id="fReceiver"><span aria-hidden="true">&times;</span></button>
			  <strong>saņēmējs:</strong> '.$receiver.' - '.returnReceiverName($conn, $receiver).'
			</div>	
		';}
		
		if($author){echo '
			<div class="alert alert-info alert-dismissible" role="alert" style="display:inline-block; padding-top: 5px; padding-bottom: 5px;">
			  <button type="button" class="close removeFilter" data-dismiss="alert" id="fAuthor"><span aria-hidden="true">&times;</span></button>
			  <strong>darbinieks:</strong> '.returnMeWho($author).'
			</div>	
		';}	
		
		if($selStatus){echo '
			<div class="alert alert-info alert-dismissible" role="alert" style="display:inline-block; padding-top: 5px; padding-bottom: 5px;">
			  <button type="button" class="close removeFilter" data-dismiss="alert" id="fStatus"><span aria-hidden="true">&times;</span></button>
			  <strong>status:</strong> '.returnStatus($selStatus).'
			</div>	
		';}		
		
		if($selService){echo '
			<div class="alert alert-info alert-dismissible" role="alert" style="display:inline-block; padding-top: 5px; padding-bottom: 5px;">
			  <button type="button" class="close removeFilter" data-dismiss="alert" id="fService"><span aria-hidden="true">&times;</span></button>
			  <strong>pakalpojums:</strong> '.returnAction($selService).'
			</div>	
		';}		

			
	
	
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
									<td>'.date('d.m.Y H:i:s', strtotime($aqRow['activityDate'])).'</td>

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

<script>
$(document).ready(function() {
   $('.selectpicker').selectpicker();
});
</script>

<div id="pleaseWait" style="display: none;">
    <h1 align="center" style="padding-top: 300px;"><i class="glyphicon glyphicon-refresh spin" style="color: #00B5AD"></i></h1>
</div>
<?php include_once("../../datepicker.php"); ?>
