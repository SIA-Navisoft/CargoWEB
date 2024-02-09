<?php
require_once('functions/base.php');
require('inc/s.php');

		//testPost
		$issuance_id = 'ID00340';


		$checkIssueance = mysqli_query($conn, "SELECT issueDate, actualDate, resource, thisTransport FROM issuance_doc WHERE issuance_id='".$issuance_id."'");
		
		if(mysqli_num_rows($checkIssueance)==1){
			
			$iRow = mysqli_fetch_array($checkIssueance);
			
			
			$issueDateFinal = date('Y-m-d', strtotime($iRow['issueDate']));
			$actualDateFinal = date('Y-m-d', strtotime($iRow['actualDate']));

			$resource = mysqli_real_escape_string($conn, $iRow['resource']);
			
			$header_thisTransport = $iRow['thisTransport'];
			$header_declaration_type_no = $iRow['declaration_type_no'];
				
			$query = mysqli_query($conn, "SELECT * FROM cargo_line WHERE issuance_id='".$issuance_id."' and status=30");	
				
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
						
						echo '<pre>';
						print_r($form_data);
						echo '</pre>';

						


						
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
							
							echo '<pre>';
							print_r($form_data);
							echo '</pre>';							
							
						}//while

						


				
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
						
						echo '<pre>';
						print_r($form_data);
						echo '</pre>';							

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
						echo $query;
						
			
						
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
						
						echo '<pre>';
						print_r($form_data);
						echo '</pre>';


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
							
							echo '<pre>';
							print_r($form_data2);
							echo '</pre>';
							
						}					
						
						echo $lineIdFinal.' finish part ';
					}//values changed

					if(floatval($row['amount'])!=$issueAmountFinal && $issueAmountFinal==0){
						echo $lineIdFinal.' do not change';
					}
					
				}//if($lineIdFinal)				

			}//for loop
			
		}
		
?>		