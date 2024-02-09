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

if (isset($_GET['search'])){$search = htmlentities($_GET['search'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['section'])){$section = htmlentities($_GET['section'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['groupProduct'])){$groupProduct = htmlentities($_GET['groupProduct'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['issueDetail'])){$issueDetail = htmlentities($_GET['issueDetail'], ENT_QUOTES, "UTF-8");}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1,IE=9" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>noliktava</title>
</head>
<body>
<?php
include('../../functions/base.php');
require('../../inc/s.php');
$date_in_name = null;
if($dFrom && $dTo){$date_in_name = ' '.date('d.m.Y', strtotime($dFrom)).' - '.date('d.m.Y', strtotime($dTo));}



header('Content-Description: File Transfer');				
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=noliktava".$date_in_name.".xls");



if($search){
	$s = $search;	
	$search = " AND (clr.thisTransport LIKE '%$s%' || clr.serialNo LIKE '%$s%' || clr.place_count LIKE '%$s%' || date_format(clr.activityDate, '%Y-%m-%d') LIKE '%$s%' || chr.clientCode LIKE '%$s%' || chr.clientName LIKE '%$s%' || chr.ownerCode LIKE '%$s%' || chr.ownerName LIKE '%$s%' || chr.acceptance_act_no LIKE '%$s%' || clr.productNr LIKE '%$s%' || ni.name1 LIKE '%$s%' || ni.name2 LIKE '%$s%')";	
}else{	
	$search = "";	
}


if($section==1){
	
    echo '<table>
            <tr><td colspan="3" width="300">noliktava</td><td colspan="3" style="max-width: 1000px;">'; if($client){echo '<strong>filtrs pēc klienta:</strong> '.$client.' - '.returnClientName($conn, $client);} echo '</td></tr>';
            echo '<tr><td colspan="3"></td><td colspan="3" style="max-width: 1000px;">'; if($_GET['search']){echo '<strong>meklētā frāze:</strong> '.$_GET['search'];} echo '</td></tr>';   
            if($dFrom && $dTo){ echo '<tr><td colspan="3"></td><td colspan="3" style="max-width: 1000px;">'; if($dFrom && $dTo){echo '<strong>filtrs pēc perioda:</strong> '.date('d.m.Y', strtotime($dFrom)).' - '.date('d.m.Y', strtotime($dTo)).'<br>';} echo '</td></tr>'; }   	
    echo '</table><br>';


	if($client){$filterClient = ' AND chr.clientCode="'.$client.'"'; $linkClient='client='.$client.'&';}else{$filterClient = null; $linkClient=null;}
			

	if($client){$andClient = " AND chr.clientCode='".$client."' ";}else{$andClient = null;}
	if($dFrom){$andDate = " AND ile.activityDate<='".date('Y-m-d', strtotime($dFrom))."' ";}else{$andDate = null;}
	if($dTo){$andDateTo = " AND ile.activityDate>='".date('Y-m-d', strtotime($dTo))."' ";}else{$andDateTo = null;}
	$between = $between2 = null;
	if($dFrom && $dTo){$between = " AND DATE_FORMAT(ile.activityDate,'%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' ";}
	if($dFrom && $dTo){$between2 = " AND DATE_FORMAT(chr.deliveryDate,'%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' ";}

	if($groupProduct=='true'){$groupProductIt = " productNr, ";}else{$groupProductIt = null;}
	
	$countIssues = $groupIt = null;
	
	$forCase = "
	
		(place_count_issued>0) OR
		(gross_issued>0) OR
		(neto_issued>0) OR
		(cubicMeters_issued>0) OR	
	
		(place_count_start_in-place_count_start_out>0) OR
		(gross_start_in-gross_start_out>0) OR
		(neto_start_in-neto_start_out>0) OR
		(cubicMeters_start_in-cubicMeters_start_out>0) OR		
		
		(place_count_end_in-place_count_end_out>0) OR
		(gross_end_in-gross_end_out>0) OR
		(neto_end_in-neto_end_out>0) OR
		(cubicMeters_end_in-cubicMeters_end_out>0)	
	";	
	
	if($issueDetail=='true'){

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
		  
		  
		  
		  
		  
		  
		  
	
		SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') < '".date('Y-m-d', strtotime($dFrom))."' AND ile.status='40' AND i.issuance_id=ile.issuance_id) ,ile.place_count,0)) AS place_count_start_out_tott,
		
		
		SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') < '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40' AND i.issuance_id=ile.issuance_id) ,ile.gross,0))       AS gross_start_out_tott,
		SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') < '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40' AND i.issuance_id=ile.issuance_id) ,ile.net,0))        AS neto_start_out_tott,
		SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') < '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40' AND i.issuance_id=ile.issuance_id) ,ile.cubicMeters,0)) AS cubicMeters_start_out_tott,	 


		SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40' AND i.issuance_id=ile.issuance_id) ,ile.place_count,0)) AS place_count_end_out_tott,
		SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40' AND i.issuance_id=ile.issuance_id) ,ile.gross,0)) AS gross_end_out_tott,
		SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40' AND i.issuance_id=ile.issuance_id) ,ile.net,0)) AS neto_end_out_tott,
		SUM(IF( (Date_format(ile.activityDate, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($dTo))."' AND ile.status='40' AND i.issuance_id=ile.issuance_id) ,ile.cubicMeters,0)) AS cubicMeters_end_out_tott,		
				  
		";
		
		$lefJoin = "
		LEFT JOIN item_ledger_entry AS i 
		ON clr.docNr=i.docNr AND i.status=40  AND (i.id=clr.id OR i.orgLine=clr.id)	
		
		";		

		$groupIt = " , (case when countIssues > 0 then i.issuance_id end)";	
		
		$forCase = "
		
			(place_count_issued_tot>0) OR
			(gross_issued_tot>0) OR
			(neto_issued_tot>0) OR
			(cubicMeters_issued_tot>0) OR		
		
			(CASE WHEN countIssues > 1 THEN place_count_start_in-place_count_start_out_tott ELSE place_count_start_in-place_count_start_out END)>0 OR
			(CASE WHEN countIssues > 1 THEN gross_start_in-gross_start_out_tott ELSE gross_start_in-gross_start_out END)>0 OR
			(CASE WHEN countIssues > 1 THEN neto_start_in-neto_start_out_tott ELSE neto_start_in-neto_start_out END)>0 OR
			(CASE WHEN countIssues > 1 THEN cubicMeters_start_in-cubicMeters_start_out_tott ELSE cubicMeters_start_in-cubicMeters_start_out END)>0 OR		
			
			(CASE WHEN countIssues > 1 THEN place_count_end_in-place_count_end_out_tott ELSE place_count_end_in-place_count_end_out END)>0 OR
			(CASE WHEN countIssues > 1 THEN gross_end_in-gross_end_out_tott ELSE gross_end_in-gross_end_out END)>0 OR
			(CASE WHEN countIssues > 1 THEN neto_end_in-neto_end_out_tott ELSE neto_end_in-neto_end_out END)>0 OR
			(CASE WHEN countIssues > 1 THEN cubicMeters_end_in-cubicMeters_end_out_tott ELSE cubicMeters_end_in-cubicMeters_end_out END)>0 		
		";
		
	}


	$resultGL7 = mysqli_query($conn, "
	

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

		WHERE clr.id ".$search." ".$filterClient."
		
		
		AND (SELECT COUNT(id.id) FROM issuance_doc AS id WHERE id.issuance_id=i.issuance_id AND Date_format(id.issueDate, '%Y-%m-%d') NOT BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' ) < 1

		GROUP BY clr.id, clr.docNr, clr.batchNo, clr.serialNo, clr.productNr ".$groupIt."	

		ORDER  BY clr.activityDate DESC, clr.id DESC
		
	") or die(mysqli_error($conn));
	
	$count_GL7 = mysqli_num_rows($resultGL7);  //IEGŪST SKAITU 					
		if ($count_GL7!=0){

			echo '<table border="1" style="font-size: 9pt;"><thead><tr>
						
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
								<td '.$rowspan.' class="'.$datacell.'">'.$row['thisTransport'].'</td>
								<td '.$rowspan.' class="'.$datacell.'">'.$row['serialNo'].'</td>';
								
								
								echo '<td bgcolor="silver" '.$rowspan.' style="vnd.ms-excel.numberformat:0">'.$place_count_start.'</td>';
								echo '<td bgcolor="silver" '.$rowspan.' style="vnd.ms-excel.numberformat:0" nowrap>'.$gross_start.'</td>';
								echo '<td bgcolor="silver" '.$rowspan.' style="vnd.ms-excel.numberformat:0">'.$neto_start.'</td>';
								echo '<td bgcolor="silver" '.$rowspan.' style="vnd.ms-excel.numberformat:0">'.$cubicMeters_start.'</td>';
								
								echo '<td '.$rowspan.' class="'.$datacell.'" style="vnd.ms-excel.numberformat:0">'.$place_count.'</td>';
								echo '<td '.$rowspan.' class="'.$datacell.'" style="vnd.ms-excel.numberformat:0">'.$gross.'</td>';
								echo '<td '.$rowspan.' class="'.$datacell.'" style="vnd.ms-excel.numberformat:0">'.$neto.'</td>';
								echo '<td '.$rowspan.' class="'.$datacell.'" style="vnd.ms-excel.numberformat:0">'.$cubicMeters.'</td>';
								
								echo '<td '.$rowspan.' class="'.$datacell.'" style="vnd.ms-excel.numberformat:0">'.$place_count_issued.'</td>';
								echo '<td '.$rowspan.' class="'.$datacell.'" style="vnd.ms-excel.numberformat:0">'.$gross_issued.'</td>';
								echo '<td '.$rowspan.' class="'.$datacell.'" style="vnd.ms-excel.numberformat:0">'.$neto_issued.'</td>';
								echo '<td '.$rowspan.' class="'.$datacell.'" style="vnd.ms-excel.numberformat:0">'.$cubicMeters_issued.'</td>';
								
								echo '<td bgcolor="silver" '.$rowspan.' style="vnd.ms-excel.numberformat:0">'.$place_count_end.'</td>';
								echo '<td bgcolor="silver" '.$rowspan.' style="vnd.ms-excel.numberformat:0">'.$gross_end.'</td>';
								echo '<td bgcolor="silver" '.$rowspan.' style="vnd.ms-excel.numberformat:0">'.$neto_end.'</td>';
								echo '<td bgcolor="silver" '.$rowspan.' style="vnd.ms-excel.numberformat:0">'.$cubicMeters_end.'</td>';											
								
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
									
									
									echo '<td bgcolor="silver" '.$rowspan.' style="vnd.ms-excel.numberformat:0">'.$place_count_start.'</td>';
									echo '<td bgcolor="silver" '.$rowspan.' style="vnd.ms-excel.numberformat:0">'.$gross_start.'</td>';
									echo '<td bgcolor="silver" '.$rowspan.' style="vnd.ms-excel.numberformat:0">'.$neto_start.'</td>';
									echo '<td bgcolor="silver" '.$rowspan.' style="vnd.ms-excel.numberformat:0">'.$cubicMeters_start.'</td>';
									echo '<td '.$rowspan.' class="'.$datacell.'" style="vnd.ms-excel.numberformat:0">'.$place_count.'</td>';
									echo '<td '.$rowspan.' class="'.$datacell.'" style="vnd.ms-excel.numberformat:0">'.$gross.'</td>';
									echo '<td '.$rowspan.' class="'.$datacell.'" style="vnd.ms-excel.numberformat:0">'.$neto.'</td>';
									echo '<td '.$rowspan.' class="'.$datacell.'" style="vnd.ms-excel.numberformat:0">'.$cubicMeters.'</td>';
									
									
									$atlikums .= '<td '.$rowspan.' bgcolor="silver" style="vnd.ms-excel.numberformat:0">'.$place_count_end.'</td>';
									$atlikums .= '<td '.$rowspan.' bgcolor="silver" style="vnd.ms-excel.numberformat:0">'.$gross_end.'</td>';
									$atlikums .= '<td '.$rowspan.' bgcolor="silver" style="vnd.ms-excel.numberformat:0">'.$neto_end.'</td>';
									$atlikums .= '<td '.$rowspan.' bgcolor="silver" style="vnd.ms-excel.numberformat:0">'.$cubicMeters_end.'</td>';									
									
									$showed='y';
									$color++;
								}else{
									$showed=null;
								}
								
							}else{
								$showed=null;
							}	
																
								echo '<td  class="'.$datacell.'" style="vnd.ms-excel.numberformat:0">'.$place_count_issued.'</td>';
								echo '<td  class="'.$datacell.'" style="vnd.ms-excel.numberformat:0">'.$gross_issued.'</td>';
								echo '<td  class="'.$datacell.'" style="vnd.ms-excel.numberformat:0">'.$neto_issued.'</td>';
								echo '<td  class="'.$datacell.'" style="vnd.ms-excel.numberformat:0">'.$cubicMeters_issued.'</td>';											
																
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
			
			echo '</tbody></table>';
			mysqli_close($conn);
		}else{
			echo 'nav neviena ieraksta!';
		}








}

if($section==2){
	
    echo '<table>
            <tr><td colspan="3" width="300">noliktava</td><td colspan="3" style="max-width: 1000px;">'; if($client){echo '<strong>filtrs pēc klienta:</strong> '.$client.' - '.returnClientName($conn, $client);} echo '</td></tr>';
            echo '<tr><td colspan="3"></td><td colspan="3" style="max-width: 1000px;">'; if($_GET['search']){echo '<strong>meklētā frāze:</strong> '.$_GET['search'];} echo '</td></tr>';   
            if($dFrom && $dTo){ echo '<tr><td colspan="3"></td><td colspan="3" style="max-width: 1000px;">'; if($dFrom && $dTo){echo '<strong>filtrs pēc perioda:</strong> '.date('d.m.Y', strtotime($dFrom)).' - '.date('d.m.Y', strtotime($dTo)).'<br>';} echo '</td></tr>'; }   	
    echo '</table><br>';

	if($client){$filterClient = ' AND chr.clientCode="'.$client.'"'; $linkClient='client='.$client.'&';}else{$filterClient = null; $linkClient=null;}
			

	if($client){$andClient = " AND chr.clientCode='".$client."' ";}else{$andClient = null;}
	if($dFrom){$andDate = " AND ile.activityDate<='".date('Y-m-d', strtotime($dFrom))."' ";}else{$andDate = null;}
	if($dTo){$andDateTo = " AND ile.activityDate>='".date('Y-m-d', strtotime($dTo))."' ";}else{$andDateTo = null;}
	$between = $between2 = null;
	if($dFrom && $dTo){$between = " AND DATE_FORMAT(ile.activityDate,'%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' ";}
	if($dFrom && $dTo){$between2 = " AND DATE_FORMAT(chr.deliveryDate,'%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' ";}

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

WHERE clr.id ".$andClient." ".$search."

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
		<table border="1" style="font-size: 9pt;">
		<thead>
		<tr>
		
		

						<tr>
							<th rowspan="2" style="vertical-align : middle;text-align:center;">mērvienība</th>';

							if($groupProduct=='true'){
								echo '<th rowspan="2" style="vertical-align : middle;text-align:center;">prece</th>';
							}							
							
							echo '
							<th colspan="3" bgcolor="silver">atlikums uz per. sākumu</th>
							<th colspan="3">saņemts per.</th>
							<th colspan="3">izdots per.</th>
							<th colspan="3" bgcolor="silver">atlikums uz per. beigām</th>
						</tr>
						
						<tr>
							<th bgcolor="silver">vietu sk.</th>
							<th bgcolor="silver">bruto svars (kg)</th>
							<th bgcolor="silver">apjoms (m3)</th>
							<th>vietu sk.</th>
							<th>bruto svars (kg)</th>
							<th>apjoms (m3)</th>
							<th>vietu sk.</th>
							<th>bruto svars (kg)</th>
							<th>apjoms (m3)</th>
							<th bgcolor="silver">vietu sk.</th>
							<th bgcolor="silver">bruto svars (kg)</th>
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
			
			$cubicMeters = floatval($icRow['cubicMeters']);
			$cubicMeters_issued = floatval($icRow['cubicMeters_issued']);
			$cubicMeters_start = floatval($icRow['cubicMeters_start_in']) - floatval($icRow['cubicMeters_start_out']);
			$cubicMeters_end = floatval($icRow['cubicMeters_end_in']) - floatval($icRow['cubicMeters_end_out']);
			
			$productUmo = $icRow['productUmo'];
		

				echo '
			<tr>
				<td>'.$productUmo.'</td>';
				
				if($groupProduct=='true'){
					
					echo '<td>'.$icRow['productNr'].' - '.$icRow['name1'].' '.$icRow['name2'].'</td>';
					
				}
				
				echo '<td bgcolor="silver"  style="vnd.ms-excel.numberformat:0">'.$place_count_start.'</td>';
				echo '<td bgcolor="silver"  style="vnd.ms-excel.numberformat:0">'.$gross_start.'</td>';
				echo '<td bgcolor="silver"  style="vnd.ms-excel.numberformat:0">'.$cubicMeters_start.'</td>';
				echo '<td  style="vnd.ms-excel.numberformat:0">'.$place_count.'</td>';
				echo '<td  style="vnd.ms-excel.numberformat:0">'.$gross.'</td>';
				echo '<td  style="vnd.ms-excel.numberformat:0">'.$cubicMeters.'</td>';
				echo '<td  style="vnd.ms-excel.numberformat:0">'.$place_count_issued.'</td>';
				echo '<td  style="vnd.ms-excel.numberformat:0">'.$gross_issued.'</td>';
				echo '<td  style="vnd.ms-excel.numberformat:0">'.$cubicMeters_issued.'</td>';
				echo '<td bgcolor="silver"  style="vnd.ms-excel.numberformat:0">'.$place_count_end.'</td>';
				echo '<td bgcolor="silver"  style="vnd.ms-excel.numberformat:0">'.$gross_end.'</td>';
				echo '<td bgcolor="silver"  style="vnd.ms-excel.numberformat:0">'.$cubicMeters_end.'</td>';
				

				
				echo '
				
			</tr>';
	
		}
		echo '
		</tbody>
		</table>				
		';
	
	
	}
	echo '<br><br>';	



}

if($section==3){
	
    echo '<table>
            <tr><td colspan="3" width="300">noliktava</td><td colspan="3" style="max-width: 1000px;">'; if($client){echo '<strong>filtrs pēc klienta:</strong> '.$client.' - '.returnClientName($conn, $client);} echo '</td></tr>';
            echo '<tr><td colspan="3"></td><td colspan="3" style="max-width: 1000px;">'; if($_GET['search']){echo '<strong>meklētā frāze:</strong> '.$_GET['search'];} echo '</td></tr>';   
            if($dFrom && $dTo){ echo '<tr><td colspan="3"></td><td colspan="3" style="max-width: 1000px;">'; if($dFrom && $dTo){echo '<strong>filtrs pēc perioda:</strong> '.date('d.m.Y', strtotime($dFrom)).' - '.date('d.m.Y', strtotime($dTo)).'<br>';} echo '</td></tr>'; }   	
    echo '</table><br>';

	if($client){$filterClient = ' AND chr.clientCode="'.$client.'"'; $linkClient='client='.$client.'&';}else{$filterClient = null; $linkClient=null;}
			

	if($client){$andClient = " AND chr.clientCode='".$client."' ";}else{$andClient = null;}
	if($dFrom){$andDate = " AND ile.activityDate<='".date('Y-m-d', strtotime($dFrom))."' ";}else{$andDate = null;}
	if($dTo){$andDateTo = " AND ile.activityDate>='".date('Y-m-d', strtotime($dTo))."' ";}else{$andDateTo = null;}
	$between = $between2 = null;
	if($dFrom && $dTo){$between = " AND DATE_FORMAT(ile.activityDate,'%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' ";}
	if($dFrom && $dTo){$between2 = " AND DATE_FORMAT(chr.deliveryDate,'%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($dFrom))."' AND '".date('Y-m-d', strtotime($dTo))."' ";}

	if($groupProduct=='true'){$groupProductIt = " productNr, ";}else{$groupProductIt = null;}
	
	
	


$inCargo = mysqli_query($conn, "
SELECT STRAIGHT_JOIN

	clr.productUmo, clr.assistantUmo, clr.productNr,
	YEAR(clr.activityDate) AS adate,
	
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

WHERE clr.id ".$andClient." ".$search."

GROUP  BY ".$groupProductIt." clr.productUmo, adate ASC

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

ORDER  BY clr.productNr DESC, adate ASC
");

	
	
	
			$table1 = '
				<table border="1" style="font-size: 9pt; width: 100%;">
				<thead>
				<tr>
				
								<tr>
									<th rowspan="2" style="vertical-align : middle;text-align:center;">Pieņemšanas gads.</th>
									<th colspan="3" bgcolor="silver">atlikums uz per. sākumu</th>
									<th colspan="3">saņemts per.</th>
									<th colspan="3">izdots per.</th>
									<th colspan="3" bgcolor="silver">atlikums uz per. beigām</th>
								</tr>
								
								<tr>
									<th bgcolor="silver">vietu sk.</th>
									<th bgcolor="silver">bruto svars (kg)</th>
									<th bgcolor="silver">apjoms (m3)</th>
									<th>vietu sk.</th>
									<th>bruto svars (kg)</th>
									<th>apjoms (m3)</th>
									<th>vietu sk.</th>
									<th>bruto svars (kg)</th>
									<th>apjoms (m3)</th>
									<th bgcolor="silver">vietu sk.</th>
									<th bgcolor="silver">bruto svars (kg)</th>
									<th bgcolor="silver">apjoms (m3)</th>							
								</tr>		
				</tr>
				</thead>
				<tbody>';
				
				$table2 = '
				</tbody>
				</table><br><br>';				
	
	
	if(mysqli_num_rows($inCargo)>0){


		$products = array();
		while($row = mysqli_fetch_assoc($inCargo)){
			  $product_data = array($row);
			  $products[] = array($row['productNr'] => $product_data);
		}

		 $countp = count($products); 
		 $product_data = array();
		 
		 for ($x = 0; $x <= $countp; $x++) {
			
			if(isset($products[$x])){	
			
				foreach($products[$x] as $key => $value){
					
					foreach($value as $k => $v){
						
							$place_count = floatval($v['place_count']);
							$place_count_issued = floatval($v['place_count_issued']);
							$place_count_start = floatval($v['place_count_start_in']) - floatval($v['place_count_start_out']);
							$place_count_end = floatval($v['place_count_end_in']) - floatval($v['place_count_end_out']);
							
							$gross = floatval($v['gross']);
							$gross_issued = floatval($v['gross_issued']);
							$gross_start = floatval($v['gross_start_in']) - floatval($v['gross_start_out']);
							$gross_end = floatval($v['gross_end_in']) - floatval($v['gross_end_out']);
							
							$cubicMeters = floatval($v['cubicMeters']);
							$cubicMeters_issued = floatval($v['cubicMeters_issued']);
							$cubicMeters_start = floatval($v['cubicMeters_start_in']) - floatval($v['cubicMeters_start_out']);
							$cubicMeters_end = floatval($v['cubicMeters_end_in']) - floatval($v['cubicMeters_end_out']);

							$ad = $v['adate'];		 

							$product_data[$v['productNr']][] = array(
													'place_count' => $place_count, 
													'place_count_issued' => $place_count_issued, 
													'place_count_start' => $place_count_start, 
													'place_count_end' => $place_count_end, 
													'gross' => $gross, 
													'gross_issued' => $gross_issued, 
													'gross_start' => $gross_start, 
													'gross_end' => $gross_end, 
													'cubicMeters' => $cubicMeters, 
													'cubicMeters_issued' => $cubicMeters_issued, 
													'cubicMeters_start' => $cubicMeters_start, 
													'cubicMeters_end' => $cubicMeters_end, 
													'ad' => $ad
												);					
						
					}

				}
				
			}
			
		}

		foreach($product_data AS $key => $val){
			 $count = count($val);
			
			 if($key){

			 
				 if($groupProduct=='true'){
					$query = mysqli_query($conn, "SELECT name1, name2 FROM n_items WHERE code='".$key."'");
					$row = mysqli_fetch_array($query);
					echo '<table>';
							echo '<tr><td colspan="3" width="300"></td><td colspan="3" style="max-width: 1000px;">'.$row['name1'].' '.$row['name2'].' '.$key.'</td></tr>';     	
					echo '</table><br>';
				 }
				
				 echo $table1;
				 
				 $place_count_start=$gross_start=$cubicMeters_start=$place_count=$gross=$cubicMeters=$place_count_issued=$gross_issued=$cubicMeters_issued=$place_count_end=$gross_end=$cubicMeters_end=null;
				 for ($x = 0; $x <= $count; $x++) {	
						
						if(isset($val[$x])){
						
										echo '
									<tr>
										<td>'.$val[$x]['ad'].'</td>';
										echo '<td bgcolor="silver" style="vnd.ms-excel.numberformat:0">'.$val[$x]['place_count_start'].'</td>';
										echo '<td bgcolor="silver" style="vnd.ms-excel.numberformat:0">'.$val[$x]['gross_start'].'</td>';
										echo '<td bgcolor="silver" style="vnd.ms-excel.numberformat:0">'.$val[$x]['cubicMeters_start'].'</td>';
										echo '<td style="vnd.ms-excel.numberformat:0">'.$val[$x]['place_count'].'</td>';
										echo '<td style="vnd.ms-excel.numberformat:0">'.$val[$x]['gross'].'</td>';
										echo '<td style="vnd.ms-excel.numberformat:0">'.$val[$x]['cubicMeters'].'</td>';
										echo '<td style="vnd.ms-excel.numberformat:0">'.$val[$x]['place_count_issued'].'</td>';
										echo '<td style="vnd.ms-excel.numberformat:0">'.$val[$x]['gross_issued'].'</td>';
										echo '<td style="vnd.ms-excel.numberformat:0">'.$val[$x]['cubicMeters_issued'].'</td>';
										echo '<td bgcolor="silver" style="vnd.ms-excel.numberformat:0">'.$val[$x]['place_count_end'].'</td>';
										echo '<td bgcolor="silver" style="vnd.ms-excel.numberformat:0">'.$val[$x]['gross_end'].'</td>';
										echo '<td bgcolor="silver" style="vnd.ms-excel.numberformat:0">'.$val[$x]['cubicMeters_end'].'</td>';
							
										echo '
										
									</tr>';

									$place_count_start += $val[$x]['place_count_start'];
									$gross_start += $val[$x]['gross_start'];
									$cubicMeters_start += $val[$x]['cubicMeters_start'];
									$place_count += $val[$x]['place_count'];
									$gross += $val[$x]['gross'];
									$cubicMeters += $val[$x]['cubicMeters'];
									$place_count_issued += $val[$x]['place_count_issued'];
									$gross_issued += $val[$x]['gross_issued'];
									$cubicMeters_issued += $val[$x]['cubicMeters_issued'];
									$place_count_end += $val[$x]['place_count_end'];
									$gross_end += $val[$x]['gross_end'];
									$cubicMeters_end += $val[$x]['cubicMeters_end'];
									
						}
					
				 }
				 echo '<tr>
						<td>Kopā:</td>
						<td bgcolor="silver" style="vnd.ms-excel.numberformat:0">'.$place_count_start.'</td>
						<td bgcolor="silver" style="vnd.ms-excel.numberformat:0">'.$gross_start.'</td>
						<td bgcolor="silver" style="vnd.ms-excel.numberformat:0">'.$cubicMeters_start.'</td>
						<td style="vnd.ms-excel.numberformat:0">'.$place_count.'</td>
						<td style="vnd.ms-excel.numberformat:0">'.$gross.'</td>
						<td style="vnd.ms-excel.numberformat:0">'.$cubicMeters.'</td>
						<td style="vnd.ms-excel.numberformat:0">'.$place_count_issued.'</td>
						<td style="vnd.ms-excel.numberformat:0">'.$gross_issued.'</td>
						<td style="vnd.ms-excel.numberformat:0">'.$cubicMeters_issued.'</td>
						<td bgcolor="silver" style="vnd.ms-excel.numberformat:0">'.$place_count_end.'</td>
						<td bgcolor="silver" style="vnd.ms-excel.numberformat:0">'.$gross_end.'</td>
						<td bgcolor="silver" style="vnd.ms-excel.numberformat:0">'.$cubicMeters_end.'</td>
					  </tr>';
				 echo $table2;
			 }
		}
	
	}
	echo '<br><br>';

}




?>
</body>
</html>


