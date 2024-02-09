<?php
error_reporting(E_ALL ^ E_NOTICE);
require('lock.php');

$page_file="received";
$page_folder="received";


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

$dFrom = date('01.m.Y');
$dTo = date('d.m.Y');

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
		$("#pleaseWait").toggle();
        $('#contenthere').load('/pages/<?=$page_folder;?>/<?=$page_file;?>.php<?=$glpage;?>');
    });
})
</script>

<script>
$(document).ready(function(){
    $('.classhistory').click(function(){
		$("#pleaseWait").toggle();
        $('#contenthere').load('/pages/<?=$page_folder;?>/<?=$page_file;?>.php?view=history');
    });
})
</script>



<script>
function newDoc(val) {
	$("#pleaseWait").toggle();
	$('#contenthere').load('/pages/<?=$page_folder;?>/<?=$page_file;?>.php?view=edit&id='+val+'');
}
</script>

<script>
function selClient(val) {
	var val = val.value;
	$("#pleaseWait").toggle();
	$('#contenthere').load('/pages/<?=$page_folder;?>/<?=$page_file;?>.php?client='+val+'&dFrom=<?=$dFrom;?>&dTo=<?=$dTo;?>');
}

function groupProduct() {
	
	$("#pleaseWait").toggle();
	var val = $('#groupProduct').prop('checked');
    var search = $.post("pages/<?=$page_folder;?>/<?=$page_file;?>.php?client=<?=$client;?>&dFrom=<?=$dFrom;?>&dTo=<?=$dTo;?>&groupProduct="+val,function(data){
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
    var search = $.post("pages/<?=$page_folder;?>/<?=$page_file;?>.php?client=<?=$client;?>&dFrom=<?=$dFrom;?>&dTo=<?=$dTo;?>&issueDetail="+val,function(data){
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
	
	var client = '<?=$client;?>';
	var dFrom = '<?=$dFrom;?>';
	var dTo = '<?=$dTo;?>';
	var groupProduct = '<?=$groupProduct;?>';
	
    var search = $.post("/pages/<?=$page_folder;?>/search.php?view=received", {name:value, client:client, dFrom:dFrom, dTo:dTo, groupProduct:groupProduct},function(data){
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

<script>
function dateFilter() {

$("#pleaseWait").toggle();

var dFrom = $('#dFrom').val();
var dTo = $('#dTo').val();

$('#contenthere').load('/pages/<?=$page_folder;?>/<?=$page_file;?>.php?client=<?=$client;?>&dFrom='+dFrom+'&dTo='+dTo+'');
}

$('#searchWait').keyup(function(){

	$('#link_excel').attr('href','pages/<?=$page_folder;?>/excel.php?search='+$(this).val());

});
</script>
				
<?php
//skats uz ierakstiem
if (!$view){?>
	
	<?php

	require('inc/s.php');

	echo '<div id="contenthere">';

	echo '
	<div id="pleaseWait" style="display: none;">
		<h1 align="center" style="padding-top: 300px;"><i class="glyphicon glyphicon-refresh spin" style="color: #00B5AD"></i></h1>
	</div>';

?>
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
		$('#contenthere').load('/pages/<?=$page_folder;?>/'+encodeURI(url)+''+encodeURI(client)+''+encodeURI(dFrom)+''+encodeURI(dTo)+'&groupProduct=<?=urlencode($groupProduct);?>');
		event.preventDefault();
	});
});
</script>
<?php

	
		echo '<div class="page-header" style="margin-top: -5px;">
		  
			<div class="btn-group btn-group-xs" role="group" aria-label="Small button group" style="display:inline-block;"> 
				<a class="btn btn-default active classlist"><i class="glyphicon glyphicon-list" style="color: #00B5AD" title="pamatdati"></i></a> 
			</div>';
			

			echo '
			<div style="float: right;" >
					<div style="display: inline-block;">
						<input type="text" class="form-control input-xs datepicker" id="dFrom" value="'.date('01.m.Y').'">
					</div>	
					<div style="display: inline-block;">
						<input type="text" class="form-control input-xs datepicker" id="dTo" value="'.date('d.m.Y').'">
					</div>						
					<a class="btn btn-default btn-xs" onclick="dateFilter();"><i class="glyphicon glyphicon-refresh" style="color: #00B5AD"  title="filtrēt"></i></a> ';

					$thisFrom = $thisTo = null;
					if($dFrom){ $thisFrom = '&dFrom='.date('Y-m-d', strtotime($dFrom)); } 
					if($dTo){ $thisTo = '&dTo='.date('Y-m-d', strtotime($dTo));}					
					
					echo '
					
						<div class="dropdown" style="display: inline-block;">
						  <button class="btn btn-default dropdown-toggle btn-xs" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
							EXCEL
							<span class="caret"></span>
						  </button>
						  <ul class="dropdown-menu pull-right" aria-labelledby="dropdownMenu1">
							<li><a id="link_excel" href="pages/'.$page_folder.'/excel.php?section=1'.$thisFrom.''.$thisTo.'" target="_blank">detalizācija</a></li>
							<li><a id="link_excel" href="pages/'.$page_folder.'/excel.php?section=2'.$thisFrom.''.$thisTo.'" target="_blank">apgrozījums periodā</a></li>
							<li><a id="link_excel" href="pages/'.$page_folder.'/excel.php?section=3'.$thisFrom.''.$thisTo.'" target="_blank">apgrozījums periodā (gadi)</a></li>

						  </ul>
						</div>					
					
					';
		
			echo '
			</div>';			
			  	  
		echo '</div>';
		
		echo '<p style="display:inline-block;">noliktava</p>';



				

		echo ' <div style="float: right;"><div id="showWait" style="display: inline-block;"></div><div style="display: inline-block;"><input type="text" id="searchWait" class="form-control input-xs" onkeyup="search(this.value)" placeholder="meklēt"></div></div>';   		
		
		echo '
			<div class="form-group col-md-4 pull-right">
			  <select class="form-control selectpicker btn-group-xs" data-live-search="true" title="filtrēt pēc klienta" onchange="selClient(this)">';
			  
			   $selectClient = mysqli_query($conn, "SELECT DISTINCT(clientCode) AS clientCode, clientName FROM cargo_header WHERE (status='20'  OR status='30')") or die(mysqli_error($conn));
			  
			  while($rowc = mysqli_fetch_array($selectClient)){
				  echo '<option value="'.$rowc['clientCode'].'">'.$rowc['clientCode'].' '.$rowc['clientName'].'</option>';
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
		
	
echo '<div id="results">';	



$rec_limit = $a_p_l; $offset=0; $max_pages = 10;  //DEFINĒJAM LIMITUS
$link_to = $page_file.'?page='; //LINKS KAS TIKS ATVĒRTS KAD NOSPIEDĪS UZ LAPAS, RAKSTĪT LĪDZ page 

$inCargo = mysqli_query($conn, "
SELECT STRAIGHT_JOIN

	clr.productUmo, clr.assistantUmo, clr.productNr,
	
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
WHERE  clr.id 

GROUP  BY clr.productUmo

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
		
		

						<tr>
							<th rowspan="2" style="vertical-align : middle;text-align:center;">mērvienība</th>
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

			$place_count = floatval($icRow['place_count']);
			$place_count_issued = floatval($icRow['place_count_issued']);
			$place_count_start = floatval($icRow['place_count_start_in']) - floatval($icRow['place_count_start_out']);
			$place_count_end = floatval($icRow['place_count_end_in']) - floatval($icRow['place_count_end_out']);
			
			$gross = floatval($icRow['gross']);
			$gross_issued = floatval($icRow['gross_issued']);
			$gross_start = floatval($icRow['gross_start_in']) - floatval($icRow['gross_start_out']);
			$gross_end = floatval($icRow['gross_end_in']) - floatval($icRow['gross_end_out']);

			$neto = floatval($icRow['neto']);
			$neto_issued = floatval($icRow['neto_issued']);
			$neto_start = floatval($icRow['neto_start_in']) - floatval($icRow['neto_start_out']);
			$neto_end = floatval($icRow['neto_end_in']) - floatval($icRow['neto_end_out']);			
			
			$cubicMeters = floatval($icRow['cubicMeters']);
			$cubicMeters_issued = floatval($icRow['cubicMeters_issued']);
			$cubicMeters_start = floatval($icRow['cubicMeters_start_in']) - floatval($icRow['cubicMeters_start_out']);
			$cubicMeters_end = floatval($icRow['cubicMeters_end_in']) - floatval($icRow['cubicMeters_end_out']);
			
			$productUmo = $icRow['productUmo'];
		

				echo '
			<tr>
				<td>'.$productUmo.'</td>';
				
				if($groupProduct=='true'){
					
					echo '<td>'.$icRow['productNr'].'</td>';
					
				}
				
				echo '<td bgcolor="silver">'.$place_count_start.'</td>';
				echo '<td bgcolor="silver">'.$gross_start.'</td>';
				echo '<td bgcolor="silver">'.$neto_start.'</td>';
				echo '<td bgcolor="silver">'.$cubicMeters_start.'</td>';
				echo '<td>'.$place_count.'</td>';
				echo '<td>'.$gross.'</td>';
				echo '<td>'.$neto.'</td>';
				echo '<td>'.$cubicMeters.'</td>';
				echo '<td>'.$place_count_issued.'</td>';
				echo '<td>'.$gross_issued.'</td>';
				echo '<td>'.$neto_issued.'</td>';
				echo '<td>'.$cubicMeters_issued.'</td>';
				echo '<td bgcolor="silver">'.$place_count_end.'</td>';
				echo '<td bgcolor="silver">'.$gross_end.'</td>';
				echo '<td bgcolor="silver">'.$neto_end.'</td>';
				echo '<td bgcolor="silver">'.$cubicMeters_end.'</td>';
				

				
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

		
$query = "
SELECT STRAIGHT_JOIN

	clr.*, 
    chr.clientCode, 
    chr.clientName, 
    chr.acceptance_act_no,
	

    ni.name1, 
    ni.name2, 


	SUM(IF( (Date_format(clr.activityDate, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' AND ile.status!='40') ,clr.place_count,0)) AS place_count,
	SUM(IF( (Date_format(clr.activityDate, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' AND ile.status!='40') ,clr.gross,0))       AS gross,
	SUM(IF( (Date_format(clr.activityDate, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' AND ile.status!='40') ,clr.net,0))         AS neto,
	SUM(IF( (Date_format(clr.activityDate, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' AND ile.status!='40') ,clr.cubicMeters,0)) AS cubicMeters,
	
	
	SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40') ,ile.place_count,0)) AS place_count_issued,
	SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40') ,ile.gross,0)) AS gross_issued,
	SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40') ,ile.net,0)) AS neto_issued,
	SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40') ,ile.cubicMeters,0)) AS cubicMeters_issued,
	
	
	SUM(IF( (Date_format(clr.activityDate, '%Y-%m-%d') < '".date('Y-m-d', strtotime($dFrom))."' AND ile.status!='40') ,clr.place_count,0))                      AS place_count_start_in,
	
	SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') < '".date('Y-m-d', strtotime($dFrom))."' AND ile.status='40') ,ile.place_count,0))                       AS place_count_start_out,
	
	SUM(IF( (Date_format(clr.activityDate, '%Y-%m-%d') < '".date('Y-m-d', strtotime($dFrom))."' AND ile.status!='40') ,clr.gross,0))                            AS gross_start_in,
	SUM(IF( (Date_format(clr.activityDate, '%Y-%m-%d') < '".date('Y-m-d', strtotime($dFrom))."' AND ile.status!='40') ,clr.net,0))                              AS neto_start_in,
	
	SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') < '".date('Y-m-d', strtotime($dFrom))."' AND ile.status='40') ,ile.gross,0))                             AS gross_start_out,
	SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') < '".date('Y-m-d', strtotime($dFrom))."' AND ile.status='40') ,ile.net,0))                               AS neto_start_out,
	
	SUM(IF( (Date_format(clr.activityDate, '%Y-%m-%d') < '".date('Y-m-d', strtotime($dFrom))."' AND ile.status!='40') ,clr.cubicMeters,0))                      AS cubicMeters_start_in,
	
	SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') < '".date('Y-m-d', strtotime($dFrom))."' AND ile.status='40') ,ile.cubicMeters,0))                       AS cubicMeters_start_out,
	
	SUM(IF( (Date_format(clr.activityDate, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($dTo))."' AND ile.status!='40') ,clr.place_count,0))                       AS place_count_end_in,
	
	SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40') ,ile.place_count,0))                        AS place_count_end_out,
	
	SUM(IF( (Date_format(clr.activityDate, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($dTo))."' AND ile.status!='40') ,clr.gross,0))                             AS gross_end_in,
	SUM(IF( (Date_format(clr.activityDate, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($dTo))."' AND ile.status!='40') ,clr.net,0))                               AS neto_end_in,
	
	SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40') ,ile.gross,0))        					   AS gross_end_out,
	SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40') ,ile.net,0))                                AS neto_end_out,	
	
	SUM(IF( (Date_format(clr.activityDate, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($dTo))."' AND ile.status!='40') ,clr.cubicMeters,0))                       AS cubicMeters_end_in,
	
	SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40') ,ile.cubicMeters,0))                        AS cubicMeters_end_out

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

WHERE  clr.id AND (SELECT COUNT(id.id) FROM issuance_doc AS id WHERE id.issuance_id=i.issuance_id AND Date_format(id.issueDate, '%Y-%m-%d') NOT BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' ) < 1

GROUP BY clr.id, clr.docNr, clr.batchNo, clr.serialNo, clr.productNr

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



ORDER  BY clr.activityDate DESC, clr.id DESC
";		
		
		
		
		
		
		
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
							
							<th colspan="4" bgcolor="silver">atlikums uz perioda sākumu</th>
							<th colspan="4">saņemts periodā</th>
							<th colspan="4">izdots periodā</th>
							<th colspan="4" bgcolor="silver">atlikums uz perioda beigām</th>
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
							<th bgcolor="silver">apjoms (m3)</th>							
						</tr>
						
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
				
			$color=1;		
					
			while($row = mysqli_fetch_array($resultGL7)){

			$place_count_start = floatval($row['place_count_start_in']) - floatval($row['place_count_start_out']);
			$gross_start = floatval($row['gross_start_in']) - floatval($row['gross_start_out']);
			$neto_start = floatval($row['neto_start_in']) - floatval($row['neto_start_out']);
			$cubicMeters_start = floatval($row['cubicMeters_start_in']) - floatval($row['cubicMeters_start_out']);
			
			
			$place_count = floatval($row['place_count']);
			$gross = floatval($row['gross']);
			$neto = floatval($row['neto']);
			$cubicMeters = floatval($row['cubicMeters']);
			
			$place_count_issued = floatval($row['place_count_issued']);
			$gross_issued = floatval($row['gross_issued']);
			$neto_issued = floatval($row['neto_issued']);
			$cubicMeters_issued = floatval($row['cubicMeters_issued']);
			
			$place_count_end = floatval($row['place_count_end_in']) - floatval($row['place_count_end_out']);
			$gross_end = floatval($row['gross_end_in']) - floatval($row['gross_end_out']);
			$neto_end = floatval($row['neto_end_in']) - floatval($row['neto_end_out']);
			$cubicMeters_end = floatval($row['cubicMeters_end_in']) - floatval($row['cubicMeters_end_out']);
						
				if ($color % 2 == 0){
					$datacell='datacelltwo';				
				}else{
					$datacell='datacellone';
				}	
						
				echo '	<tr>
							<td nowrap class="'.$datacell.'">';
							if($row['activityDate']!='0000-00-00 00:00:00'){echo date('d.m.Y', strtotime($row['activityDate']));}

							echo '<td nowrap class="'.$datacell.'">'.$row['productNr'].' - '.$row['name1'].' '.$row['name2'];

							echo '<td nowrap class="'.$datacell.'">'.$row['clientCode'].' - '.$row['clientName'];
							echo '<td nowrap class="'.$datacell.'">'.$row['acceptance_act_no'];
							echo '
							<td nowrap class="'.$datacell.'">'.$row['thisTransport'].'
							<td class="'.$datacell.'">'.$row['serialNo'];
							
							
							echo '<td bgcolor="silver">'.$place_count_start.'</td>';
							echo '<td bgcolor="silver">'.$gross_start.'</td>';
							echo '<td bgcolor="silver">'.$neto_start.'</td>';
							echo '<td bgcolor="silver">'.$cubicMeters_start.'</td>';
							echo '<td class="'.$datacell.'">'.$place_count.'</td>';
							echo '<td class="'.$datacell.'">'.$gross.'</td>';
							echo '<td class="'.$datacell.'">'.$neto.'</td>';
							echo '<td class="'.$datacell.'">'.$cubicMeters.'</td>';
							echo '<td class="'.$datacell.'">'.$place_count_issued.'</td>';
							echo '<td class="'.$datacell.'">'.$gross_issued.'</td>';
							echo '<td class="'.$datacell.'">'.$neto_issued.'</td>';
							echo '<td class="'.$datacell.'">'.$cubicMeters_issued.'</td>';
							echo '<td bgcolor="silver">'.$place_count_end.'</td>';
							echo '<td bgcolor="silver">'.$gross_end.'</td>';
							echo '<td bgcolor="silver">'.$neto_end.'</td>';
							echo '<td bgcolor="silver">'.$cubicMeters_end.'</td>';											
							
						echo '</tr>';
						
				$color++;
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
<?php include_once("datepicker.php"); ?>