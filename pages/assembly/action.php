<?php
error_reporting(E_ALL ^ E_NOTICE);
require('../../lock.php');

$page_file="assembly";


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

$r = $id = $action = null;
if (isset($_GET['r'])){$r = htmlentities($_GET['r'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['id'])){$id = htmlentities($_GET['id'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['action'])){$action = htmlentities($_GET['action'], ENT_QUOTES, "UTF-8");}


include('../../functions/base.php');
require('../../inc/s.php');

if($action=='deleteLine' && $id){
	
	deleteSomeRow("cargo_line", "WHERE id = '".intval($id)."'");
	
}

if($action=='approveLine' && $id){
	
	$form_data = array(
	'status' => 10
	);
	updateSomeRow("cargo_header", $form_data, "WHERE id='".intval($id)."' LIMIT 1");
	
}

if($action=='receiveLine' && $id){
	
	$form_data = array(
	'status' => 20
	);
	updateSomeRow("cargo_header", $form_data, "WHERE id='".intval($id)."' LIMIT 1");
	
}

if($action=='cancelLine' && $id){


	$lineId = intval($_GET['lineId']);

	if($lineId){

		$form_data = array(
			'issueActivityDate' => '',
			'issue_to' => '',
			'issueAmount' => '',
			'issue_assistant_amount' => '',
			'action' => 20,	
			'issue_resource' => ''
			);
			updateSomeRow("cargo_line", $form_data, "WHERE id='".intval($lineId)."'");

		$form_data = array(	
			'lastBy' => $myid,
			'lastDate' => date('Y-m-d H:i:s')
			);
			updateSomeRow("cargo_header", $form_data, "WHERE id='".intval($id)."' LIMIT 1");			
	}	

}







if($action=='moveLine' && $id){

	$lineId = intval($_GET['lineId']);

	if($lineId){


		$query = mysqli_query($conn, "SELECT * FROM cargo_line WHERE id='".$lineId."'");
		$row = mysqli_fetch_array($query);


		if($row['amount']==$row['issueAmount']){

			$docNr = $row['docNr'];
			$docNrTo = $row['issue_to'];
			$activityDate = $row['issueActivityDate'];
			$productNr = $row['productNr'];
			$amount = $row['issueAmount'];
			$location = $row['location'];
			
			$productUmo = $row['productUmo'];
			$resourceNr = $row['issue_resource'];

			$issue_assistant_amount = $row['issue_assistant_amount'];

			$assistantUmo = $row['assistantUmo'];
			
			$tare = $row['tare'];
			$gross = $row['gross'];
			$net = $row['net'];
			$cubicMeters = $row['cubicMeters'];
			$serialNo = $row['serialNo'];
			$batchNo = $row['batchNo'];
			$lot_no = $row['lot_no'];
			$container_type_no = $row['container_type_no'];
			$thisTransport = $row['thisTransport'];
			$declaration_type_no = $row['declaration_type_no'];
			$cargo_status = $row['cargo_status'];
			$seal_no = $row['seal_no'];
			$weighing_act_no = $row['weighing_act_no'];				

			$form_data = array(
				'docNr' => $docNrTo,
				'activityDate' => $row['issueActivityDate'],
				'amount' => $row['amount'],
				'assistant_amount' => $row['assistant_amount'],
				'action' => 20,	
				'issueActivityDate' => '',
				'issue_to' => '',
				'issueAmount' => '',
				'issue_assistant_amount' => '',
				'action' => 20,	
				'issue_resource' => ''
				);
				updateSomeRow("cargo_line", $form_data, "WHERE id='".$lineId."'");	

			
				$result = mysqli_query($conn,"SELECT * FROM cargo_header WHERE docNr='".$docNr."'");
				$row = mysqli_fetch_array($result);
				$cargoCode = $row['cargoCode'];
				$clientCode = $row['clientCode'];
				$clientName = $row['clientName'];
				$ownerCode = $row['ownerCode'];
				$ownerName = $row['ownerName'];
				$receiverCode = $row['receiverCode'];
				$receiverName = $row['receiverName'];
				$deliveryDate = $row['deliveryDate'];

				$form_data_log_n = array(
				'docNr' => $docNr,
				'deliveryDate' => $deliveryDate,
				'activityDate' => $activityDate,
				'cargoCode' => $cargoCode,
				'type' => 'negative',				
				'productNr' => $productNr,
				'amount' => $amount*(-1),
				'assistant_amount' => $issue_assistant_amount*(-1),
				'location' => $location,
				'clientCode' => $clientCode,
				'clientName' => $clientName,
				'ownerCode' => $ownerCode,
				'ownerName' => $ownerName,
				'receiverCode' => $receiverCode,
				'receiverName' => $receiverName,
				'enteredDate' => date('Y-m-d H:i:s'),
				'enteredBy' => $myid,
				'orgLine' => $lineId,
				'transfer' => 1,
				'action' => 20,
				'productUmo' => $productUmo,
				'assistantUmo' => $assistantUmo,
				'status' => 20,
				'resource' => $resourceNr,
				'tare' => $tare,
				'gross' => $gross,
				'net' => $net,
				'cubicMeters' => $cubicMeters,
				'serialNo' => $serialNo,
				'batchNo' => $batchNo,
				'lot_no' => $lot_no,
				'container_type_no' => $container_type_no,
				'thisTransport' => $thisTransport,
				'declaration_type_no' => $declaration_type_no,
				'cargo_status' => $cargo_status,
				'seal_no' => $seal_no,
				'weighing_act_no' => $weighing_act_no			
				);
				insertNewRows("item_ledger_entry", $form_data_log_n);
				


				$form_data_log_p = array(
				'docNr' => $docNrTo,
				'deliveryDate' => $deliveryDate,
				'activityDate' => $activityDate, 
				'cargoCode' => returnCargoCode($conn, $docNrTo, 2),
				'type' => 'positive',				
				'productNr' => $productNr,
				'amount' => $amount,
				'assistant_amount' => $issue_assistant_amount,
				'location' => $location,	
				'clientCode' => $clientCode,
				'clientName' => $clientName,
				'ownerCode' => $ownerCode,
				'ownerName' => $ownerName,
				'receiverCode' => $receiverCode,
				'receiverName' => $receiverName,				
				'enteredDate' => date('Y-m-d H:i:s'),
				'enteredBy' => $myid,
				'orgLine' => $lineId,
				'transfer' => 1,
				'action' => 20,
				'productUmo' => $productUmo,
				'assistantUmo' => $assistantUmo,
				'status' => 20,
				'resource' => $resourceNr,
				'tare' => $tare,
				'gross' => $gross,
				'net' => $net,
				'cubicMeters' => $cubicMeters,
				'serialNo' => $serialNo,
				'batchNo' => $batchNo,
				'lot_no' => $lot_no,
				'container_type_no' => $container_type_no,
				'thisTransport' => $thisTransport,
				'declaration_type_no' => $declaration_type_no,
				'cargo_status' => $cargo_status,
				'seal_no' => $seal_no,
				'weighing_act_no' => $weighing_act_no				
				);
				insertNewRows("item_ledger_entry", $form_data_log_p);			


		}else{

			$docNr = $row['docNr'];
			$docNrTo = $row['issue_to'];
			$activityDate = $row['issueActivityDate'];
			$productNr = $row['productNr'];
			$amount = $row['issueAmount'];
			$location = $row['location'];

			$productUmo = $row['productUmo'];
			$resourceNr = $row['issue_resource'];
			
			$leftAmount = $row['amount']-$row['issueAmount'];

			$mThisDate = $row['thisDate'];
			$mThisTransport = $row['thisTransport'];

			$assistant_amount = $row['assistant_amount'];
			$issue_assistant_amount = $row['issue_assistant_amount'];
			
			$assistantLeftAmount = $assistant_amount-$issue_assistant_amount;

			$assistantUmo = $row['assistantUmo'];
			
			$tare = $row['tare'];
			$gross = $row['gross'];
			$net = $row['net'];
			$cubicMeters = $row['cubicMeters'];
			$serialNo = $row['serialNo'];
			$batchNo = $row['batchNo'];
			$lot_no = $row['lot_no'];
			$container_type_no = $row['container_type_no'];
			$thisTransport = $row['thisTransport'];
			$declaration_type_no = $row['declaration_type_no'];
			$cargo_status = $row['cargo_status'];
			$seal_no = $row['seal_no'];
			$weighing_act_no = $row['weighing_act_no'];				
				
			$form_data = array(
			'activityDate' => $activityDate,
			'amount' => $leftAmount,
			'assistant_amount' => $assistantLeftAmount,	
			'issueActivityDate' => '',
			'issue_to' => '',
			'issueAmount' => '',
			'issue_assistant_amount' => '',
			'action' => 20,	
			'issue_resource' => '' 			
			);
			updateSomeRow("cargo_line", $form_data, "WHERE id='".$lineId."'");	

			
			$form_data_i = array(
			'docNr' => $docNrTo,
			'batchNo' => $batchNo,
			'activityDate' => $activityDate,
			'productNr' => $productNr,
			'productUmo' => $productUmo,
			'amount' => $row['issueAmount'],
			'assistant_amount' => $issue_assistant_amount,
			'enteredDate' => date('Y-m-d H:i:s'),
			'location' => $location,
			
			'assistantUmo' => $assistantUmo,
			'enteredBy' => $myid,
			'thisDate' => $mThisDate,
			'thisTransport' => $mThisTransport,
			'orgLine' => $lineId,
			'status' => 20,
			'action' => 20,	
						
			);
			insertNewRows("cargo_line", $form_data_i);

			$result = mysqli_query($conn,"SELECT * FROM cargo_header WHERE docNr='".$docNr."'");
			$row = mysqli_fetch_array($result);
			$cargoCode = $row['cargoCode'];
			$clientCode = $row['clientCode'];
			$clientName = $row['clientName'];
			$ownerCode = $row['ownerCode'];
			$ownerName = $row['ownerName'];
			$receiverCode = $row['receiverCode'];
			$receiverName = $row['receiverName'];
			$deliveryDate = $row['deliveryDate'];

			$form_data_log_n = array(
			'docNr' => $docNr,
			'deliveryDate' => $deliveryDate,
			'activityDate' => $activityDate, 
			'cargoCode' => $cargoCode,	
			'type' => 'negative',				
			'productNr' => $productNr,
			'amount' => $amount*(-1),
			'assistant_amount' => $issue_assistant_amount*(-1),
			'location' => $location,	
			'clientCode' => $clientCode,
			'clientName' => $clientName,
			'ownerCode' => $ownerCode,
			'ownerName' => $ownerName,
			'receiverCode' => $receiverCode,
			'receiverName' => $receiverName,				
			'enteredDate' => date('Y-m-d H:i:s'),
			'enteredBy' => $myid,
			'orgLine' => $lineId,
			'transfer' => 1,
			'action' => 20,
			'productUmo' => $productUmo,
			'assistantUmo' => $assistantUmo,
			'status' => 20,
			'resource' => $resourceNr,
			'tare' => $tare,
			'gross' => $gross,
			'net' => $net,
			'cubicMeters' => $cubicMeters,
			'serialNo' => $serialNo,
			'batchNo' => $batchNo,
			'lot_no' => $lot_no,
			'container_type_no' => $container_type_no,
			'thisTransport' => $thisTransport,
			'declaration_type_no' => $declaration_type_no,
			'cargo_status' => $cargo_status,
			'seal_no' => $seal_no,
			'weighing_act_no' => $weighing_act_no					
			);
			insertNewRows("item_ledger_entry", $form_data_log_n);
			
			$form_data_log_p = array(
			'docNr' => $docNrTo,
			'deliveryDate' => $deliveryDate,
			'activityDate' => $activityDate, 
			'cargoCode' => $cargoCode,	
			'type' => 'positive',				
			'productNr' => $productNr,
			'amount' => $amount,
			'assistant_amount' => $issue_assistant_amount,
			'location' => $location,	
			'clientCode' => $clientCode,
			'clientName' => $clientName,
			'ownerCode' => $ownerCode,
			'ownerName' => $ownerName,
			'receiverCode' => $receiverCode,
			'receiverName' => $receiverName,				
			'enteredDate' => date('Y-m-d H:i:s'),
			'enteredBy' => $myid,
			'orgLine' => $lineId,
			'transfer' => 1,
			'action' => 20,
			'productUmo' => $productUmo,
			'assistantUmo' => $assistantUmo,
			'status' => 20,
			'resource' => $resourceNr,
			'tare' => $tare,
			'gross' => $gross,
			'net' => $net,
			'cubicMeters' => $cubicMeters,
			'serialNo' => $serialNo,
			'batchNo' => $batchNo,
			'lot_no' => $lot_no,
			'container_type_no' => $container_type_no,
			'thisTransport' => $thisTransport,
			'declaration_type_no' => $declaration_type_no,
			'cargo_status' => $cargo_status,
			'seal_no' => $seal_no,
			'weighing_act_no' => $weighing_act_no					
			);
			insertNewRows("item_ledger_entry", $form_data_log_p);



		}

		$form_data = array(	
			'lastBy' => $myid,
			'lastDate' => date('Y-m-d H:i:s')
			);
			updateSomeRow("cargo_header", $form_data, "WHERE id='".intval($id)."' LIMIT 1");
					
	}
}



	
	mysqli_close($conn);


?>

