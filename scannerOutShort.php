<?php
error_reporting(E_ALL ^ E_NOTICE);
require('lock.php');

$page_file="scannerOutShort";

$status_dayname = array( "1" => "pirmdiena", "2" => "otrdiena", "3" => "trešdiena", "4" => "ceturtdiena", "5" => "piektdiena", "6" => "sestdiena", "7" => "svētdiena");
$current_date = date("d.m.Y");
$current_time = date("H:i");
$current_day = $status_dayname[date("N")];



require('inc/s.php');
$result = mysqli_query($conn,"SELECT u_rights.p_view, u_rights.p_edit, s_pages.page_header, s_pages.page_icon, s_pages.page_table
								FROM setup_pages AS s_pages
								JOIN user_rights AS u_rights
								ON u_rights.page_name = s_pages.page_file
								WHERE u_rights.user_id = '".$myid."' AND u_rights.page_name='".$page_file."'");
if (!$result){die("Attention! Query to show fields failed.");}

ob_start();

if (mysqli_num_rows($result)<1){header("Location: welcome");die(0);}
$row = mysqli_fetch_assoc($result);
$p_view=$row['p_view'];
$p_edit=$row['p_edit'];

$page_header=$row['page_header'];

include('functions/base.php');
include('header.php');
$view = $action = $section = $id = $query = null;
if (isset($_GET['view'])){$view = htmlentities($_GET['view'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['section'])){$section = htmlentities($_GET['section'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['action'])){$action = htmlentities($_GET['action'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['id'])){$id = htmlentities($_GET['id'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['query'])){$query = htmlentities($_GET['query'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['res'])){$res = htmlentities($_GET['res'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['mode'])){$mode = htmlentities($_GET['mode'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['exitingSerial'])){$exitingSerial = htmlentities($_GET['exitingSerial'], ENT_QUOTES, "UTF-8");}

if (isset($_GET['serial'])){$serial = htmlentities($_GET['serial'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['placement'])){$placement = htmlentities($_GET['placement'], ENT_QUOTES, "UTF-8");}

if(!empty($_GET['page'])) {$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);if(false === $page) {$page = 1;}}else{$page = 1;}  //IEGŪSTAM LAPAS NUMURU

$shift=null;
if($id){
	
	$shift=returnActiveShift($conn, $id);
	if(!$shift){
		$shift='M01';
	}
	if(isset($_SESSION['shift'])){
		$shift=$_SESSION['shift'];
	}
	
}

?>
<style>					
input[type], .bootstrap-select {
	height: 34px;
	line-height: 1.5;
	font-size: 12px;
	padding: 1px 5px;
	border-radius: 3px;
}		
</style>
<?		

if(isset($_POST['allow']) && $_POST['allow']=='yes' && $action=='ask'){
	$action='';
}

if($action=='ask' && $id && $serial){
	
	?>
								
		<script>
		$(document).ready(function(){
			if(confirm("Preces galamēŗķi sakrīt, bet kuģi nē. Vai tiešām vēlaties pielikt šo preci?")){
				$('form#accept').submit();
			}else{
				window.location.href = "?view=<?=$view;?>&id=<?=$id;?>";
			}
		});
		</script>	

		<form name="scan" id="accept" enctype='multipart/form-data' method="POST" action="">
		
			<input type='hidden' name='source' value='scan'/>
			<input type="hidden" name="shift" value="<?=$shift;?>">
			<input type="hidden" name="id" value="<?=$id;?>">
			<input type="hidden" name="serial" value="<?=$serial;?>">
			<input type="hidden" name="view" value="<?=$view;?>">
			<input type="hidden" name="placement" value="<?=$placement;?>">
			<input type="hidden" name="allow" value="yes">
			
		</form>
	
	<?	
	die();
	
}				

// ADD, EDIT or DELETE
if($_SERVER["REQUEST_METHOD"] == "POST")
{
	if (empty($_POST["source"])){$source = null;}else{$source = htmlentities($_POST['source'], ENT_QUOTES, "UTF-8");}
	
	if($source=='shiftChange'){
		
		if(isset($_POST['shift'])){
			$_SESSION['shift']=$_POST['shift'];
		}
		
		header("Location: ".$page_file."?view=scan&id=".$id."");
		die(0);		
		
	}
	
	if($source=='correction'){
		
		if($id){
			
			$query = mysqli_query($conn, "SELECT id FROM issuance_doc WHERE issuance_id='".$id."' AND scanStatus='200' AND status=0");
			if(mysqli_num_rows($query)>0){
				
				if (isset($_POST['serial'])){$serial = htmlentities($_POST['serial'], ENT_QUOTES, "UTF-8");}
				
				$query2 = mysqli_query($conn, "SELECT id FROM cargo_line WHERE issuance_id='".$id."' AND serialNo='".$serial."' AND status=20");
				if(mysqli_num_rows($query2)>0){
					
					$form_data = array(
						'issueAmount' => '',
						'issue_assistant_amount' => '',
						'issueBy' => '',
						'issueDate' => '',
						'actualDate' => '',
						'issuance_id' => '',
						'for_issue' => 0,

						'issue_place_count' => 0,
						'issueTare' => 0,
						'issueGross' => 0,
						'issueNet' => 0,
						'issueCubicMeters' => 0,
						
						'issue_resource' => '',
						'issue_thisTransport' => '',
						'issue_weighing_act_no' => '',
						'issue_container_type_no' => '',
						'issue_declaration_type_no' => '',
						'issue_cargo_status' => '',
						'issue_seal_no' => '',
						'issue_lot_no' => '',					
						'fact_for_delta' => 0,
						'real_delta' => 0,
						'placement' => '',			
						'issuePlacement' => ''			
					);
					updateSomeRow("cargo_line", $form_data, "WHERE issuance_id='".$id."' AND serialNo='".$serial."' AND for_issue='1'");

					$_SESSION['serialAction']='deleted';
					$_SESSION['serial']=$serial;					
					
				}else{
					$_SESSION['serialAction']='error';
					$_SESSION['serial']=$serial;
				}
			}
			
			
		}
		
		header("Location: ".$page_file."?view=correction&id=".$id."");
		die(0);			
		
	}
	
	if($source=='add'){

		if (isset($_POST['destination'])){$destination = htmlentities($_POST['destination'], ENT_QUOTES, "UTF-8");}
		if (isset($_POST['contractNr'])){$contractNr = htmlentities($_POST['contractNr'], ENT_QUOTES, "UTF-8");}	

		$query = mysqli_query($conn, "SELECT customerNr FROM agreements WHERE contractNr='".$contractNr."'");
		$row = mysqli_fetch_array($query);
		$clientCode = $row['customerNr'];
		   
		if(in_array($clientCode, $allowedClients) && $destination){

			$places=$decks=null;
			if(isset($_POST['places'])){
				$places = implode(',', $_POST['places']);
			}
			
			if(isset($_POST['decks'])){
				$decks   = implode(',', $_POST['decks']);
			}			
			
			if($places || $decks){
				
				$id=findLastRow("issuance_doc")+1;
				  
				$issuance_id = 'ID'.sprintf("%'05d\n", $id);
				$issuance_id = trim($issuance_id);

				$now = date('Y-m-d H:i:s');
				$form_data = array(
					'issuance_id' => $issuance_id,

					'agreements' => $contractNr,
					'clientCode' => $clientCode,
					'destination' => $destination,
					
					'issueDate' => $now,
					'actualDate' => $now,
					'date' => $now,
					'applicationDate' => $now,
					
					
						
					'forScan' => 1,
					'createdBy' => $myid,
					'createdDate' => $now,
					'decks' => $decks,
					'places' => $places		

				);
				insertNewRows("issuance_doc", $form_data);
				
				header("Location: ".$page_file."?view=scan&id=".$issuance_id."");
				die(0);
				
			}else{
				
				header("Location: ".$page_file."");
				die(0);			
			}
			
			
		}else{
			
			header("Location: ".$page_file."");
			die(0);			
		}   

	}
	
	if ($source=='scan') {
		
		if (isset($_POST['serial'])){$serial = htmlentities($_POST['serial'], ENT_QUOTES, "UTF-8");}
		if (isset($_POST['allow'])){$allow = htmlentities($_POST['allow'], ENT_QUOTES, "UTF-8");}
		if (isset($_POST['placement'])){$placement = htmlentities($_POST['placement'], ENT_QUOTES, "UTF-8");}
		if (isset($_POST['shift'])){$shift = htmlentities($_POST['shift'], ENT_QUOTES, "UTF-8");}
		if($serial && $id){
			
			
			if(!$mode){
				
				
				$canBeSaved = checkIfSerialNoInIssuanceDoc($conn, $id, $serial);
				
				if($canBeSaved==1){
					
					$destination = returnIssueDestination($conn, $id);

					if($destination!='VISI'){
						$sameDestination = checkIfSerialNumberHasSameDestination($conn, $serial, $destination);
						
						if($sameDestination==0){
							
							
							$_SESSION['serial']=$serial;
							$_SESSION['serialAction']='addError';							
							
							header("Location: ".$page_file."?view=scan&id=".$id."");
							die(0);
							
						}
						if($sameDestination==1){
							
						}
						
					}
				
					$query = mysqli_query($conn, "SELECT id FROM scanner_lines_issuance WHERE issuance_id='".$id."' AND serialNo='".$serial."'");
					$count = mysqli_num_rows($query);
					
					if($count==0){
						
						
						$form_data = array(
							'issuance_id' => $id,
							'serialNo' => $serial,
							'createdBy' => $myid,
							'createdDate' => date('Y-m-d H:i:s'),
							'placement' => $placement,
							'shift' => $shift
						);
						insertNewRows("scanner_lines_issuance", $form_data);
						
						$_SESSION['serial']=$serial;
						$_SESSION['serialAction']='add';
						
					}
					
					if($count==1){
						$_SESSION['serial']=$serial;
						$_SESSION['serialAction']='add';
					}
					
					if(checkIfScannedIssuance($conn, $id)==1){

						$query = "UPDATE issuance_doc SET scanStatus='100' WHERE issuance_id = '".$id."'";
						mysqli_query($conn, $query) or die(mysqli_error($conn));					
						
					}	
					
					$query = "INSERT INTO scanner_lines_issuance_logs (issuance_id, serialNo, type) VALUES ('".$id."', '".$serial."', '0')";
					mysqli_query($conn, $query) or die(mysqli_error($conn));					
					
				}else{
					
						$query = "INSERT INTO scanner_lines_issuance_logs (issuance_id, serialNo, type) VALUES ('".$id."', '".$serial."', '1')";
						mysqli_query($conn, $query) or die(mysqli_error($conn));					
					
						$_SESSION['serial']=$serial;
						$_SESSION['serialAction']='addError';					
				}			
			
			}
			
			if($mode=='delete'){
				
				mysqli_query($conn,"DELETE FROM scanner_lines_issuance WHERE issuance_id='".$id."' AND serialNo='".$serial."'") or die(mysqli_error($conn));
				$deleted = mysqli_affected_rows($conn);
				
				if($deleted==1){
					$_SESSION['serial']=$serial;
					$_SESSION['serialAction']='delete';
				}	
				
			}			
						
		}else{
			$_SESSION['serialAction']='noSerial';
		}

		header("Location: ".$page_file."?view=scan&id=".$id."");
		die(0);		
			
	}	
	
	if($source=='finish' && $id){
		
		require('inc/s.php');
		$query = mysqli_query($conn, "SELECT id, serialNo, createdBy, createdDate, placement, shift FROM scanner_lines_issuance WHERE issuance_id='".$id."'");
		if(mysqli_num_rows($query)>0){
			
			
			$ouw = mysqli_query($conn, "SELECT thisTransport, declaration_type_no FROM issuance_doc WHERE issuance_id='".$id."'") or die(mysqli_error($conn));	
			
			$ou = mysqli_fetch_array($ouw);			
			
			
			while($row = mysqli_fetch_array($query)){
				
				$scanId = $row['id'];
				$createdBy = $row['createdBy'];
				$createdDate = $row['createdDate'];

				$getDetails = mysqli_query($conn, "SELECT * FROM cargo_line WHERE serialNo='".$row['serialNo']."'");
				
				if(mysqli_num_rows($getDetails)>0){
					
					$gdRow = mysqli_fetch_array($getDetails);

					$form_data = array(
						'issueAmount' => $gdRow['amount'],
						'issue_assistant_amount' => $gdRow['assistant_amount'],
						
						'issueBy' => $myid,
						'issueDate' => $issueDate,
						'actualDate' => $actualDate,
						
						'issuance_id' => $id,
						
						'for_issue' => 1,

						'issue_place_count' => $gdRow['place_count'],
						'issueTare' => $gdRow['tare'],
						'issueGross' => $gdRow['gross'],
						'issueNet' => $gdRow['net'],
						'issueCubicMeters' => $gdRow['cubicMeters'],

						'issue_resource' => $gdRow['resource'],
						
						'issue_thisTransport' => $ou['thisTransport'],
						
						'issue_weighing_act_no' => $gdrow['issue_weighing_act_no'],
						'issue_container_type_no' => $gdrow['issue_container_type_no'],
						
						'issue_declaration_type_no' => $ou['declaration_type_no'],
						
						'issue_cargo_status' => $gdrow['issue_cargo_status'],
						'issue_seal_no' => $gdrow['issue_seal_no'],
						'issue_lot_no' => $gdrow['issue_lot_no'],
						
						'fact_for_delta' => $gdRow['amount'],
						'real_delta' => 0,
						
						'issuanceScannedBy' => $createdBy, 
						'issuanceScannedDate' => $createdDate, 
						'issuanceScanId' => $scanId,
						'placement' => $row['placement'],
						'issuePlacement' => $row['placement'],
						'shift' => $row['shift']
						
						
						
					);
					updateSomeRow("cargo_line", $form_data, "WHERE serialNo='".$row['serialNo']."' LIMIT 1");
					
				}

			}
			
		}
		
		$query = "UPDATE issuance_doc SET scanned='1', scanFinishedBy='".$myid."', scanFinishedDate='".date('Y-m-d H:i:s')."', scanStatus='200' WHERE issuance_id = '".$id."'";
		mysqli_query($conn, $query) or die(mysqli_error($conn));		
		
		header("Location: ".$page_file."");
		die(0);		
		
	}//finish
	
	
	
}//post
?>
<style>
.aborder{
	border-right: solid #eee 1px !important;
}
.table td, .table th {
	padding: 2px !important;
	font-size: 15px;
}
</style>
<nav class="navbar navbar-default" style="margin-top: -20px; border-radius: 0px;">
  <div class="container-fluid">
    <div class="navbar-header">
	  
	  <a class="navbar-brand aborder menuClick" href="<?=$page_file;?>" title="Izdošanas dokumenti"><i class="glyphicon glyphicon-list"></i></a>
	  <? if($view=='scan' && $id!=''){ ?>

	  <? } ?> 
	  <? if(!$view){ ?>
		<a class="navbar-brand aborder menuClick" href="<?=$page_file;?>?view=add" title="Dokumenta pievienošana"><i style="color: green;" class="glyphicon glyphicon-plus"></i></a>
	  <? } ?> 

    </div>

    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-2">
      <ul class="nav navbar-nav">
		<li><a href="<?=$page_file?>?view=add">IZVEIDOT IZDOŠANAS DOKUMENTU</a></li>
      </ul>

    </div>
  </div>
</nav>

<style>
.col-centered{
    float: none;
    margin: 0 auto;
}
</style> 

<div class="container-fluid">
	<div class="row">
		<div class="col-lg-10 col-centered">
			<div class="panel panel-default">
				<div class="panel-body"> 

						<?
							$showId = null;
							if($id){ $showId .= '(<b>';
							
							$ladingNr = returnladingNrFromDoc($conn, $id);
							if($ladingNr){
								$showId .= $ladingNr;
							}else{
								$showId .= $id;
							}
							
							$showId .= '</b>)'; }
						
							$title = 'Izdošanas dokumenti '.$showId;
							if($view=='scan' && $mode=='delete'){
								$title = 'Dzēšanas režīms '.$showId;
							}
							if($view=='scan' && $mode==''){
								$title = 'Skenēšanas režīms '.$showId;
							}
							if($view=='recognize' && $mode==''){
								$title = 'Atpazīšanas režīms '.$showId;
							}
							if($view=='add'){
								$title = 'Pievienot dokumentu';
							}
							if($view=='correction'){
								$title = 'Skenēšanas korekcija';
							}		
				
				if($view=='correction' && !$id){
					
					require('inc/s.php');	
					$rec_limit = $a_p_l; $offset=0; $max_pages = 10;  //DEFINĒJAM LIMITUS
					$link_to = $page_file.'?page='; //LINKS KAS TIKS ATVĒRTS KAD NOSPIEDĪS UZ LAPAS, RAKSTĪT LĪDZ page 
					
					$query = "
						SELECT issuance_doc.* , 
						 (SELECT SUM(cargo_line.issueAmount) FROM cargo_line WHERE issuance_doc.issuance_id=cargo_line.issuance_id) AS total, 
						 (SELECT SUM(cargo_line.issue_place_count) FROM cargo_line WHERE issuance_doc.issuance_id=cargo_line.issuance_id) AS issue_place_count, 
						 (SELECT SUM(cargo_line.issueGross) FROM cargo_line WHERE issuance_doc.issuance_id=cargo_line.issuance_id) AS issueGross, 
						 (SELECT SUM(cargo_line.issueCubicMeters) FROM cargo_line WHERE issuance_doc.issuance_id=cargo_line.issuance_id) AS issueCubicMeters		
						FROM issuance_doc 	
						WHERE issuance_doc.status='0' AND scanned='1' AND forScan=1
						AND (SELECT COUNT(cargo_line.id) FROM cargo_line WHERE issuance_doc.issuance_id=cargo_line.issuance_id AND cargo_line.status!=20)=0
						AND clientCode IN(".$allowedClientsList.")";  //NEPIECIEŠAMAIS VAICĀJUMS
					list($page_menu, $result_all, $resultGL7) = pageing_menu($offset, $rec_limit, $max_pages, $link_to, $page, $conn, $query);  //TIEK IEGŪTS ARRAY KO UZŅEM AR LIST 

					echo $page_menu;   //IZVADA TABULU AR LAPĀM			
					
					$count_GL7 = mysqli_num_rows($resultGL7);  //IEGŪST SKAITU 					
					if ($count_GL7!=0){

						echo '
						<div class="table-responsive">
						  <table class="table table-hover table-responsive" border="1" style="border: 1px solid #ddd !important;">
						  <thead><tr>
							<th>izdošana</th>
							<th>klienta kods - nosaukums</th>
							<th>datums</th>
							<th>izdošanas akta nr.</th>
							<th>izdošanas datums</th>
							<th>faktiskais datums</th>
							<th>transporta veids</th>
							<th>izs. transporta nr.</th>
							
							<th>daudzums izdošanai</th>
							<th>vietu skaits</th>
							<th>bruto (kg)</th>
							<th>apjoms (m3)</th>
						</tr></thead><tbody>';
								
						while($row = mysqli_fetch_array($resultGL7)){

							echo '	<tr class="menuClick" onclick="window.location=\''.$page_file.'?view=correction&id='.$row['issuance_id'].'\'">';
										echo '<td>'.$row['issuance_id'];
										echo '<td nowrap>'.$row['clientCode'].'-'.returnClientName($conn, $row['clientCode']);
										echo '<td>'.date('d.m.Y', strtotime($row['date']));
										echo '<td>'.$row['issuance_act_no'];
										echo '<td>'.date('d.m.Y', strtotime($row['issueDate']));
										echo '<td>'.date('d.m.Y', strtotime($row['actualDate']));
								
										echo '<td>'.$row['transport'];
										echo '<td>'.$row['thisTransport'];
										
										echo '<td>'.floatval($row['total']);
										echo '<td>'.$row['issue_place_count'];
										echo '<td>'.floatval($row['issueGross']);
										echo '<td>'.floatval($row['issueCubicMeters']);
									echo '</tr>';
						}
						
						echo '</tbody></table></div>';
						
					}else{
						echo '<i class="glyphicon glyphicon-ban-circle glyphicon-lg" style="color: red;"></i> nav neviena ieraksta!';
					}
					
				}
				
				
				
				if($view=='correction' && $id){
					
					?>			
											
						<div class="row">
						
							<div class="col-lg-6">
							  <div id="response">
								<?php
								
									$getM3 = mysqli_query($conn, "
										SELECT COUNT(serialNo) AS fact, SUM(amount) AS m3

										FROM cargo_line

										WHERE issuance_id = '".$id."'									
									");
									$rowMr = mysqli_fetch_array($getM3);
									$m3 = floatval($rowMr['m3']);

									$fact = $rowMr['fact'];									
			
									if($_SESSION['serial']){
										
										$labelClass=null;
										$showInfo = 'Ok';
										if($_SESSION['serialAction']=='deleted'){
											$labelClass='success';
										}
										if($_SESSION['serialAction']=='error'){
											$labelClass='danger';
										}
											
										if($_SESSION['serialAction']=='error'){
											$showInfo = 'NAV DOK.';
										}
										
										echo '<span class="label label-'.$labelClass.'" style="height: 30px; width: 100% !important; margin: 2px; font-size: 17px; color: black; padding: 10px 2px 0px 2px; display: block;">
												'.$showInfo.' '.$_SESSION['serial'].'
											  </span>';
										
										unset($_SESSION['serial']);
										unset($_SESSION['serialAction']);
										unset($_SESSION['serialInDoc']);
									}
										  
									$buttonIcon='<i class="glyphicon glyphicon-minus" style="color: red;"></i>';

								?>
							  </div>
						    </div><br>
							
						    <div class="col-lg-6">
							
							
								<form name="correction" enctype='multipart/form-data' method="POST" action="">
								
									<input type='hidden' name='source' value='correction'/>							

								
									<div class="input-group">
										<span class="input-group-btn">
										  <a class="btn btn-default" id="serialClear"><i class="glyphicon glyphicon-remove" style="color: red;"></i></a>
										</span>									  
										<input type="text" class="form-control" id="serial" name="serial" placeholder="sērijas nr." autocomplete="off" autofocus oninvalid="this.setCustomValidity('šim laukam jābūt aizpildītam')" onchange="this.setCustomValidity('')" required>
										<span class="input-group-btn">
										  <a class="btn btn-default" id="serialButton" href="javascript:document.correction.submit()"><?=$buttonIcon;?></a>
										</span>
									</div>
									  
								</form> 
								  
						    </div>
						  
						</div>					
					
					<?
				}				

				if($view=='add'){
							
					if(COUNT($allowedClients)==1){
							
						if($allowedClients[0]){	
						
							?>
							
							<script>
								$(function() {
									 
									$('#destination').on('change', function(){
										
										if($('#destination').val() != '') {
											$('#secondLevel').show(); 
										} else {
											$('#secondLevel').hide(); 
										} 
										
									});
									
									$('.forPlaces, .forDecks').on('click', function(){
										
										var places = $(".places").is(":checked");
										var decks = $(".decks").is(":checked");
										
										if(places==true || decks==true){
											$('#addButton').show();
										}else{
											$('#addButton').hide();
										}
									});
									
								});					
							</script>
							
							<?						

							echo '							
							<form id="add" name="add" enctype="multipart/form-data" method="POST" action="">
								<input type="hidden" name="source" value="add"/>';
								
								$selectCustomers = mysqli_query($conn, "
								
									SELECT c.Code, c.Name, a.contractNr
									FROM n_customers AS c
									
									LEFT JOIN agreements AS a
									ON c.Code=a.customerNr
									
									WHERE a.useScan=1 AND a.customerNr='".$allowedClients[0]."'
									
								") or die(mysqli_error($conn));
								$rowc = mysqli_fetch_array($selectCustomers);
								echo '<input type="hidden" id="contractNr" name="contractNr" value="'.$rowc['contractNr'].'">';							
									
								echo '																					
								<div class="form-group col-md-3" style="padding: 0px;">
									<label class="lb-sm" for="destination">galamērķis</label>
									<select class="form-control selectpicker btn-group-xs input-xs" name="destination" id="destination"  data-live-search="true" title="galamērķis">';
									
									  $selectReceiver = mysqli_query($conn, "SELECT Code, name FROM destinations WHERE status=1") or die(mysqli_error($conn));
									  while($rowr = mysqli_fetch_array($selectReceiver)){
										echo '<option value="'.$rowr['Code'].'"  '; if($kIrow['destination']==$rowr['Code']){echo 'selected';} echo ' >'.$rowr['Code'].' - '.$rowr['name'].'</option>';
									  }
									
									echo '
									</select>
								</div>';								
								
								echo '<div style="width: 100%; display: none;" id="secondLevel"><div class="form-group col-md-3" style="display: inline-block; padding: 0px 2px 0px 0px; width: 50%;">
										<label>rūmes</label><br>';	
										
										for ($x = 1; $x <= 7; $x++) {
											
											echo '	
											<span class="button-checkbox">
												<button type="button" class="btn btn-xs forPlaces" data-color="success"><i class="state-icon glyphicon glyphicon-check"></i>&nbsp;R'.$x.'</button>
												<input type="checkbox" class="hidden places" id="places['.$x.']" name="places['.$x.']" value="R'.$x.'">
											</span>';
											
										}
								echo '</div>';	

								echo '<div class="form-group col-md-3" style="display: inline-block; padding: 0px 0px 4px 15px; width: 50%; float: right;">
										<label>klāji</label><br>';	
										
										for ($x = 1; $x <= 7; $x++) {

											echo '	
											<span class="button-checkbox">
												<button type="button" class="btn btn-xs forDecks" data-color="success"><i class="state-icon glyphicon glyphicon-check"></i>&nbsp;K'.$x.'</button>
												<input type="checkbox" class="hidden decks" id="decks['.$x.']" name="decks['.$x.']" value="K'.$x.'">
											</span>';
											
										}
								echo '</div></div><br>
								
								<a class="btn btn-success col-xs-12" style="font-size: 17px; display: none;" id="addButton" href="javascript:document.add.submit()">pievienot</a>								
								
							</form>';
							
						}						

					}	
					
					
					if(COUNT($allowedClients)>1){
							
						?>
						
						<script>
							$(function() {
								 
								$('#contractNr').on('change', function(){
									
									if($('#contractNr').val() != '') {
										$('#firstLevel').show(); 
									} else {
										$('#firstLevel').hide(); 
									} 
									
								});
								
								$('#destination').on('change', function(){
									
									if($('#destination').val() != '') {
										$('#secondLevel').show(); 
									} else {
										$('#secondLevel').hide(); 
									} 
									
								});								
								
								$('.forPlaces, .forDecks').on('click', function(){
									
									var places = $(".places").is(":checked");
									var decks = $(".decks").is(":checked");
									
									if(places==true && decks==true){
										$('#addButton').show();
									}else{
										$('#addButton').hide();
									}
								});
								
							});					
						</script>							
						<?						

							echo '							
							<form id="add" name="add" enctype="multipart/form-data" method="POST" action="">
								<input type="hidden" name="source" value="add"/>';
									
								echo '			
								<div class="form-group col-md-3" style="padding: 0px;">
									<label for="contractNr">līgums / klienta kods - nosaukums</label>
									<select class="form-control selectpicker btn-group-xs"  id="contractNr" name="contractNr"  data-live-search="true" title="līgums / klienta kods - nosaukums">';
									
									
									$selectCustomers = mysqli_query($conn, "
									
										SELECT Code, Name, a.contractNr 
										FROM n_customers AS c
										
										LEFT JOIN agreements AS a
										ON c.Code=a.customerNr
										
										WHERE a.useScan=1
										
									") or die(mysqli_error($conn));
									while($rowc = mysqli_fetch_array($selectCustomers)){
										echo '<option  value="'.$rowc['contractNr'].'">'.$rowc['contractNr'].' / '.$rowc['Code'].' - '.$rowc['Name'].'</option>';
									}
									
									echo '
									</select>	
								</div>';									
									
									
								echo '																					
								<div class="form-group col-md-3" style="padding: 0px; display: none;" id="firstLevel">
									<label class="lb-sm" for="destination">galamērķis</label>
									<select class="form-control selectpicker btn-group-xs input-xs" name="destination" id="destination"  data-live-search="true" title="galamērķis">';
									
									  $selectReceiver = mysqli_query($conn, "SELECT Code, name FROM destinations WHERE status=1") or die(mysqli_error($conn));
									  while($rowr = mysqli_fetch_array($selectReceiver)){
										echo '<option value="'.$rowr['Code'].'"  '; if($kIrow['destination']==$rowr['Code']){echo 'selected';} echo ' >'.$rowr['Code'].' - '.$rowr['name'].'</option>';
									  }
									
									echo '
									</select>
								</div>';								
								
								echo '<div style="width: 100%; display: none;" id="secondLevel"><div class="form-group col-md-3" style="display: inline-block; padding: 0px 2px 0px 0px; width: 50%;">
										<label>rūmes</label><br>';	
										
										for ($x = 1; $x <= 7; $x++) {
											
											echo '	
											<span class="button-checkbox">
												<button type="button" class="btn btn-xs forPlaces" data-color="success"><i class="state-icon glyphicon glyphicon-check"></i>&nbsp;R'.$x.'</button>
												<input type="checkbox" class="hidden places" id="places['.$x.']" name="places['.$x.']" value="R'.$x.'">
											</span>';
											
										}
								echo '</div>';	

								echo '<div class="form-group col-md-3" style="display: inline-block; padding: 0px 0px 4px 15px; width: 50%; float: right;">
										<label>klāji</label><br>';	
										
										for ($x = 1; $x <= 7; $x++) {

											echo '	
											<span class="button-checkbox">
												<button type="button" class="btn btn-xs forDecks" data-color="success"><i class="state-icon glyphicon glyphicon-check"></i>&nbsp;K'.$x.'</button>
												<input type="checkbox" class="hidden decks" id="decks['.$x.']" name="decks['.$x.']" value="K'.$x.'">
											</span>';
											
										}
								echo '</div></div><br>
								
								<a class="btn btn-success col-xs-12" style="font-size: 17px; display: none;" id="addButton" href="javascript:document.add.submit()">pievienot</a>								
								
							</form>';
						

					}					
					
				}

				if($view && $view!='correction' && $id){
				
					$query = mysqli_query($conn, "SELECT id
					FROM issuance_doc 
					WHERE status='0' AND scanned='0' AND forScan=1
					AND clientCode IN(".$allowedClientsList.")
					AND issuance_id='".$id."'");
					
					if(mysqli_num_rows($query)==0){
						header("Location: ".$page_file."");
						die(0);	
					}	
					
				}
				
				
				if (!$view){
					
					require('inc/s.php');	
					$rec_limit = $a_p_l; $offset=0; $max_pages = 10;  //DEFINĒJAM LIMITUS
					$link_to = $page_file.'?page='; //LINKS KAS TIKS ATVĒRTS KAD NOSPIEDĪS UZ LAPAS, RAKSTĪT LĪDZ page 
					
					$query = "
						SELECT issuance_doc.* , 
						 (SELECT SUM(cargo_line.issueAmount) FROM cargo_line WHERE issuance_doc.issuance_id=cargo_line.issuance_id) AS total, 
						 (SELECT SUM(cargo_line.issue_place_count) FROM cargo_line WHERE issuance_doc.issuance_id=cargo_line.issuance_id) AS issue_place_count, 
						 (SELECT SUM(cargo_line.issueGross) FROM cargo_line WHERE issuance_doc.issuance_id=cargo_line.issuance_id) AS issueGross, 
						 (SELECT SUM(cargo_line.issueCubicMeters) FROM cargo_line WHERE issuance_doc.issuance_id=cargo_line.issuance_id) AS issueCubicMeters		
						FROM issuance_doc 	
						WHERE issuance_doc.status='0' AND issuance_doc.scanned='0' AND issuance_doc.forScan=1
						AND issuance_doc.destination!='' AND (issuance_doc.places!='' OR issuance_doc.decks!='')
						AND (SELECT COUNT(cargo_line.id) FROM cargo_line WHERE issuance_doc.issuance_id=cargo_line.issuance_id)=0
						AND issuance_doc.clientCode IN(".$allowedClientsList.")";  //NEPIECIEŠAMAIS VAICĀJUMS
					list($page_menu, $result_all, $resultGL7) = pageing_menu($offset, $rec_limit, $max_pages, $link_to, $page, $conn, $query);  //TIEK IEGŪTS ARRAY KO UZŅEM AR LIST 

					echo $page_menu;   //IZVADA TABULU AR LAPĀM			
					
					$count_GL7 = mysqli_num_rows($resultGL7);  //IEGŪST SKAITU 					
						if ($count_GL7!=0){

							echo '
							<div class="table-responsive">
							  <table class="table table-hover table-responsive" border="1" style="border: 1px solid #ddd !important;">
							  <thead><tr>
								<th>izdošana</th>
								<th>klienta kods - nosaukums</th>
								<th>datums</th>
								<th>izdošanas akta nr.</th>
								<th>izdošanas datums</th>
								<th>faktiskais datums</th>

								<th>transporta veids</th>
								<th>izs. transporta nr.</th>
								
								<th>daudzums izdošanai</th>
								<th>vietu skaits</th>
								<th>bruto (kg)</th>
								<th>apjoms (m3)</th>
							</tr></thead><tbody>';
									
							while($row = mysqli_fetch_array($resultGL7)){

								echo '	<tr class="menuClick" onclick="window.location=\''.$page_file.'?view=scan&id='.$row['issuance_id'].'\'">';
											echo '<td>'.$row['issuance_id'];
											echo '<td nowrap>'.$row['clientCode'].'-'.returnClientName($conn, $row['clientCode']);
											echo '<td>'.date('d.m.Y', strtotime($row['date']));
											echo '<td>'.$row['issuance_act_no'];
											echo '<td>'.date('d.m.Y', strtotime($row['issueDate']));
											echo '<td>'.date('d.m.Y', strtotime($row['actualDate']));
				
											echo '<td>'.$row['transport'];
											echo '<td>'.$row['thisTransport'];
											
											echo '<td>'.floatval($row['total']);
											echo '<td>'.$row['issue_place_count'];
											echo '<td>'.floatval($row['issueGross']);
											echo '<td>'.floatval($row['issueCubicMeters']);
										echo '</tr>';
							}
							
							echo '</tbody></table></div>';
							
						}else{
							echo '<i class="glyphicon glyphicon-ban-circle glyphicon-lg" style="color: red;"></i> nav neviena ieraksta!';
						}

					echo '</div>';				
							
				}

				if($view=='scan' && $id){
					
					?>			
											
						<div class="row">
						
							<div class="col-lg-6">
							  <div id="response">
								<?php
								
									require('inc/s.php');

									$scanLines = mysqli_query($conn, "
									
										SELECT COUNT(serialNo) AS fact
									
										FROM scanner_lines_issuance 
										WHERE issuance_id='".$id."'
																				
									");	
									
									$rowSl = mysqli_fetch_array($scanLines);
									
									$fact = $rowSl['fact'];	

									$getM3 = mysqli_query($conn, "
										SELECT SUM(l.amount) AS m3

										FROM cargo_line AS l

										LEFT JOIN scanner_lines_issuance AS i
										ON l.serialNo=i.serialNo

										WHERE i.issuance_id = '".$id."'									
									");
									$rowMr = mysqli_fetch_array($getM3);
									$m3 = floatval($rowMr['m3']);

									if($_SESSION['serial']){
										
										$labelClass=null;
										if($_SESSION['serialAction']=='add'){
											$labelClass='success';
											
											?>
											<script>
											$(document).ready(function() {
												$('body').css('background', '#5cb85c');
											});
											</script>
											<?											
											
										}
										if($_SESSION['serialAction']=='delete'){
											$labelClass='danger';
											
											?>
											<script>
											$(document).ready(function() {
												$('body').css('background', '#d9534f');
											});
											</script>
											<?												
											
										}
										if($_SESSION['serialAction']=='addError'){
											$labelClass='danger';
											
											?>
											<script>
											$(document).ready(function() {
												$('body').css('background', '#d9534f');
											});
											</script>
											<?												
											
										}											
															
										$showInfo=null;
										if($_SESSION['serialInDoc']=='y' && $_SESSION['serial']!='NEZINĀMS'){
											$showInfo = '<i class="glyphicon glyphicon-barcode" id="addImageToExiting" style="color: yellow;"></i>';
											
											?>
											<script>
											$(document).ready(function() {
												$('body').css('background', '#d9534f');
											});
											</script>
											<?	
										}
										if($_SESSION['serialAction']=='addError'){
											$showInfo = 'NAV DOK.';
											?>
											<script>
											$(document).ready(function() {
												$('body').css('background', '#d9534f');
											});
											</script>
											<?											
										}  
																				
										unset($_SESSION['serial']);
										unset($_SESSION['serialAction']);
										unset($_SESSION['serialInDoc']);
									}else{
										
										if($_SESSION['serialAction']=='noSerial'){
											$labelClass='danger';
											
											?>
											<script>
											$(document).ready(function() {
												$('body').css('background', '#d9534f');
											});
											</script>
											<?												
											
										}	

										unset($_SESSION['serial']);
										unset($_SESSION['serialAction']);
										unset($_SESSION['serialInDoc']);
										
									}
										  
									$buttonIcon='<i class="glyphicon glyphicon-plus" style="color: green;"></i>';	  
									if($mode=='delete'){
										$buttonIcon='<i class="glyphicon glyphicon-erase" style="color: red;"></i>';
									}
								?>
							  </div>
						    </div>
							
						    <div class="col-lg-6">
							
							
								<div class="col-xs-5" style="padding: 0px;">
									<?
									
									
										function detectLog($conn, $issuance_id, $serialNo, $logId){
											
											$logs = mysqli_query($conn, "SELECT MIN(id) AS min FROM scanner_lines_issuance_logs WHERE issuance_id='".$issuance_id."' AND serialNo='".$serialNo."'");
											$row = mysqli_fetch_array($logs);	

											if($row['min']==$logId){
												return 0;
											}
											return 1;											
											
										}
									
										$slogs = mysqli_query($conn, "SELECT * FROM scanner_lines_issuance_logs WHERE issuance_id='".$id."' ORDER BY id DESC LIMIT 10");
										while($srow = mysqli_fetch_array($slogs)){
											
											$color=$bcolor=null;
											
											$detectLog = detectLog($conn, $srow['issuance_id'], $srow['serialNo'], $srow['id']);
											if($detectLog==1){ $bcolor='background-color: yellow;'; }
											
											
											if($srow['type']==1){$color='color: red;';}
											echo '<span style="'.$color.$bcolor.' font-weight: bold;">'.$srow['serialNo'].'</span><br>';
											
										}
									?>
								</div>
								<div class="col-xs-7" style="padding: 0px;">
								
									<style>

										.bootstrap-select {
										  width: 100%;
										}
										.bootstrap-select .btn {

										  font-size: 65px;
										  border: 0px;
										  font-weight: 1000;
										  margin: 0px;
										  padding: 0px;
										  

										}
										.bootstrap-select.btn-group .dropdown-toggle .filter-option {
											text-align: center !important;
										}

									</style>
								
									<form name="scan" id="scan" enctype='multipart/form-data' method="POST" action="">
									
										<input type='hidden' name='source' value='scan'/>							
										<input type='hidden' name='shift' value='<?=$shift;?>'>							
																	
										<?
																				
											if($mode!='delete'){
											
												$ouw = mysqli_query($conn, "
												
													SELECT places, decks
												
													FROM issuance_doc 
													WHERE issuance_id='".$id."'
																							
												") or die(mysqli_error($conn));	
												
												$ou = mysqli_fetch_array($ouw);
												
												$places = $ou['places'];
												$decks = $ou['decks'];
												
												$query = mysqli_query($conn, "SELECT placement FROM scanner_lines_issuance WHERE issuance_id='".$id."' ORDER BY id DESC") or die(mysqli_error($conn));
												$rowe = mysqli_fetch_array($query);
												
												$placement=null;
												$placement = $rowe['placement'];

												echo '<div class="form-group col-md-3 forSelect" style="height: 105px; padding: 1px; border: 1px solid #ccc; border-radius: 4px;">';
														
														$allPlaces = explode(',',$places);
														echo '<select class="form-control selectpicker" id="placement" name="placement"  data-live-search="true" title="novietojums">';
														
														$itsused=null;
														for ($x = 1; $x <= 7; $x++) {
															
															$checked_view=null;
															$ap=1;
															if (in_array('R'.$x, $allPlaces)){
																
																echo '<option value="R'.$x.'" data-code="R'.$x.'" '; 
																	if($placement=='R'.$x){echo 'selected';} 
																	if(!$placement && 1==$ap++){ echo 'selected'; $itsused='y'; } 
																echo '>R'.$x.'</option>';
																
															}
															
														}
														
														$allDecks = explode(',',$decks);
														for ($x = 1; $x <= 7; $x++) {

															$checked_view=null;
															$ad=1;
															if (in_array('K'.$x, $allDecks)){
																
																echo '<option value="K'.$x.'" data-code="K'.$x.'" '; 
																	if($placement=='K'.$x){echo 'selected';} 
																	if(!$placement && 1==$ad++ && !$itsused){ echo 'selected'; }
																echo '>K'.$x.'</option>';

															}
															
														}												
														
														echo '</select>';
												echo '</div>';
											
											}
											
										?>
									
											<style>
												input[type] {
													height: 70px;
													line-height: 1.5;
													font-size: 24px;
													padding: 1px 5px;
													border-radius: 3px;
													margin: 22px 0px -15px 0px;
												}										
											</style>
									  
											<input type="text" class="form-control" id="serial" name="serial" placeholder="sērijas nr." autocomplete="off" oninvalid="this.setCustomValidity('šim laukam jābūt aizpildītam')" onchange="this.setCustomValidity('')" autofocus required>
									  
									</form> 
									  
								
								<br>
								
								
									<form name="finish" id="finish" enctype='multipart/form-data' method="POST" action="">
									
										<input type='hidden' name='source' value='finish'/>
										
									</form>	
									
									
									
									
								</div>	
								
								
								
								
								
								
								
								
								
								
								
								
								
								

								
								
								
								
								
								
								
								
								
								
								
								
								
								
								
								
								
								
								
								
								
						
						  
						</div>					
					
					<?
					
				}					
				?>
				
				</div>
			</div>
			
		
			
			
		</div>
		
		<? if($view=='scan' && $id){ ?>
		<div class="panel panel-default">
			<div class="panel-body"> 
			
				<style>
					.wrap{
						text-align:center
					}
					.left{
						float: left;
					}
					.right{
						float: right;
					}
					.center{
						margin: 3px 0px 0px 0px;
					    display:inline-block;
						font-size: 25px;
					}				
				</style>
			
				<?
				
					$scanLines = mysqli_query($conn, "
					
						SELECT COUNT(serialNo) AS fact
					
						FROM scanner_lines_issuance 
						WHERE issuance_id='".$id."'
																
					");	
					
					$rowSl = mysqli_fetch_array($scanLines);
					
					$fact = $rowSl['fact'];

					$scanLinesM3 = mysqli_query($conn, "
					
						SELECT SUM(l.cubicMeters) as m3
						FROM cargo_line AS l

						JOIN scanner_lines_issuance AS s
						ON l.serialNo=s.serialNo
						
						WHERE s.issuance_id='".$id."'
																
					");	
					
					$rowSlM3 = mysqli_fetch_array($scanLinesM3);
					
					$m3 = $rowSlM3['m3'];	

					$scanLinesShift = mysqli_query($conn, "
					
						SELECT COUNT(serialNo) AS fact
					
						FROM scanner_lines_issuance 
						WHERE issuance_id='".$id."' AND shift='".$shift."'
																
					");	
					
					$rowSh = mysqli_fetch_array($scanLinesShift);
					
					$factShift = $rowSh['fact'];

					$scanLinesShM3 = mysqli_query($conn, "
					
						SELECT SUM(l.cubicMeters) as m3
						FROM cargo_line AS l

						JOIN scanner_lines_issuance AS s
						ON l.serialNo=s.serialNo
						
						WHERE s.issuance_id='".$id."' AND s.shift='".$shift."'
																
					");	
					
					$rowShM3 = mysqli_fetch_array($scanLinesShM3);
					
					$Shm3 = $rowShM3['m3'];					
				
				?>
			
				<div class="wrap">
				
					<div class="left">
						<div>Nosk.: <b><?=$fact;?></b></div>
						<div>Apjo.: <b><?=floatval($m3);?></b></div>
					</div>
					
					<div class="right">
						<div>Nosk.: <b><?=$factShift;?></b></div>
						<div>Apjo.: <b><?=floatval($Shm3);?></b></div>
					</div>
					
					<div class="center">
						<b><?=$shift;?><b>
					</div>
					
				</div>


			</div>
		</div>
		<? } ?>		
		
	</div>
	




	
	
<style>
p strong {
	font-weight: 1000;
}
</style>	
	
<div id="helpModal" class="modal fade">
	<div class="modal-dialog">
	
		<div class="modal-content">
			<div class="modal-body" style="font-size: 16px;">
				<p><b>Pogu palīdzība</b></p>
				
				
				<p>
					Lai nomainītu novietojumu ir jāiziet no sērijas numura lauka ar <strong>##</strong>.<br>
				</p>
				<p>					
					Mainīt rūmi var ar <strong>*</strong> un no <strong>1</strong> līdz <strong>7</strong>.<br>
					Mainīt klāju var ar <strong>#</strong> un no <strong>1</strong>  līdz <strong>7</strong>.<br>						
				</p>
				
				<p>Ar <strong>**</strong> aktivizē sērijas numura lauku.</p>
				
				<p>Nomainīt maiņu var ar <strong>*77</strong></p>
				
				<p>Pabeigt dokumentu var ar <strong>#0</strong></p>
				
				
				<p>Šo logu var atvērt/aizvērt ar <strong>00</strong></p>
				
				<p>Ar zaļu rāmi apvilktais lauks ir aktīvs. <br><b>Starp simboliem nedrīks būt ilgas pauzes.</b></p>
			</div>
		</div>
	</div>
</div>

<div id="changeShift" class="modal fade">
	<div class="modal-dialog">
	
		<div class="modal-content">
			<div class="modal-body" style="font-size: 16px;">
				<p><b>Nomainīt maiņu</b></p>
				
				
				<p>
					Lai nomainītu maiņu noskenējiet maiņas kodu.<br>
				</p>
				
				<form name="shiftChange" id="shiftChange" enctype='multipart/form-data' method="POST" action="">
					<input type="hidden" name="source" value="shiftChange">
					<input type="text" class="form-control" id="shift" name="shift" placeholder="Maiņas nr." autocomplete="off" autofocus>	
				</form>
				
				<br>
				
			</div>
		</div>
	</div>
</div>
	
	
	
	<script type='text/javascript'>

		$(document).ready(function(){
			$('#serialClear').on('click', function(){
				location.reload();
				$("#pleaseWait").toggle();
			});
			
			$('#serialButton').click(function(){
				$("#pleaseWait").toggle();
			});	
			
			$('.menuClick').click(function(){
				$("#pleaseWait").toggle();
			});		
			
		});

	</script>


							<script>

							$('body').keypress(function(e){
								if (e.keyCode == 13)
								{
										if($('#serial').val()!=''){
											$("#pleaseWait").toggle();
											$('#serial').blur();
											$('body').css('background', '#ffff4d');
											$('#scan').submit();
										}
								}
							});

							$(document).ready(function() {					
										
								//pārslēdz novietojumu
								Mousetrap.bind('* 1', function() { 
									selectvar = "R1";
									$("#placement option[data-code='" + selectvar + "']").prop("selected", true).trigger('change');
								});
								
								Mousetrap.bind('* 2', function() { 
									selectvar = "R2";
									$("#placement option[data-code='" + selectvar + "']").prop("selected", true).trigger('change');
								});
								
								Mousetrap.bind('* 3', function() { 
									selectvar = "R3";
									$("#placement option[data-code='" + selectvar + "']").prop("selected", true).trigger('change');
								});
								
								Mousetrap.bind('* 4', function() { 
									selectvar = "R4";
									$("#placement option[data-code='" + selectvar + "']").prop("selected", true).trigger('change');
								});
								
								Mousetrap.bind('* 5', function() { 
									selectvar = "R5";
									$("#placement option[data-code='" + selectvar + "']").prop("selected", true).trigger('change');
								});
								
								Mousetrap.bind('* 6', function() { 
									selectvar = "R6";
									$("#placement option[data-code='" + selectvar + "']").prop("selected", true).trigger('change');
								});
								
								Mousetrap.bind('* 7', function() { 
									selectvar = "R7";
									$("#placement option[data-code='" + selectvar + "']").prop("selected", true).trigger('change');
								});
								
								Mousetrap.bind('# 1', function() { 
									selectvar = "K1";
									$("#placement option[data-code='" + selectvar + "']").prop("selected", true).trigger('change');
								});
								
								Mousetrap.bind('# 2', function() { 
									selectvar = "K2";
									$("#placement option[data-code='" + selectvar + "']").prop("selected", true).trigger('change');
								});
								
								Mousetrap.bind('# 3', function() { 
									selectvar = "K3";
									$("#placement option[data-code='" + selectvar + "']").prop("selected", true).trigger('change');
								});
								
								Mousetrap.bind('# 4', function() { 
									selectvar = "K4";
									$("#placement option[data-code='" + selectvar + "']").prop("selected", true).trigger('change');
								});
								
								Mousetrap.bind('# 5', function() { 
									selectvar = "K5";
									$("#placement option[data-code='" + selectvar + "']").prop("selected", true).trigger('change');
								});
								
								Mousetrap.bind('# 6', function() { 
									selectvar = "K6";
									$("#placement option[data-code='" + selectvar + "']").prop("selected", true).trigger('change');
								});
								
								Mousetrap.bind('# 7', function() { 
									selectvar = "K7";
									$("#placement option[data-code='" + selectvar + "']").prop("selected", true).trigger('change');
								});
								//^^^ pārslēdz novietojumu
								
								//pabeidz dokumentu
								Mousetrap.bind('# 0', function() { 
								
									if(confirm('Vai tiešām vēlaties pabeigt dokumentu?')){
										$("#pleaseWait").toggle();
										$('body').css('background', '#ffff4d');
										$('#finish').submit();
									}
									
								});
								
								
								//aktivizē fokusu inputam
								Mousetrap.bind('* *', function() { 
									$("#serial").focus();
									
									setTimeout(
									  function() 
									  {
										$('#serial').val('');
									  }, 10);								
									
									
								});		

								//atver aizver palīdzību
								Mousetrap.bind('0 0', function() { 
								
									if($('#helpModal').is(':visible')){
										$("#helpModal").modal('hide');
									}else{
										$("#helpModal").modal('show');
									}
									
								});	

								//atver aizver maiņas maiņu
								Mousetrap.bind('* 7 7', function() { 
								
									if($('#changeShift').is(':visible')){
										$("#changeShift").modal('hide');
									}else{
										$("#changeShift").modal('show');
									}
									
								});									
								
								//pazaudē fokusu inputam
								$('body').on('keyup', '#serial', function(){
									
									if($('#serial').val()=='##'){
										$('#serial').blur();
										$('#serial').val('');
									}
									
									if($('#serial').val()=='00'){
										
										if($('#helpModal').is(':visible')){
											$("#helpModal").modal('hide');
										}else{
											$("#helpModal").modal('show');
										}										
										
										$('#serial').blur();
										$('#serial').val('');
									}

									if($('#serial').val()=='*77'){
										
										if($('#changeShift').is(':visible')){
											$("#changeShift").modal('hide');
										}else{
											$("#changeShift").modal('show');
										}										
										
										$('#serial').blur();
										$('#serial').val('');
									}	

									if($('#serial').val()=='#0'){

										$('#serial').val('');
										if(confirm('Vai tiešām vēlaties pabeigt dokumentu?')){
											$("#pleaseWait").toggle();
											$('body').css('background', '#ffff4d');
											$('#finish').submit();					
										}

									}	
									
								});			

								//nosūta formu ja skenēšanas poga nospiesta
								$('#serial').on('keyup', function (event) {
									 
									if(event.keyCode==86 || event.which==86){
										if($('#serial').val()!=''){
											$("#pleaseWait").toggle();
											$('#serial').blur();
											$('body').css('background', '#ffff4d');
											$('#scan').submit();
										}
									}
									
								});	
								
								//nosūta formu ja skenēšanas poga nospiesta (maiņas maiņai)
								$('#shift').on('keyup', function (event) {
 
									if(event.keyCode==86 || event.which==86){
										if($('#shift').val()!=''){
											$("#pleaseWait").toggle();
											$('#shift').blur();
											$('body').css('background', '#ffff4d');
											$('#shiftChange').submit();
										}
									}
									
								});								

								$('#serial').addClass('green-border');
								$(function(){
									$('input:required').on('blur', function(){									   
									   $('.forSelect').addClass('green-border');
									   $('#serial').removeClass('green-border');
									   console.log('uzliekam selectam noņemam inputam');
									});
									
									$('input:required').on('focus', function(){
									   $('#serial').addClass('green-border');
									   $('.forSelect').removeClass('green-border');
									   console.log('uzliekam inputam noņemam selectam');
									});		
									
								});				
								
								$('#changeShift').on('shown.bs.modal', function () {
									$('#shift').focus();
								});		
																	
							});
						
							</script>
	<style>
	.green-border {
	  border: 5px solid green !important;  
	}

	</style>
	<div id="pleaseWait" style="display: none;">
		<h1 align="center" style="padding-top: 300px;"><i class="glyphicon glyphicon-refresh spin" style="color: #00B5AD"></i></h1>
	</div>

	<?php include("footer.php"); ?>	
	
	
</div>





