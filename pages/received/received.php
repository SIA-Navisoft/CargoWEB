<?php
error_reporting(E_ALL ^ E_NOTICE);
require('../../lock.php');

$page_file="received";
$page_folder="received";



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

$view = $action = $section = $id = $query = $searchquery= null;
if (isset($_GET['view'])){$view = htmlentities($_GET['view'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['id'])){$id = htmlentities($_GET['id'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['res'])){$res = htmlentities($_GET['res'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['ser'])){$ser = htmlentities($_GET['ser'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['client'])){$client = htmlentities($_GET['client'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['dFrom'])){$dFrom = htmlentities($_GET['dFrom'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['dTo'])){$dTo = htmlentities($_GET['dTo'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['sr'])){$sr = htmlentities($_GET['sr'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['search'])){$searchquery = htmlentities($_GET['search'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['groupProduct'])){$groupProduct = htmlentities($_GET['groupProduct'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['issueDetail'])){$issueDetail = htmlentities($_GET['issueDetail'], ENT_QUOTES, "UTF-8");}

include('../../functions/base.php');
require('../../inc/s.php');

if(!empty($_GET['page'])) {$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);if(false === $page) {$page = 1;}}else{$page = 1;}  //IEGŪSTAM LAPAS NUMURU
if($page){$glpage = '?page='.$page;}else{$glpage = null;}


if(!$dFrom){$dFrom = date('01.m.Y');}
if(!$dTo){$dTo = date('d.m.Y');}

?>

<script>
$(document).ready(function(){
    $('.classlist').click(function(){
		$("#pleaseWait").toggle();
        $('#contenthere').load('/pages/<?=$page_folder;?>/<?=$page_file;?>.php<?=$glpage;?>');
    });
})
</script>

<script>

$(function() {
	$(".paging").delegate("a", "click", function() {	
		var url = $(this).attr('href');
		
		var client = '<?=$client;?>'; 
		if(client){
			var client = '&client='+client;
		}else{
			var client = '';
		}

		var dFrom = '<?=$dFrom;?>'; 
		if(dFrom){
			var dFrom = '&dFrom='+dFrom;
		}else{
			var dFrom = '';
		}		

		var dTo = '<?=$dTo;?>'; 
		if(dTo){
			var dTo = '&dTo='+dTo;
		}else{
			var dTo = '';
		}

		$("#pleaseWait").toggle();
		$('#contenthere').load('/pages/<?=$page_folder;?>/'+encodeURI(url)+''+encodeURI(client)+''+encodeURI(dFrom)+''+encodeURI(dTo)+'&groupProduct=<?=urlencode($groupProduct);?>&issueDetail=<?=urlencode($issueDetail);?>&search=<?=urlencode($searchquery);?>');
		event.preventDefault();
	});
});
</script>

<script>
function selClient(val) {
	$("#pleaseWait").toggle();
	var val = val.value;
	$('#contenthere').load('/pages/<?=$page_folder;?>/<?=$page_file;?>.php?client='+val+'&dFrom=<?=$dFrom;?>&dTo=<?=$dTo;?>&groupProduct=<?=$groupProduct;?>&issueDetail=<?=$issueDetail;?>&search=<?=urlencode($searchquery);?>');
}

function groupProduct() {
	
	$("#pleaseWait").toggle();
	var val = $('#groupProduct').prop('checked');
    var search = $.post("pages/<?=$page_folder;?>/<?=$page_file;?>.php?client=<?=$client;?>&dFrom=<?=$dFrom;?>&dTo=<?=$dTo;?>&search=<?=urlencode($searchquery);?>&issueDetail=<?=$issueDetail;?>&groupProduct="+val,function(data){
        $("#contenthere").html(data);
				
    });
	if(search){
		$('#showWait').html('<i class="glyphicon glyphicon-refresh spin" style="color: #00B5AD"></i>&nbsp;&nbsp;');
	}
    search.done(function( data ) {
		$('#showWait').html('');
    });	

}

function issueDetail() {
	
	$("#pleaseWait").toggle();
	var val = $('#issueDetail').prop('checked');
    var search = $.post("pages/<?=$page_folder;?>/<?=$page_file;?>.php?client=<?=$client;?>&dFrom=<?=$dFrom;?>&dTo=<?=$dTo;?>&search=<?=urlencode($searchquery);?>&groupProduct=<?=$groupProduct;?>&issueDetail="+val,function(data){
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

<script type="text/javascript">

function search(value) {
    
	
	
	var client = '&client=<?=$client;?>';
	var dFrom = '&dFrom=<?=$dFrom;?>';
	var dTo = '&dTo=<?=$dTo;?>';
	var groupProduct = '&groupProduct=<?=$groupProduct;?>&issueDetail=<?=$issueDetail;?>';
	
    var search = $.post("/pages/<?=$page_folder;?>/search.php?view=received"+client+""+dFrom+""+dTo+""+groupProduct+"", {name:value},function(data){
        $("#results").html(data);		
    });
	if(search){
		$('#showWait').html('<i class="glyphicon glyphicon-refresh spin" style="color: #00B5AD"></i>&nbsp;&nbsp;');
	}
    search.done(function( data ) {
		$('#showWait').html('');
    });	
}

$('#searchWait').keyup(function(){

	$('#link_excel').attr('href','pages/<?=$page_folder;?>/excel.php?client=<?=$client;?>&issueDetail=<?=$issueDetail;?><?php if($groupProduct){ echo '&groupProduct='.$groupProduct; } if($dFrom){ echo '&dFrom='.date('Y-m-d', strtotime($dFrom)); } if($dTo){ echo '&dTo='.date('Y-m-d', strtotime($dTo));} ?>&search='+$(this).val());

});
</script>

<script>
function dateFilter() {

$("#pleaseWait").toggle();

var dFrom = $('#dFrom').val();
var dTo = $('#dTo').val();

$('#contenthere').load('/pages/<?=$page_folder;?>/<?=$page_file;?>.php?client=<?=$client;?>&issueDetail=<?=$issueDetail;?><?php if($page){echo '&page='.$page;} if($groupProduct){ echo '&groupProduct='.$groupProduct; } ?>&dFrom='+dFrom+'&dTo='+dTo+'&search=<?=urlencode($searchquery);?>');
}
</script>

<?php

	echo '
	<div id="pleaseWait" style="display: none;">
		<h1 align="center" style="padding-top: 300px;"><i class="glyphicon glyphicon-refresh spin" style="color: #00B5AD"></i></h1>
	</div>';

if(!$view){
?>

	<script>
	$('#removeFilter').on('click', function() {
		$("#pleaseWait").toggle();
		$('#contenthere').load('/pages/<?=$page_folder;?>/<?=$page_file;?>.php');
	});
	</script>
<?php	
		echo '<div class="page-header" style="margin-top: -5px;">
		  
			<div class="btn-group btn-group-xs" role="group" aria-label="Small button group" style="display:inline-block;"> 
				<a class="btn btn-default active classlist" ><i class="glyphicon glyphicon-list" style="color: #00B5AD" title="pamatdati"></i></a>			
			</div>';

			echo '
			<div style="float: right;" >
					<div style="display: inline-block;">
						<input type="text" class="form-control input-xs datepicker" id="dFrom" value="'; if($dFrom){echo date('d.m.Y', strtotime($dFrom)); } else { echo date('01.m.Y'); } echo '">
					</div>
					<div style="display: inline-block;">
						<input type="text" class="form-control input-xs datepicker" id="dTo" value="'; if($dTo){echo date('d.m.Y', strtotime($dTo)); } else { echo date('d.m.Y'); } echo '">
					</div>						
					<a class="btn btn-default btn-xs" onclick="dateFilter();"><i class="glyphicon glyphicon-refresh" style="color: #00B5AD"  title="filtrēt"></i></a> ';
					
					$thisFrom = $thisTo = $thisClient = $thisGroupProduct = $thisIssueDetail = null;
					if($dFrom){ $thisFrom = '&dFrom='.date('Y-m-d', strtotime($dFrom)); } 
					if($dTo){ $thisTo = '&dTo='.date('Y-m-d', strtotime($dTo));}					
					if($client){ $thisClient = '&client='.$client;}					
					if($groupProduct){ $thisGroupProduct = '&groupProduct='.$groupProduct;}	
					if($issueDetail=='true'){ $thisIssueDetail = '&issueDetail='.$issueDetail;}				
					
					
					
					echo '
					
						<div class="dropdown" style="display: inline-block;">
						  <button class="btn btn-default dropdown-toggle btn-xs" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
							EXCEL
							<span class="caret"></span>
						  </button>
						  <ul class="dropdown-menu pull-right" aria-labelledby="dropdownMenu1">
							<li><a id="link_excel" href="pages/'.$page_folder.'/excel.php?section=1'.$thisFrom.''.$thisTo.''.$thisClient.''.$thisGroupProduct.''.$thisIssueDetail.'" data-org="pages/'.$page_folder.'/excel.php?section=1'.$thisFrom.''.$thisTo.''.$thisClient.''.$thisGroupProduct.''.$thisIssueDetail.'" target="_blank">detalizācija</a></li>
							<li><a id="link_excel" href="pages/'.$page_folder.'/excel.php?section=2'.$thisFrom.''.$thisTo.''.$thisClient.''.$thisGroupProduct.'" target="_blank">apgrozījums periodā</a></li>
							<li><a id="link_excel" href="pages/'.$page_folder.'/excel.php?section=3'.$thisFrom.''.$thisTo.''.$thisClient.''.$thisGroupProduct.'" target="_blank">apgrozījums periodā (gadi)</a></li>

						  </ul>
						</div>					
					
					';					

			echo '
			</div>';			
			  	  
		echo '</div>';
		
		echo '<p style="display:inline-block;">noliktava</p>';

		$svalue = $ssearch = null;
		if($searchquery){
			$svalue = ' value="'.$searchquery.'"';
			$ssearch = " AND (clr.thisTransport LIKE '%$searchquery%' || clr.serialNo LIKE '%$searchquery%' || clr.place_count LIKE '%$searchquery%' || date_format(clr.activityDate, '%Y-%m-%d') LIKE '%$searchquery%' || chr.clientCode LIKE '%$searchquery%' || chr.clientName LIKE '%$searchquery%' || chr.ownerCode LIKE '%$searchquery%' || chr.ownerName LIKE '%$searchquery%' || chr.acceptance_act_no LIKE '%$searchquery%' || clr.productNr LIKE '%$searchquery%' || ni.name1 LIKE '%$searchquery%' || ni.name2 LIKE '%$searchquery%')";
		}
		
		echo ' <div style="float: right;"><div id="showWait" style="display: inline-block;"></div><div style="display: inline-block;"><input type="text" id="searchWait" class="form-control input-xs" onkeyup="search(this.value)" placeholder="meklēt" '.$svalue.'></div></div>';  
		
		if($client){echo '
			<div class="alert alert-info alert-dismissible" role="alert" style="display:inline-block; padding-top: 5px; padding-bottom: 5px;">
			  <button type="button" class="close" data-dismiss="alert" id="removeFilter"><span aria-hidden="true">&times;</span></button>
			  <strong>filtrs pēc klienta:</strong> '.$client.' - '.returnClientName($conn, $client).'
			</div>	
		';}
		
		echo '
			<div class="form-group col-md-4 pull-right">
			  <select class="form-control selectpicker btn-group-xs" data-live-search="true" title="filtrēt pēc klienta" onchange="selClient(this)"  style="display: inline-block;">';
			  
			   $selectClient = mysqli_query($conn, "SELECT DISTINCT(clientCode) AS clientCode, clientName FROM cargo_header WHERE (status='20' OR status='30')") or die(mysqli_error($conn));
			  
			  while($rowc = mysqli_fetch_array($selectClient)){
				  echo '<option value="'.$rowc['clientCode'].'"';
				  	if($client==$rowc['clientCode']){echo ' selected';}
				  echo ' >'.$rowc['clientCode'].' '.$rowc['clientName'].'</option>';
			  }
			  
			  echo '
			  </select>	
			  
				<div class="checkbox" style="display: inline-block;">
					<label><input type="checkbox" id="groupProduct" onchange="groupProduct()" '; if($groupProduct=='true'){echo ' checked';} echo '> grupēt pa precēm</label>
				</div>	
				
				<div class="checkbox" style="display: inline-block;">
					<label><input type="checkbox" id="issueDetail" onchange="issueDetail()" '; if($issueDetail=='true'){echo ' checked';} echo '> izdošanas detalizācija</label>
				</div>				

			</div>

		<div class="clearfix"></div>			
		';
		
	if($client){$filterClient = ' AND chr.clientCode="'.$client.'"'; $linkClient='client='.$client.'&';}else{$filterClient = null; $linkClient=null;}
	if($issueDetail){$linkIssueDetail ='issueDetail='.$issueDetail.'&';}else{$linkIssueDetail=null;}

	
echo '<div id="results">';		
	$addLink =  $addLinkTo = null;
	if($dFrom){$addLink = 'dFrom='.$dFrom.'&';}
	if($dTo){$addLinkTo = 'dTo='.$dTo.'&';}
	$rec_limit = $a_p_l; 
	$offset=0; $max_pages = 10;  //DEFINĒJAM LIMITUS
	$link_to = $page_file.'?'.$addLink.''.$addLinkTo.''.$linkClient.''.$linkIssueDetail.'page='; //LINKS KAS TIKS ATVĒRTS KAD NOSPIEDĪS UZ LAPAS, RAKSTĪT LĪDZ page 

	$offsetz = ($page - 1) * $rec_limit;	

	if($client){$andClient = " AND chr.clientCode='".$client."' ";}else{$andClient = null;}
	if($dFrom){$andDate = " AND ile.activityDate<='".date('Y-m-d', strtotime($dFrom))."' ";}else{$andDate = null;}
	if($dTo){$andDateTo = " AND ile.activityDate>='".date('Y-m-d', strtotime($dTo))."' ";}else{$andDateTo = null;}
	$between = $between2 = null;
	if($dFrom && $dTo){$between = " AND DATE_FORMAT(ile.activityDate,'%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' ";}
	if($dFrom && $dTo){$between2 = " AND DATE_FORMAT(chr.deliveryDate,'%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' ";}

	if($groupProduct=='true'){$groupProductIt = " clr.productNr, ";}else{$groupProductIt = null;}
	
$inCargo = mysqli_query($conn, "
SELECT STRAIGHT_JOIN

	clr.productUmo, clr.assistantUmo, clr.productNr,

	ni.name1, 
	ni.name2,
	
	SUM(IF( (ile.status!='40') ,clr.amount,0))           AS amount,
	SUM(IF( (ile.status!='40') ,clr.assistant_amount,0)) AS assistant_amount,
	SUM(IF( (ile.status!='40') ,clr.volume,0))           AS volume,
	SUM(IF( (ile.status!='40') ,clr.tare,0))             AS tare, 
	
	SUM(IF( (Date_format(clr.activityDate, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' AND ile.status!='40') ,clr.gross,0)) AS gross,
	SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40') ,ile.gross,0))  AS gross_issued,
	SUM(IF( (Date_format(clr.activityDate, '%Y-%m-%d') < '".date('Y-m-d', strtotime($dFrom))."' AND ile.status!='40') ,clr.gross,0))                                                AS gross_start_in,
	SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') < '".date('Y-m-d', strtotime($dFrom))."' AND ile.status='40') ,ile.gross,0))                                                 AS gross_start_out,
	SUM(IF( (Date_format(clr.activityDate, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($dTo))."' AND ile.status!='40') ,clr.gross,0))                                                 AS gross_end_in,
	SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40') ,ile.gross,0))                                                  AS gross_end_out,
	
	SUM(IF( (Date_format(clr.activityDate, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' AND ile.status!='40') ,clr.net,0))   AS neto,
	SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40') ,ile.net,0))    AS neto_issued,
	SUM(IF( (Date_format(clr.activityDate, '%Y-%m-%d') < '".date('Y-m-d', strtotime($dFrom))."' AND ile.status!='40') ,clr.net,0))                                                  AS neto_start_in,
	SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') < '".date('Y-m-d', strtotime($dFrom))."' AND ile.status='40') ,ile.net,0))                                                   AS neto_start_out,
	SUM(IF( (Date_format(clr.activityDate, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($dTo))."' AND ile.status!='40') ,clr.net,0))                                                   AS neto_end_in,
	SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40') ,ile.net,0))                                                    AS neto_end_out,
		
	SUM(IF( (ile.status!='40') ,clr.net,0))          AS net,
	SUM(IF( (ile.status!='40') ,clr.document_net,0)) AS document_net,
	SUM(IF( (ile.status!='40') ,clr.delta_net,0))    AS delta_net,

	SUM(IF( (Date_format(clr.activityDate, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' AND ile.status!='40') ,clr.cubicMeters,0)) AS cubicMeters,
	SUM(IF( (Date_format(clr.activityDate, '%Y-%m-%d') < '".date('Y-m-d', strtotime($dFrom))."' AND ile.status!='40') ,clr.cubicMeters,0))                                                AS cubicMeters_start_in,
	SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') < '".date('Y-m-d', strtotime($dFrom))."' AND ile.status='40') ,ile.cubicMeters,0))                                                 AS cubicMeters_start_out,
	SUM(IF( (Date_format(clr.activityDate, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($dTo))."' AND ile.status!='40') ,clr.cubicMeters,0))                                                 AS cubicMeters_end_in,
	SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40') ,ile.cubicMeters,0))                                                  AS cubicMeters_end_out,
	SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40') ,ile.cubicMeters,0))  AS cubicMeters_issued,	

	SUM(IF( (Date_format(clr.activityDate, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' AND ile.status!='40') ,clr.place_count,0)) AS place_count,	
	SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40') ,ile.place_count,0))  AS place_count_issued,	
	SUM(IF( (Date_format(clr.activityDate, '%Y-%m-%d') < '".date('Y-m-d', strtotime($dFrom))."' AND ile.status!='40') ,clr.place_count,0))                                                AS place_count_start_in,	
	SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') < '".date('Y-m-d', strtotime($dFrom))."' AND ile.status='40') ,ile.place_count,0))                                                 AS place_count_start_out,
	SUM(IF( (Date_format(clr.activityDate, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($dTo))."' AND ile.status!='40') ,clr.place_count,0))                                                 AS place_count_end_in,
	SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40') ,ile.place_count,0))                                                  AS place_count_end_out
	
	
FROM cargo_line_receive AS clr 

LEFT JOIN cargo_header_receive AS chr 
ON clr.docNr=chr.docNr	

LEFT JOIN n_items AS ni 
ON clr.productNr=ni.code

JOIN item_ledger_entry AS ile
	ON clr.docNr=ile.docNr 
	AND clr.serialNo=ile.serialNo 
	AND clr.productNr=ile.productNr 
	AND (ile.cargoLine=clr.id OR ile.orgLine=clr.id)
	AND ((clr.status='20' OR clr.status='30') && clr.status!='40')
	AND Date_format(clr.activityDate, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($dTo))."'

WHERE clr.id ".$andClient." ".$ssearch."

GROUP  BY ".$groupProductIt." clr.productUmo


HAVING 
(
	(place_count_start_in-place_count_start_out) > 0 OR
	(gross_start_in-gross_start_out) > 0 OR
	(neto_start_in-neto_start_out) > 0 OR
	(cubicMeters_start_in-cubicMeters_start_out) > 0 OR
	(place_count) > 0 OR
	(gross) > 0 OR
	(neto) > 0 OR
	(cubicMeters) > 0 OR
	(place_count_issued) > 0 OR
	(gross_issued) > 0 OR
	(neto_issued) > 0 OR
	(cubicMeters_issued) > 0 OR
	(place_count_end_in-place_count_end_out) > 0 OR
	(gross_end_in-gross_end_out) > 0 OR
	(neto_end_in-neto_end_out) > 0 OR
	(cubicMeters_end_in-cubicMeters_end_out) > 0
	
)


ORDER  BY clr.productNr DESC
");


	
	

	
	
	
	
	
	
	
	
	
	
	if(mysqli_num_rows($inCargo)>0){
	
	
		echo '
		<div class="table-responsive">
		<table class="table table-hover table-responsive" border="1">
		<thead>
		<tr>
		
		

						<tr>';
						

							echo '<th rowspan="2" style="vertical-align : middle;text-align:center;">mērvienība</th>';

							if($groupProduct=='true'){
								echo '<th rowspan="2" style="vertical-align : middle;text-align:center;">prece</th>';
							}							
							
							echo '
							<th colspan="4" bgcolor="silver">atlikums uz per. sākumu</th>
							<th colspan="4">saņemts per.</th>
							<th colspan="4">izdots per.</th>
							<th colspan="4" bgcolor="silver">atlikums uz per. beigām</th>
						</tr>
						
						<tr>
							<th bgcolor="silver">vietu sk.</th>
							<th bgcolor="silver">bruto svars (kg)</th>
							<th bgcolor="silver">neto svars (kg)</th>
							<th bgcolor="silver">apjoms (m3)</th>
							<th>vietu sk.</th>
							<th>bruto svars (kg)</th>
							<th>neto svars (kg)</th>
							<th>apjoms (m3)</th>
							<th>vietu sk.</th>
							<th>bruto svars (kg)</th>
							<th>neto svars (kg)</th>
							<th>apjoms (m3)</th>
							<th bgcolor="silver">vietu sk.</th>
							<th bgcolor="silver">bruto svars (kg)</th>
							<th bgcolor="silver">neto svars (kg)</th>
							<th bgcolor="silver">apjoms (m3)</th>							
						</tr>		
		</tr>
		
		</thead>
		<tbody>
		';		
	
		while($icRow = mysqli_fetch_array($inCargo)){				
	
			
			//mērvienība
			$productUmo = $icRow['productUmo'];
			
			//atlikums uz per. sākumu
			$place_count_start = floatval($icRow['place_count_start_in']) - floatval($icRow['place_count_start_out']);
			$gross_start = floatval($icRow['gross_start_in']) - floatval($icRow['gross_start_out']);
			$neto_start = floatval($icRow['neto_start_in']) - floatval($icRow['neto_start_out']);
			$cubicMeters_start = floatval($icRow['cubicMeters_start_in']) - floatval($icRow['cubicMeters_start_out']);
			
			//saņemts per.
			$place_count = floatval($icRow['place_count']);
			$gross = floatval($icRow['gross']);
			$neto = floatval($icRow['neto']);
			$cubicMeters = floatval($icRow['cubicMeters']);
			
			//izdots per.
			$place_count_issued = floatval($icRow['place_count_issued']);
			$gross_issued = floatval($icRow['gross_issued']);
			$neto_issued = floatval($icRow['neto_issued']);
			$cubicMeters_issued = floatval($icRow['cubicMeters_issued']);
			
			//atlikums uz per. beigām
			$place_count_end = floatval($icRow['place_count_end_in']) - floatval($icRow['place_count_end_out']);
			$gross_end = floatval($icRow['gross_end_in']) - floatval($icRow['gross_end_out']);
			$neto_end = floatval($icRow['neto_end_in']) - floatval($icRow['neto_end_out']);
			$cubicMeters_end = floatval($icRow['cubicMeters_end_in']) - floatval($icRow['cubicMeters_end_out']);
			
			
		

				echo '
			<tr>';
				
				//mērvienība
				echo '<td nowrap>'.$productUmo.'</td>';

				
				//prece
				if($groupProduct=='true'){
					
					echo '<td nowrap>'.$icRow['productNr'].' - '.$icRow['name1'].' '.$icRow['name2'].'</td>';
					
				}
				
				//atlikums uz per. sākumu
				echo '<td nowrap bgcolor="silver">'.$place_count_start.'</td>';
				echo '<td nowrap bgcolor="silver">'.$gross_start.'</td>';
				echo '<td nowrap bgcolor="silver">'.$neto_start.'</td>';
				echo '<td nowrap bgcolor="silver">'.$cubicMeters_start.'</td>';
				
				//saņemts per.
				echo '<td nowrap>'.$place_count.'</td>';
				echo '<td nowrap>'.$gross.'</td>';
				echo '<td nowrap>'.$neto.'</td>';
				echo '<td nowrap>'.$cubicMeters.'</td>';
				
				//izdots per.
				echo '<td nowrap>'.$place_count_issued.'</td>';
				echo '<td nowrap>'.$gross_issued.'</td>';
				echo '<td nowrap>'.$neto_issued.'</td>';
				echo '<td nowrap>'.$cubicMeters_issued.'</td>';
				
				//atlikums uz per. beigām
				echo '<td nowrap bgcolor="silver">'.$place_count_end.'</td>';
				echo '<td nowrap bgcolor="silver">'.$gross_end.'</td>';
				echo '<td nowrap bgcolor="silver">'.$neto_end.'</td>';
				echo '<td nowrap bgcolor="silver">'.$cubicMeters_end.'</td>';
				

				
				echo '
				
			</tr>
	
			';
	
		}
		echo '
		</tbody>
		</table>
		</div>				
		';
	
	
	}
	
	$countIssues = $countIssuesOr = $groupIt = null;
	if($issueDetail=='true'){


		$countIssuesOr = "
			(place_count_issued_tot) > 0 OR
			(gross_issued_tot) > 0 OR
			(neto_issued_tot) > 0 OR
			(cubicMeters_issued_tot) > 0 OR
			(place_count_end_out_tot) > 0 OR
			(gross_end_out_tot) > 0 OR
			(neto_end_out_tot) > 0 OR
			(cubicMeters_end_out_tot) > 0 OR

			(place_count_start_out_tot) > 0 OR
			(gross_start_out_tot) > 0 OR
			(neto_start_out_tot) > 0 OR
			(cubicMeters_start_out_tot	) > 0 OR		
		";

		$countIssues = "
						  
			
			
			SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40') ,ile.place_count,0)) AS place_count_end_out_tot,
			SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40') ,ile.gross,0)) AS gross_end_out_tot,
			SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40') ,ile.net,0)) AS neto_end_out_tot,
			SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40') ,ile.cubicMeters,0)) AS cubicMeters_end_out_tot,
			
			SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') < '".date('Y-m-d', strtotime($dFrom))."' AND ile.status='40') ,ile.place_count,0)) AS place_count_start_out_tot,
			SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') < '".date('Y-m-d', strtotime($dFrom))."' AND ile.status='40') ,ile.gross,0)) AS gross_start_out_tot,
			SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') < '".date('Y-m-d', strtotime($dFrom))."' AND ile.status='40') ,ile.net,0)) AS neto_start_out_tot,
			SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') < '".date('Y-m-d', strtotime($dFrom))."' AND ile.status='40') ,ile.cubicMeters,0)) AS cubicMeters_start_out_tot,
			
			SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40' AND ile.issuance_id=i.issuance_id) ,ile.place_count,0)) AS place_count_issued_tot,
			SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40' AND ile.issuance_id=i.issuance_id) ,ile.gross,0)) AS gross_issued_tot,
			SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40' AND ile.issuance_id=i.issuance_id) ,ile.net,0)) AS neto_issued_tot,
			SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40' AND ile.issuance_id=i.issuance_id) ,ile.cubicMeters,0)) AS cubicMeters_issued_tot,

		(
	
			SELECT COUNT(DISTINCT(ilei.issuance_id)) FROM item_ledger_entry AS ilei
			WHERE clr.docNr=ilei.docNr 
			AND clr.serialNo=ilei.serialNo
			
			AND clr.productNr=ilei.productNr
			
			
			
			
				AND (ilei.cargoLine=clr.id OR ilei.orgLine=clr.id)
				AND ilei.id NOT IN (SELECT MIN(zz.id) FROM item_ledger_entry AS zz WHERE zz.docNr=ilei.docNr AND zz.serialNo=ilei.serialNo AND zz.productNr=ilei.productNr)	
			
				
			
			
			
			AND Date_format(ilei.activityDate, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."'

			
			AND ilei.status='40'
			
				  
		  
		  ) AS countIssues,	i.issuance_id,

				  
		";
		
		$lefJoin = "
		LEFT JOIN item_ledger_entry AS i 
		ON clr.docNr=i.docNr AND i.status=40  AND (i.id=clr.id OR i.orgLine=clr.id)	
		
		";		

		$groupIt = " , (case when countIssues > 0 then i.issuance_id end)";	
		
	}



	$query = "
	

		SELECT STRAIGHT_JOIN
		
		clr.id, clr.activityDate, clr.productNr, clr.thisTransport, clr.serialNo, clr.docNr, 
		chr.clientCode, 
		chr.clientName, 
		chr.acceptance_act_no,
		
		ni.name1, 
		ni.name2,		

		".$countIssues."

		SUM(IF( (Date_format(clr.activityDate, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' AND ile.status!='40') ,clr.place_count,0)) AS place_count,
		SUM(IF( (Date_format(clr.activityDate, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' AND ile.status!='40') ,clr.gross,0))       AS gross,
		SUM(IF( (Date_format(clr.activityDate, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' AND ile.status!='40') ,clr.net,0))         AS neto,
		SUM(IF( (Date_format(clr.activityDate, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' AND ile.status!='40') ,clr.cubicMeters,0)) AS cubicMeters,
		
		
		SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40') ,ile.place_count,0)) AS place_count_issued,
		SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40') ,ile.gross,0)) AS gross_issued,
		SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40') ,ile.net,0)) AS neto_issued,
		SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40') ,ile.cubicMeters,0)) AS cubicMeters_issued,
		
		
		SUM(IF( (Date_format(clr.activityDate, '%Y-%m-%d') < '".date('Y-m-d', strtotime($dFrom))."' AND ile.status!='40') ,clr.place_count,0))                        AS place_count_start_in,	
		SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') < '".date('Y-m-d', strtotime($dFrom))."' AND ile.status='40') ,ile.place_count,0))                         AS place_count_start_out,	
		SUM(IF( (Date_format(clr.activityDate, '%Y-%m-%d') < '".date('Y-m-d', strtotime($dFrom))."' AND ile.status!='40') ,clr.gross,0))                              AS gross_start_in,
		SUM(IF( (Date_format(clr.activityDate, '%Y-%m-%d') < '".date('Y-m-d', strtotime($dFrom))."' AND ile.status!='40') ,clr.net,0))                                AS neto_start_in,
		
		SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') < '".date('Y-m-d', strtotime($dFrom))."' AND ile.status='40') ,ile.gross,0))                               AS gross_start_out,
		SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') < '".date('Y-m-d', strtotime($dFrom))."' AND ile.status='40') ,ile.net,0))                                 AS neto_start_out,
		
		SUM(IF( (Date_format(clr.activityDate, '%Y-%m-%d') < '".date('Y-m-d', strtotime($dFrom))."' AND ile.status!='40') ,clr.cubicMeters,0))                        AS cubicMeters_start_in,
		
		SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') < '".date('Y-m-d', strtotime($dFrom))."' AND ile.status='40') ,ile.cubicMeters,0))                         AS cubicMeters_start_out,
		
		SUM(IF( (Date_format(clr.activityDate, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($dTo))."' AND ile.status!='40') ,clr.place_count,0))                         AS place_count_end_in,
		
		SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40') ,ile.place_count,0))                          AS place_count_end_out,
		
		SUM(IF( (Date_format(clr.activityDate, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($dTo))."' AND ile.status!='40') ,clr.gross,0))                               AS gross_end_in,
		SUM(IF( (Date_format(clr.activityDate, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($dTo))."' AND ile.status!='40') ,clr.net,0))                                 AS neto_end_in,
		
		SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40') ,ile.gross,0))                                AS gross_end_out,
		SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40') ,ile.net,0))                                  AS neto_end_out,	
		
		SUM(IF( (Date_format(clr.activityDate, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($dTo))."' AND ile.status!='40') ,clr.cubicMeters,0))                         AS cubicMeters_end_in,
		
		SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40') ,ile.cubicMeters,0))                          AS cubicMeters_end_out

		FROM cargo_line_receive AS clr 

		LEFT JOIN cargo_header_receive AS chr 
		ON clr.docNr=chr.docNr	

		LEFT JOIN n_items AS ni 
		ON clr.productNr=ni.code
		
		LEFT JOIN item_ledger_entry AS i 
		ON clr.docNr=i.docNr AND i.status=40  AND (i.id=clr.id OR i.orgLine=clr.id)	

		JOIN item_ledger_entry AS ile
			ON clr.docNr=ile.docNr 
			AND clr.serialNo=ile.serialNo 
			AND clr.productNr=ile.productNr 
			AND (ile.cargoLine=clr.id OR ile.orgLine=clr.id)
			AND ((clr.status='20' OR clr.status='30') && clr.status!='40')
			AND Date_format(clr.activityDate, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($dTo))."'

		WHERE clr.id ".$ssearch." ".$filterClient."

		AND (SELECT COUNT(id.id) FROM issuance_doc AS id WHERE id.issuance_id=i.issuance_id AND Date_format(id.issueDate, '%Y-%m-%d') NOT BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' ) < 1

		GROUP BY clr.id, clr.docNr, clr.batchNo, clr.serialNo, clr.productNr ".$groupIt."
		
HAVING 
(

	".$countIssuesOr."
	(place_count_start_in-place_count_start_out) > 0 OR
	(gross_start_in-gross_start_out) > 0 OR
	(neto_start_in-neto_start_out) > 0 OR
	(cubicMeters_start_in-cubicMeters_start_out) > 0 OR
	(place_count) > 0 OR
	(gross) > 0 OR
	(neto) > 0 OR
	(cubicMeters) > 0 OR
	(place_count_issued) > 0 OR
	(gross_issued) > 0 OR
	(neto_issued) > 0 OR
	(cubicMeters_issued) > 0 OR
	(place_count_end_in-place_count_end_out) > 0 OR
	(gross_end_in-gross_end_out) > 0 OR
	(neto_end_in-neto_end_out) > 0 OR
	(cubicMeters_end_in-cubicMeters_end_out) > 0 
	
)		
		
		ORDER  BY clr.activityDate DESC, clr.id DESC
	
	";		

echo '<br><br>'.$issueDetail.'<br><br>';

if($myid==29){
echo "<br><br>
SELECT STRAIGHT_JOIN<br>
<br>
clr.id, clr.activityDate, clr.productNr, clr.thisTransport, clr.serialNo, clr.docNr, <br>
chr.clientCode, <br>
chr.clientName, <br>
chr.acceptance_act_no,<br>
<br>
ni.name1,<br> 
ni.name2,	<br>	
<br>
".$countIssues."<br>
<br>
SUM(IF( (Date_format(clr.activityDate, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' AND ile.status!='40') ,clr.place_count,0)) AS place_count,<br>
SUM(IF( (Date_format(clr.activityDate, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' AND ile.status!='40') ,clr.gross,0))       AS gross,<br>
SUM(IF( (Date_format(clr.activityDate, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' AND ile.status!='40') ,clr.net,0))         AS neto,<br>
SUM(IF( (Date_format(clr.activityDate, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' AND ile.status!='40') ,clr.cubicMeters,0)) AS cubicMeters,<br>
<br>
<br>
SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40') ,ile.place_count,0)) AS place_count_issued,<br>
SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40') ,ile.gross,0)) AS gross_issued,<br>
SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40') ,ile.net,0)) AS neto_issued,<br>
SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40') ,ile.cubicMeters,0)) AS cubicMeters_issued,<br>
<br>
<br>
SUM(IF( (Date_format(clr.activityDate, '%Y-%m-%d') < '".date('Y-m-d', strtotime($dFrom))."' AND ile.status!='40') ,clr.place_count,0))                        AS place_count_start_in,<br>	
SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') < '".date('Y-m-d', strtotime($dFrom))."' AND ile.status='40') ,ile.place_count,0))                         AS place_count_start_out,	<br>
SUM(IF( (Date_format(clr.activityDate, '%Y-%m-%d') < '".date('Y-m-d', strtotime($dFrom))."' AND ile.status!='40') ,clr.gross,0))                              AS gross_start_in,<br>
SUM(IF( (Date_format(clr.activityDate, '%Y-%m-%d') < '".date('Y-m-d', strtotime($dFrom))."' AND ile.status!='40') ,clr.net,0))                                AS neto_start_in,<br>
<br>
SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') < '".date('Y-m-d', strtotime($dFrom))."' AND ile.status='40') ,ile.gross,0))                               AS gross_start_out,<br>
SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') < '".date('Y-m-d', strtotime($dFrom))."' AND ile.status='40') ,ile.net,0))                                 AS neto_start_out,<br>
<br>
SUM(IF( (Date_format(clr.activityDate, '%Y-%m-%d') < '".date('Y-m-d', strtotime($dFrom))."' AND ile.status!='40') ,clr.cubicMeters,0))                        AS cubicMeters_start_in,<br>
<br>
SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') < '".date('Y-m-d', strtotime($dFrom))."' AND ile.status='40') ,ile.cubicMeters,0))                         AS cubicMeters_start_out,<br>
<br>
SUM(IF( (Date_format(clr.activityDate, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($dTo))."' AND ile.status!='40') ,clr.place_count,0))                         AS place_count_end_in,<br>
<br>
SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40') ,ile.place_count,0))                          AS place_count_end_out,<br>
<br>
SUM(IF( (Date_format(clr.activityDate, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($dTo))."' AND ile.status!='40') ,clr.gross,0))                               AS gross_end_in,<br>
SUM(IF( (Date_format(clr.activityDate, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($dTo))."' AND ile.status!='40') ,clr.net,0))                                 AS neto_end_in,<br>
<br>
SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40') ,ile.gross,0))                                AS gross_end_out,<br>
SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40') ,ile.net,0))                                  AS neto_end_out,	<br>
<br>
SUM(IF( (Date_format(clr.activityDate, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($dTo))."' AND ile.status!='40') ,clr.cubicMeters,0))                         AS cubicMeters_end_in,<br>
<br>
SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40') ,ile.cubicMeters,0))                          AS cubicMeters_end_out<br>
<br>
FROM cargo_line_receive AS clr <br>
<br>
LEFT JOIN cargo_header_receive AS chr <br>
ON clr.docNr=chr.docNr	<br>
<br>
LEFT JOIN n_items AS ni <br>
ON clr.productNr=ni.code<br>
<br>
LEFT JOIN item_ledger_entry AS i <br>
ON clr.docNr=i.docNr AND i.status=40  AND (i.id=clr.id OR i.orgLine=clr.id)	<br>
<br>
JOIN item_ledger_entry AS ile<br>
	ON clr.docNr=ile.docNr <br>
	AND clr.serialNo=ile.serialNo <br>
	AND clr.productNr=ile.productNr <br>
	AND (ile.cargoLine=clr.id OR ile.orgLine=clr.id)<br>
	AND ((clr.status='20' OR clr.status='30') && clr.status!='40')<br>
	AND Date_format(clr.activityDate, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($dTo))."'<br>
	<br>
WHERE clr.id ".$ssearch." ".$filterClient."<br>
<br>
AND (SELECT COUNT(id.id) FROM issuance_doc AS id WHERE id.issuance_id=i.issuance_id AND Date_format(id.issueDate, '%Y-%m-%d') NOT BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' ) < 1<br>
<br>
GROUP BY clr.id, clr.docNr, clr.batchNo, clr.serialNo, clr.productNr ".$groupIt."<br>
<br>
HAVING <br>
(<br>
<br>
".$countIssuesOr."<br>
(place_count_start_in-place_count_start_out) > 0 OR<br>
(gross_start_in-gross_start_out) > 0 OR<br>
(neto_start_in-neto_start_out) > 0 OR<br>
(cubicMeters_start_in-cubicMeters_start_out) > 0 OR<br>
(place_count) > 0 OR<br>
(gross) > 0 OR<br>
(neto) > 0 OR<br>
(cubicMeters) > 0 OR<br>
(place_count_issued) > 0 OR<br>
(gross_issued) > 0 OR<br>
(neto_issued) > 0 OR<br>
(cubicMeters_issued) > 0 OR<br>
(place_count_end_in-place_count_end_out) > 0 OR<br>
(gross_end_in-gross_end_out) > 0 OR<br>
(neto_end_in-neto_end_out) > 0 OR<br>
(cubicMeters_end_in-cubicMeters_end_out) > 0 <br>
<br>
)		<br>
<br>
ORDER  BY clr.activityDate DESC, clr.id DESC<br>
";
}
	   
		//VAJAG NOŅEMT countIssues gross_issued DESC, pēc IZDOŠANAS DATU PIELIKŠANAS <<< UZMANĪBU >>>>

	list($page_menu, $result_all, $resultGL7) = pageing_menu($offset, $rec_limit, $max_pages, $link_to, $page, $conn, $query);  //TIEK IEGŪTS ARRAY KO UZŅEM AR LIST 

	echo $page_menu;   //IZVADA TABULU AR LAPĀM			
	
	$count_GL7 = mysqli_num_rows($resultGL7);  //IEGŪST SKAITU 					
		if ($count_GL7!=0){

			echo '	<div class="table-responsive"><table class="table table-hover table-responsive" border="1"><thead><tr>
						
						<tr>
							<th rowspan="2" style="vertical-align : middle;text-align:center;">piegādes dat.</th>
							<th rowspan="2" style="vertical-align : middle;text-align:center;">preces kods - nosaukums</th>
							<th rowspan="2" style="vertical-align : middle;text-align:center;">klienta kods - nosaukums</th>
							<th rowspan="2" style="vertical-align : middle;text-align:center;">pieņemšanas akta nr.</th>
							<th rowspan="2" style="vertical-align : middle;text-align:center;">transporta nr.</th>
							<th rowspan="2" style="vertical-align : middle;text-align:center;">seriālais nr.</th>
							
							<th colspan="4" bgcolor="silver" nowrap>atlikums uz perioda sākumu</th>
							<th colspan="4">saņemts periodā</th>
							<th colspan="4">izdots periodā</th>
							<th colspan="4" bgcolor="silver">atlikums uz perioda beigām</th>';

							if($issueDetail=='true'){
								echo '<th colspan="7">izdošana</th>';
							}

							echo '
						</tr>
						
						<tr>
							<th bgcolor="silver">vietu skaits</th>
							<th bgcolor="silver">bruto svars (kg)</th>
							<th bgcolor="silver">neto svars (kg)</th>
							<th bgcolor="silver">apjoms (m3)</th>
							<th>vietu skaits</th>
							<th>bruto svars (kg)</th>
							<th>neto svars (kg)</th>
							<th>apjoms (m3)</th>
							<th>vietu skaits</th>
							<th>bruto svars (kg)</th>
							<th>neto svars (kg)</th>
							<th>apjoms (m3)</th>
							<th bgcolor="silver">vietu skaits</th>
							<th bgcolor="silver">bruto svars (kg)</th>
							<th bgcolor="silver">neto svars (kg)</th>
							<th bgcolor="silver">apjoms (m3)</th>';
							
							if($issueDetail=='true'){
								echo '
								<th>izdošanas<br>datums</th>
								<th>izdošanas<br>akta nr.</th>
								<th>transporta<br>veids</th>
								<th nowrap>izvests ar (vagona <br>vai a/m nr.vai<br>kuģa nosaukums)</th>
								
								<th>deklarācijas<br>nr.</th>
								<th>saņēmējs</th>';
							}

							echo '
						</tr>';
						
					echo '	
					</tr></thead><tbody>';
					
			echo '
			<style>
				td.datacelltwo {
					background-color: white; color: black;
				}
				td.datacellone {
					background-color: #dbccd3; color: black;
				}
			</style>
			';		
					
			$color=1; $lastRow=null; $showed=null; $lastRowi=null; $showedi=null; $datacell='datacellone'; $forCountIssues=0;
			while($row = mysqli_fetch_array($resultGL7)){
			

				
				
				$place_count = floatval($row['place_count']);
				$gross = floatval($row['gross']);
				$neto = floatval($row['neto']);
				$cubicMeters = floatval($row['cubicMeters']);
				
				if($row['countIssues']>1){
					$place_count_issued = floatval($row['place_count_issued_tot']);
					$gross_issued = floatval($row['gross_issued_tot']);
					$neto_issued = floatval($row['neto_issued_tot']);
					$cubicMeters_issued = floatval($row['cubicMeters_issued_tot']);
				}else{
					$place_count_issued = floatval($row['place_count_issued']);
					$gross_issued = floatval($row['gross_issued']);
					$neto_issued = floatval($row['neto_issued']);
					$cubicMeters_issued = floatval($row['cubicMeters_issued']);
				}
			
				
				if($row['countIssues']>1){
					if($lastRowi!=$row['id']){
						$remember_place_count_end_out = 0;
						$remember_gross_end_out = 0;
						$remember_neto_end_out = 0;
						$remember_cubicMeters_end_out = 0;

						$remember_place_count_start = 0;
						$remember_gross_start = 0;
						$remember_neto_start = 0;
						$remember_cubicMeters_start = 0;						
					}
					$remember_place_count_end_out += floatval($row['place_count_end_out_tot']);
					$remember_gross_end_out += floatval($row['gross_end_out_tot']);
					$remember_neto_end_out += floatval($row['neto_end_out_tot']);
					$remember_cubicMeters_end_out += floatval($row['cubicMeters_end_out_tot']);

					$remember_place_count_start += floatval($row['place_count_start_out_tot']);
					$remember_gross_start += floatval($row['gross_start_out_tot']);
					$remember_neto_start += floatval($row['neto_start_out_tot']);
					$remember_cubicMeters_start += floatval($row['cubicMeters_start_out_tot']);
					
					$lastRowi=$row['id'];
				}else{
					$remember_place_count_end_out = 0;
					$remember_gross_end_out = 0;
					$remember_neto_end_out = 0;
					$remember_cubicMeters_end_out = 0;
					
					$remember_place_count_start = 0;
					$remember_gross_start = 0;
					$remember_neto_start = 0;
					$remember_cubicMeters_start = 0;					
					$lastRow=null;
				}			
			
				if($row['countIssues']>1 && $lastRowi==$row['id']){				
					$place_count_end = floatval($row['place_count_end_in']) - $remember_place_count_end_out;
					$gross_end = floatval($row['gross_end_in']) - $remember_gross_end_out;
					$neto_end = floatval($row['neto_end_in']) - $remember_neto_end_out;
					$cubicMeters_end = floatval($row['cubicMeters_end_in']) - $remember_cubicMeters_end_out;	


					$place_count_start = floatval($row['place_count_start_in']) - $remember_place_count_start;
					$gross_start = floatval($row['gross_start_in']) - $remember_gross_start;
					$neto_start = floatval($row['neto_start_in']) - $remember_neto_start;
					$cubicMeters_start = floatval($row['cubicMeters_start_in']) - $remember_cubicMeters_start;
					
				}else{
					$place_count_end = floatval($row['place_count_end_in']) - floatval($row['place_count_end_out']);
					$gross_end = floatval($row['gross_end_in']) - floatval($row['gross_end_out']);
					$neto_end = floatval($row['neto_end_in']) - floatval($row['neto_end_out']);
					$cubicMeters_end = floatval($row['cubicMeters_end_in']) - floatval($row['cubicMeters_end_out']);


					$place_count_start = floatval($row['place_count_start_in']) - floatval($row['place_count_start_out']);
					$gross_start = floatval($row['gross_start_in']) - floatval($row['gross_start_out']);
					$neto_start = floatval($row['neto_start_in']) - floatval($row['neto_start_out']);
					$cubicMeters_start = floatval($row['cubicMeters_start_in']) - floatval($row['cubicMeters_start_out']);
					
				}

			
			
			

				echo '	<tr>';
					
				$rowspan=null;
				if($issueDetail=='true'){
					
				}
				if($row['countIssues']==1 || $row['countIssues']==0){	
					
					
								if ($color % 2 == 0){
									$datacell='datacelltwo';				
								}else{
									$datacell='datacellone';
								}					
					
					
								echo '
								<td '.$rowspan.' nowrap class="'.$datacell.'">'; 
								
								if($row['activityDate']!='0000-00-00 00:00:00'){echo date('d.m.Y', strtotime($row['activityDate']));}
								echo '</td>';
								echo '<td nowrap '.$rowspan.' class="'.$datacell.'">'.$row['productNr'].' - '.$row['name1'].' '.$row['name2'].'</td>';

								echo '<td nowrap '.$rowspan.' class="'.$datacell.'">'.$row['clientCode'].' - '.$row['clientName'].'</td>';
								echo '<td nowrap '.$rowspan.' class="'.$datacell.'">'.$row['acceptance_act_no'].'</td>';
								echo '
								<td nowrap '.$rowspan.' class="'.$datacell.'">'.$row['thisTransport'].'</td>
								<td '.$rowspan.' class="'.$datacell.'">'.$row['serialNo'].'</td>';
								
								
								echo '<td bgcolor="silver" '.$rowspan.'>'.$place_count_start.'</td>';
								echo '<td bgcolor="silver" '.$rowspan.'>'.$gross_start.'</td>';
								echo '<td bgcolor="silver" '.$rowspan.'>'.$neto_start.'</td>';
								echo '<td bgcolor="silver" '.$rowspan.'>'.$cubicMeters_start.'</td>';
								echo '<td '.$rowspan.' class="'.$datacell.'">'.$place_count.'</td>';
								echo '<td '.$rowspan.' class="'.$datacell.'">'.$gross.'</td>';
								echo '<td '.$rowspan.' class="'.$datacell.'">'.$neto.'</td>';
								echo '<td '.$rowspan.' class="'.$datacell.'">'.$cubicMeters.'</td>';
								echo '<td '.$rowspan.' class="'.$datacell.'">'.$place_count_issued.'</td>';
								echo '<td '.$rowspan.' class="'.$datacell.'">'.$gross_issued.'</td>';
								echo '<td '.$rowspan.' class="'.$datacell.'">'.$neto_issued.'</td>';
								echo '<td '.$rowspan.' class="'.$datacell.'">'.$cubicMeters_issued.'</td>';
								echo '<td bgcolor="silver" '.$rowspan.'>'.$place_count_end.'</td>';
								echo '<td bgcolor="silver" '.$rowspan.'>'.$gross_end.'</td>';
								echo '<td bgcolor="silver" '.$rowspan.'>'.$neto_end.'</td>';
								echo '<td bgcolor="silver" '.$rowspan.'>'.$cubicMeters_end.'</td>';											
								
								if($issueDetail=='true'){
									
									$get_issues = mysqli_query($conn, "
										SELECT e.activityDate, i.issuance_act_no, i.transport, i.transport_name, i.thisTransport, i.declaration_type_no, i.receiverCode, i.receiverName2, r.name, r.country
										FROM item_ledger_entry AS e

										LEFT JOIN issuance_doc AS i 
										ON e.issuance_id=i.issuance_id

										LEFT JOIN receivers AS r
										ON i.receiverCode=r.Code

										WHERE e.docNr='".$row['docNr']."' 
										AND e.serialNo='".$row['serialNo']."'
				
										AND e.productNr='".$row['productNr']."'								
										AND e.issuance_id!='' AND i.issuance_id='".$row['issuance_id']."' 
										AND e.status='40' 
										AND Date_format(e.activityDate, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."'
									");
									
									if(mysqli_num_rows($get_issues)>0){
										$rowi = mysqli_fetch_array($get_issues);
										
											echo '<td nowrap class="'.$datacell.'">';
												
											echo date('d.m.Y', strtotime($rowi['activityDate'])).'</td>';
											echo '<td nowrap class="'.$datacell.'">'.$rowi['issuance_act_no'].'</td>';
											echo '<td nowrap class="'.$datacell.'">'.$rowi['transport'].'</td>';
											echo '<td nowrap class="'.$datacell.'">';
											
												//kuģis iekraušanai   - transport_name
												//izs. transporta nr. - thisTransport
												if($rowi['transport']=='kuģis'){

													if($rowi['transport_name']){
														echo $rowi['transport_name'];
													}else{
														echo $rowi['thisTransport'];
													}
													
												}else{
													echo $rowi['thisTransport'];
												}

											echo '</td>';
											
											echo '<td nowrap class="'.$datacell.'">'.$rowi['declaration_type_no'].'</td>';
											
											$receiverName = $rowi['name'];
											if($rowi['receiverName2']){ $receiverName = $rowi['receiverName2']; }
											echo '<td nowrap class="'.$datacell.'">'.$rowi['receiverCode'].' - '.$receiverName.' ('.$rowi['country'].')</td>';
										
									}else{
											echo '<td nowrap class="'.$datacell.'"></td>';
											echo '<td nowrap class="'.$datacell.'"></td>';
											echo '<td nowrap class="'.$datacell.'"></td>';
											echo '<td nowrap class="'.$datacell.'"></td>';
											echo '<td nowrap class="'.$datacell.'"></td>';
											echo '<td nowrap class="'.$datacell.'"></td>';
									
									}
									
								}

					$color++;		
				}


				if($row['countIssues']>1){	
					
							$atlikums=null;
							if($lastRow!=$row['id']){								
								$lastRow=$row['id'];
								
								if($showed!='y'){
									
									if ($color % 2 == 0){
										$datacell='datacelltwo';				
									}else{
										$datacell='datacellone';
									}									
									
									
									if($issueDetail=='true'){
										$rowspan=' rowspan="'.$row['countIssues'].'" style="vertical-align: middle;"';
									}

									echo '
									<td '.$rowspan.' nowrap class="'.$datacell.'">';

									if($row['activityDate']!='0000-00-00 00:00:00'){echo date('d.m.Y', strtotime($row['activityDate']));}
									echo '</td>';
									echo '<td nowrap '.$rowspan.' class="'.$datacell.'">'.$row['productNr'].' - '.$row['name1'].' '.$row['name2'].'</td>';

									echo '<td nowrap '.$rowspan.' class="'.$datacell.'">'.$row['clientCode'].' - '.$row['clientName'].'</td>';
									echo '<td nowrap '.$rowspan.' class="'.$datacell.'">'.$row['acceptance_act_no'].'</td>';
									echo '
									<td nowrap '.$rowspan.' class="'.$datacell.'">'.$row['thisTransport'].'</td>
									<td '.$rowspan.' class="'.$datacell.'">'.$row['serialNo'].'</td>';
									
									
									echo '<td bgcolor="silver" '.$rowspan.'>'.$place_count_start.'</td>';
									echo '<td bgcolor="silver" '.$rowspan.'>'.$gross_start.'</td>';
									echo '<td bgcolor="silver" '.$rowspan.'>'.$neto_start.'</td>';
									echo '<td bgcolor="silver" '.$rowspan.'>'.$cubicMeters_start.'</td>';
									echo '<td '.$rowspan.' class="'.$datacell.'">'.$place_count.'</td>';
									echo '<td '.$rowspan.' class="'.$datacell.'">'.$gross.'</td>';
									echo '<td '.$rowspan.' class="'.$datacell.'">'.$neto.'</td>';
									echo '<td '.$rowspan.' class="'.$datacell.'">'.$cubicMeters.'</td>';
									
									
									$atlikums .= '<td '.$rowspan.' bgcolor="silver">'.$place_count_end.'</td>';
									$atlikums .= '<td '.$rowspan.' bgcolor="silver">'.$gross_end.'</td>';
									$atlikums .= '<td '.$rowspan.' bgcolor="silver">'.$neto_end.'</td>';
									$atlikums .= '<td '.$rowspan.' bgcolor="silver">'.$cubicMeters_end.'</td>';									
									
									$showed='y';
									$color++;
								}else{
									$showed=null;
								}
								
							}else{
								$showed=null;
							}		
							
								echo '<td  class="'.$datacell.'"> '.$place_count_issued.'</td>';
								echo '<td  class="'.$datacell.'">'.$gross_issued.'</td>';
								echo '<td  class="'.$datacell.'">'.$neto_issued.'</td>';
								echo '<td  class="'.$datacell.'">'.$cubicMeters_issued.'</td>';										
															
								echo $atlikums;
								
								if($issueDetail=='true'){
							
									$get_issues = mysqli_query($conn, "
										SELECT e.activityDate, i.issuance_act_no, i.transport, i.transport_name, i.thisTransport, i.declaration_type_no, i.receiverCode, r.name, r.country
										FROM item_ledger_entry AS e

										LEFT JOIN issuance_doc AS i 
										ON e.issuance_id=i.issuance_id

										LEFT JOIN receivers AS r
										ON i.receiverCode=r.Code

										WHERE e.docNr='".$row['docNr']."' 
										AND e.serialNo='".$row['serialNo']."'
				
										AND e.productNr='".$row['productNr']."'								
										AND e.issuance_id!='' AND i.issuance_id='".$row['issuance_id']."' 
										AND e.status='40' 
										
										AND Date_format(e.activityDate, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."'
										
									");
									
									
									if(mysqli_num_rows($get_issues)>0){
										$rowi = mysqli_fetch_array($get_issues);
										
											echo '<td nowrap class="'.$datacell.'">';
												 
											echo date('d.m.Y', strtotime($rowi['activityDate'])).'</td>';
											echo '<td nowrap class="'.$datacell.'">'.$rowi['issuance_act_no'].'</td>';
											echo '<td nowrap class="'.$datacell.'">'.$rowi['transport'].'</td>';
											echo '<td nowrap class="'.$datacell.'">';
											
												//kuģis iekraušanai   - transport_name
												//izs. transporta nr. - thisTransport
												if($rowi['transport']=='kuģis'){

													if($rowi['transport_name']){
														echo $rowi['transport_name'];
													}else{
														echo $rowi['thisTransport'];
													}
													
												}else{
													echo $rowi['thisTransport'];
												}

											echo '</td>';
											
											echo '<td nowrap class="'.$datacell.'">'.$rowi['declaration_type_no'].'</td>';
											echo '<td nowrap class="'.$datacell.'">'.$rowi['receiverCode'].' - '.$rowi['name'].' ('.$rowi['country'].')</td>';
										
									}else{
											echo '<td nowrap class="'.$datacell.'"></td>';
											echo '<td nowrap class="'.$datacell.'"></td>';
											echo '<td nowrap class="'.$datacell.'"></td>';
											echo '<td nowrap class="'.$datacell.'"></td>';
											echo '<td nowrap class="'.$datacell.'"></td>';
											echo '<td nowrap class="'.$datacell.'"></td>';
													
									}

								}
							
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




if($view=='edit' && $id){

	echo '<div class="page-header" style="margin-top: -5px;">
	  
		<div class="btn-group btn-group-xs" role="group" aria-label="Small button group" style="display:inline-block;"> 
			<a class="btn btn-default active classlist" ><i class="glyphicon glyphicon-list" style="color: #00B5AD" title="pamatdati"></i></a>';
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


		
			$inCargo = mysqli_query($conn, "SELECT SUM(amount) as amount, SUM(volume) as volume, productUmo FROM cargo_line WHERE docNr='".$isDocNr."' GROUP BY productUmo") or die (mysqli_error($conn));

			if(mysqli_num_rows($inCargo)>0){
			
			echo '<p style="display:inline-block;">daudzumi</p>';
			
				echo '
				<div class="table-responsive">
					<table class="table table-hover table-responsive">
						<thead>
							<th>mērvienība</th>
							<th>daudzums</th>
						</thead>
						<tbody>
						';			
			
			while($icRow = mysqli_fetch_array($inCargo)){


				echo '

						
							<tr>
								<td>'.$icRow['productUmo'].'</td>
								<td>'.floatval($icRow['amount']).'</td>
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
	
	
	<div class="table-responsive"><table class="table-responsive"><table class="table table-hover table-responsive">
		<thead> 
			<tr>
				<th>produkta nr</th>
				<th>partijas nr</th>
				<th>noliktava</th>
				<th>piegādes dat.</th>
				<th>transporta nr.</th>
				<th>izdošanas nr.</th>
				<th>daudzums</th>
				<th>mērvienība</th>

				<th>statuss</th>';
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

						<td>';
						if($rowL['status']==40){
							echo $rowL['issuance_id'];
						}else{
							echo '-';
						}
						echo '						
						<td>';
						if($rowL['status']==40){
							echo '-'.floatval($rowL['amount']);
						}else{
							echo floatval($rowL['amount']);
						}
						
						
						echo '<td>'.$rowL['productUmo'].'</td>';
						
						echo '<td>'.returnCargoLineStatus($conn, $rowL['id']);
						
						
					echo '</tr>';
		}

		
	echo '</tbody>
	</table></div>';	
	

}


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
   $('.selectpicker').selectpicker();
});
</script>
<?php include_once("../../datepicker.php"); ?>

