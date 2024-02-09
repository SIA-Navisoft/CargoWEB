<?php
error_reporting(E_ALL ^ E_NOTICE);
require('../../lock.php');

$page_file="release";


require('../../inc/s.php');
include('../../functions/base.php');

if (isset($_GET['view'])){$view = htmlentities($_GET['view'], ENT_QUOTES, "UTF-8");}
$id = intval($_GET['id']);


if (isset($_GET['s'])){$s = htmlentities($_GET['s'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['issuanceId'])){$issuanceId = htmlentities($_GET['issuanceId'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['issuanceStatus'])){$issuanceStatus = htmlentities($_GET['issuanceStatus'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['acceptanceActNo'])){$acceptanceActNo = htmlentities($_GET['acceptanceActNo'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['clientCode'])){$clientCode = htmlentities($_GET['clientCode'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['agreements'])){$agreements = htmlentities($_GET['agreements'], ENT_QUOTES, "UTF-8");}







	if($sr){
		$s = mysqli_real_escape_string($conn, $sr);	
		$search = " AND (cargo_line.productNr LIKE '%$s%' || cargo_line.thisTransport LIKE '%$s%' || cargo_line.thisDate LIKE '%$s%' || cargo_line.batchNo LIKE '%$s%')";	
	}else{
		$search = "";	
	} 

	if($dis=='disabled'){ 
		$showHi = "cargo_line.status='40' AND cargo_line.issuance_id='".$issuanceId."'"; 
	}else{ 
		$showHi = "(cargo_line.status='20' OR cargo_line.status='30') AND (cargo_line.issuance_id='' OR cargo_line.issuance_id='".$issuanceId."')"; 
	}
	
	$numz = null;
	if($acceptanceActNo){ $numz = " AND cargo_header.acceptance_act_no='".$acceptanceActNo."'"; }




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

					WHERE ".$showHi." ".$search." AND cargo_header.clientCode='".$clientCode."' AND cargo_header.agreements='".$agreements."' ".$numz." AND cargo_line.action!='23' AND cargo_line.action!='27'
					AND cargo_line.for_issue=1

					GROUP BY cargo_line.productUmo
		
			") or die (mysqli_error($conn));

	if(mysqli_num_rows($inCargo)>0){


		$getd = mysqli_query($conn, "SELECT `resource` FROM issuance_doc WHERE issuance_id='".$issuanceId."'");
		$getdRow = mysqli_fetch_array($getd);		
	
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

					if($getdRow['resource']=='BERKR' || $getdRow['resource']=='BERKRSAN'){
						echo '
						<th>faktiskais T</th>
						<th>Δ T</th>';
					}

					echo '
					<th nowrap>saņ. Δ neto (kg)</th>
					<th nowrap>apjoms (m3)</th>
				</thead>
				<tbody>
				';			
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
							
							if($icRow['status']==20 && $icRow['for_issue']==0){
								echo floatval($icRow['pc']);
							}
							
							if($icRow['status']==40 && $icRow['for_issue']==0){
								echo floatval($icRow['pc']);
							}
							
							if($icRow['status']==30 || $icRow['for_issue']==1){
								echo floatval($icRow['ipc']);
							}

							echo '</td>';							
							
							echo '<td>';
							
							if($icRow['status']==20 && $icRow['for_issue']==0){
								echo floatval($icRow['t']);
							}
							
							if($icRow['status']==40 && $icRow['for_issue']==0){
								echo floatval($icRow['t']);
							}
							
							if($icRow['status']==30 || $icRow['for_issue']==1){
								echo floatval($icRow['it']);
							}

							echo '</td>
							<td>';
							
							if($icRow['status']==20 && $icRow['for_issue']==0){
								echo floatval($icRow['b']);
							}
							
							if($icRow['status']==40 && $icRow['for_issue']==0){
								echo floatval($icRow['b']);
							}
							
							if($icRow['status']==30 || $icRow['for_issue']==1){
								echo floatval($icRow['ib']);
							}						

							echo '</td>
							<td>';
							
							if($icRow['status']==20 && $icRow['for_issue']==0){
								echo floatval($icRow['n']);
							}
							
							if($icRow['status']==40 && $icRow['for_issue']==0){
								echo floatval($icRow['n']);
							}
							
							if($icRow['status']==30 || $icRow['for_issue']==1){
								echo floatval($icRow['isn']);
							}

							echo '</td>';

							if($getdRow['resource']=='BERKR' || $getdRow['resource']=='BERKRSAN'){
								echo '
								<td>'.floatval($icRow['ffd']).'</td>
								<td>'.floatval($icRow['rd']).'</td>';
							}

							echo '
							<td>'.floatval($icRow['dn']).'</td>




							<td>';
							
							if($icRow['status']==20 && $icRow['for_issue']==0){
								echo floatval($icRow['a']);
							}
							
							if($icRow['status']==40 && $icRow['for_issue']==0){
								echo floatval($icRow['a']);
							}
							
							if($icRow['status']==30 || $icRow['for_issue']==1){
								echo floatval($icRow['ia']);
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