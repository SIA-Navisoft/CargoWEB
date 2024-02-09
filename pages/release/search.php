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

if (isset($_GET['client'])){$client = htmlentities($_GET['client'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['cargo'])){$cargo = htmlentities($_GET['cargo'], ENT_QUOTES, "UTF-8");}

if (isset($_GET['agrNr'])){$agrNr = htmlentities($_GET['agrNr'], ENT_QUOTES, "UTF-8");}

if (isset($_GET['eid'])){$eid = htmlentities($_GET['eid'], ENT_QUOTES, "UTF-8");}


if (isset($_GET['agr'])){$agr = htmlentities($_GET['agr'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['cli'])){$cli = htmlentities($_GET['cli'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['num'])){$num = htmlentities($_GET['num'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['issuance'])){$issuance = htmlentities($_GET['issuance'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['showToGive'])){$showToGive = htmlentities($_GET['showToGive'], ENT_QUOTES, "UTF-8");}



if($page){$glpage = '?page='.$page;}else{$glpage = null;}
if($page){$elpage = '&page='.$page;}else{$elpage = null;}


if($view=='release'){
	
	require('../../inc/s.php');
	
	
	if($client){
		$client = " AND clientCode='".mysqli_real_escape_string($conn, $client)."'";
	}else{
		$client = null;
	}
	if($_POST['name']){
	$s = mysqli_real_escape_string($conn, $_POST['name']);	
	$search = " AND (deliveryDate LIKE '%$s%' || cargoCode LIKE '%$s%' || ladingNr LIKE '%$s%' || clientCode LIKE '%$s%' || clientName LIKE '%$s%' || ownerCode LIKE '%$s%' || ownerName LIKE '%$s%')";
	$searchUrl = 'search='.$s.'&';
	
	}else{
	$searchUrl = null;	
	$search = "";
	
	}
	
	
	
	$rec_limit = $a_p_l; $offset=0; $max_pages = 10;  //DEFINĒJAM LIMITUS
	$link_to = $page_file.'?'.$searchUrl.'page='; //LINKS KAS TIKS ATVĒRTS KAD NOSPIEDĪS UZ LAPAS, RAKSTĪT LĪDZ page 
	$query = "SELECT * FROM cargo_header WHERE ((status='20' OR status='30') OR rowSent='1' && status!='40') ".$search." ".$client."";  //NEPIECIEŠAMAIS VAICĀJUMS
	list($page_menu, $result_all, $resultGL7) = pageing_menu($offset, $rec_limit, $max_pages, $link_to, $page, $conn, $query);  //TIEK IEGŪTS ARRAY KO UZŅEM AR LIST 

	echo $page_menu;   //IZVADA TABULU AR LAPĀM			
	
	$count_GL7 = mysqli_num_rows($resultGL7);  //IEGŪST SKAITU 					
		if ($count_GL7!=0){

			echo '	<div class="table-responsive"><table class="table table-hover table-responsive" border="1" style="border: 1px solid #ddd !important;"><thead><tr>
						<th>piegādes dat.</th>
						<th>dokumenta nr.</th>
						<th>pavadzīmes nr.</th>
						<th>klienta kods - nosaukums</th>				
						<th>īpašnieka kods - nosaukums</th>
						<th>statuss</th>
					</tr></thead><tbody>';
			while($row = mysqli_fetch_array($resultGL7)){

				echo '	<tr class="classlistedit" onclick="newDoc('.$row['id'].')">
							<td>';
							if($row['deliveryDate']!='0000-00-00 00:00:00'){echo date('d.m.Y', strtotime($row['deliveryDate']));}
							echo '
							<td>'.$row['cargoCode'].'
							<td>'.$row['ladingNr'].'
							<td>'.$row['clientCode'].' - '.$row['clientName'].'				
							<td>'.$row['ownerCode'].' - '.$row['ownerName'].'
							<td>'.returnCargoStatus($conn, $row['id']).'
						</tr>';
			}
			
			echo '</tbody></table></div>';
		}else{
			echo '<i class="glyphicon glyphicon-ban-circle glyphicon-lg" style="color: red;"></i> nav neviena ieraksta!';
		}
	mysqli_close($conn);	
}

if($view=='cargo'){
	
?>
<script>
$(function() {
	$(".paging").delegate("a", "click", function(event) {	
		var url = $(this).attr('href');
		

		$('#contenthere').load('/pages/release/'+url);
		event.preventDefault();
	});
});
</script>
<?php

	require('../../inc/s.php');

	if($cargo){$filterCargo = ' AND issuance_doc.issuance_id="'.$cargo.'"';}else{$filterCargo = null;}
	if($client){$filterCust = ' AND issuance_doc.clientCode="'.$client.'"';}else{$filterCust = null;}	

	if($_POST['name']){
		$s = mysqli_real_escape_string($conn, $_POST['name']);	
		$search = " AND (issuance_id LIKE '%$s%' || cargo LIKE '%$s%' || issueDate LIKE '%$s%' || actualDate LIKE '%$s%' || brigade LIKE '%$s%' || date LIKE '%$s%' || time_from LIKE '%$s%' || time_to LIKE '%$s%' || place LIKE '%$s%' || transport LIKE '%$s%')";
		$searchUrl = '&search='.$s;
			
	}else{
		$searchUrl = null;	
		$search = "";
	
	}
	
	$keepFilter=null;
	if($cargo){$keepFilter.='&cargo='.$cargo;}
	if($client){$keepFilter.='&cust='.$client;}	
	
	$rec_limit = $a_p_l; $offset=0; $max_pages = 10;  //DEFINĒJAM LIMITUS
	$link_to = $page_file.'?view='.$searchUrl.$keepFilter.'&page='; //LINKS KAS TIKS ATVĒRTS KAD NOSPIEDĪS UZ LAPAS, RAKSTĪT LĪDZ page 


	$query = "
		SELECT issuance_doc.* , 
		 (SELECT SUM(cargo_line.issueAmount) FROM cargo_line WHERE issuance_doc.issuance_id=cargo_line.issuance_id) AS total, 
		 (SELECT SUM(cargo_line.issue_place_count) FROM cargo_line WHERE issuance_doc.issuance_id=cargo_line.issuance_id) AS issue_place_count, 
		 (SELECT SUM(cargo_line.issueGross) FROM cargo_line WHERE issuance_doc.issuance_id=cargo_line.issuance_id) AS issueGross, 
		 (SELECT SUM(cargo_line.issueCubicMeters) FROM cargo_line WHERE issuance_doc.issuance_id=cargo_line.issuance_id) AS issueCubicMeters		
		FROM issuance_doc 	
		WHERE issuance_doc.status='0' ".$search." ".$filterCargo." ".$filterCust."
		ORDER BY CASE WHEN issuance_doc.scanStatus=200 THEN issuance_doc.scanStatus END DESC";  //NEPIECIEŠAMAIS VAICĀJUMS	
		
	list($page_menu, $result_all, $resultGL7) = pageing_menu($offset, $rec_limit, $max_pages, $link_to, $page, $conn, $query);  //TIEK IEGŪTS ARRAY KO UZŅEM AR LIST 

	echo $page_menu;   //IZVADA TABULU AR LAPĀM			
	
	$count_GL7 = mysqli_num_rows($resultGL7);  //IEGŪST SKAITU 					
		if ($count_GL7!=0){

			echo '	<div class="table-responsive"><table class="table table-hover table-responsive" border="1" style="border: 1px solid #ddd !important;"><thead><tr>
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
		}else{
			echo '<i class="glyphicon glyphicon-ban-circle glyphicon-lg" style="color: red;"></i> nav neviena ieraksta!';
		}
			
	mysqli_close($conn);	
}

if($view=='cargoHis'){
	
?>
<script>
$(function() {
	$(".paging").delegate("a", "click", function(event) {	
		var url = $(this).attr('href');
	

		$('#contenthere').load('/pages/release/'+url);
		event.preventDefault();
	});
});
</script>
<?php	
	
	
	require('../../inc/s.php');	
	
	if($cargo){$filterCargo = ' AND issuance_doc.issuance_id="'.$cargo.'"';}else{$filterCargo = null;}
	if($client){$filterCust = ' AND issuance_doc.clientCode="'.$client.'"';}else{$filterCust = null;}	
	
	if($_POST['name']){
		$s = mysqli_real_escape_string($conn, $_POST['name']);	
		$search = " AND (issuance_id LIKE '%$s%' || cargo LIKE '%$s%' || issueDate LIKE '%$s%' || actualDate LIKE '%$s%' || brigade LIKE '%$s%' || date LIKE '%$s%' || time_from LIKE '%$s%' || time_to LIKE '%$s%' || place LIKE '%$s%' || transport LIKE '%$s%' || issuance_act_no LIKE '%$s%')";
		$searchUrl = 'search='.$s.'&';
		
		}else{
		$searchUrl = null;	
		$search = "";
			
	}
	
	
	
	
	$keepFilter=null;
	if($cargo){$keepFilter.='&cargo='.$cargo;}
	if($client){$keepFilter.='&cust='.$client;}
				
	$rec_limit = $a_p_l; $offset=0; $max_pages = 10;  //DEFINĒJAM LIMITUS
	$link_to = $page_file.'?view=history'.$keepFilter.'&'.$searchUrl.'page='; //LINKS KAS TIKS ATVĒRTS KAD NOSPIEDĪS UZ LAPAS, RAKSTĪT LĪDZ page 

	$query = "
		SELECT issuance_doc.* , 
		 (SELECT SUM(cargo_line.issueAmount) FROM cargo_line WHERE issuance_doc.issuance_id=cargo_line.issuance_id) AS total, 
		 (SELECT SUM(cargo_line.issue_place_count) FROM cargo_line WHERE issuance_doc.issuance_id=cargo_line.issuance_id) AS issue_place_count, 
		 (SELECT SUM(cargo_line.issueGross) FROM cargo_line WHERE issuance_doc.issuance_id=cargo_line.issuance_id) AS issueGross, 
		 (SELECT SUM(cargo_line.issueCubicMeters) FROM cargo_line WHERE issuance_doc.issuance_id=cargo_line.issuance_id) AS issueCubicMeters		
		FROM issuance_doc 	
		WHERE issuance_doc.status='10' ".$search." ".$filterCargo." ".$filterCust."";  //NEPIECIEŠAMAIS VAICĀJUMS		
	
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
						echo '<td align="center">
								<a href="print.php?view=tpage&id='.$id.'&eid='.$row['issuance_id'].'" target="_blank"><i class="glyphicon glyphicon-print" style="color: #00B5AD"  title="Tālmaņa lapa"></i></a>							
						</tr>';
			}
			
			echo '</tbody></table></div>';
		}else{
			echo '<i class="glyphicon glyphicon-ban-circle glyphicon-lg" style="color: red;"></i> nav neviena ieraksta!';
		}	
	
	mysqli_close($conn);	
}

if($view=='releaseHis'){
	require('../../inc/s.php');
	
	if($client){
		$client = " AND clientCode='".mysqli_real_escape_string($conn, $client)."'";
	}else{
		$client = null;
	}
	if($_POST['name']){

	$s = mysqli_real_escape_string($conn, $_POST['name']);
	
	
	$search = " AND (deliveryDate LIKE '%$s%' || cargoCode LIKE '%$s%' || ladingNr LIKE '%$s%' || clientCode LIKE '%$s%' || clientName LIKE '%$s%' || ownerCode LIKE '%$s%' || ownerName LIKE '%$s%')";
	$searchUrl = 'search='.$s.'&';
	
	}else{
	$searchUrl = null;	
	$search = "";
		
	}
	
	
	
	$rec_limit = $a_p_l; $offset=0; $max_pages = 10;  //DEFINĒJAM LIMITUS
	$link_to = $page_file.'?'.$searchUrl.'page='; //LINKS KAS TIKS ATVĒRTS KAD NOSPIEDĪS UZ LAPAS, RAKSTĪT LĪDZ page 
	$query = "SELECT * FROM cargo_header WHERE (status='40') ".$search." ".$client."";  //NEPIECIEŠAMAIS VAICĀJUMS
	list($page_menu, $result_all, $resultGL7) = pageing_menu($offset, $rec_limit, $max_pages, $link_to, $page, $conn, $query);  //TIEK IEGŪTS ARRAY KO UZŅEM AR LIST 

	echo $page_menu;   //IZVADA TABULU AR LAPĀM			
	
	$count_GL7 = mysqli_num_rows($resultGL7);  //IEGŪST SKAITU 					
		if ($count_GL7!=0){

			echo '	<div class="table-responsive"><table class="table table-hover table-responsive" border="1" style="border: 1px solid #ddd !important;"><thead><tr>
						<th>piegādes dat.</th>
						<th>dokumenta nr.</th>
						<th>pavadzīmes nr.</th>
						<th>klienta kods - nosaukums</th>				
						<th>īpašnieka kods - nosaukums</th>
						<th>statuss</th>
					</tr></thead><tbody>';
			while($row = mysqli_fetch_array($resultGL7)){

				echo '	<tr class="classlistedit" onclick="newDoc('.$row['id'].')">
							<td>';
							if($row['deliveryDate']!='0000-00-00 00:00:00'){echo date('d.m.Y', strtotime($row['deliveryDate']));}
							echo '
							<td>'.$row['cargoCode'].'
							<td>'.$row['ladingNr'].'
							<td>'.$row['clientCode'].' - '.$row['clientName'].'				
							<td>'.$row['ownerCode'].' - '.$row['ownerName'].'
							<td>'.returnCargoStatus($conn, $row['id']).'
						</tr>';
			}
			
			echo '</tbody></table></div>';
		}else{
			echo '<i class="glyphicon glyphicon-ban-circle glyphicon-lg" style="color: red;"></i> nav neviena ieraksta!';
		}
	mysqli_close($conn);	
}	


// izdošanas sarakstā meklēšana
if($view=='releaseList'){
	require('../../inc/s.php');
	if (isset($_GET['docNr'])){$docNr = htmlentities($_GET['docNr'], ENT_QUOTES, "UTF-8");}
	$docNr = mysqli_real_escape_string($conn, $docNr);
	

	if($_POST['name']){
	$s = mysqli_real_escape_string($conn, $_POST['name']);


	$getLocationNames = mysqli_query($conn, "SELECT id FROM n_location WHERE name LIKE '%$s%'");	
	$gln = mysqli_fetch_array($getLocationNames);	
	
	if($gln['id']){
		$sl = " OR location LIKE '%".$gln['id']."%'";
	}else{
		$sl = null;
	}	

	
	$search = " AND (productNr LIKE '%$s%' OR thisTransport LIKE '%$s%' OR thisDate LIKE '%$s%' OR batchNo LIKE '%$s%' ".$sl.")";	
	}else{
	$search = "";	
	}

	
		echo '
		<div class="table-responsive"><table class="table table-hover table-responsive border="1" style="border: 1px solid #ddd !important;"">
		<thead> 
			<tr>
				<th>produkta nr</th>
				<th>partijas nr.</th>
				<th>noliktava</th>
				<th>piegādes dat.</th>
				<th>transporta nr.</th>
				<th>atlikušais daudzums</th>
				<th>mērvienība</th>
				<th>daudzums<br>izdošanas</th>

				<th>atlikušais palīg mērv. daudzums</th>
				<th>palīg mērvienība</th>
				<th>palīg mērv. daudzums<br>izdošanai</th>

				<th>izdošanas<br>datums</th>
				
				<th>darbība</th>				
			</tr>
		</thead>
		<tbody>';

if($issuance){
	$getd = mysqli_query($conn, "SELECT destination FROM issuance_doc WHERE issuance_id='".$issuance."'");
	$getdRow = mysqli_fetch_array($getd);

	$destination = $getdRow['destination'];	
}



		$numz = null;
		if($num){ $numz = " AND cargo_header.acceptance_act_no='".$num."'"; }
		
		$destz = null;
		
		if($destination && $destination!='VISI'){ $destz = " AND substring_index(cargo_line.productNr,' ',1) LIKE '".$destination."%'"; }		
		
		$lines = mysqli_query($conn, "
				SELECT cargo_line.*
				FROM cargo_line 

				LEFT JOIN cargo_header 
				ON cargo_line.docNr=cargo_header.docNr 
				 
				WHERE (cargo_line.status='20' OR cargo_line.status='30')  AND cargo_line.action!='23' AND cargo_line.action!='27'
				".$search." AND cargo_header.clientCode='".$cli."' AND cargo_header.agreements='".$agr."' ".$numz." ".$destz."
				ORDER BY cargo_line.productNr, cargo_line.location, cargo_line.status, cargo_line.id
			");		

if(mysqli_num_rows($lines)>0){		
		$r=0; $k=0;
		while($rowL = mysqli_fetch_array($lines)){
			
			
		if($rowL['status']<20){
			
			echo '	<tr bgcolor="#eee" class="classlistedit">
						<td>'.$rowL['productNr'].'<br>';
						$getNames = mysqli_query($conn, "SELECT name1, name2, unitOfMeasurement FROM n_items WHERE code='".$rowL['productNr']."'");
						$gNrow = mysqli_fetch_array($getNames);
						echo $gNrow['name1'].' '.$gNrow['name2'];
						
						echo '
						<td>'.$rowL['batchNo'].'
						<td>'.$rowL['location'].' - '.returnLocationName($conn, $rowL['location']).'
						<td>'.date('d.m.Y', strtotime($rowL['thisDate'])).'
						<td>'.$rowL['thisTransport'].'
						<td>'.floatval($rowL['amount']).'
						<td>'.$rowL['productUmo'];
						echo '
						<td align="center"> -					
						

						<td>'.floatval($rowL['assistant_amount']).'
						<td>'.$rowL['assistantUmo'];
						echo '
						<td align="center"> -					
						';

						echo '
						<td align="center"> - ';						
						echo '<td>'.returnCargoLineStatus($conn, $rowL['id']);
						  
					echo '						
					</tr>';			
		}else{	
		
			if(($rowL['status']==20)){		
				$r++;
				echo '<input type="hidden" name="lineId[]" value="'.$rowL['id'].'">';
			}
			if(($rowL['status']==30)){		
				$k++;
				echo '<input type="hidden" name="lineIdFinal[]" value="'.$rowL['id'].'">';
			}			
			echo '<input type="hidden" name="docNr[]" value="'.$rowL['docNr'].'">';
			echo '	<tr class="classlistedit">
						<td>
						'.$rowL['productNr'].'<br>';
						$getNames = mysqli_query($conn, "SELECT name1, name2, unitOfMeasurement FROM n_items WHERE code='".$rowL['productNr']."'");
						$gNrow = mysqli_fetch_array($getNames);
						echo $gNrow['name1'].' '.$gNrow['name2'];
						
						echo '
						<td>'.$rowL['batchNo'].'
						<td>'.$rowL['location'].' - '.returnLocationName($conn, $rowL['location']).'
						<td>'.date('d.m.Y', strtotime($rowL['thisDate'])).'
						<td>'.$rowL['thisTransport'].'
						<td>'.floatval($rowL['amount']).'
						<td>'.$rowL['productUmo'];
						
						if(($rowL['status']==20)){
							echo '
							<td>
							<div class="">
							  <input type="text" class="form-control numbersOnly lineAmount'.$rowL['id'].'" name="issueAmount[]" placeholder="daudzums" value="'.floatval($rowL['amount']).'" 
							  
							  type="number" min="1" max="'.floatval($rowL['amount']).'" 
							  step="0.01"
						     onKeyUp="if(this.value>'.floatval($rowL['amount']).'){this.value=\''.floatval($rowL['amount']).'\';}else if(this.value<1){this.value=\'1\';}"
							  
							  
							  
							  oninvalid="this.setCustomValidity(\'šim laukam jābūt aizpildītam\')" oninput="setCustomValidity(\'\')" required
							 ';
							 if($p_edit!='on'){echo ' disabled';}
							 echo' 
							  >
							</div>						
							';
							if($rowL['issueDate']!='0000-00-00 00:00:00'){
								$issueDate = date('d.m.Y', strtotime($rowL['issueDate']));
							}else{
								$issueDate = date('d.m.Y');
							}
							echo '
							<td>
							<div class="">
							  <input type="text" class="form-control datepicker lineDate'.$rowL['id'].'" name="issueDate[]" seconddatepicker" value="'.$issueDate.'" oninvalid="this.setCustomValidity(\'šim laukam jābūt aizpildītam\')" oninput="setCustomValidity(\'\')" required>
							</div>						
							';							
						}
						
						if(($rowL['status']==30)){
							echo '
							<td>
							<div class="">
							  <input type="text" class="form-control lineAmount'.$rowL['id'].'" name="issueAmountFinal[]" placeholder="daudzums" value="'.floatval($rowL['issueAmount']).'" readonly
							  ';
							  if($p_edit!='on'){echo ' disabled';}
							  echo' 							  
							  >
							</div>						
							';
							echo '
							<td>
							<div class="">
							  <input type="text" class="form-control datepicker lineDate'.$rowL['id'].'" name="issueDateFinal[]" value="'.date('d.m.Y', strtotime($rowL['issueDate'])).'" readonly>
							</div>						
							';							
						}
						if(($rowL['status']==40)){
							echo '
							<td>
							<div class="">
							  <input type="text" class="form-control lineAmount'.$rowL['id'].'" placeholder="daudzums" value="'.floatval($rowL['issueAmount']).'" disabled>
							</div>						
							';
							echo '
							<td>
							<div class="">
							  <input type="text" class="form-control datepicker lineDate'.$rowL['id'].'" value="'.date('d.m.Y', strtotime($rowL['issueDate'])).'" disabled>
							</div>						
							';							
						}						
						

						// palīg mērv
						echo '
						<td>'.floatval($rowL['assistant_amount']).'
						<td>'.$rowL['assistantUmo'];
						
						if(($rowL['status']==20)){
							echo '
							<td>
							<div class="">
							  <input type="text" class="form-control numbersOnly lineAmount'.$rowL['id'].'" name="issueAssistantAmount[]" placeholder="palīg mērv. daudzums" value="'.floatval($rowL['assistant_amount']).'" 
							  
							  type="number" min="1" max="'.floatval($rowL['assistant_amount']).'" 
							  step="0.01"
						     onKeyUp="if(this.value>'.floatval($rowL['assistant_amount']).'){this.value=\''.floatval($rowL['assistant_amount']).'\';}else if(this.value<1){this.value=\'1\';}"
							  
							  
							  
							  oninvalid="this.setCustomValidity(\'šim laukam jābūt aizpildītam\')" oninput="setCustomValidity(\'\')" required
							 ';
							 if($p_edit!='on'){echo ' disabled';}
							 echo' 
							  >
							</div>						
							';							
						}
						
						if(($rowL['status']==30)){
							echo '
							<td>
							<div class="">
							  <input type="text" class="form-control lineAmount'.$rowL['id'].'" name="issueAssistantAmount[]" placeholder="palīg mērv. daudzums" value="'.floatval($rowL['issue_assistant_amount']).'" readonly
							  ';
							  if($p_edit!='on'){echo ' disabled';}
							  echo' 							  
							  >
							</div>						
							';							
						}
						if(($rowL['status']==40)){
							echo '
							<td>
							<div class="">
							  <input type="text" class="form-control lineAmount'.$rowL['id'].'" placeholder="palīg mērv. daudzums" value="'.floatval($rowL['issue_assistant_amount']).'" disabled>
							</div>						
							';
						
						}




						
						echo '<td>';
						if(($p_edit=='on')){
							
							
							
						
							if(($rowL['status']==20)){
											echo '
											<a class="btn btn-default btn-xs" id="approveOneLine'.$rowL['id'].'" onclick="approveOneLine('.$rowL['id'].')" style="display: inline-block;"><i class="glyphicon glyphicon-ok" style="color: blue;"></i> nodot</a>';
							}

							if(($rowL['status']==30)){

								echo '<a target="_blank" href="print?view=izs&id='.$row['issuance_id'].'" class="btn btn-default btn-xs" style="margin-right: 2px;"><i class="glyphicon glyphicon-print" style="color: silver;"></i> drukāt</a>';

								echo '<a class="btn btn-default btn-xs" style="margin-right: 2px;" id="cancelOneLine'.$rowL['id'].'" onclick="cancelOneLine('.$rowL['id'].')"><i class="glyphicon glyphicon-ban-circle" style="color: red;"></i> atsaukt</a>';	
								
								echo '<a class="btn btn-default btn-xs" id="receiveOneLine'.$rowL['id'].'" onclick="receiveOneLine('.$rowL['id'].')"><i class="glyphicon glyphicon-ok" style="color: green;"></i> izdot</a>';
							}

							if(($rowL['status']==40)){
								echo returnCargoLineStatus($conn, $rowL['id']);
							}
						

						}else{
							echo returnCargoLineStatus($conn, $rowL['id']);
						}						
						
						  
					echo '						
					</tr>';
					
		}		
			
		}
		
	echo '<input type="hidden" name="issueResult" value="'.$r.'">';
	echo '<input type="hidden" name="issueResultFinal" value="'.$k.'">';	
	echo '</tbody>
	</table>';
}else{
	echo '<td colspan="10"><i class="glyphicon glyphicon-ban-circle glyphicon-lg" style="color: red;"></i> nav neviena ieraksta!</td>';
	echo '</tbody>
	</table>';	
	
}

}		


// uzdošanas sarakstā meklēšana
if($view=='cargoList'){
	require('../../inc/s.php');

?>
<script>
$(function() {
	$(".paging").delegate("a", "click", function(event) {	
		var url = $(this).attr('href');
		
		
			var view = '&view=edit';

			var search = $('#searchWait').val();

			$('#contenthere').load('/pages/release/'+url+''+view+'&id=<?=$eid;?>&showToGive=<?=$showToGive;?>&sr='+search);
		
		event.preventDefault();
	});
});
</script>
<?php

	if (isset($_GET['docNr'])){$docNr = htmlentities($_GET['docNr'], ENT_QUOTES, "UTF-8");}
	$docNr = mysqli_real_escape_string($conn, $docNr);
	
	if (isset($_GET['issuance'])){$issuance = htmlentities($_GET['issuance'], ENT_QUOTES, "UTF-8");}	
	$issuance = mysqli_real_escape_string($conn, $issuance);	
	
	
	if (isset($_GET['estatus'])){$estatus = htmlentities($_GET['estatus'], ENT_QUOTES, "UTF-8");}	
	$estatus = mysqli_real_escape_string($conn, $estatus);
	
	

	if($_POST['name']){
	$s = mysqli_real_escape_string($conn, $_POST['name']);


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

	if($estatus==10){
		$status = " AND cargo_line.status='40' AND cargo_line.issuance_id='".$issuance."'";
	}else{
		$status = " AND (cargo_line.issuance_id='' OR cargo_line.issuance_id='".$issuance."') ";
	}


if($issuance){
	$getd = mysqli_query($conn, "SELECT places, decks, forScan FROM issuance_doc WHERE issuance_id='".$issuance."'");
	$getdRow = mysqli_fetch_array($getd);
}


		echo '
		<div class="table-responsive"><table class="table table-hover table-responsive" border="1" style="border: 1px solid #ddd !important;">
		<thead> 
			<tr>
				<th>darbība  '; echo'</th>
				<th>iekļauts dokumentā</th>';
				if($getdRow['forScan']==1){ echo '<th>novietojums</th>'; }
				
				echo '
				<th>brāķis<br><br></th>
				<th>piegādes<br>dat.</th>
				<th>produkta nr</th>
				
				<th>seriālais nr.</th>
				<th>atlikušais daudzums</th>
				<th>mērvienība</th>
				<th>daudzums<br>izdošanai</th>

				<th>atlikušais palīg mērv. daudzums</th>
				<th>palīg mērvienība</th>
				<th>palīg mērv. daudzums<br>izdošanai</th>
				<th>saņ. dok. neto (kg)</th>
				<th>vietu skaits</th>				
				<th>tara (kg)</th>
				<th>bruto (kg)</th>
				<th>neto (kg)</th>
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
				<th>plombes nr.</th>

				<th>svēršanas akta nr.</th>
								
			</tr>
		</thead>
		<tbody>';

		echo '<input type="hidden" name="docNr" value="'.$docNr.'">';


		$forAnd=null;
		if($issuance){
			$getd = mysqli_query($conn, "SELECT destination, status, forScan, places, decks FROM issuance_doc WHERE issuance_id='".$issuance."'");
			$getdRow = mysqli_fetch_array($getd);

			$destination = $getdRow['destination'];
			$issuanceStatus = $getdRow['status'];
			
		}

		$numz = null;
		if($num){ $numz = " AND cargo_header.acceptance_act_no='".$num."'"; }


		$destz = null;
		
		if($destination && $destination!='VISI'){ $destz = " AND substring_index(cargo_line.productNr,' ',1) LIKE '".$destination."%'"; }



		$showToGive = $_GET['showToGive'];
		$includedInDoc = null;

		if($showToGive=='true'){
			$includedInDoc = " AND cargo_line.for_issue = 1 ";
		}	


	$rec_limit = $i_p_l; $offset=0; $max_pages = 10;  //DEFINĒJAM LIMITUS
	$link_to = $page_file.'?page='; //LINKS KAS TIKS ATVĒRTS KAD NOSPIEDĪS UZ LAPAS, RAKSTĪT LĪDZ page 





		
		if($issuanceStatus!=10){
			
			$forAnd= "
					AND (
							(
							
								cargo_line.status = '30' AND EXISTS(SELECT cargo_line.* 
								FROM cargo_line 
								LEFT JOIN cargo_header 
                                ON cargo_line.docnr = cargo_header.docnr 
								WHERE cargo_header.agreements='".$agr."' ".$numz." ".$destz." ".$status." ".$search."  AND cargo_line.action!='23' AND cargo_line.action!='27'
								AND cargo_line.status = '30') 
								
							) OR (
							
								cargo_line.status = '20' AND NOT EXISTS(SELECT cargo_line.* 
                                FROM cargo_line 
                                LEFT JOIN cargo_header 
                                ON cargo_line.docnr = cargo_header.docnr 
								WHERE cargo_header.agreements='".$agr."' ".$numz." ".$destz." ".$status." ".$search."  AND cargo_line.action!='23' AND cargo_line.action!='27'
                                AND cargo_line.status = '30') 
							)
									   
                        )			
			";
		}




		$lines = "
				SELECT cargo_line.*, cargo_header.deliveryDate AS hDeliveryDate
				FROM cargo_line 

				LEFT JOIN cargo_header 
				ON cargo_line.docNr=cargo_header.docNr 
				 
				WHERE cargo_header.clientCode='".$cli."' 
				AND cargo_header.agreements='".$agr."' ".$numz." ".$destz."
				".$status." ".$search." ".$includedInDoc." AND cargo_line.action!='23' AND cargo_line.action!='27'
				
				
				".$forAnd."
						
				ORDER BY cargo_header.deliveryDate				
			";




	list($page_menu, $result_all, $resultGL7) = pageing_menu($offset, $rec_limit, $max_pages, $link_to, $page, $conn, $lines);  //TIEK IEGŪTS ARRAY KO UZŅEM AR LIST 

	echo $page_menu;   //IZVADA TABULU AR LAPĀM			
	
if(mysqli_num_rows($resultGL7)>0){		

		$r=0; $k=0;
		while($rowL = mysqli_fetch_array($resultGL7)){
			
			
		if($rowL['status']<20){
			
		}else{	
		
			if($rowL['status']==40){ $dis = 'disabled'; }else{ $dis = null; }

			if($rowL['status']==20 && $dis!='disabled'){		
				$r++;
				echo '<input type="hidden" name="lineId[]" value="'.$rowL['id'].'">';
			}
			if($rowL['status']==30 && $dis!='disabled'){		
				$k++;
				echo '<input type="hidden" name="lineIdFinal[]" value="'.$rowL['id'].'">';
			}			
		
			echo '	<tr class="classlistedit" id="actionOneLine'.$rowL['id'].'">';



			echo '<td nowrap>';
			if(($p_edit=='on')){
				
			
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

			}else{
				echo returnCargoLineStatus($conn, $rowL['id']);
			}				


			echo '<td>';
				if($rowL['for_issue']==1){ echo '<i class="glyphicon glyphicon-ok" style="color: green;"></i>'; }
			
			if($getdRow['forScan']==1){
				echo '<td>';//novietojums līnijās
				
					
				if($rowL['status']==20 && $rowL['for_issue']==0 && $dis!='disabled'){
					
					$allPlaces = explode(',',$getdRow['places']);
					echo '<select class="form-control selectpicker btn-group-xs input-xs" name="issuePlacement[]" id="issuePlacement'.$rowL['id'].'" data-live-search="true" data-width="80px" title=" " '.$dis.'>';
					for ($x = 1; $x <= 7; $x++) {
						
						$checked_view=null;
						if (in_array('R'.$x, $allPlaces)){
							
							echo '<option value="R'.$x.'" '; if($rowL['placement']=='R'.$x){echo 'selected';} echo '>R'.$x.'</option>';

						}
						
					}
					
					$allDecks = explode(',',$getdRow['decks']);
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
										<td nowrap>'.returnMeWho($rowL['issuanceScannedBy']).'</td>	
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

if(($rowL['status']==20 && $rowL['for_issue']==0)){
	echo '
	<td>
	<div class="">
	  <input type="text" class="form-control numbersOnly"  style="min-width: 70px;" id="issueAmount'.$rowL['id'].'" name="issueAmount[]" placeholder="daudzums" value="'.floatval($rowL['amount']).'" 
	  
	  type="number" min="1" max="'.floatval($rowL['amount']).'" 
	  step="0.01"
	 onKeyUp="if(this.value>'.floatval($rowL['amount']).'){this.value=\''.floatval($rowL['amount']).'\';}else if(this.value<1){this.value=\'1\';}"
	  
	  
	  
	  oninvalid="this.setCustomValidity(\'šim laukam jābūt aizpildītam\')" oninput="setCustomValidity(\'\')" required
	  ';
	  if($p_edit!='on'){echo ' disabled';}
	  echo' 							  
	  >
	</div>						
	';
		
}

if(($rowL['status']==30 || $rowL['for_issue']==1)){
	echo '
	<td>
	<div class="">
	  <input type="text" class="form-control lineAmount'.$rowL['id'].'" style="min-width: 70px;" name="issueAmountFinal[]" placeholder="daudzums" value="'.floatval($rowL['issueAmount']).'" readonly
	  ';
	  if($p_edit!='on'){echo ' disabled';}
	  echo' 							  
	  >
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

if(($rowL['status']==20 && $rowL['for_issue']==0)){
	echo '
	<td>
	<div class="">
	  <input type="text" class="form-control numbersOnly" style="min-width: 70px;" id="issueAssistantAmount'.$rowL['id'].'" name="issueAssistantAmount[]" placeholder="palīg mērv. daudzums" value="'.floatval($rowL['assistant_amount']).'" 
	  
	  type="number" min="1" max="'.floatval($rowL['assistant_amount']).'" 
	  step="0.01"
	 onKeyUp="if(this.value>'.floatval($rowL['assistant_amount']).'){this.value=\''.floatval($rowL['assistant_amount']).'\';}else if(this.value<1){this.value=\'1\';}"
	  
	  
	  
	  oninvalid="this.setCustomValidity(\'šim laukam jābūt aizpildītam\')" oninput="setCustomValidity(\'\')" required
	  ';
	  if($p_edit!='on'){echo ' disabled';}
	  echo' 							  
	  >
	</div>						
	';
		
}

if(($rowL['status']==30 || $rowL['for_issue']==1)){
	echo '
	<td>
	<div class="">
	  <input type="text" class="form-control lineAmount'.$rowL['id'].'" style="min-width: 70px;" name="issueAssistantAmountFinal[]" placeholder="plīg mērv. daudzums" value="'.floatval($rowL['issue_assistant_amount']).'" readonly
	  ';
	  if($p_edit!='on'){echo ' disabled';}
	  echo' 							  
	  >
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

if($rowL['status']==30 || $rowL['for_issue']==1){
	echo ' value="'.floatval($rowL['issue_place_count']).'" readonly';
}
echo ' '.$dis.'>';

echo '
<td>
<input 

type="text" min="1" max="'.floatval($rowL['tare']).'" 
step="0.01"
onKeyUp="if(this.value>'.floatval($rowL['tare']).'){this.value=\''.floatval($rowL['tare']).'\';}else if(this.value<1){this.value=\'1\';}"

class="form-control numbersOnly"  style="min-width: 70px;" placeholder="tara" id="eTare'.$rowL['id'].'" name="eTare[]" '; 
if($rowL['status']==20 && $rowL['for_issue']==0){
	echo ' value="'.floatval($rowL['tare']).'"';
}

if($rowL['status']==40 && $rowL['for_issue']==0){
	echo ' value="'.floatval($rowL['tare']).'"';
}

if($rowL['status']==30 || $rowL['for_issue']==1){
	echo ' value="'.floatval($rowL['issueTare']).'" readonly';
} 
echo ' '.$dis.'>';

echo '
<td>
<input 

type="text" min="1" max="'.floatval($rowL['gross']).'" 
step="0.01"
onKeyUp="if(this.value>'.floatval($rowL['gross']).'){this.value=\''.floatval($rowL['gross']).'\';}else if(this.value<1){this.value=\'1\';}"

class="form-control numbersOnly"  style="min-width: 70px;" placeholder="bruto" id="eGross'.$rowL['id'].'" name="eGross[]" '; 

if($rowL['status']==20 && $rowL['for_issue']==0){
	echo ' value="'.floatval($rowL['gross']).'"';
}

if($rowL['status']==40 && $rowL['for_issue']==0){
	echo ' value="'.floatval($rowL['gross']).'"';
}

if($rowL['status']==30 || $rowL['for_issue']==1){
	echo ' value="'.floatval($rowL['issueGross']).'" readonly';
} echo ' '.$dis.'>';


echo '
<td>
<input 

type="text" min="1" max="'.floatval($rowL['net']).'" 
step="0.01"
onKeyUp="if(this.value>'.floatval($rowL['net']).'){this.value=\''.floatval($rowL['net']).'\';}else if(this.value<1){this.value=\'0\';}"

class="form-control numbersOnly" style="min-width: 70px;" placeholder="neto" id="eNet'.$rowL['id'].'" name="eNet[]" '; 

if($rowL['status']==20 && $rowL['for_issue']==0){
	echo ' value="'.floatval($rowL['net']).'"';
}

if($rowL['status']==40 && $rowL['for_issue']==0){
	echo ' value="'.floatval($rowL['net']).'"';
}

if($rowL['status']==30 || $rowL['for_issue']==1){
	echo ' value="'.floatval($rowL['issueNet']).'" readonly';
} echo ' '.$dis.'>';

echo '<td><input type="text" class="form-control '.$rowL['productUmo'].'dn" style="min-width: 70px;" placeholder="saņ. Δ neto" id="eDeltaNet'.$rowL['id'].'"  value="'.floatval($rowL['delta_net']).'" disabled>';

echo '
<td>
<input 

type="text" min="1" max="'.floatval($rowL['cubicMeters']).'" 
step="0.01"
onKeyUp="if(this.value>'.floatval($rowL['cubicMeters']).'){this.value=\''.floatval($rowL['cubicMeters']).'\';}else if(this.value<1){this.value=\'1\';}"

class="form-control numbersOnly" style="min-width: 70px;" placeholder="m3" id="eCubicMeters'.$rowL['id'].'" name="eCubicMeters[]" '; 

if($rowL['status']==20 && $rowL['for_issue']==0){
	echo ' value="'.floatval($rowL['cubicMeters']).'"';
}

if($rowL['status']==40 && $rowL['for_issue']==0){
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
						echo '
						<td>'.$rowL['thisTransport'].'
						<td><input type="text" class="form-control" style="min-width: 100px;" placeholder="transporta nr." id="issueThisTransport'.$rowL['id'].'" name="issueThisTransport[]" ';
						
						if($rowL['status']==20 && $rowL['for_issue']==0){
							echo ' value="'.$rowL['thisTransport'].'"';
						}

						if($rowL['status']==40 && $rowL['for_issue']==0){
							echo ' value="'.$rowL['thisTransport'].'"';
						}

						if($rowL['status']==30 || $rowL['for_issue']==1){
							echo ' value="';
							if($rowL['issue_thisTransport']==''){echo $thisTransport;}else{echo $rowL['issue_thisTransport'];}
							echo '" readonly';
						}						
						
						echo ' '.$dis.'>

						<td><input type="text" class="form-control" style="min-width: 100px;" placeholder="deklarācijas nr." id="issueDeclarationTypeNo'.$rowL['id'].'" name="issueDeclarationTypeNo[]" ';
						
						
						if($rowL['status']==20 && $rowL['for_issue']==0){
							echo ' value="'.$rowL['declaration_type_no'].'"';
						}

						if($rowL['status']==40 && $rowL['for_issue']==0){
							echo ' value="'.$rowL['declaration_type_no'].'"';
						}

						if($rowL['status']==30 || $rowL['for_issue']==1){
							echo ' value="';
							if($rowL['issue_declaration_type_no']==''){echo $declaration_type_no;}else{echo $rowL['issue_declaration_type_no'];}
							echo '" readonly';
						}						
						
						echo ' '.$dis.'>';

						echo '<td>'.$rowL['cargo_status'].'</td>';

						echo '<td>'.$rowL['seal_no'].'</td>';
						echo '<td>'.$rowL['weighing_act_no'].'</td>';
					
						  
					echo '						
					</tr>';
					
		}		
			
		}
if($p_edit=='on' && $dis!='disabled'){		
	$knowIt = mysqli_query($conn, "SELECT * FROM issuance_doc WHERE id='".intval($eid)."'");

	$kIrow = mysqli_fetch_array($knowIt);	

	echo '<input type="hidden" id="issueDateFinal" name="issueDateFinal" value="'.date('d.m.Y', strtotime($kIrow['issueDate'])).'">';
	echo '<input type="hidden" id="actualDateFinal" name="actualDateFinal" value="'.date('d.m.Y', strtotime($kIrow['actualDate'])).'">';
	echo '<input type="hidden" id="issuance_id" name="issuance_id" value="'.$kIrow['issuance_id'].'">';
	
	echo '<input type="hidden" name="issueResult" value="'.$r.'">';
	echo '<input type="hidden" name="issueResultFinal" value="'.$k.'">';	
	echo '</tbody>
	</table>';
	
	
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
	
	
	
	
	
	
	
	
}else{
	echo '<td colspan="10"><i class="glyphicon glyphicon-ban-circle glyphicon-lg" style="color: red;"></i> nav neviena ieraksta!</td>';
	echo '</tbody>
	</table>';	
	
}

}		
?>
<script>
$(document).ready(function() {
   $('.selectpicker').selectpicker();
});
</script>
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