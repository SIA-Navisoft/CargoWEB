<?php
error_reporting(E_ALL ^ E_NOTICE);

require('../../lock.php');

$page_file="release";



require('../../inc/s.php');
$result = mysqli_query($conn,"SELECT u_rights.p_view, u_rights.p_edit, s_pages.page_header, s_pages.page_icon, s_pages.page_table
								FROM setup_pages AS s_pages
								JOIN user_rights AS u_rights
								ON u_rights.page_name = s_pages.page_file
								WHERE u_rights.user_id = '".$myid."' AND u_rights.page_name='".$page_file."'");
if (!$result){die("Attention! Query to show fields failed.");}

if (mysqli_num_rows($result)<1){header("Location: welcome");die(0);}

echo '<script src="../../js/cbx.js"></script>';

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

$view = $action = $section = $id = $query = $searchquery= null;
if (isset($_GET['view'])){$view = htmlentities($_GET['view'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['id'])){$id = htmlentities($_GET['id'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['res'])){$res = htmlentities($_GET['res'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['ser'])){$ser = htmlentities($_GET['ser'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['client'])){$client = htmlentities($_GET['client'], ENT_QUOTES, "UTF-8");}

if (isset($_GET['cargo'])){$cargo = htmlentities($_GET['cargo'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['cust'])){$cust = htmlentities($_GET['cust'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['sr'])){$sr = htmlentities($_GET['sr'], ENT_QUOTES, "UTF-8");}

if (isset($_GET['dest'])){$dest = htmlentities($_GET['dest'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['showToGive'])){$showToGive = htmlentities($_GET['showToGive'], ENT_QUOTES, "UTF-8");}

include('../../functions/base.php');
require('../../inc/s.php');

if(!empty($_GET['page'])) {$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);if(false === $page) {$page = 1;}}else{$page = 1;}  //IEGŪSTAM LAPAS NUMURU
if($page){$glpage = '?page='.$page;}else{$glpage = null;}
?>

<script>
$(document).ready(function(){
    $('.classlist').click(function(){
        $('#contenthere').load('/pages/release/release.php<?=$glpage;?>');
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
$(document).ready(function(){
    $('.classhistory').click(function(){
        $('#contenthere').load('/pages/release/release.php?view=history');
    });
})
</script>

<script>
$(function() {
	$(".paging").delegate("a", "click", function(event) {	
		var url = $(this).attr('href');
		
		var view = '<?=$view;?>'; 
		if(view){
			var view = '&view='+view;
		}else{
			var view = '';
		}
	
		if(view){
			
			var sr = '<?=$sr;?>';
			if(sr){
				var search = '<?=$sr;?>';
			}else{
				var search = $('#searchWait').val();
			}
			
			
			$('#contenthere').load('/pages/release/'+url+''+view+'&id=<?=$id;?>&showToGive=<?=$showToGive;?>&sr='+search);
		}else{
			$('#contenthere').load('/pages/release/'+url+''+view);
		}
		event.preventDefault();
	});
});
</script>

<script>
function selCargo(val) {
	var val = val.value;
	var search = $('#searchWait').val();
	$('#contenthere').load('/pages/release/release.php?cargo='+val+'&search='+search+'<?php if($cust){echo '&cust='.$cust;} if($dest){echo '&dest='.$dest;} ?>');
}

function selDestination(val) {
	var val = val.value;
	var search = $('#searchWait').val();
	$('#contenthere').load('/pages/release/release.php?dest='+val+'&search='+search+'<?php if($cust){echo '&cust='.$cust;} ?>');
}
</script>

<script>
function selCust(val) {
	
	var val = val.value;
	var search = $('#searchWait').val();
	$('#contenthere').load('/pages/release/release.php?cust='+val+'&search='+search+'<?php if($cargo){echo '&cargo='.$cargo;} ?>');
}
</script>

<script type="text/javascript">

function getStates(value) {
	
    var search = $.post("/pages/release/search.php?view=cargo<?php if($cargo){ echo '&cargo='.$cargo; } ?>", {name:value},function(data){
        $("#results").html(data);		
    });
	if(search){
		console.log('S1');
		$('#showWait').html('<i class="glyphicon glyphicon-refresh spin" style="color: #00B5AD"></i>&nbsp;&nbsp;');
	}
    search.done(function( data ) {
		$('#showWait').html('');
    });	
}
</script>

<?php
if(!$view){
?>

	<script>
	$('#removeFilterCargo').on('click', function() {
		var search = $('#searchWait').val();
		$('#contenthere').load('/pages/release/release.php?cust=<?=$cust;?>&search='+search);
	});
	$('#removeFilterCust').on('click', function() {
		var search = $('#searchWait').val();
		$('#contenthere').load('/pages/release/release.php?cargo=<?=$cargo;?>&search='+search);
	});	
	</script>
<?php	
		echo '<div class="page-header" style="margin-top: -5px;">
		  
			<div class="btn-group btn-group-xs" role="group" aria-label="Small button group" style="display:inline-block;"> 
				<a class="btn btn-default active classlist" ><i class="glyphicon glyphicon-list" style="color: #00B5AD" title="pamatdati"></i></a>
				<a class="btn btn-default classhistory" ><i class="glyphicon glyphicon-time" style="color: #00B5AD"  title="vēsture"></i></a>	
				';
				
				echo '<a class="btn btn-default classadd" ><i class="glyphicon glyphicon-plus" style="color: #00B5AD"  title="pievienot"></i></a>';					
				
			echo '					
			</div>';
			if ($res=="done"){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-success btn-xs" >saglabāts!</div></div>';}
			  	  
		echo '</div>';
		
		echo '<p style="display:inline-block;">izdošana</p>';

		echo ' <div style="float: right;"><div id="showWait" style="display: inline-block;"></div><div style="display: inline-block;"><input type="text" id="searchWait" class="form-control input-xs" onkeyup="getStates(this.value)" placeholder="meklēt" value="'.$_GET['search'].'"></div></div>';  


		if($cargo){echo '
			<div class="alert alert-info alert-dismissible" role="alert" style="display:inline-block; padding-top: 5px; padding-bottom: 5px;">
			  <button type="button" class="close" data-dismiss="alert" id="removeFilterCargo"><span aria-hidden="true">&times;</span></button>
			  <strong>filtrs pēc izdošanas:</strong> '.$cargo.'
			</div>	
		';}

		$forId = null;
		if($cust){echo '
			<div class="alert alert-info alert-dismissible" role="alert" style="display:inline-block; padding-top: 5px; padding-bottom: 5px;">
			  <button type="button" class="close" data-dismiss="alert" id="removeFilterCust"><span aria-hidden="true">&times;</span></button>
			  <strong>filtrs pēc klienta:</strong> '.$cust.'
			</div>';
			$forId = " AND clientCode='".$cust."'";
		}		
		
		echo '
			<div class="form-group col-md-2 pull-right">
			  <select class="form-control selectpicker btn-group-xs" data-live-search="true" title="filtrēt pēc izdošanas" onchange="selCargo(this)">';
			   $selectClient = mysqli_query($conn, "SELECT issuance_id FROM issuance_doc WHERE status='0' ".$forId." GROUP BY issuance_id") or die(mysqli_error($conn));
			  
			  while($rowc = mysqli_fetch_array($selectClient)){
				  echo '<option value="'.$rowc['issuance_id'].'"';
				  if($rowc['issuance_id']==$cargo){ echo ' selected';}
				 echo ' >'.$rowc['issuance_id'].'</option>';
			  }
			  
			  echo '
			  </select>	
			</div>';
			
			if($cust){
				
				if(checkIfAllowedScannerClient($conn, $cust)==1){
					
					echo '			
					<div class="form-group col-md-2 pull-right">	
						<select class="form-control selectpicker btn-group-xs input-xs" name="destination" id="destination"  data-live-search="true" title="galamērķis" onchange="selDestination(this)">';
						
						  $selectReceiver = mysqli_query($conn, "SELECT Code, name FROM destinations WHERE status=1") or die(mysqli_error($conn));
						  while($rowr = mysqli_fetch_array($selectReceiver)){
								echo '<option value="'.$rowr['Code'].'"  '; if($dest==$rowr['Code']){echo 'selected';} echo ' >'.$rowr['Code'].' - '.$rowr['name'].'</option>';
						  }
						
						echo '
						</select>				
					</div>';
					
				}	
				
			}			
			
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
				  echo '<option value="'.$rowc['Code'].'"';
				  if($rowc['Code']==$cust){ echo ' selected';}
				 echo ' >'.$rowc['Code'].' ('.$rowc['Name'].')</option>';
			  }
			  
			  echo '
			  </select>
			</div>';			
			
				
			
			echo '
			<div class="clearfix"></div>
			';		

			if($cargo){$filterCargo = ' AND issuance_doc.issuance_id="'.$cargo.'"';}else{$filterCargo = null;}
			if($cust){$filterCust = ' AND issuance_doc.clientCode="'.$cust.'"';}else{$filterCust = null;}			
			if($dest){$filterDest = ' AND issuance_doc.destination="'.$dest.'" AND issuance_doc.scanStatus=200';}else{$filterDest = null;}			
			
	
echo '<div id="results">';	

	$keepFilter=null;
	if($cargo){$keepFilter.='&cargo='.$cargo;}
	if($cust){$keepFilter.='&cust='.$cust;}
	if($dest){$keepFilter.='&dest='.$dest;}
		
	if($_GET['search']){
		$s = mysqli_real_escape_string($conn, $_GET['search']);	
		$search_result = " AND (issuance_id LIKE '%$s%' || cargo LIKE '%$s%' || issueDate LIKE '%$s%' || actualDate LIKE '%$s%' || brigade LIKE '%$s%' || date LIKE '%$s%' || time_from LIKE '%$s%' || time_to LIKE '%$s%' || place LIKE '%$s%' || transport LIKE '%$s%' || issuance_act_no LIKE '%$s%')";
		
		$searchUrl = '&search='.$s;
	
	}else{
		$searchUrl = null;	
		$search_result = "";
	
	}
					
	$rec_limit = $a_p_l; $offset=0; $max_pages = 10;  //DEFINĒJAM LIMITUS
	$link_to = $page_file.'?view='.$keepFilter.$searchUrl.'&page='; //LINKS KAS TIKS ATVĒRTS KAD NOSPIEDĪS UZ LAPAS, RAKSTĪT LĪDZ page 

	$query = "
		SELECT issuance_doc.* , 
		 (SELECT SUM(cargo_line.issueAmount) FROM cargo_line WHERE issuance_doc.issuance_id=cargo_line.issuance_id) AS total, 
		 (SELECT SUM(cargo_line.issue_place_count) FROM cargo_line WHERE issuance_doc.issuance_id=cargo_line.issuance_id) AS issue_place_count, 
		 (SELECT SUM(cargo_line.issueGross) FROM cargo_line WHERE issuance_doc.issuance_id=cargo_line.issuance_id) AS issueGross, 
		 (SELECT SUM(cargo_line.issueCubicMeters) FROM cargo_line WHERE issuance_doc.issuance_id=cargo_line.issuance_id) AS issueCubicMeters		
		FROM issuance_doc 	
		WHERE issuance_doc.status='0' ".$search_result." ".$filterCargo." ".$filterCust." ".$filterDest."
		ORDER BY CASE WHEN issuance_doc.scanStatus=200 THEN issuance_doc.scanStatus END DESC";  //NEPIECIEŠAMAIS VAICĀJUMS	
		
	list($page_menu, $result_all, $resultGL7) = pageing_menu($offset, $rec_limit, $max_pages, $link_to, $page, $conn, $query);  //TIEK IEGŪTS ARRAY KO UZŅEM AR LIST 


	$mainTable = null;

	$mainTable .= $page_menu;   //IZVADA TABULU AR LAPĀM			
	
	$count_GL7 = mysqli_num_rows($resultGL7);  //IEGŪST SKAITU 					
		if ($count_GL7!=0){

			$mainTable .= '	<div class="table-responsive"><table class="table table-hover table-responsive" border="1" style="border: 1px solid #ddd !important;"><thead><tr>
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
					
			$countPlaceTotal=$countCubicMetersTotal=0;		
			while($row = mysqli_fetch_array($resultGL7)){
				
				$trAction = ' onclick="newDoc('.$row['id'].')"';
				if($row['scanStatus']==100){
					$trAction = ' style="background-color: silver;"';
				}
				
				$show_green=null;
				if($row['scanStatus']==200){
					$show_green = ' style="background-color: #ABEBC6; color: black;"';
				}							
							
				$mainTable .= '	<tr  class="classlistedit" '.$trAction.'>';
							
							$mainTable .= '<td '.$show_green.'>'.$row['issuance_id'].' ';
							
							$checkIfAllowedScannerClient = checkIfAllowedScannerClient($conn, $row['clientCode']);
							if($checkIfAllowedScannerClient){
								
								$checkLinesScanned = checkLinesScanned($conn, $row['issuance_id']);
								$checkPossibleLinesInRelease = checkPossibleLinesInRelease($conn, $row['clientCode'], $row['agreements'], $row['issuance_id'], $row['destination']);
								
								$linesScannedPercents='0';
								if($checkLinesScanned>0 && $checkPossibleLinesInRelease>0){
									$linesScannedPercents = $checkLinesScanned/$checkPossibleLinesInRelease*100;
								}
								
								$mainTable .= '<i title="noskenēts / pieejams">( ';
								
								$mainTable .= $checkLinesScanned.' / '.$checkPossibleLinesInRelease.' <b>'.number_format((float)$linesScannedPercents, 2, '.', '').'%</b>';
								
								$mainTable .= ' )</i>';
							}							
							
							$mainTable .= '<td nowrap>'.$row['clientCode'].'-'.returnClientName($conn, $row['clientCode']);
							$mainTable .= '<td>'.date('d.m.Y', strtotime($row['date']));
							$mainTable .= '<td>'.$row['issuance_act_no'];
							$mainTable .= '<td>'.date('d.m.Y', strtotime($row['issueDate']));
							$mainTable .= '<td>'.date('d.m.Y', strtotime($row['actualDate']));
	
							$mainTable .= '<td>'.$row['transport'];
							$mainTable .= '<td>'.$row['thisTransport'];
							
							$mainTable .= '<td>'.floatval($row['total']);
							$mainTable .= '<td>'.$row['issue_place_count'];
							$mainTable .= '<td>'.floatval($row['issueGross']);
							$mainTable .= '<td>'.floatval($row['issueCubicMeters']).' ';
							
							
							if($checkIfAllowedScannerClient){
								
								$checkM3Scanned = checkM3Scanned($conn, $row['issuance_id']);
								$checkPossibleM3InRelease = checkPossibleM3InRelease($conn, $row['clientCode'], $row['agreements'], $row['issuance_id'], $row['destination']);
								
								$linesScannedPercentsM3='0';
								if($checkM3Scanned>0 && $checkPossibleM3InRelease>0){
									$linesScannedPercentsM3 = $checkM3Scanned/$checkPossibleM3InRelease*100;
								}
								
								$mainTable .= '<i title="noskenēts / pieejams">( ';
								
								$mainTable .= $checkM3Scanned.' / '.$checkPossibleM3InRelease.' <b>'.number_format((float)$linesScannedPercentsM3, 2, '.', '').'%</b>';
								
								$mainTable .= ' )</i>';
							}							
							
							
						$mainTable .= '</tr>';
						
						$countPlaceTotal += $row['issue_place_count'];
						$countCubicMetersTotal += floatval($row['issueCubicMeters']);
			}
			
			$mainTable .= '</tbody></table></div>';
			
			if($cust && $dest){
				
				if(checkIfAllowedScannerClient($conn, $cust)==1){
					
					echo '<div style="float: right;">vietu skaits kopā: <b>'.$countPlaceTotal.'</b> apjoms (m3) kopā: <b>'.$countCubicMetersTotal.'</b></div><br><br>';
					
				}

			}			
			
			
			echo $mainTable;
			
			
			mysqli_close($conn);
		}else{
			echo '<i class="glyphicon glyphicon-ban-circle glyphicon-lg" style="color: red;"></i> nav neviena ieraksta!';
		}
		echo '</div>';
}
if($view=='edit'){
?>
<script>


</script>
	<script>	  
	function approveOneLine(val) {
		event.preventDefault();

		var lineAmount = $('.lineAmount'+val).val();
		var lineDate = $('.lineDate'+val).val();
		
		var actualDate = $('.actualDate'+val).val();
		
		var searchWait = $('#searchWait').val();
		var searchWait = encodeURI(searchWait);
		
		var eDelta = $('#eDelta'+val).val();
		var eDeltaT = $('#eDeltaT'+val).val(); //starpība

		alert(eDelta+' '+eDeltaT);
		
		$.ajax({
			global: false,
			type: 'GET',
			url: '/pages/release/action.php?action=approveOneLine&id='+val+'&cardId=<?=$id;?>&issueAmount='+lineAmount+'&issueDate='+lineDate+'&actualDate='+actualDate+'&eDelta='+eDelta+'&eDeltaT='+eDeltaT+'',
			beforeSend: function(){
				$('#approveOneLine'+val).html("<i class=\"glyphicon glyphicon-ban-circle\" style=\"color: red;\"></i> gaidiet...");
				$("#approveOneLine"+val).prop("disabled",true);
			},				
			success: function (result) {
				console.log(result+' CL-6');
				
					if(result=='STOP'){
						location.reload();
					}		
		
				$('#contenthere').load('/pages/release/release.php?view=edit&id=<?=$id;?>&sr='+searchWait+'&res=approved');
			},
			error: function (request, status, error) {
				serviceError();
			}
		});
	}	  
	</script>	
	<script>	  
	function receiveOneLine(val) {
		event.preventDefault();

		var lineAmount = $('.lineAmount'+val).val();
		var lineDate = $('.lineDate'+val).val();		
		
		var searchWait = $('#searchWait').val();
		var searchWait = encodeURI(searchWait);
		
		$.ajax({
			global: false,
			type: 'GET',
			url: '/pages/release/action.php?action=receiveOneLine&id='+val+'&cardId=<?=$id;?>&issueAmount='+lineAmount+'&issueDate='+lineDate+'',
			beforeSend: function(){
				$('#receiveOneLine'+val).html("<i class=\"glyphicon glyphicon-ban-circle\" style=\"color: red;\"></i> gaidiet...");
				$("#receiveOneLine"+val).prop("disabled",true);
			},				
			success: function (result) {
				console.log(result+' CL-7');
				
					if(result=='STOP'){
						location.reload();
					}				
				
				$('#contenthere').load('/pages/release/release.php?view=edit&id=<?=$id;?>&sr='+searchWait+'&res=received');
			},
			error: function (request, status, error) {
				serviceError();
			}
		});
		
	}	  
	</script>
	<script>	  
	function cancelOneLine(val) {
		event.preventDefault();

		var searchWait = $('#searchWait').val();
		var searchWait = encodeURI(searchWait);
		
		$.ajax({
			global: false,
			type: 'GET',
			url: '/pages/release/action.php?action=cancelOneLine&id='+val+'&cardId=<?=$id;?>',
			beforeSend: function(){
				$('#cancelOneLine'+val).html("<i class=\"glyphicon glyphicon-ban-circle\" style=\"color: red;\"></i> gaidiet...");
				$("#cancelOneLine"+val).prop("disabled",true);
			},				
			success: function (result) {
				console.log(result+' CL-8');
				
					if(result=='STOP'){
						location.reload();
					}				
				
				$('#contenthere').load('/pages/release/release.php?view=edit&id=<?=$id;?>&sr='+searchWait+'&res=canceled');
			},
			error: function (request, status, error) {
				serviceError();
			}
		});
	}	  
	</script>	
<?php	


	$knowIt = mysqli_query($conn, "
				SELECT issuance_doc.*,
					(SELECT COUNT(id) FROM cargo_line WHERE issuance_doc.issuance_id=cargo_line.issuance_id AND cargo_line.for_issue=1 AND cargo_line.status>20) AS totRows
				FROM issuance_doc 
				 
				WHERE id='".intval($id)."'
			") or die(mysqli_error($conn));
	$kIrow = mysqli_fetch_array($knowIt);
	
	
	
	$thisTransport = $kIrow['thisTransport'];
	$declaration_type_no = $kIrow['declaration_type_no'];

	$id = '';
	
	
	echo '<div class="page-header" style="margin-top: -5px;">
	  
		<div class="btn-group btn-group-xs" role="group" aria-label="Small button group" style="display:inline-block;"> 
			<a class="btn btn-default '; if($kIrow['status']==0){ echo 'active'; } echo ' classlist" ><i class="glyphicon glyphicon-list" style="color: #00B5AD" title="pamatdati"></i></a> 
			<a class="btn btn-default '; if($kIrow['status']==10){ echo 'active'; } echo ' classhistory" ><i class="glyphicon glyphicon-time" style="color: #00B5AD"  title="vēsture"></i></a>';
			echo '<a class="btn btn-default classadd" ><i class="glyphicon glyphicon-plus" style="color: #00B5AD"  title="pievienot"></i></a>';	
			
			if($kIrow['status']==10){
			echo '<a class="btn btn-default" href="print.php?view=tpage&id='.$id.'&eid='.$kIrow['issuance_id'].'" target="_blank"><i class="glyphicon glyphicon-print" style="color: #00B5AD"  title="Tālmaņa lapa"></i></a>';
			}
			echo '
		</div>';
		if ($res=="done"){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-success btn-xs">saglabāts!</div></div>';}
		if ($res=="del"){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-success btn-xs">izdzēsts!</div></div>';}
		if ($res=="error"){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-danger btn-xs">kļūda!</div></div>';}
		if ($res=="approved"){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-success btn-xs">nodots!</div></div>';}
		if ($res=="received"){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-success btn-xs">saņemts!</div></div>';}
		if ($res=="changed"){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-success btn-xs">pārvietots!</div></div>';}
		echo '<div class="pull-right" style="margin-top: -2px; display: none;" id="serror" id="hideMessage"><div class="btn btn-danger btn-xs">kļūda!</div></div>';
			  
	echo '</div>';	

	

	
	$checkIfPossibleBadRelease = null;
	if($kIrow['status']==10){
		$checkIfPossibleBadRelease = checkIfPossibleBadRelease($conn, $kIrow['issuance_id']);
	}
	
	echo '<p style="display:inline-block;">izdot'; if($kIrow['status']==10){ echo 'a'; } 
	echo'</p> <div style="display:inline-block; font-weight: bold;'; if($kIrow['status']==10 && $checkIfPossibleBadRelease){ echo 'background-color: red; color: white;'; } echo '"> '.$kIrow['issuance_id'].' '.$checkIfPossibleBadRelease.' </div>';
	
	
	if($kIrow['scanFinishedBy']>0){
		
		echo '
		<div style="float: right;">
			Skenēja: <b>'.returnMeWho($kIrow['scanFinishedBy']).'</b> Laiks: <b>'.date('d.m.Y H:i:s', strtotime($kIrow['scanFinishedDate'])).'</b>
		</div>';

	}	
	
	echo '<div class="clearfix"></div>';
	
	
	

	$disabled='disabled';

	if($kIrow['status']!=0 || !$kIrow['id']){ $dis = 'disabled'; }else{ $dis = null; }
	
	if($kIrow['totRows']>0){ $hide = 'disabled'; } else { $hide = NULL; }
  
if($dis!='disabled'){
?>
	<script>
	$(document).ready(function () {
		$('#edit_issuance').on('submit', function(e) {			
			e.preventDefault();
		
			var searchWait = $('#searchWait').val();
			var searchWait = encodeURI(searchWait);
					
			
			$.ajax({
				url : '/pages/release/post.php?r=edit_issuance',
				type: "POST",
				data: $(this).serializeArray(),
				beforeSend: function(){
					
					$('.actionLine').html("gaidiet...");
					$(".actionLine").prop("disabled",true);

				},			
				success: function (data) {

					console.log(data+' CL-1');
					
					if(data=='STOP'){
						location.reload();
					}
					
					$('#contenthere').load('/pages/release/release.php?view=edit&id=<?=$kIrow['id'];?>&sr='+searchWait+'&res=done');
				},
				error: function (jXHR, textStatus, errorThrown) {
					alert(errorThrown);
				}
			});
		});
	});
	</script>

	
	
<form id="edit_issuance">  
		<input type="hidden" name="issuance_id" value="<?=$kIrow['issuance_id'];?>">
<?php } ?>

<style>
.asterisc {
  display: inline-block;
  color: red;
}
</style>

<style>
.asterisc-blue {
  display: inline-block;
  color: blue;
}
</style>
		
	<div class="form-group col-md-8">	
		<label class="asterisc">* nodošanai obligāti lauki</label> <label class="asterisc-blue">* izdošanai obligāti lauki</label>
	</div>
	<div class="clearfix"></div>
	
    <div class="form-group col-md-3">
      <label for="deliveryDate">klienta kods - nosaukums <span class="asterisc">*</span></label>
      <input type="text" class="form-control" value="<?=$kIrow['clientCode'].' - '.returnClientName($conn, $kIrow['clientCode']);?>" disabled>
    </div> 
  
	<div class="form-group col-md-3">
      <label for="deliveryDate">līgums <span class="asterisc">*</span></label>
      <input type="text" class="form-control" value="<?=$kIrow['agreements'];?>" disabled>
    </div> 

	<div class="form-group col-md-3">
      <label>pieņemšanas akta nr.</label>
      <input type="text" class="form-control" value="<?=$kIrow['acceptance_act_no'];?>" disabled>
    </div> 	 
 
<?php
		echo '
			
			<div class="form-group col-md-3">	
			<label for="receiverCountry">pakalpojums <span class="asterisc">*</span></label>			
				<select class="form-control selectpicker btn-group-xs"  data-live-search="true" title="pakalpojums" name="resource" '.$dis.' '.$hide.'>';	
				$selectResource = mysqli_query($conn, "
					SELECT r.id AS id, r.name AS name 
					FROM n_resource AS r
					LEFT JOIN agreements_lines AS al
					ON r.id=al.service
					WHERE al.contractNr='".$kIrow['agreements']."' AND al.deleted!='1'  AND al.keeping!='on' AND al.extra_resource!='on'
					GROUP BY r.id
					") or die(mysqli_error($conn));
				while($rowi = mysqli_fetch_array($selectResource)){
					echo '<option value="'.$rowi['id'].'"';

						if($kIrow['resource']!='' && $kIrow['resource']==$rowi['id']){ echo ' selected';}
						
					echo '>'.$rowi['id'].' ('.$rowi['name'].')</option>';
				}
				
				echo '
				</select>
			</div>
			
		';	
?>

 
  
    <div class="form-group col-md-2">
      <label for="deliveryDate">izdošanas datums <span class="asterisc">*</span></label>
      <input type="text" class="form-control datepicker" name="i_issueDate" value="<?=date('d.m.Y', strtotime($kIrow['issueDate']));?>" <?=$dis;?> <?=$hide;?>>
    </div>
    <div class="form-group col-md-2">
      <label for="deliveryDate">faktiskais datums <span class="asterisc">*</span></label>
      <input type="text" class="form-control datepicker" name="i_actualDate" value="<?=date('d.m.Y', strtotime($kIrow['actualDate']));?>" <?=$dis;?> <?=$hide;?>>
    </div>
	
    <div class="form-group col-md-2">
      <label for="deliveryDate">brigāde</label>
      <input type="text" class="form-control" name="brigade" value="<?=$kIrow['brigade'];?>" <?=$dis;?> <?=$hide;?>>
    </div>	


    <div class="form-group col-md-3">
      <label>pieteikuma nr. <span class="asterisc-blue">*</span></label>
      <input type="text" class="form-control" name="applicationNo" placeholder="pieteikuma nr." value="<?=$kIrow['application_no'];?>" <?=$dis;?> <?=$hide;?>>
    </div>	

    <div class="form-group col-md-3">
      <label>pieteikuma datums <span class="asterisc">*</span></label>
      <input type="text" class="form-control datepicker" name="applicationDate" value="<?=date('d.m.Y', strtotime($kIrow['applicationDate']));?>" <?=$dis;?> <?=$hide;?>>
    </div>
	<div class="clearfix"></div>

    <div class="form-group col-md-2">
      <label for="deliveryDate">datums <span class="asterisc">*</span></label>
      <input type="text" class="form-control datepicker" name="date" value="<?=date('d.m.Y', strtotime($kIrow['date']));?>" <?=$dis;?> <?=$hide;?>>
    </div>
    <div class="form-group col-md-1">
      <label for="deliveryDate">laiks no </label>
      <input type="text" class="form-control" name="time_from" placeholder="08:00" value="<?=$kIrow['time_from'];?>" <?=$dis;?> onblur="validateTime(this)" <?=$hide;?>>
    </div>
    <div class="form-group col-md-1">
      <label for="deliveryDate">laiks līdz</label>
      <input type="text" class="form-control" name="time_to" placeholder="17:00" value="<?=$kIrow['time_to'];?>" <?=$dis;?> onblur="validateTime(this)" <?=$hide;?>>
    </div>	


    <div class="form-group col-md-2">
      <label for="deliveryDate">rūme</label>
      <input type="text" class="form-control" name="place" value="<?=$kIrow['place'];?>" <?=$dis;?> <?=$hide;?>>
    </div>
		



	<?php
	echo '
			
			<div class="form-group col-md-3">
			<label for="deliveryType">transporta veids <span class="asterisc">*</span></label>
			  <select class="form-control selectpicker btn-group-xs" name="transport" data-live-search="true" title="transporta veids"  '.$dis.' '.$hide.'>';
			  $selectTypeT = mysqli_query($conn, "
			  SELECT transport FROM transport
			  WHERE status=1 ORDER BY transport") or die(mysqli_error($conn));
			  while($rowt = mysqli_fetch_array($selectTypeT)){
				  echo '<option value="'.$rowt['transport'].'"';
				  if($rowt['transport']==$kIrow['transport']){ echo ' selected';}
				 echo ' >'.$rowt['transport'].'</option>';
			  }
			  
			  echo '
			  </select>	
			</div>';
			?>

    <div class="form-group col-md-3">
      <label for="thisTransport">izs. transporta nr. <span class="asterisc">*</span></label>
      <input type="text" class="form-control" name="thisTransport" id="thisTransport" placeholder="izs. transporta nr." value="<?=$thisTransport;?>" <?=$dis;?> <?=$hide;?>>
    </div>			
	
	<div class="clearfix"></div>
	
    <div class="form-group col-md-3">
      <label for="transportName">kuģis iekraušanai <span class="asterisc-blue">*</span></label>
      <input type="text" class="form-control" name="transportName" id="transportName" value="<?=$kIrow['transport_name'];?>" <?=$dis;?> <?=$hide;?>>
    </div>		
	

    <div class="form-group col-md-3">
      <label for="manifestNo">manifesta nr. <span class="asterisc-blue">*</span></label>
      <input type="text" class="form-control" name="manifestNo" id="manifestNo" value="<?=$kIrow['manifest_no'];?>" <?=$dis;?> <?=$hide;?>>
    </div>	

    <div class="form-group col-md-3">
      <label for="issuanceActNo">izdošanas akta nr. <span class="asterisc">*</span></label>
      <input type="text" class="form-control" name="issuanceActNo" id="issuanceActNo" value="<?=$kIrow['issuance_act_no'];?>" <?=$dis;?> <?=$hide;?>>
    </div>	
	
    <div class="form-group col-md-3">
      <label for="declarationTypeNo">deklarācijas nr. <span class="asterisc-blue">*</span></label>
      <input type="text" class="form-control" name="declarationTypeNo" id="declarationTypeNo" placeholder="deklarācijas nr." value="<?=$declaration_type_no;?>" <?=$dis;?> <?=$hide;?>>
    </div>	
	<div class="clearfix"></div>
	
	<script>
		$('#receiverCode').on('change', function() {
				var typez = $('#receiverCode option:selected').attr('data-cnid');

				$( "#countryOption" ).load( "/pages/release/change.php?view=sort_receiver&select="+typez+"" );
				
		});
	</script> 	
<?php

echo '

<div class="form-group col-md-3">
<label for="receiverCode">saņēmēja kods - nosaukums <span class="asterisc-blue">*</span></label>
  <select class="form-control selectpicker btn-group-xs"  name="receiverCode" id="receiverCode"  data-live-search="true" title="saņēmēja kods - nosaukums"  '.$dis.' '.$hide.'>';
  $selectReceiver = mysqli_query($conn, "SELECT Code, name, country FROM receivers") or die(mysqli_error($conn));
  while($rowr = mysqli_fetch_array($selectReceiver)){
	  echo '<option value="'.$rowr['Code'].'" data-cnid="'.$rowr['country'].'"';
	  if($kIrow['receiverCode']==$rowr['Code']){echo ' selected';}
	  echo '>'.$rowr['Code'].' - '.$rowr['name'].'</option>';
  }
  
  echo '
  </select>	
</div>';

		echo '
		<div class="form-group col-md-3">
		  <label for="receiverName2">saņemēja nosaukums 2</label>
		  <input type="text" class="form-control" name="receiverName2" id="receiverName2" placeholder="saņemēja nosaukums 2" value="'.$kIrow['receiverName2'].'">
		</div>
		';

		echo '
		<div id="countryOption">	
			<div class="form-group col-md-2 ">
			<label class="lb-sm" for="receiverCountry">saņēmēja valsts <span class="asterisc-blue">*</span></label>
			  <select class="form-control selectpicker btn-group-xs input-xs" name="receiverCountry" id="receiverCountry"  data-live-search="true" title="saņēmēja valsts" '.$dis.' '.$hide.'>';
			  $selectReceiver = mysqli_query($conn, "SELECT Code, country FROM countries") or die(mysqli_error($conn));
			  while($rowr = mysqli_fetch_array($selectReceiver)){
				  echo '<option value="'.$rowr['Code'].'"';
					if($rowr['Code']==$kIrow['receiverCountry']){ echo ' selected'; }
				  echo '>'.$rowr['Code'].' - '.$rowr['country'].'</option>';
			  }
				echo '
			  </select>	
			</div>


			 <div class="form-group col-md-2">
			  <label class="lb-sm"  for="cargoStatus">kravas status</label>
				<select class="form-control selectpicker btn-group-xs input-xs" id="cargoStatus" name="cargoStatus"  data-live-search="true" title="kravas status" '.$dis.' '.$hide.'>';
				
						echo '
						<option value="C" '; if($kIrow['cargo_status']=='C'){echo 'selected';} echo '>C - eksports</option>
						<option value="N" '; if($kIrow['cargo_status']=='N'){echo 'selected';} echo '>N - tranzīts</option>
						<option value="EU" '; if($kIrow['cargo_status']=='EU'){echo 'selected';} echo '>EU</option>';

				echo '
				</select>	
			 </div>			
		</div>
			';
			
		




		if(checkIfAllowedScannerClient($conn, $kIrow['clientCode'])==1){
		
			echo '
			<div class="form-group col-md-2">
				<label class="lb-sm"  for="destination">galamērķis</label>
				<select class="form-control selectpicker btn-group-xs input-xs" name="destination" id="destination"  data-live-search="true" title="galamērķis" '.$dis.' '.$hide.'>';
				
				  $selectReceiver = mysqli_query($conn, "SELECT Code, name FROM destinations WHERE status=1") or die(mysqli_error($conn));
				  while($rowr = mysqli_fetch_array($selectReceiver)){
						echo '<option value="'.$rowr['Code'].'"  '; if($kIrow['destination']==$rowr['Code']){echo 'selected';} echo ' >'.$rowr['Code'].' - '.$rowr['name'].'</option>';
				  }
				
				echo '
				</select>
			</div>	
			<div class="clearfix"></div>		
			';	
				
			echo '<div class="form-group col-md-3">
					<label>rūmes</label><br>';	
					
					$allPlaces = explode(',',$kIrow['places']);
					for ($x = 1; $x <= 7; $x++) {
						
						$checked_view=null;
						if (in_array('R'.$x, $allPlaces)){$checked_view='checked="checked"';}
						
						echo '	
						<span class="button-checkbox">
							<button type="button" class="btn btn-xs" data-color="success"><i class="state-icon glyphicon glyphicon-check"></i>&nbsp;R'.$x.'</button>
							<input type="checkbox" class="hidden" id="places['.$x.']" name="places['.$x.']" value="R'.$x.'" '.$checked_view.'>
						</span>';
						
					}
			echo '</div>';	

			echo '<div class="form-group col-md-3">
					<label>klāji</label><br>';	
					
					$allDecks = explode(',',$kIrow['decks']);
					for ($x = 1; $x <= 7; $x++) {

						$checked_view=null;
						if (in_array('K'.$x, $allDecks)){$checked_view='checked="checked"';}

						echo '	
						<span class="button-checkbox">
							<button type="button" class="btn btn-xs" data-color="success"><i class="state-icon glyphicon glyphicon-check"></i>&nbsp;K'.$x.'</button>
							<input type="checkbox" class="hidden" id="decks['.$x.']" name="decks['.$x.']" value="K'.$x.'" '.$checked_view.'>
						</span>';
						
					}
			echo '</div>';	
			
			
			echo '<div class="form-group col-md-3">
					<label>skenēšanai</label><br>';	

						$checked_view=null;
						if ($kIrow['forScan']==1){$checked_view='checked="checked"';}

						echo '	
						<span class="button-checkbox">
							<button type="button" class="btn btn-xs" data-color="success"><i class="state-icon glyphicon glyphicon-check"></i></button>
							<input type="checkbox" class="hidden" id="forScan" name="forScan" '.$checked_view.'>
						</span>';

			echo '</div>';				
			
			

		}



		
			
			
		$allowSubmit=null;	
	    if($kIrow['clientCode']!='' && $kIrow['agreements']!='' && $kIrow['resource']!='' && $kIrow['issueDate']!='0000-00-00 00:00:00' && $kIrow['actualDate']!='0000-00-00 00:00:00' && $kIrow['applicationDate']!='0000-00-00 00:00:00' && $kIrow['date']!='0000-00-00 00:00:00' && $kIrow['transport']!='' && $thisTransport!='' && $kIrow['issuance_act_no']!=''){
			$allowSubmit = 'Y';
		}
		
		$allowRelease=null;	
	    if($kIrow['clientCode']!='' && $kIrow['agreements']!='' && $kIrow['resource']!='' && $kIrow['issueDate']!='0000-00-00 00:00:00' && $kIrow['actualDate']!='0000-00-00 00:00:00' && $kIrow['application_no']!='' && $kIrow['applicationDate']!='0000-00-00 00:00:00' && $kIrow['date']!='0000-00-00 00:00:00' && $kIrow['transport']!='' && $thisTransport!='' && $kIrow['transport_name']!='' && $kIrow['manifest_no']!='' && $kIrow['issuance_act_no']!='' && $declaration_type_no!='' && $kIrow['receiverCode']!='' && $kIrow['receiverCountry']!=''){
			$allowRelease = 'Y';
		}		

?>


  <div class="clearfix"></div><br>
  <?php if($dis!='disabled'){ ?>
	<?php if($hide!='disabled'){ ?>
		<button type="submit" class="btn btn-default btn-xs" id="savebtn" style="display: inline-block;"><i class="glyphicon glyphicon-floppy-save" style="color: blue; display: inline-block;"></i> saglabāt</button>
	<?php } ?>
<div class="deleteIt" style="display: inline-block;">

</div>

</form>	
	
  <?php } ?>

	
<div id="selected">	
	<?php	

?>

<script type="text/javascript">

function getStates(value) {
    var search = $.post("/pages/release/search.php?view=cargoList&showToGive=<?=$showToGive;?>&docNr=&agr=<?=$kIrow['agreements'];?>&cli=<?=$kIrow['clientCode'];?>&eid=<?=$kIrow['id'];?>&estatus=<?=$kIrow['status'];?>&issuance=<?=$kIrow['issuance_id'];?>&num=<?=$kIrow['acceptance_act_no'];?>", {name:value},function(data){
        $("#results").html(data);		
    });
	if(search){
		console.log('S2');
		$('#showWait').html('<i class="glyphicon glyphicon-refresh spin" style="color: #00B5AD"></i>&nbsp;&nbsp;');
	}
    search.done(function( data ) {
		$('#showWait').html('');
    });	
}

function showToGive() {
	
	
	var sr = '<?=$sr;?>';
	if(sr){
		var search = '<?=$sr;?>';
	}else{
		var search = $('#searchWait').val();
	}
	
	var val = $('#showToGive').prop('checked');
    var search = $.post("pages/release/release.php?view=edit&id=<?=$kIrow['id'];?>&sr="+search+"&showToGive="+val,function(data){
        $("#contenthere").html(data);
				
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

echo ' <div style="float: right; margin-top: -20px;">
		
		<div class="checkbox" style="display: inline-block;">
			<label><input type="checkbox" id="showToGive" onchange="showToGive()" style="margin: -1px 0px 0px -17px;" '; if($showToGive=='true'){echo ' checked';} echo '> rādīt tikai nodotos </label>
		</div>


		<div id="showWait" style="display: inline-block;"></div>
		<div style="display: inline-block;">
			<input type="text" id="searchWait" class="form-control input-xs" onkeyup="getStates(this.value)"'; if($sr){echo ' value="'.$sr.'" ';} echo ' placeholder="meklēt">
		</div>
	   </div>

			<div class="clearfix"></div>
			';			  
	  

	function checkAllStatus($conn, $agr, $cli, $status, $issuance_id){
		
		$Query = mysqli_query($conn, "
			SELECT cargo_line.id FROM cargo_line 

			LEFT JOIN cargo_header 
			ON cargo_line.docNr=cargo_header.docNr 

			WHERE cargo_line.status='".$status."' 
			AND cargo_line.issuance_id='".$issuance_id."'
			AND cargo_header.clientCode='".$cli."'
			AND cargo_header.agreements='".$agr."'			
		");
		
		if(mysqli_num_rows($Query)>0){
			return 1;
		}else{
			return 0;
		}
		
	}

	function checkAllStatusLines($conn, $agr, $cli, $issuance_id){
		
		$Query = mysqli_query($conn, "
			SELECT cargo_line.id FROM cargo_line 

			LEFT JOIN cargo_header 
			ON cargo_line.docNr=cargo_header.docNr 

			WHERE cargo_header.clientCode='".$cli."' 
			AND cargo_header.agreements='".$agr."' 
			AND cargo_line.issuance_id='".$issuance_id."' AND cargo_line.for_issue='1' AND cargo_line.status='30'
		");
		
		if(mysqli_num_rows($Query)>0){
			return 1;
		}else{
			return 0;
		}
		
	}

		echo '<div class="clearfix"></div>';	
	
	echo '
<br>
	
<div class="panel panel-default">
	<div class="panel-body">
		<div class="table-responsive">';
		
if($dis!='disabled'){
?>
	<script>
	$(document).ready(function () {
		$('#release_lines').on('submit', function(e) {			
			e.preventDefault();
		
			var searchWait = $('#searchWait').val();
			var searchWait = encodeURI(searchWait);
			
			var action = document.activeElement.getAttribute('id');			
			
			$.ajax({
				url : '/pages/release/post.php?r=release_lines&action='+action,
				type: "POST",
				data: $(this).serializeArray(),
				beforeSend: function(){
					
					$("#pleaseWait").toggle();
					$('.actionLine').html("gaidiet...");
					$(".actionLine").prop("disabled",true);

				},	
						
				success: function (data) {
					console.log(data+' CL-2');
					
					if(data=='STOP'){
						location.reload();
					}
					
					$('#contenthere').load('/pages/release/release.php?view=edit&id=<?=$kIrow['id'];?>&sr='+searchWait+'&res=done');
				},
				error: function (jXHR, textStatus, errorThrown) {
					alert(errorThrown);
				}
			});
		});
	});
	</script>
	

<form id="release_lines">	
<input type="hidden" name="header_thisTransport" value="<?=$thisTransport;?>">
<input type="hidden" name="header_declaration_type_no" value="<?=$declaration_type_no;?>">	
<?php	
}
		
// KOPSUMMAS DAUDZUMI

echo '<div class="clearfix"></div>';		

	echo '<div id="totSums">';
	if($sr){
		$s = mysqli_real_escape_string($conn, $sr);	
		$search = " AND (cargo_line.productNr LIKE '%$s%' || cargo_line.thisTransport LIKE '%$s%' || cargo_line.thisDate LIKE '%$s%' || cargo_line.batchNo LIKE '%$s%')";	
	}else{
		$search = "";	
	} 

	if($dis=='disabled'){ 
		$showHi = "cargo_line.status='40' AND cargo_line.issuance_id='".$kIrow['issuance_id']."'"; 
	}else{ 
		$showHi = "(cargo_line.status='20' OR cargo_line.status='30') AND (cargo_line.issuance_id='' OR cargo_line.issuance_id='".$kIrow['issuance_id']."')"; 
	}
	
	$numz = null;
	if($kIrow['acceptance_act_no']){ $numz = " AND cargo_header.acceptance_act_no='".$kIrow['acceptance_act_no']."'"; }
	
	$destz = null;
	if($kIrow['destination'] && $kIrow['destination']!='VISI'){ $destz = " AND substring_index(cargo_line.productNr,' ',1) LIKE '".$kIrow['destination']."%'"; }	


	$inCargo = mysqli_query($conn, "
					SELECT COUNT(cargo_line.id) AS count, SUM(cargo_line.amount) as amount, SUM(cargo_line.issueAmount) as issueAmount, 
					SUM(cargo_line.volume) as volume, 
					SUM(cargo_line.tare) as t, SUM(cargo_line.issueTare) as it, 
					SUM(cargo_line.gross) as b, SUM(cargo_line.issueGross) as ib, 
					SUM(cargo_line.net) as n, SUM(cargo_line.issueNet) as isn,
					SUM(cargo_line.cubicMeters) as a, SUM(cargo_line.issueCubicMeters) as ia, 
					SUM(cargo_line.delta_net) as dn,
					SUM(cargo_line.document_net) as don,	
					
					SUM(cargo_line.fact_for_delta) as ffd,
					SUM(cargo_line.real_delta) as rd,					

					cargo_line.productUmo, cargo_line.productNr, SUM(cargo_line.place_count) AS pc, SUM(cargo_line.issue_place_count) AS ipc,
					
					SUM(cargo_line.assistant_amount) as assistant_amount, SUM(cargo_line.issue_assistant_amount) as issue_assistant_amount, cargo_line.assistantUmo, cargo_line.status, cargo_line.resource, cargo_line.extra_resource, cargo_line.for_issue
					
					FROM cargo_line 

					LEFT JOIN cargo_header 
					ON cargo_line.docNr=cargo_header.docNr 					

					WHERE ".$showHi." ".$search." AND cargo_header.clientCode='".$kIrow['clientCode']."' AND cargo_header.agreements='".$kIrow['agreements']."' ".$numz." ".$destz." AND cargo_line.action!='23' AND cargo_line.action!='27'
					AND cargo_line.for_issue=1

					GROUP BY cargo_line.productUmo
		
			") or die (mysqli_error($conn));
			
			
			
			

	if(mysqli_num_rows($inCargo)>0){
	
	echo '<p style="display:inline-block;">daudzumi</p>';
	
		echo '
		<div class="table-responsive">
			<table class="table table-hover table-responsive" border="1" style="border: 1px solid #ddd !important;">
				<thead>
					
					<th>rindas izdošanai</th>
					
					<th>mērvienība</th>
					<th>daudzums</th>
					<th>daudzums izdošanai</th>
					<th>palīg mērvienība</th>
					<th>atlikušais palīg mērv. daudzums</th>
					<th>palīg mērv. daudzums izdošanai</th>
					<th>saņ. dok. neto (kg)</th>
					<th>vietu skaits</th>
					<th nowrap>tara (kg)</th>
					<th nowrap>bruto (kg)</th>
					<th nowrap>neto (kg)</th>';


					if($kIrow['resource']=='BERKR' || $kIrow['resource']=='BERKRSAN'){
						echo '
						<th>faktiskais T</th>
						<th>Δ T</th>';
					}

					echo '
					<th nowrap>saņ. Δ neto (kg)</th>
					<th nowrap>apjoms (m3)</th>
				</thead>
				<tbody>';	
				
					while($icRow = mysqli_fetch_array($inCargo)){				
						
						echo '				
						<tr>
							
							<td>'.$icRow['count'].'</td>';		
									
							echo '		
							<td>'.$icRow['productUmo'].'</td>
							<td>'.floatval($icRow['amount']).'</td>
							<td>';
							if(floatval($icRow['issueAmount']>0)){
								echo floatval($icRow['issueAmount']);
							}else{
								echo floatval($icRow['amount']);
							}
							
							echo '</td>


							<td>'.$icRow['assistantUmo'].'</td>
							<td>'.floatval($icRow['assistant_amount']).'</td>
							<td>';
							if(floatval($icRow['issue_assistant_amount']>0)){
								echo floatval($icRow['issue_assistant_amount']);
							}else{
								echo floatval($icRow['assistant_amount']);
							}
							
							echo '</td>
							<td>'.floatval($icRow['don']).'</td>';
							
							echo '<td>';
							
							if($icRow['status']==20 && $icRow['for_issue']==0 || $icRow['status']==40){
								echo floatval($icRow['pc']);
							}
							
							if(($icRow['status']==30 || $icRow['for_issue']==1) && ($icRow['status']!=40)){
								echo floatval($icRow['ipc']);
							}

							echo '</td>';							
							
							echo '<td>';
							
							if($icRow['status']==20 && $icRow['for_issue']==0 || $icRow['status']==40){
								echo floatval($icRow['t']);
							}
							
							if(($icRow['status']==30 || $icRow['for_issue']==1) && ($icRow['status']!=40)){
								echo floatval($icRow['it']);
							}

							echo '</td>
							<td>';
							
							if($icRow['status']==20 && $icRow['for_issue']==0 || $icRow['status']==40){
								echo floatval($icRow['b']);
							}
							
							if(($icRow['status']==30 || $icRow['for_issue']==1) && ($icRow['status']!=40)){
								echo floatval($icRow['ib']);
							}						

							echo '</td>
							<td>';
							
							if($icRow['status']==20 && $icRow['for_issue']==0 || $icRow['status']==40){
								echo floatval($icRow['n']);
							}
							
							if(($icRow['status']==30 || $icRow['for_issue']==1) && ($icRow['status']!=40)){
								echo floatval($icRow['isn']);
							}

							echo '</td>';

							if($kIrow['resource']=='BERKR' || $kIrow['resource']=='BERKRSAN'){
								echo '
								<td>'.floatval($icRow['ffd']).'</td>
								<td>'.floatval($icRow['rd']).'</td>';
							}

							echo '
							<td>'.floatval($icRow['dn']).'</td>
							<td>';
							
							if($icRow['status']==20 && $icRow['for_issue']==0 || $icRow['status']==40){
								echo floatval($icRow['a']);
							}
							
							if(($icRow['status']==30 || $icRow['for_issue']==1) && ($icRow['status']!=40)){
							}						
							
							echo '</td>
							
						</tr>';
					
					}
				echo '
				</tbody>
			</table>
		</div>				
		';
		
	}
	echo '</div>';

//^^ KOPSUMMAS DAUDZUMI	


?>

<script>

function approveOneLine(val) {	
			
			$.ajax({
				url : '/pages/release/post.php?r=release_lines&liId='+val+'&action=approveLine',
				type: "POST",
				data: {
					issueAmount: $("#issueAmount"+val).val(),
					issueAssistantAmount: $("#issueAssistantAmount"+val).val(),
					
					e_place_count: $("#e_place_count"+val).val(),
					eTare: $("#eTare"+val).val(),
					eGross: $("#eGross"+val).val(),
					eNet: $("#eNet"+val).val(),
					issueThisTransport: $("#issueThisTransport"+val).val(),
					issueDeclarationTypeNo: $("#issueDeclarationTypeNo"+val).val(),
					
					eCubicMeters: $("#eCubicMeters"+val).val(),
					issuePlacement: $("#issuePlacement"+val).val(),
					
					issueDateFinal: $("#issueDateFinal").val(),	
					actualDateFinal: $("#actualDateFinal").val(),	
					resource: $('[name="resource"]').val(),						
					issuance_id: $("#issuance_id").val(),

					eDelta: $('#eDelta'+val).val(),
					eDeltaT: $('#eDeltaT'+val).val()					

			    },
				beforeSend: function(){
					

				},	
						
				success: function (data) {
					console.log(data+' CL-3');
					
					if(data=='STOP'){
						location.reload();
					}					
					
					console.log('šī ir īstā vieta');
					$( "#actionOneLine"+val ).load( "/pages/release/release_one_line.php?view=release_one_line&issuanceId=<?=$kIrow['issuance_id'];?>&acceptanceActNo=<?=urlencode($kIrow['acceptance_act_no']);?>&clientCode=<?=$kIrow['clientCode'];?>&agreements=<?=$kIrow['agreements'];?>&issuanceStatus=<?=$kIrow['status'];?>&id="+val+"" );
					
					$( "#totSums" ).load( "/pages/release/total_sums.php?view=total_sums&issuanceId=<?=$kIrow['issuance_id'];?>&acceptanceActNo=<?=urlencode($kIrow['acceptance_act_no']);?>&clientCode=<?=$kIrow['clientCode'];?>&agreements=<?=$kIrow['agreements'];?>" );
				},
				error: function (jXHR, textStatus, errorThrown) {
					alert(errorThrown);
				}
			});	

}


function cancelOneLine(val) {	
			
			$.ajax({
				url : '/pages/release/post.php?r=release_lines&liId='+val+'&action=cancelLine',
				type: "POST",
				beforeSend: function(){


				},	
						
				success: function (data) {
					console.log(data+' CL-4');
					
					if(data=='STOP'){
						location.reload();
					}					
					
					$( "#actionOneLine"+val ).load( "/pages/release/release_one_line.php?view=release_one_line&issuanceId=<?=$kIrow['issuance_id'];?>&acceptanceActNo=<?=urlencode($kIrow['acceptance_act_no']);?>&clientCode=<?=$kIrow['clientCode'];?>&agreements=<?=$kIrow['agreements'];?>&issuanceStatus=<?=$kIrow['status'];?>&id="+val+"" );
					
					$( "#totSums" ).load( "/pages/release/total_sums.php?view=total_sums&issuanceId=<?=$kIrow['issuance_id'];?>&acceptanceActNo=<?=urlencode($kIrow['acceptance_act_no']);?>&clientCode=<?=$kIrow['clientCode'];?>&agreements=<?=$kIrow['agreements'];?>" );
				},
				error: function (jXHR, textStatus, errorThrown) {
					alert(errorThrown);
				}
			});	
}
</script>

<?php

		
		echo '
	<div id="results">';

	
	echo '
		<div class="table-responsive">
			<table class="table table-hover table-responsive" border="1" style="border: 1px solid #ddd !important;">
		<thead> 
			<tr>
				<th>darbība'; 
				
				echo'</th>
				<th>iekļauts dokumentā</th>';
				
				if($kIrow['forScan']==1){
					echo '<th>novietojums</th>';
				}
				
				echo '
				<th>brāķis<br><br></th>
				<th>piegādes<br>dat.</th>
				<th>produkta nr</th>				
				<th>seriālais nr.</th>
				<th>atlikušais daudzums</th>
				<th>mērvienība</th>';
				
				if($dis!='disabled'){echo '<th>daudzums<br>izdošanai</th>';}

				echo '
				<th>atlikušais palīg mērv. daudzums</th>
				<th>palīg mērvienība</th>';
				
				if($dis!='disabled'){echo '<th>palīg mērv. daudzums<br>izdošanai</th>';}				
				echo '<th>saņ. dok. neto (kg)</th>
				<th>vietu skaits</th>
				<th>tara (kg)</th>
				<th>bruto (kg)</th>
				<th>neto (kg)</th>';

				if($kIrow['resource']=='BERKR' || $kIrow['resource']=='BERKRSAN'){
					echo '
					<th>faktiskais T</th>
					<th>Δ T</th>';
				}


				echo '
				<th>saņ. Δ neto (kg)</th>
				<th>m3 (kg)</th>

				<th>partijas nr.</th>
				<th>noliktava</th>

				<th>markas nr.</th>
				<th>konteinera tips</th>


				<th>saņ. transporta nr.</th>
				<th>izs. transporta nr.</th>

				<th>deklarācijas nr.</th>
				<th>kravas status</th>
				<th>plombes numurs</th>

				<th>svēršanas akta nr.</th>
								
			</tr>
		</thead>
		<tbody>';



	if($sr){
	$s = mysqli_real_escape_string($conn, $sr);


	$getLocationNames = mysqli_query($conn, "SELECT id FROM n_location WHERE name LIKE '%$s%'");	
	$gln = mysqli_fetch_array($getLocationNames);	
	
	if($gln['id']){
		$sl = " OR cargo_line.location LIKE '%".$gln['id']."%'";
	}else{
		$sl = null;
	}	

	
	$search = " AND 
		(
			cargo_line.productNr LIKE '%$s%' OR 
			cargo_line.thisTransport LIKE '%$s%' OR 
			cargo_line.thisDate LIKE '%$s%' OR 
			cargo_line.serialNo LIKE '%$s%' OR 
			cargo_line.lot_no LIKE '%$s%' OR 
			cargo_line.issue_lot_no LIKE '%$s%' OR 
			cargo_line.container_type_no LIKE '%$s%' OR 
			cargo_line.issue_container_type_no LIKE '%$s%' OR 

			cargo_line.declaration_type_no LIKE '%$s%' OR 
			cargo_line.issue_declaration_type_no LIKE '%$s%' OR 
			cargo_line.thisTransport LIKE '%$s%' OR 
			cargo_line.issue_thisTransport LIKE '%$s%' OR 
			cargo_line.seal_no LIKE '%$s%' OR 
			cargo_line.issue_seal_no LIKE '%$s%' OR 
			cargo_line.weighing_act_no LIKE '%$s%' OR 
			cargo_line.issue_weighing_act_no LIKE '%$s%' OR 

			cargo_line.batchNo LIKE '%$s%' 
			
			".$sl."
		)";	
	}else{
	$search = "";	
	}

		if($dis=='disabled'){ $showHi = "cargo_line.status='40' AND cargo_line.issuance_id='".$kIrow['issuance_id']."'"; }else{ $showHi = "(cargo_line.issuance_id='' OR cargo_line.issuance_id='".$kIrow['issuance_id']."')"; }
		
		$includedInDoc = null;
		if($showToGive=='true'){
			$includedInDoc = " AND cargo_line.for_issue = 1 ";
		}		

		$numz = null;
		if($kIrow['acceptance_act_no']){ $numz = " AND cargo_header.acceptance_act_no='".$kIrow['acceptance_act_no']."'"; }


		$destz = null;
		if($kIrow['destination'] && $kIrow['destination']!='VISI'){ $destz = " AND substring_index(cargo_line.productNr,' ',1) LIKE '".$kIrow['destination']."%'"; }



	$rec_limit = $i_p_l; $offset=0; $max_pages = 10;  //DEFINĒJAM LIMITUS
	$link_to = $page_file.'?page='; //LINKS KAS TIKS ATVĒRTS KAD NOSPIEDĪS UZ LAPAS, RAKSTĪT LĪDZ page 

		$lines = "
				SELECT cargo_line.*, cargo_header.deliveryDate AS hDeliveryDate
				FROM cargo_line 

				LEFT JOIN cargo_header 
				ON cargo_line.docNr=cargo_header.docNr 
				 
				WHERE  
				".$showHi." ".$search." AND cargo_header.clientCode='".$kIrow['clientCode']."' AND cargo_header.agreements='".$kIrow['agreements']."' ".$numz." ".$destz." ".$includedInDoc." AND cargo_line.action!='23' AND cargo_line.action!='27'

					AND (
							(
							
								cargo_line.status = '30' AND EXISTS(SELECT cargo_line.* 
								FROM cargo_line 
								LEFT JOIN cargo_header 
                                ON cargo_line.docnr = cargo_header.docnr 
								WHERE  ".$showHi." ".$search." AND cargo_header.clientCode='".$kIrow['clientCode']."' AND cargo_header.agreements='".$kIrow['agreements']."' ".$numz." ".$destz." AND cargo_line.action!='23' AND cargo_line.action!='27' 
								AND cargo_line.status = '30') 
								
							) OR (
							
								cargo_line.status = '20' AND NOT EXISTS(SELECT cargo_line.* 
                                FROM cargo_line 
                                LEFT JOIN cargo_header 
                                ON cargo_line.docnr = cargo_header.docnr 
								WHERE  ".$showHi." ".$search." AND cargo_header.clientCode='".$kIrow['clientCode']."' AND cargo_header.agreements='".$kIrow['agreements']."' ".$numz." ".$destz." AND cargo_line.action!='23' AND cargo_line.action!='27' 
                                AND cargo_line.status = '30') 
									   
                             ) OR (
							
								cargo_line.status = '40' AND EXISTS(SELECT cargo_line.* 
                                FROM cargo_line 
                                LEFT JOIN cargo_header 
                                ON cargo_line.docnr = cargo_header.docnr 
								WHERE  ".$showHi." ".$search." AND cargo_header.clientCode='".$kIrow['clientCode']."' AND cargo_header.agreements='".$kIrow['agreements']."' ".$numz." ".$destz." AND cargo_line.action!='23' AND cargo_line.action!='27' 
                                AND cargo_line.status = '40') 
							)
									   
                             )				
				
				ORDER BY cargo_header.deliveryDate
			";	
	



	
	list($page_menu, $result_all, $resultGL7) = pageing_menu($offset, $rec_limit, $max_pages, $link_to, $page, $conn, $lines);  //TIEK IEGŪTS ARRAY KO UZŅEM AR LIST 

	echo $page_menu;   //IZVADA TABULU AR LAPĀM			
	
	$count_GL7 = mysqli_num_rows($resultGL7);  //IEGŪST SKAITU 					

		$r=0; $k=0;
		
		while($rowL = mysqli_fetch_array($resultGL7)){

			
		if($rowL['status']<20){
					
		}else{	
			echo '<input type="hidden" name="docNr[]" value="'.$rowL['docNr'].'">';

			if(($rowL['status']==20 && $dis!='disabled')){		
				$r++;
				echo '<input type="hidden" name="lineId[]" value="'.$rowL['id'].'">';
			}
			if(($rowL['status']==30 && $dis!='disabled')){		
				$k++;
				echo '<input type="hidden" name="lineIdFinal[]" value="'.$rowL['id'].'">';
			}			
			echo '<div id="query_sort_e'.$rowL['id'].'"></div>';
			echo '	<tr class="classlistedit" id="actionOneLine'.$rowL['id'].'">
			
			<td nowrap>';

				if(($rowL['status']==20)){

					if($rowL['for_issue']==1){
						echo '<div class="btn btn-default btn-xs" style="margin-right: 2px;" onclick="cancelOneLine('.$rowL['id'].')"><i class="glyphicon glyphicon-ban-circle" style="color: red;"></i> atvērt nodošanu</div>';
					}else{
						echo '<div class="btn btn-default btn-xs" style="margin-right: 2px;" onclick="approveOneLine('.$rowL['id'].')"><i class="glyphicon glyphicon-ok" style="color: green;"></i> nodot</div>';
					}				
				
				}

				if(($rowL['status']==30)){
					echo returnCargoLineStatus($conn, $rowL['id']);				
				}
				
				if(($rowL['status']==40)){
					
					echo returnCargoLineStatus($conn, $rowL['id']);
					
				}	

				
				echo '<td>';
				if($rowL['for_issue']==1){ echo '<i class="glyphicon glyphicon-ok" style="color: green;"></i>'; }
				
				
				
			if($kIrow['forScan']==1){	
				echo '<td>';//novietojums līnijās
				
					
				if($rowL['status']==20 && $rowL['for_issue']==0 && $dis!='disabled'){
					
					echo '<select class="form-control selectpicker btn-group-xs input-xs" name="issuePlacement[]" id="issuePlacement'.$rowL['id'].'" data-live-search="true" data-width="80px" title=" " '.$dis.'>';
					for ($x = 1; $x <= 7; $x++) {
						
						$checked_view=null;
						if (in_array('R'.$x, $allPlaces)){
							
							echo '<option value="R'.$x.'" '; if($rowL['placement']=='R'.$x){echo 'selected';} echo '>R'.$x.'</option>';

						}
						
					}
					
					for ($x = 1; $x <= 7; $x++) {

						$checked_view=null;
						if (in_array('K'.$x, $allDecks)){
							
							echo '<option value="K'.$x.'" '; if($rowL['placement']=='K'.$x){echo 'selected';} echo '>K'.$x.'</option>';

						}
						
					}												
					
					echo '</select>';

				}else{
					echo $rowL['issuePlacement'];
				}					
				
			}	
				
				echo '<td>';
				if($rowL['brack']==1){echo '<i class="glyphicon glyphicon-ok" style="color: green;"></i>';}


				echo '<td nowrap>'.date('d.m.Y', strtotime($rowL['hDeliveryDate']));



					echo '
						<td nowrap>
						'.$rowL['productNr'].' - ';
						$getNames = mysqli_query($conn, "SELECT name1, name2, unitOfMeasurement FROM n_items WHERE code='".$rowL['productNr']."'");
						$gNrow = mysqli_fetch_array($getNames);
						echo $gNrow['name1'].' '.$gNrow['name2'];
						
						
						//parāda skenētāju
						$infoTable=$title=null;
						if($rowL['issuanceScannedBy']>0){

							$infoTable = '
								<div id="a1'.$rowL['id'].'" class="hidden">
								  <div class="popover-body">
									<table style="width:100%">
									  <tr>
										<td>Skenētājs:</td>
										<td nowrap>'.returnMeWho($rowL['issuanceScannedBy']).' ('.$rowL['shift'].')</td>	
									  </tr>
									  <tr>
										<td>Laiks:</td>
										<td nowrap>'.date('d.m.Y H:i:s', strtotime($rowL['issuanceScannedDate'])).'</td>
									  </tr>
									</table>
								  </div>
								</div>									
							';
							
							
							$title = ' style="background-color: #eee; height: 20px; font-size: 12px; display: table-cell; vertical-align: middle;" data-toggle="popover" data-trigger="hover" data-popover-content="#a1'.$rowL['id'].'" data-placement="right" class="form-control"';
						}
						echo $infoTable;						
						
						
					echo '<td><div '.$title.'>'.$rowL['serialNo'].'</div>';

echo '
<td>'.floatval($rowL['amount']).'
<td>'.$rowL['productUmo'];

if($rowL['status']==20 && $rowL['for_issue']==0 && $dis!='disabled'){
	echo '
	<td>
	<div class="">
	  <input type="text" class="form-control numbersOnly c_delta_t" data-idtdelta="'.$rowL['id'].'" style="min-width: 70px;" name="issueAmount[]" id="issueAmount'.$rowL['id'].'"  placeholder="daudzums" value="'.floatval($rowL['amount']).'" 
	  
	  type="number" min="1" max="'.floatval($rowL['amount']).'" 
	  step="0.01"
	 onKeyUp="if(this.value>'.floatval($rowL['amount']).'){this.value=\''.floatval($rowL['amount']).'\';}else if(this.value<1){this.value=\'1\';}"
	  
	  
	  
	  oninvalid="this.setCustomValidity(\'šim laukam jābūt aizpildītam\')" oninput="setCustomValidity(\'\')" required '.$hide.'>
	</div>						
	';
		
}
if(($rowL['status']==30 || $rowL['for_issue']==1) && $dis!='disabled'){
	echo '
	<td>
	<div class="">
	  <input type="text" class="form-control" style="min-width: 70px;" name="issueAmountFinal[]" placeholder="daudzums" value="'.floatval($rowL['issueAmount']).'" readonly>
	</div>						
	';							
}

// palīg mērvienība 
?>
<script>
 
$('#issueAmount<?=$rowL['id'];?>').on("keyup", calculate);

function calculate() {
var sum = 0;
var valz =  $('#issueAmount<?=$rowL['id'];?>').val();
var amount =  '<?=floatval($rowL['amount']);?>';
var extra = '<?=floatval($rowL['assistant_amount']);?>';
sum = (extra/amount)*valz;

$("#issueAssistantAmount<?=$rowL['id'];?>").val(sum);
}
</script>
<?php
echo '
<td>'.floatval($rowL['assistant_amount']).'
<td>'.$rowL['assistantUmo'];

if($rowL['status']==20 && $rowL['for_issue']==0 && $dis!='disabled'){
	echo '
	<td>
	<div class="">
	  <input type="text" class="form-control numbersOnly" style="min-width: 70px;" name="issueAssistantAmount[]" id="issueAssistantAmount'.$rowL['id'].'"  placeholder="palīg mērv. daudzums" value="'.floatval($rowL['assistant_amount']).'" 
	  
	  type="number" min="1" max="'.floatval($rowL['assistant_amount']).'" 
	  step="0.01"
	 onKeyUp="if(this.value>'.floatval($rowL['assistant_amount']).'){this.value=\''.floatval($rowL['assistant_amount']).'\';}else if(this.value<1){this.value=\'1\';}"
	  
	  
	  
	  oninvalid="this.setCustomValidity(\'šim laukam jābūt aizpildītam\')" oninput="setCustomValidity(\'\')" required '.$hide.'>
	</div>						
	';
		
}



if(($rowL['status']==30 || $rowL['for_issue']==1) && $dis!='disabled'){
	echo '
	<td>
	<div class="">
	  <input type="text" class="form-control" style="min-width: 70px;" name="issueAssistantAmountFinal[]" placeholder="palīg mērv. daudzums" value="'.floatval($rowL['issue_assistant_amount']).'" readonly>
	</div>						
	';							
}

echo '<td><input type="text" class="form-control '.$rowL['productUmo'].'don" style="min-width: 70px;" placeholder="saņ. dok. neto (kg)" id="eDocNet'.$rowL['id'].'"  value="'.floatval($rowL['document_net']).'" disabled>';

echo '
<td>
<input 

type="text" min="1" max="'.floatval($rowL['place_count']).'" 
step="0.01"
onKeyUp="if(this.value>'.floatval($rowL['place_count']).'){this.value=\''.floatval($rowL['place_count']).'\';}else if(this.value<1){this.value=\'1\';}"

class="form-control numbersOnly" style="min-width: 70px;" placeholder="vietu skaits" id="e_place_count'.$rowL['id'].'" name="e_place_count[]" '; 
if($rowL['status']==20 && $rowL['for_issue']==0){
	echo ' value="'.floatval($rowL['place_count']).'"';
}

if($rowL['status']==40){
	echo ' value="';
	if($rowL['issue_place_count']=='' || $rowL['issue_place_count']==0){ echo floatval($rowL['place_count']); }else{ echo floatval($rowL['issue_place_count']); }
	echo '"';
}

if(($rowL['status']==30 || $rowL['for_issue']==1) && ($rowL['status']!=40)){
	echo ' value="'.floatval($rowL['issue_place_count']).'" readonly';
} echo ' '.$dis.'>';

echo '
<td>
<input 

type="text" min="1" max="'.floatval($rowL['tare']).'" 
step="0.01"
onKeyUp="if(this.value>'.floatval($rowL['tare']).'){this.value=\''.floatval($rowL['tare']).'\';}else if(this.value<1){this.value=\'1\';}"

class="form-control numbersOnly" style="min-width: 70px;" placeholder="tara" id="eTare'.$rowL['id'].'" name="eTare[]" '; 

if($rowL['status']==20 && $rowL['for_issue']==0 || $rowL['status']==40){
	echo ' value="'.floatval($rowL['tare']).'"';
}

if(($rowL['status']==30 || $rowL['for_issue']==1) && ($rowL['status']!=40)){
	echo ' value="'.floatval($rowL['issueTare']).'" readonly';
} 

echo ' '.$dis.'>';

echo '
<td>
<input 

type="text" min="1" max="'.floatval($rowL['gross']).'" 
step="0.01"
onKeyUp="if(this.value>'.floatval($rowL['gross']).'){this.value=\''.floatval($rowL['gross']).'\';}else if(this.value<1){this.value=\'1\';}"

class="form-control numbersOnly" style="min-width: 70px;" placeholder="bruto" id="eGross'.$rowL['id'].'" name="eGross[]" '; 

if($rowL['status']==20 && $rowL['for_issue']==0 || $rowL['status']==40){
	echo ' value="'.floatval($rowL['gross']).'"';
}

if(($rowL['status']==30 || $rowL['for_issue']==1) && ($rowL['status']!=40)){
	echo ' value="'.floatval($rowL['issueGross']).'" readonly';
} echo ' '.$dis.'>';


echo '
<td nowrap>
<input 

type="text" min="1" max="'.floatval($rowL['net']).'" 
step="0.01"
onKeyUp="if(this.value>'.floatval($rowL['net']).'){this.value=\''.floatval($rowL['net']).'\';}else if(this.value<1){this.value=\'0\';}"

class="form-control numbersOnly" style="min-width: 70px;" placeholder="neto" id="eNet'.$rowL['id'].'" name="eNet[]" '; 

if($rowL['status']==20 && $rowL['for_issue']==0 || $rowL['status']==40){
	echo ' value="'.floatval($rowL['net']).'"';
}

if(($rowL['status']==30 || $rowL['for_issue']==1) && ($rowL['status']!=40)){
	echo ' value="'.floatval($rowL['issueNet']).'" readonly';
} echo ' '.$dis.'>';



if($kIrow['resource']=='BERKR' || $kIrow['resource']=='BERKRSAN'){

echo '<td nowrap><input type="text" class="form-control c_delta_t dt" data-idtdelta="'.$rowL['id'].'" style="min-width: 70px;" placeholder="faktiskais T" id="eDelta'.$rowL['id'].'"   '; 

if($rowL['status']==20 && $rowL['for_issue']==0 || $rowL['status']==40){
	echo ' name="e_real_delta[]" value="'.floatval($rowL['amount']).'"';
}

if($rowL['status']==30 || $rowL['for_issue']==1){
	echo ' value="'.floatval($rowL['fact_for_delta']).'" readonly';
} echo '>'; 





echo '<td nowrap><input type="text" class="form-control edt" style="min-width: 70px;" placeholder="Δ T" id="eDeltaT'.$rowL['id'].'" '; 

if($rowL['status']==20 && $rowL['for_issue']==0 || $rowL['status']==40){
	echo ' name="e_fact_for_delta[]" value="0" readonly';
}

if($rowL['status']==30 || $rowL['for_issue']==1){
	echo ' value="'.floatval($rowL['real_delta']).'" readonly';
} echo '>'; 


}



echo '<td><input type="text" class="form-control '.$rowL['productUmo'].'dn" style="min-width: 70px;" placeholder="saņ. Δ neto" id="eDeltaNet'.$rowL['id'].'"  value="'.floatval($rowL['delta_net']).'" disabled>';

echo '
<td>
<input 

type="text" min="1" max="'.floatval($rowL['cubicMeters']).'" 
step="0.01"
onKeyUp="if(this.value>'.floatval($rowL['cubicMeters']).'){this.value=\''.floatval($rowL['cubicMeters']).'\';}else if(this.value<1){this.value=\'1\';}"

class="form-control numbersOnly" style="min-width: 70px;" placeholder="m3" id="eCubicMeters'.$rowL['id'].'" name="eCubicMeters[]" '; 

if($rowL['status']==20 && $rowL['for_issue']==0 || $rowL['status']==40){
	echo ' value="'.floatval($rowL['cubicMeters']).'"';
}

if($rowL['status']==30 || $rowL['for_issue']==1){
	echo ' value="'.floatval($rowL['issueCubicMeters']).'" readonly';
} echo ' '.$dis.'>';



						echo '
						
						<td>'.$rowL['batchNo'].'
						<td nowrap>'.$rowL['location'].' - '.returnLocationName($conn, $rowL['location']);

						echo '<td>'.$rowL['lot_no'].'</td>';
						
						echo '<td>'.$rowL['container_type_no'].'</td>';


						echo '<td>'.$rowL['thisTransport'].'
						<td><input type="text" class="form-control" style="min-width: 100px;" placeholder="transporta nr." id="issueThisTransport'.$rowL['id'].'" name="issueThisTransport[]" ';
						
						if($rowL['status']==20 && $rowL['for_issue']==0){ 
							echo ' value="'.$thisTransport.'"';
						}
						
						if($rowL['status']==40){
							echo ' value="'.$rowL['issue_thisTransport'].'"';
						}						

						if(($rowL['status']==30 || $rowL['for_issue']==1) && ($rowL['status']!=40)){
							echo ' value="';
							if($rowL['issue_thisTransport']==''){echo $thisTransport;}else{echo $rowL['issue_thisTransport'];}
							echo '" readonly';
						}						
						
						echo ' '.$dis.'>

						<td><input type="text" class="form-control" style="min-width: 100px;" placeholder="deklarācijas nr." id="issueDeclarationTypeNo'.$rowL['id'].'" name="issueDeclarationTypeNo[]" ';
						
						
						if($rowL['status']==20 && $rowL['for_issue']==0){
							echo ' value="'.$declaration_type_no.'"';
						}
		
						if($rowL['status']==40){
							echo ' value="'.$rowL['issue_declaration_type_no'].'"';
						}		

						if(($rowL['status']==30 || $rowL['for_issue']==1) && ($rowL['status']!=40)){
							echo ' value="';
							if($rowL['issue_declaration_type_no']==''){echo $declaration_type_no;}else{echo $rowL['issue_declaration_type_no'];}
							echo '" readonly';
						}						
						
						echo ' '.$dis.'>';
						
						echo '<td>'.$rowL['cargo_status'].'</td>';					  
						
						echo '<td>'.$rowL['seal_no'].'</td>';
						echo '<td>'.$rowL['weighing_act_no'].'</td>';

					echo '</tr>';
					
		}		
			
		}

if($dis!='disabled'){		
	echo '<input type="hidden" id="issueDateFinal" name="issueDateFinal" value="'.date('d.m.Y', strtotime($kIrow['issueDate'])).'">';
	echo '<input type="hidden" id="actualDateFinal" name="actualDateFinal" value="'.date('d.m.Y', strtotime($kIrow['actualDate'])).'">';
	echo '<input type="hidden" id="issuance_id" name="issuance_id" value="'.$kIrow['issuance_id'].'">';		
	echo '<input type="hidden" name="issueResult" value="'.$r.'">';
	echo '<input type="hidden" name="issueResultFinal" value="'.$k.'">';
}	
	echo '</tbody>
	</table>';
echo '</div>';	

	if($dis!='disabled'){
		
			if(checkAllStatusLines($conn, $kIrow['agreements'], $kIrow['clientCode'], $kIrow['issuance_id'])==0){
				
				if(checkAllStatus($conn, $kIrow['agreements'], $kIrow['clientCode'], 30, '')==0 && $allowSubmit=='Y'){ 
					echo '<button type="submit" class="btn btn-default btn-xs actionLine" id="approveLine" style="margin-right: 2px;"><i class="glyphicon glyphicon-ok" style="color: green;"></i> nodot dokumentu</button>';
				}
			
			}
			

			if(checkAllStatus($conn, $kIrow['agreements'], $kIrow['clientCode'], 30, $kIrow['issuance_id'])==1){
				
				$getA = mysqli_query($conn, "SELECT actForm FROM agreements WHERE contractNr='".$kIrow['agreements']."'");
				$gAR = mysqli_fetch_array($getA);
				
				$form=null; 
				if($gAR['actForm']){$form = '&form='.$gAR['actForm'];}				
				
				echo '<a target="_blank" href="print?view=izs&id='.$kIrow['issuance_id'].''.$form.'" class="btn btn-default btn-xs" style="margin-right: 2px;"><i class="glyphicon glyphicon-print" style="color: silver;"></i> drukāt</a>';
				
				if(checkIfAllowedScannerClient($conn, $kIrow['clientCode'])==1 && $kIrow['forScan']==1){
					echo '<a target="_blank" href="shipAmount?id='.$kIrow['issuance_id'].''.$form.'" class="btn btn-default btn-xs" style="margin-right: 2px;"><i class="glyphicon glyphicon-print" style="color: silver;"></i> galamērķa atskaite</a>';					
				}

				if($gAR['actForm']==3){
					echo '<a target="_blank" href="excel?id='.$kIrow['issuance_id'].''.$form.'" class="btn btn-default btn-xs" style="margin-right: 2px;"><i class="glyphicon glyphicon-save-file" style="color: silver;"></i> EXCEL pielikums</a>';
				}

				echo '<button type="submit" class="btn btn-default btn-xs actionLine" style="margin-right: 2px;" id="cancelLine"><i class="glyphicon glyphicon-ban-circle" style="color: red;"></i> atvērt nodošanu</button>';
			
				if($p_edit=='on' && $allowRelease=='Y'){
					echo '<button type="submit" class="btn btn-default btn-xs actionLine" id="receiveLine"><i class="glyphicon glyphicon-share" style="color: green;"></i> izdot</button>';
				}
			}
		
?>		
</form>
<?php	
	}	

	if($kIrow['status']==10){

		$getA = mysqli_query($conn, "SELECT actForm FROM agreements WHERE contractNr='".$kIrow['agreements']."'");
		$gAR = mysqli_fetch_array($getA);
		
		$form=null; 
		if($gAR['actForm']){$form = '&form='.$gAR['actForm'];}				
		
		echo '<a target="_blank" href="print?view=izs&id='.$kIrow['issuance_id'].''.$form.'" class="btn btn-default btn-xs" style="margin-right: 2px;"><i class="glyphicon glyphicon-print" style="color: silver;"></i> drukāt</a>';
		
		if(checkIfAllowedScannerClient($conn, $kIrow['clientCode'])==1 && $kIrow['forScan']==1){
			echo '<a target="_blank" href="shipAmount?id='.$kIrow['issuance_id'].''.$form.'" class="btn btn-default btn-xs" style="margin-right: 2px;"><i class="glyphicon glyphicon-print" style="color: silver;"></i> galamērķa atskaite</a>';					
		}

		if($gAR['actForm']==3){
			echo '<a target="_blank" href="excel?id='.$kIrow['issuance_id'].''.$form.'" class="btn btn-default btn-xs" style="margin-right: 2px;"><i class="glyphicon glyphicon-save-file" style="color: silver;"></i> EXCEL pielikums</a>';
		}

	}	
	
	echo '
	</div>
	</div>
	</div>
	</div>
	';



	
}
if($dis!='disabled'){	
?>
<script>
$("#checkall").change(function () {
    $("input:checkbox.cb-element").prop('checked', $(this).prop("checked"));

	var target = $('input:checkbox').parent().find('input[type=hidden]').val();
	
    if(target == 0)
    {
        target = 1;
		
    }
    else
    {
        target = 0;
    }
	
    $('input:checkbox').parent().find('input[type=hidden]').val(target);	
	
});
</script>

<script>
$('input[type=checkbox]').on("change",function(){
	
    var target = $(this).parent().find('input[type=hidden]').val();
	
    if(target == 0)
    {
        target = 1;
    }
    else
    {
        target = 0;
    }
	
    $(this).parent().find('input[type=hidden]').val(target);
});
</script>
<?php
}

// izdošanas vēsture
if($view=='history' && !$id){

?>
<script>
function hisDoc(val) {
	$('#contenthere').load('/pages/release/release.php?view=history&id='+val+'');
}
</script>

<script>
function selCargo(val) {
	var val = val.value;
	var search = $('#searchWait').val();
	$('#contenthere').load('/pages/release/release.php?view=history&cargo='+val+'&search='+search+'<?php if($cust){echo '&cust='.$cust;} ?>');
}
</script>

<script>
function selCust(val) {
	var val = val.value;
	var search = $('#searchWait').val();
	$('#contenthere').load('/pages/release/release.php?view=history&cust='+val+'&search='+search+'<?php if($cargo){echo '&cargo='.$cargo;} ?>');
}
</script>

<script type="text/javascript">

function getStates(value) {
	
    var search = $.post("/pages/release/search.php?view=cargoHis<?php if($cargo){echo '&cargo='.$cargo;} if($cust){echo '&client='.$cust;} ?>", {name:value},function(data){
        $("#results").html(data);		
    });
	if(search){
		console.log('S3');
		$('#showWait').html('<i class="glyphicon glyphicon-refresh spin" style="color: #00B5AD"></i>&nbsp;&nbsp;');
	}
    search.done(function( data ) {
		$('#showWait').html('');
    });	
}
</script>



	<script>
	$('#removeFilterCargo').on('click', function() {
		var search = $('#searchWait').val();
		$('#contenthere').load('/pages/release/release.php?view=<?=$view;?>&cust=<?=$cust;?>&search='+search);
	});
	$('#removeFilterCust').on('click', function() {
		var search = $('#searchWait').val();
		$('#contenthere').load('/pages/release/release.php?view=<?=$view;?>&cargo=<?=$cargo;?>&search='+search);
	});	
	</script>
<?php	
		echo '<div class="page-header" style="margin-top: -5px;">
		  
			<div class="btn-group btn-group-xs" role="group" aria-label="Small button group" style="display:inline-block;"> 
				<a class="btn btn-default classlist" ><i class="glyphicon glyphicon-list" style="color: #00B5AD" title="pamatdati"></i></a>
				<a class="btn btn-default active classhistory" ><i class="glyphicon glyphicon-time" style="color: #00B5AD"  title="vēsture"></i></a>
				';
				
				echo '<a class="btn btn-default classadd" ><i class="glyphicon glyphicon-plus" style="color: #00B5AD"  title="pievienot"></i></a>';					
				
			echo '
				</div>';
			if ($res=="done"){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-success btn-xs" >saglabāts!</div></div>';}
			  	  
		echo '</div>';
		
		echo '<p style="display:inline-block;">izdošanas vēsture</p>';

		echo ' <div style="float: right;"><div id="showWait" style="display: inline-block;"></div><div style="display: inline-block;"><input type="text" id="searchWait" class="form-control input-xs" onkeyup="getStates(this.value)" placeholder="meklēt" value="'.$_GET['search'].'"></div></div>';  


		if($cargo){echo '
			<div class="alert alert-info alert-dismissible" role="alert" style="display:inline-block; padding-top: 5px; padding-bottom: 5px;">
			  <button type="button" class="close" data-dismiss="alert" id="removeFilterCargo"><span aria-hidden="true">&times;</span></button>
			  <strong>filtrs pēc izdošanas:</strong> '.$cargo.'
			</div>	
		';}	
		
		$forId = null;
		if($cust){echo '
			<div class="alert alert-info alert-dismissible" role="alert" style="display:inline-block; padding-top: 5px; padding-bottom: 5px;">
			  <button type="button" class="close" data-dismiss="alert" id="removeFilterCust"><span aria-hidden="true">&times;</span></button>
			  <strong>filtrs pēc klienta:</strong> '.$cust.'
			</div>';
			$forId = " AND clientCode='".$cust."'";
		}			
		
		echo '
			<div class="form-group col-md-2 pull-right">
			  <select class="form-control selectpicker btn-group-xs" data-live-search="true" title="filtrēt pēc izdošanas" onchange="selCargo(this)">';
			   $selectClient = mysqli_query($conn, "SELECT issuance_id FROM issuance_doc WHERE status='10' ".$forId." GROUP BY issuance_id") or die(mysqli_error($conn));
			 
			  while($rowc = mysqli_fetch_array($selectClient)){
				  echo '<option value="'.$rowc['issuance_id'].'"';
				  if($rowc['issuance_id']==$cargo){ echo ' selected';}
				 echo ' >'.$rowc['issuance_id'].'</option>';
			  }
			  
			  echo '
			  </select>	
			</div>


			<div class="form-group col-md-2 pull-right">
			  <select class="form-control selectpicker btn-group-xs" data-live-search="true" title="filtrēt pēc klienta" onchange="selCust(this)">';
			   $selectCust = mysqli_query($conn, "
					SELECT n_customers.Code, n_customers.Name 
					FROM n_customers
					LEFT JOIN issuance_doc					
					ON n_customers.Code=issuance_doc.clientCode
					WHERE issuance_doc.status=10
					GROUP BY n_customers.Code					
			   ") or die(mysqli_error($conn));
			  
			  while($rowc = mysqli_fetch_array($selectCust)){
				  echo '<option value="'.$rowc['Code'].'"';
				  if($rowc['Code']==$cust){ echo ' selected';}
				 echo ' >'.$rowc['Code'].' ('.$rowc['Name'].')</option>';
			  }
			  
			  echo '
			  </select>
			</div>
			
			
			<div class="clearfix"></div>
			';		
		
			if($cargo){$filterCargo = ' AND issuance_doc.issuance_id="'.$cargo.'"';}else{$filterCargo = null;}
			if($cust){$filterCust = ' AND issuance_doc.clientCode="'.$cust.'"';}else{$filterCust = null;}	

	
echo '<div id="results">';	

	$keepFilter=null;
	if($cargo){$keepFilter.='&cargo='.$cargo;}
	if($cust){$keepFilter.='&cust='.$cust;}
		
	if($_GET['search']){
		$s = mysqli_real_escape_string($conn, $_GET['search']);	
		$search_result = " AND (issuance_id LIKE '%$s%' || cargo LIKE '%$s%' || issueDate LIKE '%$s%' || actualDate LIKE '%$s%' || brigade LIKE '%$s%' || date LIKE '%$s%' || time_from LIKE '%$s%' || time_to LIKE '%$s%' || place LIKE '%$s%' || transport LIKE '%$s%' || issuance_act_no LIKE '%$s%')";
		$searchUrl = 'search='.$s.'&';
	
	}else{
		$searchUrl = null;	
		$search_result = "";
	
	}
		
	$rec_limit = $a_p_l; $offset=0; $max_pages = 10;  //DEFINĒJAM LIMITUS
	$link_to = $page_file.'?view='.$view.''.$keepFilter.'&'.$searchUrl.'page='; //LINKS KAS TIKS ATVĒRTS KAD NOSPIEDĪS UZ LAPAS, RAKSTĪT LĪDZ page 
	
	
	$query = "
		SELECT issuance_doc.* , 
		 (SELECT SUM(cargo_line.issueAmount) FROM cargo_line WHERE issuance_doc.issuance_id=cargo_line.issuance_id) AS total, 
		 (SELECT SUM(cargo_line.issue_place_count) FROM cargo_line WHERE issuance_doc.issuance_id=cargo_line.issuance_id) AS issue_place_count, 
		 (SELECT SUM(cargo_line.issueGross) FROM cargo_line WHERE issuance_doc.issuance_id=cargo_line.issuance_id) AS issueGross, 
		 (SELECT SUM(cargo_line.issueCubicMeters) FROM cargo_line WHERE issuance_doc.issuance_id=cargo_line.issuance_id) AS issueCubicMeters		
		FROM issuance_doc 	
		WHERE issuance_doc.status='10' ".$search_result." ".$filterCargo." ".$filterCust."
		ORDER BY CASE WHEN issuance_doc.scanStatus=200 THEN issuance_doc.scanStatus END DESC";  //NEPIECIEŠAMAIS VAICĀJUMS		
	
	list($page_menu, $result_all, $resultGL7) = pageing_menu($offset, $rec_limit, $max_pages, $link_to, $page, $conn, $query);  //TIEK IEGŪTS ARRAY KO UZŅEM AR LIST 

	echo $page_menu;   //IZVADA TABULU AR LAPĀM			
	
	$count_GL7 = mysqli_num_rows($resultGL7);  //IEGŪST SKAITU 					
		if ($count_GL7!=0){

			echo '	<div class="table-responsive"><table class="table table-hover table-responsive" border="1" style="border: 1px solid #ddd !important;"><thead><tr >
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
						<th>Tālmaņa lapa</th>
					</tr></thead><tbody>';
			while($row = mysqli_fetch_array($resultGL7)){

				echo '	<tr  class="classlistedit"  onclick="newDoc('.$row['id'].')">';
				
							$show_green=null;
							if($row['scanStatus']==200){
								$show_green = ' style="background-color: #ABEBC6; color: black;"';
							}				
				
							echo '<td '.$show_green.'>'.$row['issuance_id'];
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
						echo '<td align="center">
			<a href="print.php?view=tpage&id='.$id.'&eid='.$row['issuance_id'].'" target="_blank"><i class="glyphicon glyphicon-print" style="color: #00B5AD"  title="Tālmaņa lapa"></i></a>							
						</tr>';
			}
			
			echo '</tbody></table></div>';
			mysqli_close($conn);
		}else{
			echo '<i class="glyphicon glyphicon-ban-circle glyphicon-lg" style="color: red;"></i> nav neviena ieraksta!';
		}
		echo '</div>';

} // izdošanas vēsture beidzas


// izdošanas vēstures rindas
if($view=='history' && $id){

	echo '<div class="page-header" style="margin-top: -5px;">
	  
		<div class="btn-group btn-group-xs" role="group" aria-label="Small button group" style="display:inline-block;"> 
			<a class="btn btn-default classlist" ><i class="glyphicon glyphicon-list" style="color: #00B5AD" title="pamatdati"></i></a>';
			echo '<a class="btn btn-default active classhistory" ><i class="glyphicon glyphicon-time" style="color: #00B5AD"  title="vēsture"></i></a>';
			echo '<a class="btn btn-default classadd" ><i class="glyphicon glyphicon-plus" style="color: #00B5AD"  title="pievienot"></i></a>';
			
		echo '</div>';

			  
	echo '</div>';	
	$GetXY = mysqli_query($conn, "SELECT * FROM cargo_header WHERE id='".intval($id)."'");
	$GXYrow = mysqli_fetch_array($GetXY);
	if($GXYrow['cargoCode']){$cCode=$GXYrow['cargoCode'];}else{$cCode=null;}
	if($GXYrow['docNr']){$isDocNr = $GXYrow['docNr'];}else{$isDocNr = null;}
	echo '<p style="display:inline-block;"> </p> <div id="printchatbox" style="display:inline-block; font-weight: bold;">'.$cCode.'</div>';

	$result = mysqli_query($conn,"SELECT * FROM cargo_header WHERE id='".intval($id)."'");
	$row = mysqli_fetch_array($result);
	$status = $row['status'];
	$disabled='disabled';
	?>

	
	<?php
	echo '
	  <div class="form-row">
		<div class="form-group col-md-3">
		  <label for="deliveryDate">piegādes dat.</label>
		  <input type="text" class="form-control datepicker" id="deliveryDate" placeholder="piegādes dat." value="'.date('d.m.Y', strtotime($row['deliveryDate'])).'" '.$disabled.'>
		</div>	  
		<div class="form-group col-md-3">
		  <label>pavadzīmes nr</label>
		  <input type="text" class="form-control" placeholder="pavadzīmes nr" value="'.$row['ladingNr'].'" '.$disabled.'>
		</div>
		';

		echo '	
		<div class="form-group col-md-3">
		  <label>kravas tips</label>
		  <input type="text" class="form-control" placeholder="kravas tips" value="'.$row['deliveryType'].'" '.$disabled.'>
		</div>			
			';

		echo '
		<div class="form-group col-md-3">
		  <label>kravas kods</label>
		  <input type="text" class="form-control" placeholder="kravas kods" value="'.$row['deliveryCode'].'" '.$disabled.'>
		</div>			
			';			

			
		echo '<div class="clearfix"></div>
		<div class="form-group col-md-3">
		  <label>klienta kods - nosaukums</label>
		  <input type="text" class="form-control" placeholder="kravas kods" value="'.$row['clientCode'].'" '.$disabled.'>
		</div>				
			';				
			
			echo '
		<div class="form-group col-md-3">
		  <label>īpašnieka kods - nosaukums</label>
		  <input type="text" class="form-control" placeholder="īpašnieka kods - nosaukums" value="'.$row['ownerCode'].'" '.$disabled.'>
		</div>			
			';

		echo '			
		<div class="form-group col-md-3">
		  <label>saņēmēja kods - nosaukums</label>
		  <input type="text" class="form-control" placeholder="saņēmēja kods - nosaukums" value="'.$row['receiverCode'].'" '.$disabled.'>
		</div>				
			';			


			echo '			
		<div class="form-group col-md-3">
		  <label>līgums</label>
		  <input type="text" class="form-control" placeholder="līgums" value="'.$row['agreements'].'" '.$disabled.'>
		</div>			
			';			
			

			echo '
				<div class="form-group col-md-3">
				  <label for="transportNo">transporta nr.</label>
				  <input type="text" class="form-control" id="transportNo" placeholder="transporta nr." value="'.$row['transportNo'].'" '.$disabled.'>
				</div>			
			';

			
			echo '
		<div class="form-group col-md-3">
		  <label>noliktava</label>
		  <input type="text" class="form-control" placeholder="noliktava" value="'.$row['location'].'" '.$disabled.'>
		</div>				
			';
		
		echo '<div class="clearfix"></div>';	
		
		if($isDocNr){

			
			$inCargo = mysqli_query($conn, "
							SELECT SUM(amount) as amount, SUM(volume) as volume, 
							SUM(tare) as t, SUM(gross) as b, SUM(net) as n, SUM(cubicMeters) as a,
							productUmo, productNr FROM cargo_line WHERE docNr='".$isDocNr."' GROUP BY productUmo") or die (mysqli_error($conn));

			if(mysqli_num_rows($inCargo)>0){
			
			echo '<p style="display:inline-block;">daudzumi</p>';
			
				echo '
				<div class="table-responsive">
					<table class="table table-hover table-responsive" border="1" style="border: 1px solid #ddd !important;">
						<thead>
							<th>mērvienība</th>
							<th>daudzums</th>
							<th>tara (kg)</th>
							<th>bruto (kg)</th>
							<th>neto (kg)</th>
							<th>apjoms (m3)</th>
						</thead>
						<tbody>
						';			
			while($icRow = mysqli_fetch_array($inCargo)){				
				echo '

						
							<tr>
								<td>'.$icRow['productUmo'].'</td>
								<td>'.floatval($icRow['amount']).'</td>
								<td>'.floatval($icRow['t']).'</td>
								<td>'.floatval($icRow['b']).'</td>
								<td>'.floatval($icRow['n']).'</td>
								<td>'.floatval($icRow['a']).'</td>
								
							</tr>

				';
			
			}
				echo '
						</tbody>
					</table>
				</div>				
				';
				
			}
		}
		
	
		echo '

	  </div>


	  <div class="clearfix"></div>';
	  
	  echo '
	  <a href="/pages/receipt/status.php?id='.$id.'" data-toggle="modal" data-target="#showStatus" data-remote="false" class="btn btn-default btn-xs">
	    	<i class="glyphicon glyphicon-option-vertical"></i> statuss
	  </a>';	  
	  
	  echo '<div class="clearfix"></div>';
	
	$getLastLine = mysqli_query($conn, "SELECT thisDate, thisTransport FROM cargo_line WHERE docNr='".$row['docNr']."' ORDER BY id DESC") or die(mysqli_error($conn));
	$gllRow = mysqli_fetch_array($getLastLine);
	
	if($gllRow['thisDate']!='0000-00-00 00:00:00' && $gllRow['thisDate']!=''){$devTime = date('d.m.Y', strtotime($gllRow['thisDate']));}else{$devTime = date('d.m.Y', strtotime($row['deliveryDate']));}
	
	if($gllRow['thisTransport']!=''){$devTransport = $gllRow['thisTransport'];}else{$devTransport = $row['transportNo'];}	
	
	echo '<div class="clearfix"></div>
	
	
	<div class="table-responsive"><table class="table table-hover table-responsive" border="1" style="border: 1px solid #ddd !important;">
		<thead> 
			<tr>
				<th>produkta nr</th>
				<th>partijas nr</th>
				<th>noliktava</th>
				<th>piegādes dat.</th>
				<th>transporta nr.</th>
				<th>apjoms</th>
				<th>mērvienība</th>
				<th>daudzums</th>';
			echo '
			</tr>
		</thead>
		<tbody>';
		
		
		$lines = mysqli_query($conn, "SELECT * FROM cargo_line WHERE docNr='".$row['docNr']."'");
		while($rowL = mysqli_fetch_array($lines)){
			
						$getNames = mysqli_query($conn, "SELECT name1, name2, unitOfMeasurement FROM n_items WHERE code='".$rowL['productNr']."'");
						$gNrow = mysqli_fetch_array($getNames);
						
						
			echo '	<tr>
						<td>'.$rowL['productNr'].'<br>'.$gNrow['name1'].' '.$gNrow['name2'];
						
						echo '
						<td>'.$rowL['batchNo'];				
						echo '<td>'.$rowL['location'].' - '.returnLocationName($conn, $rowL['location']).'
						<td>'.date('d.m.Y', strtotime($rowL['thisDate'])).'
						<td>'.$rowL['thisTransport'].'
						<td>'.floatval($rowL['amount']);
						
						echo '<td>'.$rowL['productUmo'].'</td>';
						
						echo '
						<td>'.floatval($rowL['volume']);
						
						
					echo '</tr>';
		}

		
	echo '</tbody>
	</table></div>';	
	

} // izdošanas vēstures rindas beidzas


?>
			<?php if($id){ ?>
			<br><br>
			<div class="panel panel-default" style="margin-bottom: -5px;">
				<div class="panel-body">
					<div style="float: right; margin: -10px;">pēdējās aktivitātes veica: <?=lastActionBy($conn, $id);?></div>
				</div>
			</div>
			<?php } ?>


<div class="modal fade" id="showStatus" tabindex="-1" role="dialog" aria-labelledby="showStatusLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
	  
      <div class="modal-body">
        
      </div>
	  
    </div>
  </div>
</div>	

<script>
// Fill modal with content from link href
$("#showStatus").on("show.bs.modal", function(e) {
    var link = $(e.relatedTarget);
    $(this).find(".modal-body").load(link.attr("href"));
});
</script>

<script>
    $(document).ready(function() {
        $('.numbersOnly').keypress(function (event) {
            return isNumber(event, this)
			
        });
    });
    // THE SCRIPT THAT CHECKS IF THE KEY PRESSED IS A NUMERIC OR DECIMAL VALUE.
    function isNumber(evt, element) {

        var charCode = (evt.which) ? evt.which : event.keyCode

        if (
            (charCode != 45 || $(element).val().indexOf('-') != -1) &&      // “-” CHECK MINUS, AND ONLY ONE.
            (charCode != 46 || $(element).val().indexOf('.') != -1) &&      // “.” CHECK DOT, AND ONLY ONE.
            (charCode < 48 || charCode > 57))
            return false;

        return true;
    } 
</script>
<script>
setTimeout(function() {
    $('#hideMessage').fadeOut('fast');
}, 3000);
</script>
<script>
$('#savebtn').on('click', function() {
	$("#productNr").prop('required',true);
	$("#amount").prop('required',true);
});
</script>
<script>
$(document).ready(function() {
	$('.selectpicker').selectpicker();
});
</script>


<script>
function validateTime(obj)
{
    var timeValue = obj.value;
    if(timeValue == "" || timeValue.indexOf(":")<0)
    {
        alert("Nepareizs laika formāts. Pareiza laika formāta piemērs: 12:03");
        return false;
    }
    else
    {
        var sHours = timeValue.split(':')[0];
        var sMinutes = timeValue.split(':')[1];

        if(sHours == "" || isNaN(sHours) || parseInt(sHours)>23)
        {
            alert("Nepareizs laika formāts. Pareiza laika formāta piemērs: 12:03");
            return false;
        }
        else if(parseInt(sHours) == 0)
            sHours = "00";
        else if (sHours <10)
            sHours = "0"+sHours;

        if(sMinutes == "" || isNaN(sMinutes) || parseInt(sMinutes)>59)
        {
            alert("Nepareizs laika formāts. Pareiza laika formāta piemērs: 12:03");
            return false;
        }
        else if(parseInt(sMinutes) == 0)
            sMinutes = "00";
        else if (sMinutes <10)
            sMinutes = "0"+sMinutes;    
		var sHours = sHours.slice(-2);
		var sMinutes = sMinutes.slice(-2);
        obj.value = sHours + ":" + sMinutes;        
    }

    return true;    
}
</script>

<script>
	
$(document).ready(function(){
    $(".c_delta_t").keyup(function(){

		  var idtdelta = $(this).data('idtdelta');

		  var id = $(this).attr('id');
          var val1 = $("#issueAmount"+idtdelta).val();

		  var baz = 'issueAmount'+idtdelta;

		  if(id==baz){
			$("#eDelta"+idtdelta).val(val1);
		  }
		  

          var val2 = $("#eDelta"+idtdelta).val();
		  var total = val2-val1; 
		  total = total.toFixed(2);
          $("#eDeltaT"+idtdelta).val(total);
   });
});	

</script>
<?php
if($kIrow['issuance_id']){
	
	$isProcessed = mysqli_query($conn, "SELECT id FROM cargo_line WHERE issuance_id='".$kIrow['issuance_id']."'");
	$isP = mysqli_num_rows($isProcessed);

	if($kIrow['issuance_id'] && $isP==0){

	?>
	<script>
	$(document).ready(function () {
		$(document).on('click', '#deleteIt', function(e) {
			e.preventDefault();

			if (confirm('UZMANĪBU! Vai tiešām vēlaties dzēst ierakstu?')) {

				$.ajax({
					url : '/pages/release/post.php?r=deleteIt&id=<?=$kIrow['issuance_id'];?>',
					type: "POST",
					data: $(this).serializeArray(),
					beforeSend: function(){
						$('#savebtn').html("gaidiet...");
						$("#savebtn").prop("disabled",true);
					},			
					success: function (data) {
						console.log(data+' CL-5');
						
					if(data=='STOP'){
						location.reload();
					}						

						$('#contenthere').load('/pages/release/release.php?res=done');
					},
					error: function (jXHR, textStatus, errorThrown) {
						alert(errorThrown);
					}
				});

			}

		});

		$('.deleteIt').html('<button type="submit" class="btn btn-default btn-xs" id="deleteIt" style="margin-left: 4px;"><i class="glyphicon glyphicon-erase" style="color: red;"></i> dzēst</button>');

	});
	</script>
	<?php

	}else{
		?>
			<script>
				$(document).ready(function () {

					$('.deleteIt').html('');

				});
			</script>
		<?php
	}
}
?>
					<div id="pleaseWait" style="display: none;">
						<h1 align="center" style="padding-top: 300px;">
						
							<div style="background-color: white; border: solid #eee 1px; height: 50%; width: 50%; margin: auto;">
								<i class="glyphicon glyphicon-refresh spin" style="color: #00B5AD"></i>
								<br><span style="color: black;"><b>Uzmanību!</b> Notiek process. Lūdzu neaizveriet <b>šo lapu</b> kamēr process nav beidzies.</span><br>
							</div>
							
						</h1>
					</div>
<script>
$(function() {
  $("[data-toggle=popover]").popover({
    html: true,
    content: function() {
      var content = $(this).attr("data-popover-content");
      return $(content).children(".popover-body").html();
    },
    title: function() {
      var title = $(this).attr("data-popover-content");
      return $(title).children(".popover-heading").html();
    }
  });
});
</script>

<?php include_once("../../datepicker.php"); ?>