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

$r = $id = null;
if (isset($_GET['r'])){$r = htmlentities($_GET['r'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['id'])){$id = htmlentities($_GET['id'], ENT_QUOTES, "UTF-8");}


include('../../functions/base.php');
require('../../inc/s.php');


	if(checkScannedIssuanceStatus($conn, safeHTML($_POST['issuance_id']))==100 || checkScannedIssuanceStatus($conn, $id)==100){
		
		echo 'STOP';
		die();
		
		
	}





if($r=='issuanceDoc'){
	
	

	$lid=findLastRow("issuance_doc");

	$lid=$lid+1;
    
	$issuance_id = 'ID'.sprintf("%'05d\n", $lid);
	$issuance_id = trim($issuance_id);

	$getCargo = 'I'.date('Y').''.sprintf("%'05d\n", $lid);

	$issueDate = strtr($_POST['issueDate'], '.', '-');
	$issueDate = date('Y-m-d', strtotime($issueDate));	

	$actualDate = strtr($_POST['actualDate'], '.', '-');
	$actualDate = date('Y-m-d', strtotime($actualDate));

	$date = strtr($_POST['date'], '.', '-');
	$date = date('Y-m-d', strtotime($date));
	
	$applicationDate = strtr($_POST['applicationDate'], '.', '-');
	$applicationDate = date('Y-m-d', strtotime($applicationDate));

	$form_data = array(
	'issuance_id' => safeHTML($issuance_id),
	'docNr' => safeHTML($_POST['cargo']),
	'agreements' => safeHTML($_POST['agreements']),
	'clientCode' => safeHTML($_POST['clientCode']),
	'cargo' => $getCargo,
	'issueDate' => safeHTML($issueDate),
	'actualDate' => safeHTML($actualDate),
	'brigade' => safeHTML($_POST['brigade']),
	'date' => safeHTML($date),
	'time_from' => safeHTML($_POST['time_from']),
	'time_to' => safeHTML($_POST['time_to']),
	'place' => safeHTML($_POST['place']),
	'transport' => safeHTML($_POST['transport']),	
	'transport_name' => safeHTML($_POST['transportName']),

	'manifest_no' => safeHTML($_POST['manifestNo']),	
	'issuance_act_no' => safeHTML($_POST['issuanceActNo']),
	'receiverCode' => safeHTML($_POST['receiverCode']),
	'receiverName2' => safeHTML($_POST['receiverName2']),
	'receiverCountry' => safeHTML($_POST['receiverCountry']),	
	'resource' => safeHTML($_POST['resource']),
	'acceptance_act_no' => safeHTML($_POST['thatNumber']),
	'application_no' => safeHTML($_POST['applicationNo']),
	'applicationDate' => $applicationDate,

	'createdBy' => $myid,
	'createdDate' => date('Y-m-d H:i:s'),
	'thisTransport' => safeHTML($_POST['thisTransport']),
	'declaration_type_no' => safeHTML($_POST['declarationTypeNo']),
	'cargo_status' => safeHTML($_POST['cargoStatus']),
	'places' => 'R1,R2,R3,R4,R5,R6,R7',
	'decks' => 'K1,K2,K3,K4,K5,K6,K7'
	
	
	);
	insertNewRows("issuance_doc", $form_data);
	
}





if($r=='release_lines'){
	
	if (isset($_GET['action'])){$action = htmlentities($_GET['action'], ENT_QUOTES, "UTF-8");}
	
	$issueResult = safeHTML($_POST['issueResult']);
	$issueResultFinal = safeHTML($_POST['issueResultFinal']);

	if($action=='approveLine'){
	
		if (isset($_GET['liId'])){$liId = htmlentities($_GET['liId'], ENT_QUOTES, "UTF-8");}
	
		if($liId){

				$issueAmount = safeHTML($_POST['issueAmount']);
				$issueAssistantAmount = safeHTML($_POST['issueAssistantAmount']);
				$issueTare = safeHTML($_POST['eTare']);
				$issue_place_count = safeHTML($_POST['e_place_count']);
				$issueGross = safeHTML($_POST['eGross']);
				$issueNet = safeHTML($_POST['eNet']);
				$issueThisTransport = safeHTML($_POST['issueThisTransport']);
				$issueDeclarationTypeNo = safeHTML($_POST['issueDeclarationTypeNo']);

				$eDelta = safeHTML($_POST['eDelta']);
				$eDeltaT = safeHTML($_POST['eDeltaT']);
				
				
				
				$issueDate = strtr($_POST['issueDateFinal'], '.', '-');
				$issueDate = date('Y-m-d', strtotime($issueDate));				

				$actualDate = strtr($_POST['actualDateFinal'], '.', '-');
				$actualDate = date('Y-m-d', strtotime($actualDate));

				$issuance_id = safeHTML($_POST['issuance_id']);		

				$issueCubicMeters = safeHTML($_POST['eCubicMeters']);

				$resource = safeHTML($_POST['resource']);
				$placement = safeHTML($_POST['issuePlacement']);
				
				echo '>>>>>>>>>. '.$resource;

				$getDetails = mysqli_query($conn, "SELECT * FROM cargo_line WHERE id='".intval($liId)."'");
				$gdRow = mysqli_fetch_array($getDetails);

				if($gdRow['amount']==$issueAmount){

				
					$form_data = array(
					'issueAmount' => $issueAmount,
					'issue_assistant_amount' => $issueAssistantAmount,
					'issueBy' => $myid,
					'issueDate' => $issueDate,
					'actualDate' => $actualDate,
					'issuance_id' => $issuance_id,
					
					'for_issue' => 1,

					'issue_place_count' => $issue_place_count,
					'issueTare' => $issueTare,
					'issueGross' => $issueGross,
					'issueNet' => $issueNet,
					'issueCubicMeters' => $issueCubicMeters,

					
					'issue_resource' => $resource,
					'issue_thisTransport' => $issueThisTransport,
					'issue_weighing_act_no' => $gdrow['issue_weighing_act_no'],
					'issue_container_type_no' => $gdrow['issue_container_type_no'],
					'issue_declaration_type_no' => $issueDeclarationTypeNo,
					'issue_cargo_status' => $gdrow['issue_cargo_status'],
					'issue_seal_no' => $gdrow['issue_seal_no'],
					'issue_lot_no' => $gdrow['issue_lot_no'],
					'fact_for_delta' => $eDelta,
					'real_delta' => $eDeltaT,
					'issuePlacement' => $placement

					

					);
					updateSomeRow("cargo_line", $form_data, "WHERE id='".intval($liId)."' LIMIT 1");	
			
					
				}

				if($gdRow['amount']!=$issueAmount && $issueAmount!=0){
					
					
					$form_data = array(
					'issueAmount' => $issueAmount,
					'issue_assistant_amount' => $issueAssistantAmount,
					'issueBy' => $myid,
					'issueDate' => $issueDate,
					'actualDate' => $actualDate,
					'issuance_id' => $issuance_id,
					
					'for_issue' => 1,

					'issue_place_count' => $issue_place_count,
					'issueTare' => $issueTare,
					'issueGross' => $issueGross,
					'issueNet' => $issueNet,
					'issueCubicMeters' => $issueCubicMeters,

					'issue_resource' => $resource,
					'issue_thisTransport' => $issueThisTransport,
					'issue_weighing_act_no' => $gdrow['issue_weighing_act_no'],
					'issue_container_type_no' => $gdrow['issue_container_type_no'],
					'issue_declaration_type_no' => $issueDeclarationTypeNo,
					'issue_cargo_status' => $gdrow['issue_cargo_status'],
					'issue_seal_no' => $gdrow['issue_seal_no'],
					'issue_lot_no' => $gdrow['issue_lot_no'],
					'fact_for_delta' => $eDelta,
					'real_delta' => $eDeltaT,
					'issuePlacement' => $placement					
					);
					updateSomeRow("cargo_line", $form_data, "WHERE id='".intval($liId)."' LIMIT 1");	
					
				
				}			
			
			
			
		}
	
		if(!$liId){

				$issuance_id = safeHTML($_POST['issuance_id']);
				if($issuance_id){
					
					$form_data = array(
						'status' => 30
					);
					updateSomeRow("cargo_line", $form_data, "WHERE issuance_id='".$issuance_id."' AND for_issue='1'");

				}	

			}	

	}//approveLine
	
	
	if($action=='cancelLine'){

		if (isset($_GET['liId'])){$liId = htmlentities($_GET['liId'], ENT_QUOTES, "UTF-8");}
	
		if($liId){

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
			'issuePlacement' => ''		

			);
			updateSomeRow("cargo_line", $form_data, "WHERE id='".$liId."' AND for_issue='1'");	
	
		}

		if(!$liId){

				$issuance_id = safeHTML($_POST['issuance_id']);
			
			if($issuance_id){
				$form_data = array(
				
					'status' => 20

				);				
				
				updateSomeRow("cargo_line", $form_data, "WHERE issuance_id='".$issuance_id."' AND status='30' AND for_issue='1'");
				
			}				
			
		}
	
	}

	if($action=='receiveLine'){
		
		$issuance_id = safeHTML($_POST['issuance_id']);


		$checkIssueance = mysqli_query($conn, "SELECT issueDate, actualDate, resource, thisTransport FROM issuance_doc WHERE issuance_id='".$issuance_id."'");
		
		if(mysqli_num_rows($checkIssueance)==1){
			
			$iRow = mysqli_fetch_array($checkIssueance);
			
			
			$issueDateFinal = date('Y-m-d', strtotime($iRow['issueDate']));
			$actualDateFinal = date('Y-m-d', strtotime($iRow['actualDate']));

			$resource = mysqli_real_escape_string($conn, $iRow['resource']);
			
			$header_thisTransport = $iRow['thisTransport'];
			$header_declaration_type_no = $iRow['declaration_type_no'];
				
				
			$query = mysqli_query($conn, "SELECT * FROM cargo_line WHERE issuance_id='".$issuance_id."'");	
				
			$linesForUpdate = mysqli_num_rows($query);	
				
			$linesForUpdateActual = 0;	
			while($row = mysqli_fetch_array($query)){

				$lineIdFinal = $row['id'];	

				if($lineIdFinal){

					$issueAmountFinal = $row['issueAmount'];
					$issueAssistantAmountFinal = $row['issue_assistant_amount'];

					$e_place_count = $row['issue_place_count'];
					$eTare = $row['issueTare'];
					$eGross = $row['issueGross'];
					$eNet = $row['issueNet'];
					$eCubicMeters = $row['issueCubicMeters'];

					if(floatval($row['amount'])==$issueAmountFinal && date('Y-m-d', strtotime($row['issueDate']))==$issueDateFinal){

						$itt = null;
						if($row['issue_thisTransport']==''){
							$itt = $header_thisTransport;
						}else{
							$itt = $row['issue_thisTransport'];
						}
						
						$idtn = null;
						if($row['issue_declaration_type_no']==''){
							$idtn = $header_declaration_type_no;
						}else{
							$idtn = $row['issue_declaration_type_no'];
						}						
					
						$form_data = array(
						'issue_thisTransport' => $itt,
						'issue_declaration_type_no' => $idtn,
						'status' => 40,
						'action' => 40
						);
						updateSomeRow("cargo_line", $form_data, "WHERE id='".intval($lineIdFinal)."' LIMIT 1");	
						
						$getLines = mysqli_query($conn, "
						SELECT l.docNr AS docNr, l.productNr AS productNr, l.location AS location, h.clientCode AS clientCode, 
							   l.productUmo AS productUmo, l.assistantUmo AS assistantUmo, 
							   
							   l.tare AS tare, l.gross AS gross, l.net AS net, l.cubicMeters AS cubicMeters, l.serialNo AS serialNo, l.batchNo AS batchNo, 
							   l.lot_no AS lot_no, l.container_type_no AS container_type_no, l.thisTransport AS thisTransport, l.declaration_type_no AS declaration_type_no,
							   l.cargo_status AS cargo_status, l.seal_no AS seal_no, l.weighing_act_no AS weighing_act_no,
							 
							   l.issueTare AS issueTare, l.issue_place_count AS issue_place_count, l.issueGross AS issueGross, l.issueNet AS issueNet, l.issueCubicMeters AS issueCubicMeters,
							   l.issue_lot_no AS issue_lot_no, l.issue_container_type_no AS issue_container_type_no, l.issue_thisTransport AS issue_thisTransport,
							   l.issue_declaration_type_no AS issue_declaration_type_no, l.issue_cargo_status AS issue_cargo_status, l.issue_seal_no,
							   l.issue_weighing_act_no AS issue_weighing_act_no, l.fact_for_delta, l.real_delta,

							   h.clientName AS clientName, h.ownerCode AS ownerCode, 
							   h.ownerName AS ownerName, h.receiverCode AS receiverCode, h.receiverName AS receiverName, h.cargoCode AS cargoCode, 
							   h.deliveryDate AS deliveryDate
						FROM cargo_line AS l
						JOIN cargo_header AS h
						ON l.docNr=h.docNr
						WHERE l.id='".intval($lineIdFinal)."'
						");
						while($gl = mysqli_fetch_array($getLines)){

							$form_data = array(
							'docNr' => $gl['docNr'],
		
							'cargoCode' => $gl['cargoCode'],	
							'productNr' => $gl['productNr'],
							'deliveryDate' => $gl['deliveryDate'],
							'activityDate' => $issueDateFinal,
							'type' => 'negative',
							'amount' =>  $issueAmountFinal,
							'assistant_amount' =>  $issueAssistantAmountFinal,
							'location' =>  $gl['location'],


							'clientCode' => $gl['clientCode'],
							'clientName' => $gl['clientName'],
							'ownerCode' => $gl['ownerCode'],
							'ownerName' => $gl['ownerName'],
							'receiverCode' => $gl['receiverCode'],
							'receiverName' => $gl['receiverName'],	


							'enteredDate' => date('Y-m-d H:i:s'),
							'orgLine' => intval($lineIdFinal),
							'enteredBy' => $myid,
							'action' => 40,
							'productUmo' => $gl['productUmo'],
							'assistantUmo' => $gl['assistantUmo'],
							'status' => 40,
							'resource' => $resource,
							'place_count' => $gl['issue_place_count'],
							'tare' => $gl['issueTare'],
							'gross' => $gl['issueGross'],
							'net' => $gl['issueNet'],
							'cubicMeters' => $gl['issueCubicMeters'],
							'serialNo' => $gl['serialNo'],
							
							'batchNo' => $gl['batchNo'],
							'lot_no' => $gl['issue_lot_no'],
							
							
							'container_type_no' => $gl['issue_container_type_no'],
							'thisTransport' => $gl['issue_thisTransport'],
							'declaration_type_no' => $gl['issue_declaration_type_no'],
							'cargo_status' => $gl['issue_cargo_status'],
							'seal_no' => $gl['issue_seal_no'],
							'weighing_act_no' => $gl['issue_weighing_act_no'],
							'issuance_id' => $issuance_id,
							'fact_for_delta' => $gl['fact_for_delta'],
							'real_delta' => $gl['real_delta']
							);
							insertNewRows("item_ledger_entry", $form_data);	
							
						}//while

						$linesForUpdateActual++;
				
						echo $lineIdFinal.' finish all ';
					} // no change values

					if(floatval($row['amount'])!=$issueAmountFinal && $issueAmountFinal!=0 && date('Y-m-d', strtotime($row['issueDate']))==$issueDateFinal){
							

						$form_data = array(
						'issueAmount' => '',
						'issueBy' => $myid,
						'issueDate' => $issueDateFinal,
						'actualDate' => $actualDateFinal,
						'amount' => $row['amount']-$issueAmountFinal,
						'assistant_amount' => $row['assistant_amount']-$issueAssistantAmountFinal,

						'place_count' => $row['place_count']-$e_place_count,
						'tare' => $row['tare']-$eTare,
						'gross' => $row['gross']-$eGross,
						'net' => $row['net']-$eNet,
						'cubicMeters' => $row['cubicMeters']-$eCubicMeters, //uzliku eCubicMeters bija eNet??



						'issuance_id' => '',
						'status' => 20
						);
						updateSomeRow("cargo_line", $form_data, "WHERE id='".intval($lineIdFinal)."' LIMIT 1");						
							

						$itt = null;
						if($row['issue_thisTransport']==''){
							$itt = $header_thisTransport;
						}else{
							$itt = $row['issue_thisTransport'];
						}
						
						$idtn = null;
						if($row['issue_declaration_type_no']==''){
							$idtn = $header_declaration_type_no;
						}else{
							$idtn = $row['issue_declaration_type_no'];
						}							
							
							
						$query = "
						INSERT INTO cargo_line
						  ( 
						  docNr,
						  batchNo,
						  serialNo,
						  productNr,
						  productUmo,
						  assistantUmo,
						  transportNo,
						  activityDate,
						  amount,
						  issueAmount,
						  assistant_amount,
						  issue_assistant_amount,
						  volume,
						  enteredDate,
						  location,
						  enteredBy,
						  thisDate,
						  issueBy,
						  issueDate,
						  actualDate,
						  resource,
						  issue_resource,
						  thisTransport,
						  issue_thisTransport,
						  status,
						  editedBy,
						  editedDate,
						  orgLine,
						  issuance_id,
						  place_count,
						  tare,
						  gross,
						  net,
						  cubicMeters,
						  weighing_act_no,
						  container_type_no,
						  declaration_type_no,
						  issue_declaration_type_no,
						  cargo_status,
						  seal_no,
						  lot_no,
						  for_issue						  
						  )
						SELECT
						
						  docNr,
						  batchNo,
						  serialNo,
						  productNr,
						  productUmo,
						  assistantUmo,
						  transportNo,
						  activityDate,
						  '".$issueAmountFinal."',
						  '".$issueAmountFinal."',
						  '".$issueAssistantAmountFinal."',
						  '".$issueAssistantAmountFinal."',
						  volume,
						  enteredDate,
						  location,
						  enteredBy,
						  thisDate,
						  issueBy,
						  issueDate,
						  actualDate,
						  '".$resource."',
						  '".$resource."',
						  '".$itt."',
						  '".$itt."',
						  '40',
						  '".$myid."',
						  '".date('Y-m-d H:i:s')."',
						  '".intval($lineIdFinal)."',
						  '".$issuance_id."',
						  issue_place_count,
						  issueTare,
						  issueGross,
						  issueNet,
						  issueCubicMeters,
						  issue_weighing_act_no,
						  issue_container_type_no,
						  '".$idtn."',
						  '".$idtn."',
						  issue_cargo_status,
						  issue_seal_no,
						  issue_lot_no,
						  for_issue

						   
						FROM cargo_line WHERE id='".intval($lineIdFinal)."'";
						mysqli_query($conn, $query) or die(mysqli_error($conn));
						
			
						
						$getLines = mysqli_query($conn, "
						SELECT l.docNr AS docNr, l.productNr AS productNr, l.location AS location, h.clientCode AS clientCode, l.productUmo AS productUmo, l.assistantUmo AS assistantUmo, 
						
						l.tare AS tare, l.gross AS gross, l.net AS net, l.cubicMeters AS cubicMeters, l.serialNo AS serialNo, l.batchNo AS batchNo, 
						l.lot_no AS lot_no, l.container_type_no AS container_type_no, l.thisTransport AS thisTransport, l.declaration_type_no AS declaration_type_no,
						l.cargo_status AS cargo_status, l.seal_no AS seal_no, l.weighing_act_no AS weighing_act_no,	
						
						l.issueTare AS issueTare, l.issue_place_count AS issue_place_count, l.issueGross AS issueGross, l.issueNet AS issueNet, l.issueCubicMeters AS issueCubicMeters,
						l.issue_lot_no AS issue_lot_no, l.issue_container_type_no AS issue_container_type_no, l.issue_thisTransport AS issue_thisTransport,
						l.issue_declaration_type_no AS issue_declaration_type_no, l.issue_cargo_status AS issue_cargo_status, l.issue_seal_no,
						l.issue_weighing_act_no AS issue_weighing_act_no, l.fact_for_delta, l.real_delta,


						h.clientName AS clientName, h.ownerCode AS ownerCode, h.ownerName AS ownerName, h.receiverCode AS receiverCode, h.receiverName AS receiverName, h.cargoCode AS cargoCode, h.deliveryDate AS deliveryDate
						FROM cargo_line AS l
						JOIN cargo_header AS h
						ON l.docNr=h.docNr
						WHERE l.id='".intval($lineIdFinal)."'
						");
						while($gl = mysqli_fetch_array($getLines)){

						$form_data = array(
						'docNr' => $gl['docNr'],
			
						'cargoCode' => $gl['cargoCode'],	
						'productNr' => $gl['productNr'],
						'deliveryDate' => $gl['deliveryDate'],
						'activityDate' => $issueDateFinal,
						'type' => 'negative',
						'amount' =>  $issueAmountFinal,
						'assistant_amount' =>  $issueAssistantAmountFinal,
						'location' =>  $gl['location'],
						
						
						'clientCode' => $gl['clientCode'],
						'clientName' => $gl['clientName'],
						'ownerCode' => $gl['ownerCode'],
						'ownerName' => $gl['ownerName'],
						'receiverCode' => $gl['receiverCode'],
						'receiverName' => $gl['receiverName'],	

						
						'enteredDate' => date('Y-m-d H:i:s'),
						'orgLine' => intval($lineIdFinal),
						'enteredBy' => $myid,
						'action' => 40,
						'productUmo' => $gl['productUmo'],
						'assistantUmo' => $gl['assistantUmo'],
						'status' => 40,
						'resource' => $resource,
						'place_count' => $gl['issue_place_count'],
						'tare' => $gl['issueTare'],
						'gross' => $gl['issueGross'],
						'net' => $gl['issueNet'],
						'cubicMeters' => $gl['issueCubicMeters'],
						'serialNo' => $gl['serialNo'],
						'batchNo' => $gl['batchNo'],
						'lot_no' => $gl['issue_lot_no'],
						'container_type_no' => $gl['issue_container_type_no'],
						'thisTransport' => $gl['issue_thisTransport'],
						'declaration_type_no' => $gl['issue_declaration_type_no'],
						'cargo_status' => $gl['issue_cargo_status'],
						'seal_no' => $gl['issue_seal_no'],
						'weighing_act_no' => $gl['issue_weighing_act_no'],
						'issuance_id' => $issuance_id,
						'fact_for_delta' => $gl['fact_for_delta'],
						'real_delta' => $gl['real_delta']	

						);
						insertNewRows("item_ledger_entry", $form_data);	


						$form_data2 = array(
							'issue_place_count' => 0,
							'issueTare' => 0,
							'issueGross' => 0,
							'issueNet' => 0,
							'issueCubicMeters' => 0,
						
							
							'issue_weighing_act_no' => '',
							'issue_container_type_no' => '',
							
							'issue_cargo_status' => '',
							'issue_seal_no' => '',
							'issue_lot_no' => '',
							'for_issue' => 0,
							'fact_for_delta' => 0,
							'real_delta' => 0
							);
							updateSomeRow("cargo_line", $form_data2, "WHERE id='".intval($lineIdFinal)."' LIMIT 1");

							
						}					
						
						$linesForUpdateActual++;
						
						echo $lineIdFinal.' finish part ';
					}//values changed

					if(floatval($row['amount'])!=$issueAmountFinal && $issueAmountFinal==0){
						echo $lineIdFinal.' do not change';
					}
					
				}//if($lineIdFinal)	

			}//for loop
			
		}
		
		$checkEid = mysqli_query($conn, "SELECT id FROM cargo_line WHERE status='40' AND issuance_id='".$issuance_id."'");
		if(mysqli_num_rows($checkEid)>0){
			$form_data = array(	
			'status' => 10,
			'editedBy' => $myid,
			'editedDate' => date('Y-m-d H:i:s')
			);
			updateSomeRow("issuance_doc", $form_data, "WHERE issuance_id='".$issuance_id."' LIMIT 1");	
		echo 'issuanceClosed';
		}		
		
		
		for ($i=0; $i <= COUNT($_POST['docNr']); $i++) {
			$docNr = safeHTML($_POST['docNr'][$i]);
			$checkAll = mysqli_query($conn, "SELECT id FROM cargo_line WHERE docNr='".$docNr."' AND status!='40'");
			if(mysqli_num_rows($checkAll)==0){
				
				
				$form_data = array(	
				'status' => 40,
				'lastBy' => $myid,
				'lastDate' => date('Y-m-d H:i:s')
				);
				updateSomeRow("cargo_header", $form_data, "WHERE docNr='".$docNr."' LIMIT 1");			
			}
		}
		
		if($linesForUpdate>0){
			
			$repeated=0;
			$linesForUpdateActualLog=$linesForUpdateActual;
			
			if($linesForUpdate!=$linesForUpdateActual){	

				$plus = 0;
				
				$plus += redoReleaseOnFail($conn, $issuance_id);
				
				
				for ($x = 1; $x <= 3; $x++) {				
					
					$checkIssueance = mysqli_query($conn, "SELECT issuance_id FROM issuance_doc WHERE issuance_id='".$issuance_id."'");
					
					if(mysqli_num_rows($checkIssueance)==1){

						$query = mysqli_query($conn, "SELECT id FROM cargo_line WHERE issuance_id='".$issuance_id."' and status=30");

						if(mysqli_num_rows($checkIssueance)>0){					
							$plus += redoReleaseOnFail($conn, $issuance_id);		
						}else{
							break;
						}

					}else{
						break;
					}
					
				}
				$repeated=1;
				
				$linesForUpdateActual+=$plus;
				
			}
			
			
			$form_data = array(	
			'linesForUpdate' => $linesForUpdate,
			'linesForUpdateActual' => $linesForUpdateActual,

			'linesForUpdateLog' => $linesForUpdate,
			'linesForUpdateActualLog' => $linesForUpdateActualLog,
			'repeated' => $repeated,
			
			
			'issuance_id' => $issuance_id,
			'issuanceBy' => $myid,
			'issuanceDate' => date('Y-m-d H:i:s')
			);
			insertNewRows("release_row_count_logs", $form_data);	
				
		}
		
		
		
		
	}//receiveLine
	

}

if($r=='edit_issuance'){
	
	$issuance_id = safeHTML($_POST['issuance_id']);

	$applicationDate = strtr($_POST['applicationDate'], '.', '-');
	$applicationDate = date('Y-m-d', strtotime($applicationDate));
	
	$i_issueDate = strtr($_POST['i_issueDate'], '.', '-');
	$i_issueDate = date('Y-m-d', strtotime($i_issueDate));

	$i_actualDate = strtr($_POST['i_actualDate'], '.', '-');
	$i_actualDate = date('Y-m-d', strtotime($i_actualDate));

	$i_date = strtr($_POST['date'], '.', '-');
	$i_date = date('Y-m-d', strtotime($i_date));	
	
	$places=$decks=null;
	if(isset($_POST['places'])){
		$places = implode(',', $_POST['places']);
	}
	
	if(isset($_POST['decks'])){
		$decks   = implode(',', $_POST['decks']);
	}
	
	$forScan=0;
	if($_POST['forScan']=='on'){
		$forScan=1;
	}
	
		

	$form_data = array(
	'brigade' => safeHTML($_POST['brigade']),
	'date' => $i_date,
	'time_from' => safeHTML($_POST['time_from']),
	'time_to' => safeHTML($_POST['time_to']),
	'place' => safeHTML($_POST['place']),
	'transport' => safeHTML($_POST['transport']),
	'transport_name' => safeHTML($_POST['transportName']),
	
	'issueDate' => $i_issueDate,
	'actualDate' => $i_actualDate,

	'manifest_no' => safeHTML($_POST['manifestNo']),
	'issuance_act_no' => safeHTML($_POST['issuanceActNo']),
	'receiverCode' => safeHTML($_POST['receiverCode']),
	'receiverName2' => safeHTML($_POST['receiverName2']),
	'receiverCountry' => safeHTML($_POST['receiverCountry']),
	'resource' => safeHTML($_POST['resource']),
	'application_no' => safeHTML($_POST['applicationNo']),
	'applicationDate' => $applicationDate,

	'createdBy' => $myid,
	'createdDate' => date('Y-m-d H:i:s'),
	'thisTransport' => safeHTML($_POST['thisTransport']),
	'declaration_type_no' => safeHTML($_POST['declarationTypeNo']),	
	'cargo_status' => safeHTML($_POST['cargoStatus']),
	'destination' => safeHTML($_POST['destination']),
	'places' => $places,
	'decks' => $decks,
	'forScan' => $forScan
	);
	updateSomeRow("issuance_doc", $form_data, "WHERE issuance_id='".$issuance_id."' LIMIT 1");

	$form_data2 = array(
	'issueDate' => $i_issueDate
	);
	updateSomeRow("cargo_line", $form_data2, "WHERE issuance_id='".$issuance_id."'");		
	
	
	if(isset($_POST['thisTransport'])){
		$result = mysqli_query($conn, "SELECT id FROM cargo_line WHERE issuance_id='".$issuance_id."' AND issue_thisTransport='' AND for_issue=1");
		if(mysqli_num_rows($result)>0){

			$form_data3 = array(
				'issue_thisTransport' => safeHTML($_POST['thisTransport'])
			);
			updateSomeRow("cargo_line", $form_data3, "WHERE issuance_id='".$issuance_id."' AND issue_thisTransport='' AND for_issue=1");
		
		}
	}
	
	if(isset($_POST['declarationTypeNo'])){
		$result = mysqli_query($conn, "SELECT id FROM cargo_line WHERE issuance_id='".$issuance_id."' AND issue_declaration_type_no='' AND for_issue=1");
		if(mysqli_num_rows($result)>0){

			$form_data3 = array(
				'issue_declaration_type_no' => safeHTML($_POST['declarationTypeNo'])
			);
			updateSomeRow("cargo_line", $form_data3, "WHERE issuance_id='".$issuance_id."' AND issue_declaration_type_no='' AND for_issue=1");
		
		}		
	}	
}

if($r=='deleteIt'){

	if (isset($_POST['id'])){$id = htmlentities($_POST['id'], ENT_QUOTES, "UTF-8");}

	$result = mysqli_query($conn, "SELECT id FROM cargo_line WHERE issuance_id='".$id."'");
	
	if(mysqli_num_rows($result)==0){
		mysqli_query($conn,"DELETE FROM issuance_doc WHERE issuance_id='".$id."'") or die(mysqli_error($conn));
	}
		
}
	
	mysqli_close($conn);


?>

