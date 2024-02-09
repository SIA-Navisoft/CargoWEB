<?php
error_reporting(E_ALL ^ E_NOTICE);
require('lock.php');

$page_file="release";


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
<div class="container-fluid">
<div class="row">

  <div class="col-lg-10 col-centered">
			<div class="panel panel-default">
				<div class="panel-body"> 

<script>
$(document).ready(function(){
    $('.classlist').click(function(){
        $('#contenthere').load('/pages/release/release.php<?=$glpage;?>');
    });
})
</script>

<script>
$(document).ready(function(){
    $('.classhistory').click(function(){
        $('#contenthere').load('/pages/release/release.php?view=history');
    });
})
</script>

<script>
$(document).ready(function(){
    $('.classadd').click(function(){
        $('#contenthere').load('/pages/release/add.php');
    });
})
</script>

<script>
$(function() {
	$(".paging").delegate("a", "click", function(event) {	
		var url = $(this).attr('href');
		
		$('#contenthere').load('/pages/release/'+url);
		event.preventDefault();
	});
});
</script>

<script>
function newDoc(val) {
	$('#contenthere').load('/pages/release/release.php?view=edit&id='+val+'');
}
</script>

<script>
function lineHis(val) {
	$('#contenthere').load('/pages/release/release.php?view=history&id='+val+'');
}
</script>

<script>
function selCargo(val) {
	var val = val.value;
	$('#contenthere').load('/pages/release/release.php?cargo='+val+'');
}
</script>

<script>
function selCust(val) {
	var val = val.value;
	$('#contenthere').load('/pages/release/release.php?cust='+val+'');
}
</script>

<script type="text/javascript">

function getStates(value) {
    var search = $.post("/pages/release/search.php?view=cargo<?php if($cargo){ echo '&cargo='.$cargo; } ?>", {name:value},function(data){
        $("#results").html(data);		
    });
	if(search){
		$('#showWait').html('<i class="glyphicon glyphicon-refresh spin" style="color: #00B5AD"></i>&nbsp;&nbsp;');
	}
    search.done(function( data ) {
		$('#showWait').html('');
    });	
}
</script>

				
<?php
//skats uz ierakstiem
if (!$view){?>
	
	<?php

	require('inc/s.php');

	echo '<div id="contenthere">';									
		echo '<div class="page-header" style="margin-top: -5px;">
		  
			<div class="btn-group btn-group-xs" role="group" aria-label="Small button group" style="display:inline-block;"> 
				<a class="btn btn-default active classlist"><i class="glyphicon glyphicon-list" style="color: #00B5AD" title="pamatdati"></i></a> 
				<a class="btn btn-default classhistory" ><i class="glyphicon glyphicon-time" style="color: #00B5AD"  title="vēsture"></i></a>
				';
				
				echo '<a class="btn btn-default classadd" ><i class="glyphicon glyphicon-plus" style="color: #00B5AD"  title="pievienot"></i></a>';					
				
			echo '
			</div>';
			if ($res=="done"){echo '<div class="pull-right" style="margin-top: -2px;"><div class="btn btn-success">saglabāts!</div></div>';}
			  	  
		echo '</div>';
		
		echo '<p style="display:inline-block;">izdošana</p>';

		
		echo ' <div style="float: right;"><div id="showWait" style="display: inline-block;"></div><div style="display: inline-block;"><input type="text" id="searchWait" class="form-control input-xs" onkeyup="getStates(this.value)" placeholder="meklēt"></div></div>';   		
		
		echo '
			<div class="form-group col-md-2 pull-right">
			  <select class="form-control selectpicker btn-group-xs" data-live-search="true" title="filtrēt pēc izdošanas" onchange="selCargo(this)">';
			   $selectClient = mysqli_query($conn, "SELECT issuance_id FROM issuance_doc WHERE status='0' GROUP BY issuance_id") or die(mysqli_error($conn));
			  
			  while($rowc = mysqli_fetch_array($selectClient)){
				  echo '<option value="'.$rowc['issuance_id'].'">'.$rowc['issuance_id'].'</option>';
			  }
			  
			  echo '
			  </select>
			</div>
			
		';

		echo '
			<div class="form-group col-md-2 pull-right">
			  <select class="form-control selectpicker btn-group-xs" data-live-search="true" title="filtrēt pēc klienta" onchange="selCust(this)">';
			   $selectCust = mysqli_query($conn, "
					SELECT n_customers.Code, n_customers.Name 
					FROM n_customers
					LEFT JOIN issuance_doc					
					ON n_customers.Code=issuance_doc.clientCode
					WHERE issuance_doc.status=0
					GROUP BY n_customers.Code
				") or die(mysqli_error($conn));
			  
			  while($rowc = mysqli_fetch_array($selectCust)){
				  echo '<option value="'.$rowc['Code'].'">'.$rowc['Code'].' ('.$rowc['Name'].')</option>';
			  }
			  
			  echo '
			  </select>
			</div>
			<div class="clearfix"></div>
		';		
		
	
echo '<div id="results">';					
	$rec_limit = $a_p_l; $offset=0; $max_pages = 10;  //DEFINĒJAM LIMITUS
	$link_to = $page_file.'?page='; //LINKS KAS TIKS ATVĒRTS KAD NOSPIEDĪS UZ LAPAS, RAKSTĪT LĪDZ page 
	
	$query = "
		SELECT issuance_doc.* , 
		 (SELECT SUM(cargo_line.issueAmount) FROM cargo_line WHERE issuance_doc.issuance_id=cargo_line.issuance_id) AS total, 
		 (SELECT SUM(cargo_line.issue_place_count) FROM cargo_line WHERE issuance_doc.issuance_id=cargo_line.issuance_id) AS issue_place_count, 
		 (SELECT SUM(cargo_line.issueGross) FROM cargo_line WHERE issuance_doc.issuance_id=cargo_line.issuance_id) AS issueGross, 
		 (SELECT SUM(cargo_line.issueCubicMeters) FROM cargo_line WHERE issuance_doc.issuance_id=cargo_line.issuance_id) AS issueCubicMeters		
		FROM issuance_doc 	
		WHERE issuance_doc.status='0'
		ORDER BY CASE WHEN issuance_doc.scanStatus=200 THEN issuance_doc.scanStatus END DESC";  //NEPIECIEŠAMAIS VAICĀJUMS
	list($page_menu, $result_all, $resultGL7) = pageing_menu($offset, $rec_limit, $max_pages, $link_to, $page, $conn, $query);  //TIEK IEGŪTS ARRAY KO UZŅEM AR LIST 

	echo $page_menu;   //IZVADA TABULU AR LAPĀM			
	
	$count_GL7 = mysqli_num_rows($resultGL7);  //IEGŪST SKAITU 					
		if ($count_GL7!=0){

			echo '	<div class="table-responsive">
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

				$trAction = ' onclick="newDoc('.$row['id'].')"';
				if($row['scanStatus']==100){
					$trAction = ' style="background-color: silver;"';
				}
				
				$show_green=null;
				if($row['scanStatus']==200){
					$show_green = ' style="background-color: #ABEBC6; color: black;"';
				}					

				echo '	<tr  class="classlistedit" '.$trAction.'>';
							echo '<td '.$show_green.'>'.$row['issuance_id'].' ';
							
							$checkIfAllowedScannerClient = checkIfAllowedScannerClient($conn, $row['clientCode']);
							if($checkIfAllowedScannerClient){
								
								$checkLinesScanned = checkLinesScanned($conn, $row['issuance_id']);
								$checkPossibleLinesInRelease = checkPossibleLinesInRelease($conn, $row['clientCode'], $row['agreements'], $row['issuance_id'], $row['destination']);
								
								$linesScannedPercents='0';
								if($checkLinesScanned>0 && $checkPossibleLinesInRelease>0){
									$linesScannedPercents = $checkLinesScanned/$checkPossibleLinesInRelease*100;
								}
								
								echo '<i title="noskenēts / pieejams">( ';
								
								echo $checkLinesScanned.' / '.$checkPossibleLinesInRelease.' <b>'.number_format((float)$linesScannedPercents, 2, '.', '').'%</b>';
								
								echo ' )</i>';
							}
							
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
							echo '<td>'.floatval($row['issueCubicMeters']).' ';
							
							
							if($checkIfAllowedScannerClient){
								
								$checkM3Scanned = checkM3Scanned($conn, $row['issuance_id']);
								$checkPossibleM3InRelease = checkPossibleM3InRelease($conn, $row['clientCode'], $row['agreements'], $row['issuance_id'], $row['destination']);
								
								$linesScannedPercentsM3='0';
								if($checkM3Scanned>0 && $checkPossibleM3InRelease>0){
									$linesScannedPercentsM3 = $checkM3Scanned/$checkPossibleM3InRelease*100;
								}
								
								echo '<i title="noskenēts / pieejams">( ';
								
								echo $checkM3Scanned.' / '.$checkPossibleM3InRelease.' <b>'.number_format((float)$linesScannedPercentsM3, 2, '.', '').'%</b>';
								
								echo ' )</i>';
							}							
							
						echo '</tr>';
			}
			
			echo '</tbody></table></div>';
			mysqli_close($conn);
		}else{
			echo '<i class="glyphicon glyphicon-ban-circle glyphicon-lg" style="color: red;"></i> nav neviena ieraksta!';
		}
		echo '</div>';
}

?>

</div>
</div>
</div>
</div>
</div>
</div>


<?php include("footer.php"); ?>