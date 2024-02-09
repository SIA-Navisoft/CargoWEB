<?php
error_reporting(E_ALL ^ E_NOTICE);
require('../../lock.php');

$page_file="received";


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

$page_header=$row['page_header'];
$page_icon=$row['page_icon'];
$page_table=$row['page_table'];
mysqli_close($conn);

include('../../functions/base.php');

if(!empty($_GET['page'])) {$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);if(false === $page) {$page = 1;}}else{$page = 1;} 

if (isset($_GET['res'])){$res = htmlentities($_GET['res'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['view'])){$view = htmlentities($_GET['view'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['id'])){$id = htmlentities($_GET['id'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['do'])){$do = htmlentities($_GET['do'], ENT_QUOTES, "UTF-8");}

if (isset($_GET['dFrom'])){$dFrom = htmlentities($_GET['dFrom'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['dTo'])){$dTo = htmlentities($_GET['dTo'], ENT_QUOTES, "UTF-8");}

if (isset($_GET['client'])){$client = htmlentities($_GET['client'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['groupProduct'])){$groupProduct = htmlentities($_GET['groupProduct'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['issueDetail'])){$issueDetail = htmlentities($_GET['issueDetail'], ENT_QUOTES, "UTF-8");}

if($dFrom){$dFrom=$dFrom;}else{$dFrom=date('Y-m-01');}
if($dTo){$dTo=$dTo;}else{$dTo=date('Y-m-t');}


if($page){$glpage = '?page='.$page;}else{$glpage = null;}
if($page){$elpage = '&page='.$page;}else{$elpage = null;}
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
		$('#contenthere').load('/pages/received/'+encodeURI(url)+''+encodeURI(client)+''+encodeURI(dFrom)+''+encodeURI(dTo)+'&groupProduct=<?=urlencode($groupProduct);?>&search=<?=urlencode($_POST['name']);?>');
		event.preventDefault();
	});
});
</script>
<?php

	echo '
	<div id="pleaseWait" style="display: none;">
		<h1 align="center" style="padding-top: 300px;"><i class="glyphicon glyphicon-refresh spin" style="color: #00B5AD"></i></h1>
	</div>';

	$A1=$A2=$A3=$A4=$A5=null;
	if($client){$A1 = 'client='.$client.'&';}
	if($dFrom){$A2 = 'dFrom='.date('d.m.Y', strtotime($dFrom)).'&';}else{$A2 = 'dFrom='.date('d.m.Y').'&';}
	if($dTo){$A3 = 'dTo='.date('d.m.Y', strtotime($dTo)).'&';}else{$A3 = 'dTo='.date('d.m.Y').'&';}
	if($groupProduct){$A4 = 'groupProduct='.$groupProduct.'&';}

if($view=='received'){
	require('../../inc/s.php');
	if($client){$filterClient = ' AND chr.clientCode="'.$client.'"'; $linkClient='client='.$client.'&';}else{$filterClient = null; $linkClient=null;}	
	
	if($client){
		$client = " AND chr.clientCode='".mysqli_real_escape_string($conn, $client)."'";
	}else{
		$client = null;
	}
	if($_POST['name']){
	$s = mysqli_real_escape_string($conn, $_POST['name']);	
	$search = " AND (clr.thisTransport LIKE '%$s%' || clr.serialNo LIKE '%$s%' || clr.place_count LIKE '%$s%' || date_format(clr.activityDate, '%Y-%m-%d') LIKE '%$s%' || chr.clientCode LIKE '%$s%' || chr.clientName LIKE '%$s%' || chr.ownerCode LIKE '%$s%' || chr.ownerName LIKE '%$s%' || chr.acceptance_act_no LIKE '%$s%' || clr.productNr LIKE '%$s%' || ni.name1 LIKE '%$s%' || ni.name2 LIKE '%$s%')";
	$searchUrl = 'search='.$s.'&';
		
	}else{
	$searchUrl = null;	
	$search = "";
		
	}
	
	if($dFrom){$addLink = 'dFrom='.$dFrom.'&';}else{$addLink = 'dFrom='.date('Y-m-d').'&';}
	$rec_limit = $a_p_l; $offset=0; $max_pages = 10;  //DEFINĒJAM LIMITUS
	$link_to = $page_file.'?'.$A1.''.$A2.''.$A3.''.$A4.''.$searchUrl.'page='; //LINKS KAS TIKS ATVĒRTS KAD NOSPIEDĪS UZ LAPAS, RAKSTĪT LĪDZ page 

	$offsetz = ($page - 1) * $rec_limit;

	if($dFrom){$andDate = " AND ile.activityDate<='".date('Y-m-d', strtotime($dFrom))."' ";}else{$andDate = null;}

	if($groupProduct=='true'){$groupProductIt = " productNr, ";}else{$groupProductIt = null;}
	
	
	
	
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
			
			SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40') ,ile.place_count,0)) AS place_count_issued_tot,
			SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40') ,ile.gross,0)) AS gross_issued_tot,
			SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40') ,ile.net,0)) AS neto_issued_tot,
			SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40') ,ile.cubicMeters,0)) AS cubicMeters_issued_tot,



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

		WHERE clr.id ".$search." ".$client."

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
								<td '.$rowspan.' nowrap class="'.$datacell.'">'.$row['thisTransport'].'</td>
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
									<td '.$rowspan.' class="'.$datacell.'">'.$row['thisTransport'].'</td>
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

								echo '<td  class="'.$datacell.'">'.$place_count_issued.'</td>';
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

}

$thisFrom = $thisTo = $thisClient = $thisGroupProduct = $thisIssueDetail = null;
if($dFrom){ $thisFrom = 'dFrom='.date('Y-m-d', strtotime($dFrom)).'&'; } 
if($dTo){ $thisTo = 'dTo='.date('Y-m-d', strtotime($dTo)).'&';}					
if($client){ $thisClient = 'client='.$client.'&';}					
if($groupProduct){ $thisGroupProduct = 'groupProduct='.$groupProduct.'&';}	
if($issueDetail=='true'){ $thisIssueDetail = 'issueDetail='.$issueDetail.'&';}	
?>
<script>
$('#searchWait').keyup(function(){
	
	var href = null;
	var href = $('#link_excel').attr('data-org');

	$('#link_excel').attr('href',''+href+'&search='+$(this).val());

});
</script>

